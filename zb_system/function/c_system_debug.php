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
    if (ZbpErrorContrl::$iswarning == false) {
        if ($errno == E_WARNING) {
            return true;
        }

        if ($errno == E_USER_WARNING) {
            return true;
        }
    }
    if (ZbpErrorContrl::$isstrict == false) {
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
    ZbpErrorContrl::LogErrorInfo($errno, $errstr, $errfile, $errline, 'Error');
    if (ZbpErrorContrl::$disabled == true) {
        return true;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Error', array($errno, $errstr, $errfile, $errline));
    }

    if (ZbpErrorContrl::$islogerror == true) {
        Logs(var_export(array('Error', $errno, $errstr, $errfile, $errline), true), 'ERROR');
    }

    //@符号的错误抑制功能的实现 php7 || php8
    if (error_reporting() == 0 || !(error_reporting() & $errno)) {
        return true;
    }

    if (Debug_IgnoreError($errno)) {
        return true;
    }

    $zbe = new ZbpErrorContrl();
    $zee = $zbe->ParseError($errno, $errstr, $errfile, $errline);

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Parse'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return true;
        }
    }

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
    ZbpErrorContrl::LogErrorInfo($exception);
    if (ZbpErrorContrl::$disabled == true) {
        return true;
    }
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Exception', $exception);
    }

    if (ZbpErrorContrl::$islogerror) {
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

    $zbe = new ZbpErrorContrl();
    $zee = $zbe->ParseException($exception);

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Parse'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return true;
        }
    }

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
        ZbpErrorContrl::LogErrorInfo($error['type'], $error['message'], $error['file'], $error['line'], 'Shutdown');
        if (ZbpErrorContrl::$disabled == true) {
            return true;
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname('Shutdown', $error);
        }

        if (ZbpErrorContrl::$islogerror) {
            Logs(var_export(array('Shutdown', $error['type'], $error['message'], $error['file'], $error['line']), true), 'FATAL');
        }

        if (Debug_IgnoreError($error['type'])) {
            return true;
        }

        $zbe = new ZbpErrorContrl();
        $zee = $zbe->ParseShutdown($error);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Parse'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($zee);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return true;
            }
        }

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
    public $moreinfo = array();
    public $httpcode = 500;
    public $messagefull = null;
    /**
     * 类型(Error, Exception, Shutdown, ZbpErrorException)
     */
    public $type = __CLASS__;

    public function __construct($message = "", $code = 0, $previous = null, $file = '', $line = 0)
    {
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
    }

    public function getType()
    {
        return $this->type;
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
        return $this->httpcode;
    }
}

/**
 * Class ZbpErrorContrl (原名ZBlogException)
 */
class ZbpErrorContrl
{

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
     * 静态error_msg_list
     */
    private static $error_msg_list = array();

    /**
     * 内部_zee
     */
    private  $private_zee = null;

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
        $this->private_zee = new ZbpErrorException();
    }

    public function __get($name)
    {
        if ($name == 'file') {
            return $this->private_zee->getFile();
        }elseif ($name == 'line') {
            return $this->private_zee->getLine();
        }elseif ($name == 'code') {
            return $this->private_zee->getCode();
        }elseif ($name == 'message') {
            return $this->private_zee->getMessage();
        }else {
            return $this->private_zee->$name;
        }
    }

    /**
     * 设置最后一个ZEE，如果是Error或是Exception就转换为ZEE
     *
     * @return ZbpErrorException
     */
    public static function SetLastZEE($zee)
    {
        if (is_a($zee, 'ZbpErrorException')) {
            self::$last_zee = $zee;
        } else {
            $newzee = new ZbpErrorException($zee->getMessage(), $zee->getCode(), null, $zee->getFile(), $zee->getLine());
            self::$last_zee = $newzee;
        }

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
     * LogErrorInfo
     *
     * @return true
     */
    public static function LogErrorInfo($code, $message = null, $file = null, $line = null, $type = null)
    {
        if (is_a($code, 'Exception') || is_a($code, 'Error')) {
            $array = array();
            $array = array('code' => $code->getCode(), 'message' => $code->getMessage(), 'file' => $code->getFile(), 'line' => $code->getLine(), 'type' => get_class($code));
            if (property_exists($code, 'moreinfo')) {
                if (is_array($code->moreinfo) &&!empty($code->moreinfo)) {
                    $array['moreinfo'] = $code->moreinfo;
                }
            }
            self::$error_msg_list[] = $array;
        } else {
            self::$error_msg_list[] = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line, 'type' => $type);
        }
        if (isset($_SERVER['_error_count'])) {
            $_SERVER['_error_count'] = ($_SERVER['_error_count'] + 1);
        }
        return true;
    }

    /**
     * GetErrorInfoList
     *
     * @return array
     */
    
    public static function GetErrorInfoList()
    {
        return self::$error_msg_list;
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
        $this->private_zee = new ZbpErrorException($message, $type, null, $file, $line);
        $this->private_zee->messagefull = $message . ' (set_error_handler) ';
        $this->private_zee->type = 'Error';
        self::SetLastZEE($this->private_zee);
        return $this->private_zee;
    }

    /**
     * 解析错误信息.
     *
     * @param $error
     */
    public function ParseShutdown($error)
    {
        $this->private_zee = new ZbpErrorException($error['message'], $error['type'], null, $error['file'], $error['line']);
        $this->private_zee->messagefull = $error['message'] . ' (register_shutdown_function) ';
        $this->private_zee->type = 'Shutdown';
        self::SetLastZEE($this->private_zee);
        return $this->private_zee;
    }

    /**
     * 解析异常信息.
     *
     * @param Exception $exception
     */
    public function ParseException($exception)
    {
        $this->private_zee = new ZbpErrorException($exception->getMessage(), $exception->getCode(), null, $exception->getFile(), $exception->getLine());
        $this->private_zee->messagefull = $exception->getMessage() . ' (set_exception_handler) ';
        $this->private_zee->type = get_class($exception);
        if (is_a($exception, 'Error')) {
            $this->private_zee->messagefull = $exception->getMessage() . ' (set_error_handler) ';
        }
        if (get_class($exception) == 'ZbpErrorException') {
            $this->private_zee->moreinfo = $exception->moreinfo;
            $this->private_zee->messagefull = $exception->messagefull;
            $this->private_zee->httpcode = $exception->httpcode;
        }
        self::SetLastZEE($this->private_zee);
        return $this->private_zee;
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
            SetHttpStatusCode($this->getHttpCode());
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

        $lastzee = $this->private_zee;
        $error_id = 0;
        if (is_object($lastzee)) {
            $error_id = $lastzee->GetCode();
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
        return $this->private_zee->getType();
    }

    public function getMessageFull()
    {
        return $this->private_zee->getMessageFull();
    }

    public function getMoreInfo()
    {
        return $this->private_zee->getMoreInfo();
    }

    public function getHttpCode()
    {
        return $this->private_zee->getHttpCode();
    }

    public function getMessage()
    {
        return $this->private_zee->getMessage();
    }

    public function getCode()
    {
        return $this->private_zee->getCode();
    }

    public function getFile()
    {
        return $this->private_zee->getFile();
    }

    public function getLine()
    {
        return $this->private_zee->getLine();
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

//给ZBlogException改名为ZbpErrorContrl，然后保持延续就起了别名
if (function_exists('class_alias')) {//>5.2
    class_alias('ZbpErrorContrl', 'ZBlogException');
} else {
    class ZBlogException extends ZbpErrorContrl
    {
    }
}
