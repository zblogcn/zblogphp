<?php

/**
 * 错误调试.
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 显示全局变量.(下版转到debug页,虽然还没有做，但加了todo检查会报错)
 *
 * @return mixed
 *
 * @since 1.3.140614
 */
function Debug_PrintGlobals()
{
    $a = array();
    foreach ($GLOBALS as $n => $v) {
        $a[] = $n;
    }

    return call_user_func('print_r', $a, true);
}

/**
 *  打印全局Include文件.(下版转到debug页,虽然还没有做，但加了todo检查会报错)
 *
 * @return string
 *
 * @since 1.3
 */
function Debug_PrintIncludefiles()
{
    $a = array();
    foreach (get_included_files() as $n => $v) {
        $a[] = $v;
    }

    return call_user_func('print_r', $a, true);
}

/**
 *  打印全局自定义常量.(下版转到debug页,虽然还没有做，但加了todo检查会报错)
 *
 * @return string
 *
 * @since 1.3
 */
function Debug_PrintConstants()
{
    $a = get_defined_constants(true);
    if (isset($a['user'])) {
        $a = $a['user'];
    }

    return call_user_func('print_r', $a, true);
}

/**
 * Return true if a error can be ignored.
 *
 * @param int $errno
 *
 * @return bool
 */
function Debug_IgnoreError($errno)
{
    if (ZBlogErrorContrl::$iswarning == false) {
        if ($errno == E_WARNING) {
            return true;
        }

        if ($errno == E_USER_WARNING) {
            return true;
        }
    }
    if (ZBlogErrorContrl::$isstrict == false) {
        if ($errno == E_STRICT) {
            return true;
        }

        if ($errno == E_NOTICE) {
            return true;
        }

        if ($errno == E_USER_NOTICE) {
            return true;
        }
    }

    // 屏蔽系统的错误，防ZBP报系统的错误，不过也有可能导致ZBP内的DEPRECATED错误也被屏蔽了
    if ($errno == E_CORE_WARNING) {
        return true;
    }

    if ($errno == E_COMPILE_WARNING) {
        return true;
    }

    if (defined('E_DEPRECATED') && $errno == E_DEPRECATED) {
        return true;
    }

    if (defined('E_USER_DEPRECATED') && $errno == E_USER_DEPRECATED) {
        return true;
    }

    //E_USER_ERROR
    //E_RECOVERABLE_ERROR

    if (defined('ZBP_ERRORPROCESSING')) {
        return true;
    }

    return false;
}

/**
 * 错误调度提示.
 *
 * @param int    $errno   错误级别
 * @param string $errstr  错误信息
 * @param string $errfile 错误文件名
 * @param int    $errline 错误行
 *
 * @return bool
 */
function Debug_Error_Handler($errno, $errstr, $errfile, $errline)
{
    ZBlogErrorContrl::$errors_msg[] = array($errno, $errstr, $errfile, $errline);
    if (ZBlogErrorContrl::$disabled == true) {
        return true;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Error', array($errno, $errstr, $errfile, $errline));
    }

    if (isset($_SERVER['_error_count'])) {
        $_SERVER['_error_count'] = ($_SERVER['_error_count'] + 1);
    }

    if (ZBlogErrorContrl::$islogerror == true) {
        Logs(var_export(array('Error', $errno, $errstr, $errfile, $errline), true), 'ERROR');
    }

    //@符号的错误抑制功能的实现 php7 || php8
    if (error_reporting() == 0 || !(error_reporting() & $errno)) {
        return true;
    }

    if (Debug_IgnoreError($errno)) {
        return true;
    }

    $zbe = ZBlogErrorContrl::GetNewException('Error');
    $zbe->ParseError($errno, $errstr, $errfile, $errline);
    $zbe->Display();

    return true;
}

/**
 * 异常处理.
 *
 * @param Exception $exception 异常事件
 *
 * @return bool
 */
function Debug_Exception_Handler($exception)
{
    ZBlogErrorContrl::$errors_msg[] = array($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
    if (ZBlogErrorContrl::$disabled == true) {
        return true;
    }
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Exception', $exception);
    }

    if (isset($_SERVER['_error_count'])) {
        $_SERVER['_error_count'] = ($_SERVER['_error_count'] + 1);
    }

    if (ZBlogErrorContrl::$islogerror) {
        Logs(
            var_export(
                array(
                    'Exception',
                    $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(),
                ),
                true
            ),
            'ERROR'
        );
    }

    $zbe = ZBlogErrorContrl::GetNewException(get_class($exception));
    $zbe->ParseException($exception);
    $zbe->Display();

    return true;
}

/**
 * 当机错误处理.
 *
 * @return bool
 */
function Debug_Shutdown_Handler()
{
    if ($error = error_get_last()) {
        ZBlogErrorContrl::$errors_msg[] = array($error['type'], $error['message'], $error['file'], $error['line']);
        if (ZBlogErrorContrl::$disabled == true) {
            return true;
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname('Shutdown', $error);
        }

        if (isset($_SERVER['_error_count'])) {
            $_SERVER['_error_count'] = ($_SERVER['_error_count'] + 1);
        }

        if (ZBlogErrorContrl::$islogerror) {
            Logs(var_export(array('Shutdown', $error['type'], $error['message'], $error['file'], $error['line']), true), 'FATAL');
        }

        if (Debug_IgnoreError($error['type'])) {
            return true;
        }

        $zbe = ZBlogErrorContrl::GetNewException('Shutdown');
        $zbe->ParseShutdown($error);
        $zbe->Display();
    }

    return true;
}

/**
 * Debug DoNothing
 */
function Debug_DoNothing()
{
    return true;
}


/**
 * Class ZbpErrorException.
 */
class ZbpErrorException extends Exception
{
    public $moreinfo = null;
    public $http_code = 500;
    public $messagefull = null;

    public function __construct($message = "", $code = 0, $previous = null, $file = '', $line = 0)
    {
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
    }
}

/**
 * Class ZBlogErrorContrl
 */
class ZBlogErrorContrl
{

    /**
     * @var object 单例模式下的ZBE唯一实例
     */
    private static $private_zbe = null;

    /**
     * 最后一个last_zee
     */
    private static $last_zee = null;

    /**
     * 错误显示输出
     */
    public static $display_error = true;

    /**
     * 静态disabled
     */
    public static $disabled = false;

    /**
     * 静态isstrict
     */
    public static $isstrict = false;

    /**
     * 静态iswarning
     */
    public static $iswarning = true;

    /**
     * 静态islogerror
     */
    public static $islogerror = false;

    /**
     * 静态errors_msg
     */
    public static $errors_msg = array();

    /**
     * 代码
     */
    public $code;

    /**
     * 类型(Error, Exception, Shutdown, ZbpErrorException)
     */
    public $type;

    /**
     * 消息
     */
    public $message;

    /**
     * 完全消息
     */
    public $messagefull;

    /**
     * 更多信息 (可能是数组)
     */
    public $moreinfo;

    /**
     * 文件
     */
    public $file;

    /**
     * 行号
     */
    public $line;

    /**
     * http_code
     */
    public $http_code = 500;

    /**
     * 错误数组
     */
    public static $errarray = array(
        0     => 'UNKNOWN',
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
        30719 => 'E_ALL',
    );

    /**
     * 构造函数，定义常见错误代码
     */
    public function __construct()
    {
    }

    public static function ThrowException($error)
    {
        $e = var_export($error, true);
        throw new Exception($e);
    }

    public static function GetNewException($error_type)
    {
        $z = new self();
        $z->type = $error_type;
        return $z;
    }

    /**
     * 获取ZBE实例
     *
     * @return ZBlogException
     */
    public static function GetInstance()
    {
        if (!is_object(self::$private_zbe)) {
            self::$private_zbe = new ZBlogException();
        }
        return self::$private_zbe();
    }

    /**
     * 清除之前并获取最后一个ZEE
     *
     * @return ZbpErrorException
     */
    public static function SetLastZEE($zee)
    {
        self::$last_zee = $zee;
        return self::GetLastZEE();
    }

    /**
     * 获取最后一个ZEE
     *
     * @return ZbpErrorException
     */
    public static function GetLastZEE()
    {
        return self::$last_zee;
    }

    /**
     * 清除最后一个ZEE
     *
     * @return ZbpErrorException
     */
    public static function ClearLastZEE()
    {
        self::$last_zee = null;
        return null;
    }

    /**
     * 设定错误处理函数.
     */
    public static function SetErrorHook()
    {
        if (IS_CLI) {
            return;
        }
        set_error_handler('Debug_Error_Handler');
        set_exception_handler('Debug_Exception_Handler');
        register_shutdown_function('Debug_Shutdown_Handler');
    }

    /**
     * 清除注册的错误处理程序.
     */
    public static function ClearErrorHook()
    {
        set_error_handler('Debug_DoNothing');
        set_exception_handler('Debug_DoNothing');
        register_shutdown_function('Debug_DoNothing');
    }

    /**
     * 启用错误调度.
     */
    public static function EnableErrorHook()
    {
        self::$disabled = false;
    }

    /**
     * 禁止错误调度.
     */
    public static function DisableErrorHook()
    {
        self::$disabled = true;
    }

    private static $disabled_old_state = null;

    /**
     * 暂停错误调度.
     */
    public static function SuspendErrorHook()
    {
        if (is_null(self::$disabled_old_state)) {
            self::$disabled_old_state = self::$disabled;
        }
        self::DisableErrorHook();
    }

    /**
     * 恢复错误调度.
     */
    public static function ResumeErrorHook()
    {
        self::EnableErrorHook();
        if (!is_null(self::$disabled_old_state)) {
            self::$disabled = self::$disabled_old_state;
            self::$disabled_old_state = null;
        }
    }

    /**
     * 禁用严格模式.
     */
    public static function DisableStrict()
    {
        self::$isstrict = false;
    }

    /**
     * 启用严格模式.
     */
    public static function EnableStrict()
    {
        self::$isstrict = true;
    }

    /**
     * 禁用警告模式.
     */
    public static function DisableWarning()
    {
        self::$iswarning = false;
    }

    /**
     * 启用警告模式.
     */
    public static function EnableWarning()
    {
        self::$iswarning = true;
    }

    /**
     * Trace记录错误.
     *
     * @param string $s
     */
    public static function Trace($s)
    {
        Logs($s, 'TRACE');
    }

    /**
     * 解析错误信息.
     *
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     */
    public function ParseError($type, $message, $file, $line)
    {
        $this->code = $type;
        $this->message = $message;
        $this->messagefull = $message . ' (set_error_handler) ';
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * 解析错误信息.
     *
     * @param $error
     */
    public function ParseShutdown($error)
    {
        $this->code = $error['type'];
        $this->message = $error['message'];
        $this->messagefull = $error['message'] . ' (register_shutdown_function) ';
        $this->file = $error['file'];
        $this->line = $error['line'];
    }

    /**
     * 解析异常信息.
     *
     * @param Exception $exception
     */
    public function ParseException($exception)
    {
        $this->message = $exception->getMessage();
        $this->messagefull = $exception->getMessage() . ' (set_exception_handler) ';
        if (is_a($exception, 'Error')) {
            $this->messagefull = $exception->getMessage() . ' (set_error_handler) ';
        }
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        if (get_class($exception) == 'ZbpErrorException') {
            $this->moreinfo = $exception->moreinfo;
            $this->messagefull = $exception->messagefull;
            $this->http_code = $exception->http_code;
        }
    }

    /**
     * 输出错误信息.
     */
    public function Display()
    {
        if (self::$display_error == false) {
            return;
        }
        if (!headers_sent()) {
            SetHttpStatusCode($this->http_code);
        }
        @ob_clean();
        $error = $this;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Display'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($error);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        include dirname(__FILE__) . '/../defend/error.php';
        RunTime();

        if (IS_CLI && (IS_WORKERMAN || IS_SWOOLE)) {
            return true;
        }

        flush();
        /*
         * ``flush()`` and ``exit($errorCode)`` is for HHVM.
         * @link https://github.com/zblogcn/zblogphp/issues/32
         */
        exit(1);
    }

    /**
     * 获取出错代码信息.
     *
     * @param $file
     * @param $line
     *
     * @return array
     */
    public function get_code($file, $line)
    {
        if (strcasecmp($file, 'Unknown') == 0) {
            return array();
        }

        if (!is_readable($file)) {
            return array();
        }

        $aFile = array_slice(file($file), max(0, ($line - 5)), 10, true);
        foreach ($aFile as &$sData) {
            //&$ = ByRef
            $sData = htmlspecialchars($sData);
        }

        return $aFile;
    }

    /**
     * 得到可能的错误原因.
     *
     * @return string
     */
    public function possible_causes_of_the_error()
    {
        global $lang;
        global $bloghost;
        $result = '';

        $lastzee = self::$last_zee;
        $error_id = 0;
        if (is_object($lastzee)) {
            $error_id = $lastzee->code;
        }

        if ($error_id != 0) {
            // 代表Z-BlogPHP自身抛出的错误
            if (isset($lang['error_reasons'][$error_id])) {
                $result = $lang['error_reasons'][$error_id];
            } else {
                $result = $lang['error_reasons']['default'];
            }
        }

        // 根据关键词查找错误
        $lowerErrorReason = strtolower($this->message);
        foreach ($lang['error_reasons']['other'] as $key => $value) {
            if (strpos($lowerErrorReason, $key) > -1) {
                $result .= $value;
            }
        }

        $errorId = urlencode($error_id);
        $errorMessage = urlencode($this->message);
        $moreHelp = $lang['offical_urls']['bing_help'];
        $office_docs = $lang['offical_urls']['office_docs'];
        $office_bbs = $lang['offical_urls']['office_bbs'];
        $moreHelp = str_replace('{%id%}', $errorId, $moreHelp);
        $moreHelp = str_replace('{%message%}', $errorMessage, $moreHelp);

        $result .= $lang['error_reasons']['end'];
        $result = str_replace('{%bloghost%}', $bloghost, $result);
        $result = str_replace('{%morehelp%}', $moreHelp, $result);
        $result = str_replace('{%officedocs%}', $office_docs, $result);
        $result = str_replace('{%officebbs%}', $office_bbs, $result);
        return $result;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMessageFull()
    {
        return $this->messagefull;
    }

    public function getMoreInfo()
    {
        return $this->moreinfo;
    }

    public function getHttpCode()
    {
        return $this->http_code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getTrace()
    {
        $t = debug_backtrace();
        return $t;
    }

    public function getTraceAsString()
    {
        $t = $this->getTrace();
        return call_user_func('print_r', $t, true);
    }

}

//给ZBlogException改名为ZBlogErrorContrl，然后保持延续就起了别名
if (function_exists('class_alias')) {//>5.2
    class_alias('ZBlogErrorContrl', 'ZBlogException');
} else {
    class ZBlogException extends ZBlogErrorContrl
    {
    }
}
