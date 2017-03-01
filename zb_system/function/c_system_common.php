<?php
/**
 * 辅助通用函数
 * @package Z-BlogPHP
 * @subpackage System/CommonFunction 辅助通用函数
 * @copyright (C) RainbowSoft Studio
 */

/**
 * 得到请求方法(未必会准确的，比如SERVER没有某项，或是端口改过的)
 * @param $array
 * @return $string
 */
function GetScheme($array)
{
    if (array_key_exists('REQUEST_SCHEME', $array)) {
        if (strtolower($array['REQUEST_SCHEME']) == 'https') {
            return 'https://';
        }
    } elseif (array_key_exists('HTTPS', $array)) {
        if (strtolower($array['HTTPS']) == 'on') {
            return 'https://';
        }
    } elseif (array_key_exists('SERVER_PORT', $array)) {
        if (strtolower($array['SERVER_PORT']) == '443') {
            return 'https://';
        }
    }

    return 'http://';
}
/**
 * 获取服务器
 * @return int
 */
function GetWebServer()
{
    if (!isset($_SERVER['SERVER_SOFTWARE'])) {
        return SERVER_UNKNOWN;
    }
    $webServer = strtolower($_SERVER['SERVER_SOFTWARE']);
    if (strpos($webServer, 'apache') !== false) {
        return SERVER_APACHE;
    } elseif (strpos($webServer, 'microsoft-iis') !== false) {
        return SERVER_IIS;
    } elseif (strpos($webServer, 'nginx') !== false) {
        return SERVER_NGINX;
    } elseif (strpos($webServer, 'lighttpd') !== false) {
        return SERVER_LIGHTTPD;
    } elseif (strpos($webServer, 'kangle') !== false) {
        return SERVER_KANGLE;
    } elseif (strpos($webServer, 'caddy') !== false) {
        return SERVER_CADDY;
    } elseif (strpos($webServer, 'development server') !== false) {
        return SERVER_BUILTIN;
    } else {
        return SERVER_UNKNOWN;
    }
}

/**
 * 获取操作系统
 * @return int
 */
function GetSystem()
{
    if (in_array(strtoupper(PHP_OS), array('WINNT', 'WIN32', 'WINDOWS'))) {
        return SYSTEM_WINDOWS;
    } elseif ((strtoupper(PHP_OS) === 'UNIX')) {
        return SYSTEM_UNIX;
    } elseif (strtoupper(PHP_OS) === 'LINUX') {
        return SYSTEM_LINUX;
    } elseif (strtoupper(PHP_OS) === 'DARWIN') {
        return SYSTEM_DARWIN;
    } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'CYGWIN') {
        return SYSTEM_CYGWIN;
    } elseif (in_array(strtoupper(PHP_OS), array('NETBSD', 'OPENBSD', 'FREEBSD'))) {
        return SYSTEM_BSD;
    } else {
        return SYSTEM_UNKNOWN;
    }
}

/**
 * 获取PHP解析引擎
 * @return int
 */
function GetPHPEngine()
{
    if (defined('HHVM_VERSION')) {
        return ENGINE_HHVM;
    }

    return ENGINE_PHP;
}

/**
 * 获取PHP Version
 * @return string
 */
function GetPHPVersion()
{
    $p = phpversion();
    if (strpos($p, '-') !== false) {
        $p = substr($p, 0, strpos($p, '-'));
    }
    return $p;
}

/**
 * 自动加载类文件
 * @api Filter_Plugin_Autoload
 * @param string $classname 类名
 * @return mixed
 */
function AutoloadClass($classname)
{
    foreach ($GLOBALS['hooks']['Filter_Plugin_Autoload'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($classname);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }
    if (is_readable($f = ZBP_PATH . 'zb_system/function/lib/' . strtolower($classname) . '.php')) {
        require $f;
    }
}

/**
 * 记录日志
 * @param string $s
 * @param bool $iserror
 */
function Logs($s, $iserror = false)
{
    global $zbp;
    foreach ($GLOBALS['hooks']['Filter_Plugin_Logs'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $iserror);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }
    if ($zbp->guid) {
        if ($iserror) {
            $f = $zbp->usersdir . 'logs/' . $zbp->guid . '-error' . date("Ymd") . '.txt';
        } else {
            $f = $zbp->usersdir . 'logs/' . $zbp->guid . '-log' . date("Ymd") . '.txt';
        }
    } else {
        if ($iserror) {
            $f = $zbp->usersdir . 'logs/' . md5($zbp->path) . '-error.txt';
        } else {
            $f = $zbp->usersdir . 'logs/' . md5($zbp->path) . '.txt';
        }
    }
    ZBlogException::SuspendErrorHook();
    if ($handle = @fopen($f, 'a+')) {
        $t = date('Y-m-d') . ' ' . date('H:i:s') . ' ' . substr(microtime(), 1, 9) . ' ' . date('P');
        @fwrite($handle, '[' . $t . ']' . "\r\n" . $s . "\r\n");
        @fclose($handle);
    }
    ZBlogException::ResumeErrorHook();
}

/**
 * 页面运行时长
 * @return array
 */
function RunTime()
{
    global $zbp;

    $rt = array();
    $rt['time'] = number_format(1000 * (microtime(1) - $_SERVER['_start_time']), 2);
    $rt['query'] = $_SERVER['_query_count'];
    $rt['memory'] = $_SERVER['_memory_usage'];
    $rt['error'] = $_SERVER['_error_count'];
    if (function_exists('memory_get_usage')) {
        $rt['memory'] = (int) ((memory_get_usage() - $_SERVER['_memory_usage']) / 1024);
    }

    if (isset($zbp->option['ZC_RUNINFO_DISPLAY']) && $zbp->option['ZC_RUNINFO_DISPLAY'] == false) {
        $_SERVER['_runtime_result'] = $rt;

        return $rt;
    }

    echo '<!--' . $rt['time'] . ' ms , ';
    echo $rt['query'] . ' query';
    if (function_exists('memory_get_usage')) {
        echo ' , ' . $rt['memory'] . 'kb memory';
    }

    echo ' , ' . $rt['error'] . ' error';
    echo '-->';

    return $rt;
}

/**
 * 获得系统信息
 * @return string 系统信息
 * @since 1.4
 */
function GetEnvironment()
{
    global $zbp;
    $ajax = Network::Create();
    if ($ajax) {
        $ajax = substr(get_class($ajax), 7);
    }
    $system_environment = PHP_OS . '; ' .
    GetValueInArray(
        explode(' ', str_replace(array('Microsoft-', '/'), array('', ''), GetVars('SERVER_SOFTWARE', 'SERVER'))), 0
    ) . '; ' .
    'PHP ' . GetPHPVersion() . (IS_X64 ? ' x64' : '') . '; ' .
    $zbp->option['ZC_DATABASE_TYPE'] . '; ' . $ajax;

    return $system_environment;
}

/**
 * 通过文件获取应用URL地址
 * @param string $file 文件名
 * @return string 返回URL地址
 */
function plugin_dir_url($file)
{
    global $zbp;
    $s1 = $zbp->path;
    $s2 = str_replace('\\', '/', dirname($file) . '/');
    $s3 = '';
    $s = substr($s2, strspn($s1, $s2, 0));
    if (strpos($s, 'zb_users/plugin/') !== false) {
        $s = substr($s, strspn($s, $s3 = 'zb_users/plugin/', 0));
    } else {
        $s = substr($s, strspn($s, $s3 = 'zb_users/theme/', 0));
    }
    $a = explode('/', $s);
    $s = $a[0];
    $s = $zbp->host . $s3 . $s . '/';

    return $s;
}

/**
 * 通过文件获取应用目录路径
 * @param $file
 * @return string
 */
function plugin_dir_path($file)
{
    global $zbp;
    $s1 = $zbp->path;
    $s2 = str_replace('\\', '/', dirname($file) . '/');
    $s3 = '';
    $s = substr($s2, strspn($s1, $s2, 0));
    if (strpos($s, 'zb_users/plugin/') !== false) {
        $s = substr($s, strspn($s, $s3 = 'zb_users/plugin/', 0));
    } else {
        $s = substr($s, strspn($s, $s3 = 'zb_users/theme/', 0));
    }
    $a = explode('/', $s);
    $s = $a[0];
    $s = $zbp->path . $s3 . $s . '/';

    return $s;
}

/**
 * 通过Key从数组获取数据
 * @param string $array 数组名
 * @param string $name 下标key
 * @return mixed
 */
function GetValueInArray($array, $name)
{
    if (is_array($array)) {
        if (array_key_exists($name, $array)) {
            return $array[$name];
        }
    }
}

/**
 * 获取数组中的当前元素数据
 * @param string $array 数组名
 * @param string $name 下标key
 * @return mixed
 */
function GetValueInArrayByCurrent($array, $name)
{
    if (is_array($array)) {
        $array = current($array);

        return GetValueInArray($array, $name);
    }
}

/**
 * 分割string并取某项数据
 */
function SplitAndGet($s,$t=';',$n=0){
    $a = explode($t,$s);
    if(is_array($a)==false)
        $a=array();
    if( isset($a[$n]) ){
        return $a[$n];
    }
}

/**
 * 消连续空格
 */
function RemoveMoreSpaces($s){
    return preg_replace("/\s(?=\s)/","\\1",$s);
}

/**
 * 获取Guid
 * @return string
 */
function GetGuid()
{
    $s = str_replace('.', '', trim(uniqid('zbp', true), 'zbp'));

    return $s;
}

/**
 * 获取参数值
 * @param string $name 数组key名
 * @param string $type 默认为REQUEST
 * @return mixed|null
 */
function GetVars($name, $type = 'REQUEST')
{
    $array = &$GLOBALS[strtoupper("_$type")];

    if (isset($array[$name])) {
        return $array[$name];
    } else {
        return null;
    }
}

/**
 * 获取参数值（可设置默认返回值）
 * @param string $name 数组key名
 * @param string $type 默认为REQUEST
 * @param string $default 默认为null
 * @return mixed|null
 * @since 1.3.140614
 */
function GetVarsByDefault($name, $type = 'REQUEST', $default = null)
{
    $g = GetVars($name, $type);
    if ($g == null || $g == '') {
        return $default;
    }

    return $g;
}

/**
 * 获取数据库名
 * @return string  返回一个随机的SQLite数据文件名
 */
function GetDbName()
{

    return str_replace('-', '', '#%20' . strtolower(GetGuid())) . '.db';
}

/**
 * 获取当前网站地址
 * @param string $blogpath 网站域名
 * @param string &$cookiespath 返回cookie作用域值，要传引入
 * @return string  返回网站完整地址，如http://localhost/zbp/
 */
function GetCurrentHost($blogpath, &$cookiespath)
{

    $host = HTTP_SCHEME;

    $host .= $_SERVER['HTTP_HOST'];

    if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME']) {
        $x = $_SERVER['SCRIPT_NAME'];
        $y = $blogpath;
        $z = '';
        for ($i = 0; $i < strlen($x); $i++) {
            $f = $y . substr($x, $i - strlen($x));
            $z = substr($x, 0, $i);
            if (file_exists($f)  && is_file($f)) {
                $z = trim($z, '/');
                $z = '/' . $z . '/';
                $z = str_replace('//', '/', $z);
                $cookiespath = $z;
                return $host . $z;
            }
        }
    }

    $x = $_SERVER['SCRIPT_NAME'];
    $y = $blogpath;
    if (isset($_SERVER["CONTEXT_DOCUMENT_ROOT"]) && isset($_SERVER["CONTEXT_PREFIX"])) {
        if ($_SERVER["CONTEXT_DOCUMENT_ROOT"] && $_SERVER["CONTEXT_PREFIX"]) {
            $y = $_SERVER["CONTEXT_DOCUMENT_ROOT"] . $_SERVER["CONTEXT_PREFIX"] . '/';
        }
    }

    $z = '';

    for ($i = strlen($x); $i > 0; $i--) {
        $z = substr($x, 0, $i);
        if (strtolower(substr($y, strlen($y) - $i)) == strtolower($z)) {
            break;
        }
    }

    $cookiespath = $z;

    return $host . $z;
}

/**
 * 通过URL获取远程页面内容
 * @param string $url URL地址
 * @return string  返回页面文本内容，默认为null
 */
function GetHttpContent($url)
{

    $ajax = Network::Create();
    if (!$ajax) {
        return null;
    }

    $ajax->open('GET', $url);
    $ajax->enableGzip();
    $ajax->setTimeOuts(60, 60, 0, 0);
    $ajax->send();

    return ($ajax->status == 200) ? $ajax->responseText : null;
}

/**
 * 获取目录下文件夹列表
 * @param string $dir 目录
 * @return array 文件夹列表
 */
function GetDirsInDir($dir)
{
    $dirs = array();

    if (!file_exists($dir)) {
        return array();
    }
    if (!is_dir($dir)) {
        return array();
    }
    $dir = str_replace('\\', '/', $dir);
    if (substr($dir, -1) !== '/') {
        $dir .= '/';
    }

    if (function_exists('scandir')) {
        foreach (scandir($dir, 0) as $d) {
            if (is_dir($dir . $d)) {
                if (($d != '.') && ($d != '..')) {
                    $dirs[] = $d;
                }
            }
        }
    } else {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($dir . $file)) {
                        $dirs[] = $file;
                    }
                }
            }
            closedir($handle);
        }
    }

    return $dirs;
}

/**
 * 获取目录下指定类型文件列表
 * @param string $dir 目录
 * @param string $type 文件类型，以｜分隔
 * @return array 文件列表
 */
function GetFilesInDir($dir, $type)
{

    $files = array();

    if (!file_exists($dir)) {
        return array();
    }
    if (!is_dir($dir)) {
        return array();
    }
    $dir = str_replace('\\', '/', $dir);
    if (substr($dir, -1) !== '/') {
        $dir .= '/';
    }

    if (function_exists('scandir')) {
        foreach (scandir($dir) as $f) {
            if (is_file($dir . $f)) {
                foreach (explode("|", $type) as $t) {
                    $t = '.' . $t;
                    $i = strlen($t);
                    if (substr($f, -$i, $i) == $t) {
                        $sortname = substr($f, 0, strlen($f) - $i);
                        $files[$sortname] = $dir . $f;
                        break;
                    }
                }
            }
        }
    } else {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_file($dir . $file)) {
                        foreach (explode("|", $type) as $t) {
                            $t = '.' . $t;
                            $i = strlen($t);
                            if (substr($file, -$i, $i) == $t) {
                                $sortname = substr($file, 0, strlen($file) - $i);
                                $files[$sortname] = $dir . $file;
                                break;
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    return $files;
}

/**
 * 设置http状态头
 * @param int $number HttpStatus
 * @internal param string $status 成功获取状态码设置静态参数status
 * @return bool
 */
function SetHttpStatusCode($number)
{
    static $status = '';
    if ($status != '') {
        return false;
    }

    $codes = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        451 => 'Unavailable For Legal Reasons',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
    );

    if (isset($codes[$number])) {
        header('HTTP/1.1 ' . $number . ' ' . $codes[$number]);
        $status = $number;

        return true;
    }
}

/**
 * 302跳转
 * @param string $url 跳转链接
 */
function Redirect($url)
{
    SetHttpStatusCode(302);
    header('Location: ' . $url);
    die();
}

/**
 * 301跳转
 * @param string $url 跳转链接
 */
function Redirect301($url)
{
    SetHttpStatusCode(301);
    header('Location: ' . $url);
    die();
}
/**
 * @ignore
 */
function Http404()
{
    SetHttpStatusCode(404);
    header("Status: 404 Not Found");
}
/**
 * @ignore
 */
function Http500()
{
    SetHttpStatusCode(500);
}
/**
 * @ignore
 */
function Http503()
{
    SetHttpStatusCode(503);
}

/**
 * 设置304缓存头
 * @param string $filename 文件名
 * @param string $time 缓存时间
 */
function Http304($filename, $time)
{
    $url = $filename;
    $md5 = md5($url . $time);
    $etag = '"' . $md5 . '"';
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
    header("ETag: $etag");
    if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)) {
        SetHttpStatusCode(304);
        die();
    }
}

/**
 * 获取客户端IP
 * @return string  返回IP地址
 */
function GetGuestIP()
{
    return GetVars("REMOTE_ADDR", "SERVER");
}

/**
 * 获取客户端Agent
 * @return string  返回Agent
 */
function GetGuestAgent()
{
    return GetVars("HTTP_USER_AGENT", "SERVER");
}

/**
 * 获取请求来源URL
 * @return string  返回URL
 */
function GetRequestUri()
{
    $url = '';
    if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
        $url = $_SERVER['HTTP_X_ORIGINAL_URL'];
    } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
        $url = $_SERVER['HTTP_X_REWRITE_URL'];
        if (strpos($url, '?') !== false) {
            $querys = GetValueInArray(explode('?', $url), '1');
            foreach (explode('&', $querys) as $query) {
                $name = GetValueInArray(explode('=', $query), '0');
                $value = GetValueInArray(explode('=', $query), '1');
                $name = urldecode($name);
                $value = urldecode($value);
                if (!isset($_GET[$name])) {
                    $_GET[$name] = $value;
                }

                if (!isset($_GET[$name])) {
                    $_REQUEST[$name] = $value;
                }

                $name = '';
                $value = '';
            }
        }
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        $url = $_SERVER['REQUEST_URI'];
    } elseif (isset($_SERVER['REDIRECT_URL'])) {
        $url = $_SERVER['REDIRECT_URL'];
        if (isset($_SERVER['REDIRECT_QUERY_STRIN'])) {
            $url .= '?' . $_SERVER['REDIRECT_QUERY_STRIN'];
        }
    } else {
        $url = $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    return $url;
}

/**
 * 获取文件后缀名
 * @param string $f 文件名
 * @return string  返回小写的后缀名
 */
function GetFileExt($f)
{
    if (strpos($f, '.') === false) {
        return '';
    }

    $a = explode('.', $f);

    return strtolower(end($a));
}

/**
 * 获取文件权限
 * @param string $f 文件名
 * @return string|null  返回文件权限，数值格式，如0644
 */
function GetFilePermsOct($f)
{
    if (!file_exists($f)) {
        return null;
    }

    return substr(sprintf('%o', fileperms($f)), -4);
}

/**
 * 获取文件权限
 * @param string $f 文件名
 * @return string|null  返回文件权限，字符表达格式，如-rw-r--r--
 */
function GetFilePerms($f)
{

    if (!file_exists($f)) {
        return null;
    }

    $perms = fileperms($f);

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

    // Other
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

/**
 * 向字符串型的参数表加入一个新参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return string  返回新字符串，以|符号分隔
 */
function AddNameInString($s, $name)
{
    $pl = $s;
    $name = (string) $name;
    $apl = explode('|', $pl);
    if (in_array($name, $apl) == false) {
        $apl[] = $name;
    }
    $pl = trim(implode('|', $apl), '|');

    return $pl;
}

/**
 * 从字符串型的参数表中删除一个参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return string  返回新字符串，以|符号分隔
 */
function DelNameInString($s, $name)
{
    $pl = $s;
    $name = (string) $name;
    $apl = explode('|', $pl);
    for ($i = 0; $i <= Count($apl) - 1; $i++) {
        if ($apl[$i] == $name) {
            unset($apl[$i]);
        }
    }
    $pl = trim(implode('|', $apl), '|');

    return $pl;
}

/**
 * 在字符串参数值查找参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return bool
 */
function HasNameInString($s, $name)
{
    $pl = $s;
    $name = (string) $name;
    $apl = explode('|', $pl);

    return in_array($name, $apl);
}

/**
 * 以JSON形式返回错误信息（用于ShowError接口）
 * @param object
 */
function JsonError4ShowErrorHook($errorCode, $errorString, $file, $line)
{
    return JsonError($errorCode, $errorString, null);
}
/**
 * 以JSON形式返回错误信息
 * @param string $errorCode 错误编号
 * @param string $errorCode 错误内容
 * @param object
 */
function JsonError($errorCode, $errorString, $data)
{
    $result = array(
        'data' => $data,
        'err' => array(
            'code' => $errorCode,
            'msg' => $errorString,
            //'runtime' => RunTime(),
            'timestamp' => time(),
        ),
    );
    @ob_clean();
    echo json_encode($result);
    if ($errorCode != 0) {
        exit;
    }
}

/**
 * 以JSON形式返回正确获取信息
 * @param object 待返回内容
 * @param object
 */
function JsonReturn($data)
{
    return JsonError(0, "", $data);
}

/**
 *  XML-RPC应答错误页面
 * @param string $faultString 错误提示字符串
 * @return void
 */
function RespondError($errorCode, $errorString)
{

    $strXML = '<?xml version="1.0" encoding="UTF-8"?><methodResponse><fault><value><struct><member><name>faultCode</name><value><int>$1</int></value></member><member><name>faultString</name><value><string>$2</string></value></member></struct></value></fault></methodResponse>';
    $faultCode = time();
    $strError = $strXML;
    $strError = str_replace("$1", TransferHTML($faultCode, "[html-format]"), $strError);
    $strError = str_replace("$2", TransferHTML($errorString, "[html-format]"), $strError);

    ob_clean();
    echo $strError;
    exit;
}

/**
 *  XML-RPC脚本错误页面
 * @param string $faultString 错误提示字符串
 * @return void
 */
function ScriptError($faultString)
{
    header('Content-type: application/x-javascript; Charset=utf-8');
    ob_clean();
    echo 'alert("' . str_replace('"', '\"', $faultString) . '")';
    die();
}

/**
 *  验证字符串是否符合正则表达式
 * @param string $source 字符串
 * @param string $para 正则表达式，可用[username]|[password]|[email]|[homepage]或自定义表达式
 * @return bool
 */
function CheckRegExp($source, $para)
{
    if (strpos($para, '[username]') !== false) {
        $para = "/^[\.\_A-Za-z0-9·@\x{4e00}-\x{9fa5}]+$/u";
    } elseif (strpos($para, '[nickname]') !== false) {
        $para = '/([^\x{01}-\x{1F}\x{80}-\x{FF}\/:\\~&%;@\'"?<>|#$\*}{,\+=\[\]\(\)\{\}\t\r\n\p{C}])/u';
    } elseif (strpos($para, '[password]') !== false) {
        $para = "/^[A-Za-z0-9`~!@#\$%\^&\*\-_\?]+$/u";
    } elseif (strpos($para, '[email]') !== false) {
        $para = "/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*\.)+[a-zA-Z]*)$/u";
    } elseif (strpos($para, '[homepage]') !== false) {
        $para = "/^[a-zA-Z]+:\/\/[a-zA-Z0-9\_\-\.\&\?\/:=#\x{4e00}-\x{9fa5}]+$/u";
    } elseif (!$para) {
        return false;
    }

    return (bool) preg_match($para, $source);
}

/**
 *  通过正则表达式格式化字符串
 * @param string $source 字符串
 * @param string $para 正则表达式，可用[html-format]|[nohtml]|[noscript]|[enter]|[noenter]|[filename]|[normalname]或自定义表达式
 * @return string
 */
function TransferHTML($source, $para)
{

    if (strpos($para, '[html-format]') !== false) {
        $source = htmlspecialchars($source);
    }

    if (strpos($para, '[nohtml]') !== false) {
        $source = preg_replace("/<([^<>]*)>/si", "", $source);
        $source = str_replace("<", "˂", $source);
        $source = str_replace(">", "˃", $source);
    }

    if (strpos($para, '[noscript]') !== false) {
        $source = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $source);
        $source = preg_replace("/<(\/?script.*?)>/si", "", $source);
        $source = preg_replace("/javascript/si", "", $source);
        $source = preg_replace("/vbscript/si", "", $source);
        $source = preg_replace("/on([a-z]+)\s*=/si", "on\\=", $source);
    }
    if (strpos($para, '[enter]') !== false) {
        $source = str_replace("\r\n", "<br/>", $source);
        $source = str_replace("\n", "<br/>", $source);
        $source = str_replace("\r", "<br/>", $source);
        $source = preg_replace("/(<br\/>)+/", "<br/>", $source);
    }
    if (strpos($para, '[noenter]') !== false) {
        $source = str_replace("\r\n", "", $source);
        $source = str_replace("\n", "", $source);
        $source = str_replace("\r", "", $source);
    }
    if (strpos($para, '[filename]') !== false) {
        $source = str_replace(array("/", "#", "$", "\\", ":", "?", "*", "\"", "<", ">", "|", " "), array(""), $source);
    }
    if (strpos($para, '[normalname]') !== false) {
        $source = str_replace(array("#", "$", "(", ")", "*", "+", "[", "]", "{", "}", "?", "\\", "^", "|", ":", "'", "\"", ";", "@", "~", "=", "%", "&"), array(""), $source);
    }

    return $source;
}

/**
 *  封装HTML标签
 * @param string $html html源码
 * @return string
 */
function CloseTags($html)
{

    // strip fraction of open or close tag from end (e.g. if we take first x characters, we might cut off a tag at the end!)
    $html = preg_replace('/<[^>]*$/', '', $html); // ending with fraction of open tag

    // put open tags into an array
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $opentags = $result[1];

    // put all closed tags into an array
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closetags = $result[1];

    $len_opened = count($opentags);

    // if all tags are closed, we can return
    if (count($closetags) == $len_opened) {
        return $html;
    }

    // close tags in reverse order that they were opened
    $opentags = array_reverse($opentags);

    // self closing tags
    $sc = array('br', 'input', 'img', 'hr', 'meta', 'link');
    // ,'frame','iframe','param','area','base','basefont','col'
    // should not skip tags that can have content inside!

    for ($i = 0; $i < $len_opened; $i++) {
        $ot = strtolower($opentags[$i]);

        if (!in_array($opentags[$i], $closetags) && !in_array($ot, $sc)) {
            $html .= '</' . $opentags[$i] . '>';
        } else {
            unset($closetags[array_search($opentags[$i], $closetags)]);
        }
    }

    return $html;
}

/**
 *  获取UTF8格式的字符串的子串
 * @param string $sourcestr 源字符串
 * @param int $start 起始位置
 * @param int $cutlength 子串长度
 * @return string
 */
function SubStrUTF8_Start($sourcestr, $start, $cutlength)
{
    if (function_exists('mb_substr') && function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');

        return mb_substr($sourcestr, $start, $cutlength);
    }

    if (function_exists('iconv_substr') && function_exists('iconv_set_encoding')) {
        iconv_set_encoding("internal_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "UTF-8");

        return iconv_substr($sourcestr, $start, $cutlength);
    }

    return substr($sourcestr, $start, $cutlength);
}

/**
 *  获取UTF8格式的字符串的子串
 * @param string $sourcestr 源字符串
 * @param int $cutlength 子串长度
 * @return string
 */
function SubStrUTF8($sourcestr, $cutlength)
{

    if (function_exists('mb_substr') && function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');

        return mb_substr($sourcestr, 0, $cutlength);
    }

    if (function_exists('iconv_substr') && function_exists('iconv_set_encoding')) {
        iconv_set_encoding("internal_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "UTF-8");

        return iconv_substr($sourcestr, 0, $cutlength);
    }

    $returnstr = '';
    $i = 0;
    $n = 0;

    $str_length = strlen($sourcestr); //字符串的字节数

    while (($n < $cutlength) and ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) { //如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else {
            //其他情况下，包括小写字母和半角标点符号，
            {

                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数计1个
                $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...

            }
        }
        if ($str_length > $cutlength) {
            $returnstr = $returnstr;
        }
    }

    return $returnstr;
}

/**
 *  截取HTML格式的UTF8格式的字符串的子串
 * @param string $sourcestr 源字符串
 * @param int $cutlength 子串长度
 * @return string
 */
function SubStrUTF8_Html($sourcestr, $cutlength)
{

    if (function_exists('mb_substr') && function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
        $j = mb_strlen($sourcestr);
        $s = mb_substr($sourcestr, 0, $cutlength);
        $l = mb_substr_count($s, '<');
        $r = mb_substr_count($s, '>');
        if ($l > 0 && $l > $r) {
            for ($i = $cutlength; $i < $j; $i++) {
                $s .= mb_substr($sourcestr, $i, 1);
                if (mb_substr($sourcestr, $i, 1) == '>') {
                    break;
                }
            }
        }

        return $s;
    }

    if (function_exists('iconv_substr') && function_exists('iconv_set_encoding')) {
        iconv_set_encoding("internal_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "UTF-8");
        $j = iconv_strlen($sourcestr);
        $s = iconv_substr($sourcestr, 0, $cutlength);
        $l = substr_count($s, '<');
        $r = substr_count($s, '>');
        if ($l > 0 && $l > $r) {
            for ($i = $cutlength; $i < $j; $i++) {
                $s .= iconv_substr($sourcestr, $i, 1);
                if (iconv_substr($sourcestr, $i, 1) == '>') {
                    break;
                }
            }
        }

        return $s;
    }

    $j = strlen($sourcestr);
    $s = substr($sourcestr, 0, $cutlength);
    $l = substr_count($s, '<');
    $r = substr_count($s, '>');
    if ($l > 0 && $l > $r) {
        for ($i = $cutlength; $i < $j; $i++) {
            $s .= substr($sourcestr, $i, 1);
            if (substr($sourcestr, $i, 1) == '>') {
                break;
            }
        }
    }

    return $s;
}

/**
 *  删除文件BOM头
 * @param string $s 文件内容
 * @return string
 */
function RemoveBOM($s)
{
    $charset = array();
    $charset[1] = substr($s, 0, 1);
    $charset[2] = substr($s, 1, 1);
    $charset[3] = substr($s, 2, 1);
    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        $s = substr($s, 3);
    }

    return $s;
}

/**
 * 获取指定时区名
 * @param int $z 时区号
 * @return string 时区名
 * @since 1.3.140614
 */
function GetTimeZonebyGMT($z)
{
    $timezones = array(
        -12 => 'Etc/GMT+12',
        -11 => 'Pacific/Midway',
        -10 => 'Pacific/Honolulu',
        -9 => 'America/Anchorage',
        -8 => 'America/Los_Angeles',
        -7 => 'America/Denver',
        -6 => 'America/Tegucigalpa',
        -5 => 'America/New_York',
        -4 => 'America/Halifax',
        -3 => 'America/Argentina/Buenos_Aires',
        -2 => 'Atlantic/South_Georgia',
        -1 => 'Atlantic/Azores',
        0 => 'UTC',
        1 => 'Europe/Berlin',
        2 => 'Europe/Sofia',
        3 => 'Africa/Nairobi',
        4 => 'Europe/Moscow',
        5 => 'Asia/Karachi',
        6 => 'Asia/Dhaka',
        7 => 'Asia/Bangkok',
        8 => 'Asia/Shanghai',
        9 => 'Asia/Tokyo',
        10 => 'Pacific/Guam',
        11 => 'Australia/Sydney',
        12 => 'Pacific/Fiji',
        13 => 'Pacific/Tongatapu',
    );
    if (!isset($timezones[$z])) {
        return 'UTC';
    }

    return $timezones[$z];
}

/**
 * 对数组内的字符串进行htmlspecialchars
 * @param array $array 待过滤字符串
 * @return array
 * @since 1.4
 */
function htmlspecialchars_array($array)
{

    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = htmlspecialchars_array($value);
        } elseif (is_string($value)) {
            $value = htmlspecialchars($value);
        }
    }

    return $array;
}

/**
 * 获得一个只含数字字母和-线的string
 * @param string $s 待过滤字符串
 * @return s
 * @since 1.4
 */
function FilterCorrectName($s)
{
    return preg_replace('|[^0-9a-zA-Z_/-]|', '', $s);
}

/**
 * 确认一个对象是否可被转换为string
 * @param object $obj
 * @return bool
 * @since 1.4
 */
function CheckCanBeString($obj)
{
    // Fuck PHP 5.2!!!!
    // return $obj === null || is_scalar($obj) || is_callable([$obj, '__toString']);
    if (is_object($obj) && method_exists($obj, '__toString')) {
        return true;
    }

    if (is_null($obj)) {
        return true;
    }

    return is_scalar($obj);
}

function utf84mb_filter(&$sql)
{
    $sql = preg_replace_callback("/[\x{10000}-\x{10FFFF}]/u", 'utf84mb_convertToUCS4', $sql);
}

function utf84mb_fixHtmlSpecialChars()
{
    global $article;
    $article->Content = preg_replace_callback("/\&\#x([0-9A-Z]{2,6})\;/u", 'utf84mb_convertToUTF8', $article->Content);
    $article->Intro = preg_replace_callback("/\&\#x([0-9A-Z]{2,6})\;/u", 'utf84mb_convertToUTF8', $article->Intro);
}

function utf84mb_convertToUCS4($matches)
{
    return sprintf("&#x%s;", ltrim(strtoupper(bin2hex(iconv('UTF-8', 'UCS-4', $matches[0]))), "0"));
}

function utf84mb_convertToUTF8($matches)
{
    return iconv('UCS-4', 'UTF-8', hex2bin(str_pad($matches[1], 8, "0", STR_PAD_LEFT)));
}


//$args = 2...x
function VerifyWebToken($wt, $wt_id)
{
    $time = substr($wt, 32);
    $wt = substr($wt, 0, 32);
    $args = array();
    for ($i = 2; $i < func_num_args(); $i++) {
        $args[] = func_get_arg($i);
    }
    $sha = md5(hash("sha256", $time . $wt_id) . hash("sha256", implode($args) . $time));
    if ($wt === $sha) {
        if ($time > time()) {
            return true;
        }
    }

    return false;
}
//$time : expired second
function CreateWebToken($wt_id, $time)
{
    $time = (int) $time;
    $args = array();
    for ($i = 2; $i < func_num_args(); $i++) {
        $args[] = func_get_arg($i);
    }
    return md5(hash("sha256", $time . $wt_id) . hash("sha256", implode($args) . $time)) . $time;
}



/**
 * 处理PHP版本兼容代码
 */

if (!function_exists('hex2bin')) {
    function hex2bin($str)
    {
        $sbin = "";
        $len = strlen($str);
        for ($i = 0; $i < $len; $i += 2) {
            $sbin .= pack("H*", substr($str, $i, 2));
        }

        return $sbin;
    }
}

if (!function_exists('rrmdir')) {
    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            if (function_exists('scandir')) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != '.' && $object != '..') {
                        if (filetype($dir . '/' . $object) == 'dir') {
                            rrmdir($dir . '/' . $object);
                        } else {
                            unlink($dir . '/' . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($dir);
            } else {
                if ($handle = opendir($dir)) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            if (is_dir(rtrim(rtrim($dir, '/'), '\\') . '/' . $file)) {
                                rrmdir(rtrim(rtrim($dir, '/'), '\\') . '/' . $file);
                            } else {
                                unlink(rtrim(rtrim($dir, '/'), '\\') . '/' . $file);
                            }
                        }
                    }
                    closedir($handle);
                    rmdir($dir);
                }
            }
        }
    }
}

/**
 * URL constants as defined in the PHP Manual under "Constants usable with
 * http_build_url()".
 *
 * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
 * @see  https://github.com/jakeasmith/http_build_url/blob/master/src/http_build_url.php
 * @license  MIT
 */
if (!defined('HTTP_URL_REPLACE')) {
    define('HTTP_URL_REPLACE', 1);
}
if (!defined('HTTP_URL_JOIN_PATH')) {
    define('HTTP_URL_JOIN_PATH', 2);
}
if (!defined('HTTP_URL_JOIN_QUERY')) {
    define('HTTP_URL_JOIN_QUERY', 4);
}
if (!defined('HTTP_URL_STRIP_USER')) {
    define('HTTP_URL_STRIP_USER', 8);
}
if (!defined('HTTP_URL_STRIP_PASS')) {
    define('HTTP_URL_STRIP_PASS', 16);
}
if (!defined('HTTP_URL_STRIP_AUTH')) {
    define('HTTP_URL_STRIP_AUTH', 32);
}
if (!defined('HTTP_URL_STRIP_PORT')) {
    define('HTTP_URL_STRIP_PORT', 64);
}
if (!defined('HTTP_URL_STRIP_PATH')) {
    define('HTTP_URL_STRIP_PATH', 128);
}
if (!defined('HTTP_URL_STRIP_QUERY')) {
    define('HTTP_URL_STRIP_QUERY', 256);
}
if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
    define('HTTP_URL_STRIP_FRAGMENT', 512);
}
if (!defined('HTTP_URL_STRIP_ALL')) {
    define('HTTP_URL_STRIP_ALL', 1024);
}
if (!function_exists('http_build_url')) {
    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @param mixed $url     (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts   same as the first argument
     * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     * @return string
     */
    function http_build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);
        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;
        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');
        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & HTTP_URL_STRIP_ALL) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS
                | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_PATH
                | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT;
        } elseif ($flags & HTTP_URL_STRIP_AUTH) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS;
        }
        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }
        if ($flags & HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    // Workaround for trailing slashes
                    $url['path'] .= 'a';
                    $url['path'] = rtrim(
                            str_replace(basename($url['path']), '', $url['path']),
                            '/'
                        ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }
            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);
                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }
        if (isset($url['path']) && $url['path'] !== '' && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }
        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if ($flags & constant($strip)) {
                unset($url[$key]);
            }
        }
        $parsed_string = '';
        if (!empty($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }
        if (!empty($url['user'])) {
            $parsed_string .= $url['user'];
            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }
            $parsed_string .= '@';
        }
        if (!empty($url['host'])) {
            $parsed_string .= $url['host'];
        }
        if (!empty($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }
        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        }
        if (!empty($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }
        if (!empty($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }
        $new_url = $url;

        return $parsed_string;
    }
}

if (!function_exists('gzdecode')) {
    function gzdecode($data)
    {
         $len = strlen($data);
        if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            return null;  // Not GZIP format (See RFC 1952)
        }
         $method = ord(substr($data, 2, 1));  // Compression method
         $flags  = ord(substr($data, 3, 1));  // Flags
        if ($flags & 31 != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952
            return null;
        }
         // NOTE: $mtime may be negative (PHP integer limitations)
         $mtime = unpack("V", substr($data, 4, 4));
         $mtime = $mtime[1];
         $xfl   = substr($data, 8, 1);
         $os    = substr($data, 8, 1);
         $headerlen = 10;
         $extralen  = 0;
         $extra     = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header
            if ($len - $headerlen - 2 < 8) {
                return false;    // Invalid format
            }
            $extralen = unpack("v", substr($data, 8, 2));
            $extralen = $extralen[1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return false;    // Invalid format
            }
            $extra = substr($data, 10, $extralen);
            $headerlen += 2 + $extralen;
        }

         $filenamelen = 0;
         $filename = "";
        if ($flags & 8) {
            // C-style string file NAME data in header
            if ($len - $headerlen - 1 < 8) {
                return false;    // Invalid format
            }
            $filenamelen = strpos(substr($data, 8 + $extralen), chr(0));
            if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
                return false;    // Invalid format
            }
            $filename = substr($data, $headerlen, $filenamelen);
            $headerlen += $filenamelen + 1;
        }

         $commentlen = 0;
         $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header
            if ($len - $headerlen - 1 < 8) {
                return false;    // Invalid format
            }
            $commentlen = strpos(substr($data, 8 + $extralen + $filenamelen), chr(0));
            if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
                return false;    // Invalid header format
            }
            $comment = substr($data, $headerlen, $commentlen);
            $headerlen += $commentlen + 1;
        }

         $headercrc = "";
        if ($flags & 2) {
            // 2-bytes (lowest order) of CRC32 on header present
            if ($len - $headerlen - 2 < 8) {
                return false;    // Invalid format
            }
            $calccrc = crc32(substr($data, 0, $headerlen)) & 0xffff;
            $headercrc = unpack("v", substr($data, $headerlen, 2));
            $headercrc = $headercrc[1];
            if ($headercrc != $calccrc) {
                return false;    // Bad header CRC
            }
            $headerlen += 2;
        }

         // GZIP FOOTER - These be negative due to PHP's limitations
         $datacrc = unpack("V", substr($data, -8, 4));
         $datacrc = $datacrc[1];
         $isize = unpack("V", substr($data, -4));
         $isize = $isize[1];

         // Perform the decompression:
         $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG!
            return null;
        }
         $body = substr($data, $headerlen, $bodylen);
         $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8:
                    // Currently the only supported compression method:
                    $data = gzinflate($body);
                    break;
                default:
                    // Unknown compression method
                    return false;
            }
        } else {
            // I'm not sure if zero-byte body content is allowed.
            // Allow it for now...  Do nothing...
        }

         // Verifiy decompressed size and CRC32:
         // NOTE: This may fail with large data sizes depending on how
         //       PHP's integer limitations affect strlen() since $isize
         //       may be negative for large sizes.
        if ($isize != strlen($data) || crc32($data) != $datacrc) {
            // Bad format!  Length or CRC doesn't match!
            return false;
        }

         return $data;
    }
}

if (!function_exists('session_status')) {
    function session_status()
    {
        if (!extension_loaded('session')) {
            return 0;
        } elseif (!session_id()) {
            return 1;
        } else {
            return 2;
        }
    }
}
