<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 错误调试.
 *
 * @copyright (C) RainbowSoft Studio
 */

/**
 * 显示全局变量.
 *
 * @return mixed
 *
 * @since 1.3.140614
 *
 * @todo 下版转到debug页
 */
function Debug_PrintGlobals()
{
    $a = array();
    foreach ($GLOBALS as $n => $v) {
        $a[] = $n;
    }

    return print_r($a, true);
}

/**
 *  打印全局Include文件.
 *
 * @return string
 *
 * @since 1.3
 *
 * @todo 下版转到debug页
 */
function Debug_PrintIncludefiles()
{
    $a = array();
    foreach (get_included_files() as $n => $v) {
        $a[] = $v;
    }

    return print_r($a, true);
}

/**
 *  打印全局自定义常量.
 *
 * @return string
 *
 * @since 1.3
 *
 * @todo 下版转到debug页
 */
function Debug_PrintConstants()
{
    $a = get_defined_constants(true);
    if (isset($a['user'])) {
        $a = $a['user'];
    }

    return print_r($a, true);
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
    if (ZBlogException::$iswarning == false) {
        if ($errno == E_WARNING) {
            return true;
        }

        if ($errno == E_USER_WARNING) {
            return true;
        }
    }
    if (ZBlogException::$isstrict == false) {
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
    if (ZBlogException::$isdisable == true) {
        return true;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Error', array($errno, $errstr, $errfile, $errline));
    }

    $_SERVER['_error_count'] = $_SERVER['_error_count'] + 1;

    if (ZBlogException::$islogerror == true) {
        Logs(var_export(array('Error', $errno, $errstr, $errfile, $errline), true), true);
    }

    if (is_readable($errfile)) {
        $a = array_slice(file($errfile), max(0, $errline - 1), 1, true);
        $s = reset($a);
        if (strpos($s, '@') !== false) {
            return true;
        }
    }

    if (Debug_IgnoreError($errno)) {
        return true;
    }

    $zbe = ZBlogException::GetInstance();
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
    if (ZBlogException::$isdisable == true) {
        return true;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname('Exception', $exception);
    }

    $_SERVER['_error_count'] = $_SERVER['_error_count'] + 1;

    if (ZBlogException::$islogerror) {
        Logs(var_export(
            array('Exception',
                $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(),
            ), true), true);
    }

    $zbe = ZBlogException::GetInstance();
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
        if (ZBlogException::$isdisable == true) {
            return true;
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Handler'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname('Shutdown', $error);
        }

        $_SERVER['_error_count'] = $_SERVER['_error_count'] + 1;

        if (ZBlogException::$islogerror) {
            Logs(var_export(array('Shutdown', $error['type'], $error['message'], $error['file'], $error['line']), true), true);
        }

        if (Debug_IgnoreError($error['type'])) {
            return true;
        }

        $zbe = ZBlogException::GetInstance();
        $zbe->ParseShutdown($error);
        $zbe->Display();
    }

    return true;
}

function Debug_DoNothing()
{
    return false;
}

/**
 * Class ZBlogException.
 */
class ZBlogException
{
    private static $_zbe = null;
    public static $isdisable = false;
    private static $_isdisable = null;
    public static $isstrict = false;
    public static $iswarning = true;
    public static $error_id = 0;
    public static $error_file = null;
    public static $error_line = null;
    public static $islogerror = false;
    public $type;
    public $message;
    public $messagefull;
    public $file;
    public $line;
    public $errarray = array();

    /**
     * 构造函数，定义常见错误代码
     */
    public function __construct()
    {
        $this->errarray = array(
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
    }

    /**
     * 获取参数.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'typeName') {
            if (isset($this->errarray[$this->type])) {
                return $this->errarray[$this->type];
            } else {
                return $this->errarray[0];
            }
        }
    }

    /**
     * 获取单一实例.
     *
     * @return ZBlogException
     */
    public static function GetInstance()
    {
        if (!isset(self::$_zbe)) {
            self::$_zbe = new self();
        }

        return self::$_zbe;
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
        self::$isdisable = false;
    }

    /**
     * 禁止错误调度.
     */
    public static function DisableErrorHook()
    {
        self::$isdisable = true;
    }

    /**
     * 暂停错误调度.
     */
    public static function SuspendErrorHook()
    {
        if (self::$_isdisable !== null) {
            return;
        }

        self::$_isdisable = self::$isdisable;
        self::$isdisable = true;
    }

    /**
     * 恢复错误调度.
     */
    public static function ResumeErrorHook()
    {
        if (self::$_isdisable === null) {
            return;
        }

        self::$isdisable = self::$_isdisable;
        self::$_isdisable = null;
    }

    /**
     * 恢复错误调度.
     */
    public static function DisableStrict()
    {
        self::$isstrict = false;
    }

    public static function EnableStrict()
    {
        self::$isstrict = true;
    }

    public static function DisableWarning()
    {
        self::$iswarning = false;
    }

    public static function EnableWarning()
    {
        self::$iswarning = true;
    }

    public static function Trace($s)
    {
        Logs($s);
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
        $this->type = $type;
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
        $this->type = $error['type'];
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
        $this->type = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();

        if (self::$error_file !== null) {
            $this->file = self::$error_file;
        }

        if (self::$error_line !== null) {
            $this->line = self::$error_line;
        }
    }

    /**
     * 输出错误信息.
     */
    public function Display()
    {
        if (!headers_sent()) {
            Http500();
            ob_clean();
        }

        $error = $this;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Debug_Display'] as $fpname => &$fpsignal) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            $fpreturn = $fpname($error);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                return $fpreturn;
            }
        }

        require dirname(__FILE__) . '/../defend/error.php';
        RunTime();

        /*
         * ``flush()`` and ``exit($errorCode)`` is for HHVM.
         * @link https://github.com/zblogcn/zblogphp/issues/32
         */
        flush();
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

        $aFile = array_slice(file($file), max(0, $line - 5), 10, true);
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
        if (self::$error_id != 0) {
            // 代表Z-BlogPHP自身抛出的错误
            if (isset($lang['error_reasons'][self::$error_id])) {
                $result = $lang['error_reasons'][self::$error_id];
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

        $errorId = urlencode(self::$error_id);
        $errorMessage = urlencode($this->message);
        $moreHelp = $lang['offical_urls']['more_help'];
        $moreHelp = str_replace('{%id%}', $errorId, $moreHelp);
        $moreHelp = str_replace('{%message%}', $errorMessage, $moreHelp);

        $result .= $lang['error_reasons']['end'];
        $result = str_replace('{%bloghost%}', $bloghost, $result);
        $result = str_replace('{%morehelp%}', $moreHelp, $result);

        return $result;
    }
}
