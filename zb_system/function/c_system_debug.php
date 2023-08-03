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
    if (ZbpErrorControl::$iswarning == false) {
        if ($errno == E_WARNING) {
            return true;
        }

        if ($errno == E_USER_WARNING) {
            return true;
        }
    }
    if (ZbpErrorControl::$isstrict == false) {
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
    ZbpErrorControl::AddErrorList($errno, $errstr, $errfile, $errline, 'Error');
    if (ZbpErrorControl::$disabled == true) {
        return true;
    }

    //已废弃接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Error', array($errno, $errstr, $errfile, $errline));
    }

    if (ZbpErrorControl::$islogerror == true) {
        $err = ZbpErrorControl::$errarray[$errno];
        Logs(var_export(array($err, $errno, $errstr, $errfile, $errline), true), 'ERROR');
    }

    //@符号的错误抑制功能的实现 php7 || php8
    if (error_reporting() == 0 || !(error_reporting() & $errno)) {
        //if (function_exists('error_clear_last ')) {
        //    error_clear_last();
        //}
        return true;
    }

    if (Debug_IgnoreError($errno)) {
        return true;
    }

    $zec = new ZbpErrorControl();
    $zee = $zec->ParseError($errno, $errstr, $errfile, $errline);

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_ZEE'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee, 'Error');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    //这是Filter_Plugin_Zbp_ShowError接口的替代品，无须改动插件函数的参数
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_Common'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee->getCode(), $zee->getMessage(), $zee->getFile(), $zee->getLine(), $zee->getMoreInfo(), $zee->getHttpCode());
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    //原始Filter_Plugin_Debug_Handler在173已废除，如果Handler_ZEC or Common没有处理，就转入Display
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Display'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zec);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    $zec->Display();

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
    ZbpErrorControl::AddErrorList($exception);
    if (ZbpErrorControl::$disabled == true) {
        return true;
    }

    //已废弃接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Exception', $exception);
    }

    if (ZbpErrorControl::$islogerror) {
        Logs(
            var_export(
                array(
                    'Exception',
                    $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(),
                ),
                true
            ),
            'EXCEPTION'
        );
    }

    $zec = new ZbpErrorControl();
    $zee = $zec->ParseException($exception);

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_ZEE'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee, 'Exception');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_Common'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zee->getCode(), $zee->getMessage(), $zee->getFile(), $zee->getLine(), $zee->getMoreInfo(), $zee->getHttpCode());
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    //原始Filter_Plugin_Debug_Handler在173已废除，如果Handler_ZEC or Common没有处理，就转入Display
    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Display'] as $fpname => &$fpsignal) {
        $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        $fpreturn = $fpname($zec);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            return $fpreturn;
        }
    }

    $zec->Display();

    return true;
}

/**
 * 当机错误处理.(不再使用)
 *
 * @return bool
 */
function Debug_Shutdown_Handler()
{
    if ($error = error_get_last()) {
        ZbpErrorControl::AddErrorList($error['type'], $error['message'], $error['file'], $error['line'], 'Fatal');
        if (ZbpErrorControl::$disabled == true) {
            return true;
        }

        //已废弃接口
        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname('Shutdown', $error);
        }

        if (ZbpErrorControl::$islogerror) {
            Logs(var_export(array('Fatal', $error['type'], $error['message'], $error['file'], $error['line']), true), 'FATAL');
        }

        if (Debug_IgnoreError($error['type'])) {
            return true;
        }

        $zec = new ZbpErrorControl();
        $zee = $zec->ParseFatal($error);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_ZEE'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($zee, 'Fatal');
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler_Common'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($zee->getCode(), $zee->getMessage(), $zee->getFile(), $zee->getLine(), $zee->getMoreInfo(), $zee->getHttpCode());
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        //原始Filter_Plugin_Debug_Handler在173已废除，如果Handler_ZEC or Common没有处理，就转入Display
        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Display'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($zec);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        $zec->Display();
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
    /**
     * 类型(Error, Exception, Fatal, ZbpErrorException)
     */
    public $type = __CLASS__;
    public $moreinfo = array();
    public $httpcode = 500;
    public $messagefull = null;

    public function __construct($message = "", $code = 0, $previous = null, $file = '', $line = 0, $type = null, $moreinfo = array(), $httpcode = null, $messagefull = null)
    {
        if (is_array($message)) {
            $array = $message;
            $message = $array['message'];
            if (isset($array['code'])) {
                $code = $array['code'];
            }
            if (isset($array['previous'])) {
                $previous = $array['previous'];
            }
            if (isset($array['file'])) {
                $file = $array['file'];
            }
            if (isset($array['line'])) {
                $line = $array['line'];
            }
            if (isset($array['type'])) {
                $type = $array['type'];
            }
            if (isset($array['moreinfo'])) {
                $moreinfo = $array['moreinfo'];
            }
            if (isset($array['httpcode'])) {
                $httpcode = $array['httpcode'];
            }
            if (isset($array['messagefull'])) {
                $messagefull = $array['messagefull'];
            }
        }
        if (function_exists('class_alias')) {//>5.2
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
        $this->file = $file;
        $this->line = $line;
        if (is_array($moreinfo)) {
            $this->moreinfo = $moreinfo;
        }
        if (!empty($type)) {
            $this->type = $type;
        }
        if (!empty($httpcode)) {
            $this->httpcode = (int) $httpcode;
        }
        $this->messagefull = $messagefull;
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
 * Class ZbpErrorControl (原名ZBlogException)
 */
class ZbpErrorControl
{

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
        }elseif ($name == 'type') {
            return $this->private_zee->getType();
        }elseif ($name == 'messagefull') {
            return $this->private_zee->getMessageFull();
        }elseif ($name == 'httpcode') {
            return $this->private_zee->getHttpCode();
        }elseif ($name == 'moreinfo') {
            return $this->private_zee->getMoreInfo();
        }
    }

    /**
     * AddErrorList
     *
     * @return true
     */
    public static function AddErrorList($code, $message = null, $file = null, $line = null, $type = null)
    {
        if (is_a($code, 'Exception') || is_a($code, 'Error')) {
            $array = array();
            $array = array('code' => $code->getCode(), 'message' => $code->getMessage(), 'file' => $code->getFile(), 'line' => $code->getLine(), 'type' => get_class($code));
            if (property_exists($code, 'moreinfo')) {
                if (is_array($code->moreinfo) &&!empty($code->moreinfo)) {
                    $array['moreinfo'] = $code->moreinfo;
                }
            }
            if (property_exists($code, 'type')) {
                $array['type'] = $code->type;
            }
            if (property_exists($code, 'httpcode')) {
                $array['httpcode'] = $code->httpcode;
            }
            if (property_exists($code, 'messagefull')) {
                $array['messagefull'] = $code->messagefull;
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
     * GetErrorList
     *
     * @return array
     */
    
    public static function GetErrorList()
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
        //register_shutdown_function('Debug_Shutdown_Handler');
    }

    /**
     * 清除注册的错误处理程序.
     */
    public static function ClearErrorHook()
    {
        if (IS_CLI) {
            return;
        }
        set_error_handler('Debug_DoNothing');
        set_exception_handler('Debug_DoNothing');
        //register_shutdown_function('Debug_DoNothing');
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
     * 直接扔出内部zee
     */
    public function ThrowError()
    {
        throw $this->private_zee;
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
        return $this->private_zee;
    }

    /**
     * 解析错误信息.
     *
     * @param $error
     */
    public function ParseFatal($error)
    {
        $this->private_zee = new ZbpErrorException($error['message'], $error['type'], null, $error['file'], $error['line']);
        $this->private_zee->messagefull = $error['message'] . ' (register_shutdown_function) ';
        $this->private_zee->type = 'Fatal';
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
            $this->private_zee->messagefull = $exception->getMessage() . ' (set_exception_error_handler) ';
        }
        if (get_class($exception) == 'ZbpErrorException') {
            $this->private_zee->moreinfo = $exception->moreinfo;
            $this->private_zee->messagefull = $exception->messagefull;
            $this->private_zee->httpcode = $exception->httpcode;
        }
        return $this->private_zee;
    }

    /**
     * 输出错误信息.
     */
    public function Display()
    {
        if (self::$display_error == false) {
            return true;
        }
        if (!headers_sent()) {
            SetHttpStatusCode($this->getHttpCode());
        }
        @ob_clean();
        $error = $this;

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
        if (isset($lang['error_reasons']) && isset($lang['error_reasons']['other']) && is_array($lang['error_reasons']['other'])) {
            foreach ($lang['error_reasons']['other'] as $key => $value) {
                if (strpos($lowerErrorReason, $key) > -1) {
                    $result .= $value;
                }
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

//给ZBlogException改名为ZbpErrorControl，然后保持延续就起了别名
if (function_exists('class_alias')) {//>5.2
    class_alias('ZbpErrorControl', 'ZBlogException');
} else {
    class ZBlogException extends ZbpErrorControl
    {
    }
}
