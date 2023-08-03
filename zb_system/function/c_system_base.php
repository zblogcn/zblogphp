<?php

/**
 * 系统初始化等相关操作.
 */

/**
 * ZBLOGPHP
 *
 * @var ZBlogPHP;
 */
$zbp = null;

error_reporting(E_ALL);

defined('ZBP_PATH') || define('ZBP_PATH', rtrim(str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../')), '/') . '/');
defined('ZBP_HOOKERROR') || define('ZBP_HOOKERROR', true);
defined('ZBP_OBSTART') || define('ZBP_OBSTART', true);
defined('ZBP_SAFEMODE') || define('ZBP_SAFEMODE', false);

//强制开启debug模式，需要开启时请打开注释
//defined('ZBP_DEBUGMODE') || define('ZBP_DEBUGMODE', true);

if (ZBP_OBSTART) {
    ob_start();
}

/**
 * 加载系统基础函数.
 */
require ZBP_PATH . 'zb_system/function/c_system_version.php';
require ZBP_PATH . 'zb_system/function/c_system_common.php';
require ZBP_PATH . 'zb_system/function/c_system_compat.php';
require ZBP_PATH . 'zb_system/function/c_system_defined.php';
require ZBP_PATH . 'zb_system/function/c_system_plugin.php';
require ZBP_PATH . 'zb_system/function/c_system_debug.php';
require ZBP_PATH . 'zb_system/function/c_system_function.php';
require ZBP_PATH . 'zb_system/function/c_system_route.php';
require ZBP_PATH . 'zb_system/function/c_system_event.php';
require ZBP_PATH . 'zb_system/function/c_system_api.php';

if (ZBP_HOOKERROR) {
    ZbpErrorControl::SetErrorHook();
}

/**
 * 指定加载类的目录并注册加载函数到系统
 */
if (function_exists('RunTime_Begin')) {
    RunTime_Begin();
}
$GLOBALS['autoload_class_dirs'] = array();
if (function_exists('AddAutoloadClassDir')) {
    AddAutoloadClassDir(ZBP_PATH . 'zb_system/function/lib');
}
spl_autoload_register('AutoloadClass');

if (is_readable($file_base = ZBP_PATH . 'vendor/autoload.php') && PHP_VERSION_ID >= 50300) {
    include_once $file_base;
}

/*
 * 定义POST类型序列
 * @param  id=>{id,name,template,urlrule,classname,actions,routes}
 * id = 0 ~ 255是系统预留的类型，非系统自定义类型id > 255
 */
$GLOBALS['posttype'] = include ZBP_PATH . 'zb_system/defend/posttype.php';

/*
 *定义命令
 */
$GLOBALS['actions'] = include ZBP_PATH . 'zb_system/defend/actions.php';

/*
 *定义数据表
 */
$GLOBALS['table'] = include ZBP_PATH . 'zb_system/defend/table.php';

/*
 *定义数据结构
 */
$GLOBALS['datainfo'] = include ZBP_PATH . 'zb_system/defend/datainfo.php';

/*
 * 版本兼容处理
 * PHP 7.4移除了get_magic_quotes_gpc
 * https://github.com/php/php-src/commit/b2ea507beab862a0167af6b99f44fe9c695ca4f0
 */
if (function_exists('get_magic_quotes_gpc') && PHP_VERSION_ID < 70400 && call_user_func('get_magic_quotes_gpc')) {

    function _stripslashes(&$var)
    {
        if (is_array($var)) {
            foreach ($var as $k => &$v) {
                _stripslashes($v);
            }
        } else {
            $var = stripslashes($var);
        }
    }

    _stripslashes($_GET);
    _stripslashes($_POST);
    _stripslashes($_COOKIE);
    _stripslashes($_REQUEST);
}

/*
 * CLI Mock 处理
 */
if (IS_CLI && !IS_WORKERMAN && !IS_SWOOLE) {
    if (isset($GLOBALS['argv'])) {
        $_SERVER["QUERY_STRING"] = implode('&', array_slice($GLOBALS['argv'], 1));
    } else {
        $_SERVER["QUERY_STRING"] = '';
    }

    $_SERVER["HTTP_HOST"] = "localhost";
    $_SERVER['SERVER_SOFTWARE'] = "CLI";
    $_GET = array();
    parse_str($_SERVER["QUERY_STRING"], $_GET);
    parse_str($_SERVER["QUERY_STRING"], $_REQUEST);
    // $_POST = json_decode(file_get_contents('php://stdin'), true);
}

/*
 * 定义系统全局变量
 */

/*
 * 默认路由url数组
 */
$GLOBALS['routes'] = array();
/*
 * 当前动作命令
 */
$GLOBALS['action'] = '';
/*
 * 当前请求路径
 */
$GLOBALS['currenturl'] = GetRequestUri();
$GLOBALS['fullcurrenturl'] = '';
$GLOBALS['currentscript'] = GetRequestScript();
$GLOBALS['fullcurrentscript'] = ZBP_PATH . $GLOBALS['currentscript'];
/*
 * 语言包
 */
$GLOBALS['lang'] = array(); // array
$GLOBALS['langs'] = null; // object
/*
 * 系统根路径
 */
$GLOBALS['blogpath'] = ZBP_PATH;
/*
 * 用户路径
 */
$GLOBALS['usersdir'] = ZBP_PATH . 'zb_users/';
/*
 * System路径
 */
$GLOBALS['systemdir'] = ZBP_PATH . 'zb_system/';
/*
 * Api Mods路径
 */
$GLOBALS['apimodsdir'] = $GLOBALS['systemdir'] . 'api/';
/*
 * Admin路径
 */
$GLOBALS['admindir'] = $GLOBALS['systemdir'] . 'admin/';
/*
 * CACHE路径
 */
$GLOBALS['cachedir'] = $GLOBALS['usersdir'] . 'cache/';
/*
 * LOGS路径
 */
$GLOBALS['logsdir'] = $GLOBALS['usersdir'] . 'logs/';
/*
 * DATA路径
 */
$GLOBALS['datadir'] = $GLOBALS['usersdir'] . 'data/';

/*
 * 已激活插件列表
 */
$GLOBALS['activedapps'] = array();
//保留activeapps，兼容以前版本
$GLOBALS['activeapps'] = &$GLOBALS['activedapps'];

/*
 * 加载设置
 */
//有ZBP_USER_OPTION，ZBP_PRESET_ENV，ZBP_PRESET_HOST，ZBP_PRESET_COOKIESPATH，ZBP_PRESET_THEME，ZBP_PRESET_THEME_STYLE，ZBP_PRESET_PLUGINS，ZBP_PRESET_DISABLE_ROOT 等数个预设的环境变量
$file_base = GetVarsFromEnv('ZBP_PRESET_ENV', 'constant|environment|server');
if (!empty($file_base) && is_readable($file_base) && class_exists('ZbpEnv')) {
    ZbpEnv::LoadByPath($file_base);
}
$GLOBALS['option'] = include ZBP_PATH . 'zb_system/defend/option.php';
$GLOBALS['option_user_file'] = array();
if (!ZBP_HOOKERROR && is_readable($file_base = GetVarsFromEnv('ZBP_USER_OPTION'))) {
    $GLOBALS['option_user_file'] = include $file_base;
    define('ZBP_PRESET_OPTION_USED', true);
} elseif (is_readable($file_base = $GLOBALS['usersdir'] . 'c_option.php')) {
    $GLOBALS['option_user_file'] = include $file_base;
}
$GLOBALS['option'] = array_merge($GLOBALS['option'], $GLOBALS['option_user_file']);

$GLOBALS['blogtitle'] = $GLOBALS['option']['ZC_BLOG_SUBNAME']; // 不是漏写！
$GLOBALS['blogname'] = &$GLOBALS['option']['ZC_BLOG_NAME'];
$GLOBALS['blogsubname'] = &$GLOBALS['option']['ZC_BLOG_SUBNAME'];
$GLOBALS['blogtheme'] = &$GLOBALS['option']['ZC_BLOG_THEME'];
$GLOBALS['blogstyle'] = &$GLOBALS['option']['ZC_BLOG_CSS'];
$GLOBALS['cookiespath'] = null;

$preset_bloghost = GetVarsFromEnv('ZBP_PRESET_HOST');
if ($preset_bloghost != '') {
    $preset_cookiespath = GetVarsFromEnv('ZBP_PRESET_COOKIESPATH');
    define('ZBP_PRESET_HOST_USED', true);
    $preset_bloghost = rtrim($preset_bloghost, '/');
    $preset_cookiespath = rtrim($preset_cookiespath, '/') . '/';
    $GLOBALS['bloghost'] = $preset_bloghost . $preset_cookiespath;
    $GLOBALS['cookiespath'] = $preset_cookiespath;
} else {
   $GLOBALS['bloghost'] = GetCurrentHost($GLOBALS['blogpath'], $GLOBALS['cookiespath']); 
}

$GLOBALS['usersurl'] = $GLOBALS['bloghost'] . 'zb_users/';
$GLOBALS['systemurl'] = $GLOBALS['bloghost'] . 'zb_system/';
$GLOBALS['adminurl'] = $GLOBALS['bloghost'] . 'zb_system/admin/';

/*
 * Api Mods
 */
$GLOBALS['api_public_mods'] = array();
$GLOBALS['api_private_mods'] = array();
$GLOBALS['api_allow_mods_rule'] = array();
$GLOBALS['api_disallow_mods_rule'] = array();

/*
 * 系统实例化
 */
AutoloadClass('ZBlogPHP');
AutoloadClass('DbSql');
AutoloadClass('Config');

$GLOBALS['zbp'] = ZBlogPHP::GetInstance();
$GLOBALS['zbp']->Initialize();

/*
 * 加载主题和插件APP
 */
if (ZBP_SAFEMODE === false) {
    $theme_preset = GetVarsFromEnv('ZBP_PRESET_THEME');
    if ($theme_preset != '') {
        $GLOBALS['blogtheme'] = $theme_preset;
        $style_preset = GetVarsFromEnv('ZBP_PRESET_THEME_STYLE');
        if ($style_preset != '') {
            $GLOBALS['blogstyle'] = $style_preset;
        }
    }
    $theme_name = $GLOBALS['blogtheme'];
    $file_base = $GLOBALS['usersdir'] . 'theme/' . $GLOBALS['blogtheme'] . '/theme.xml';
    $theme_include = $GLOBALS['usersdir'] . 'theme/' . $GLOBALS['blogtheme'] . '/include.php';

    if (is_readable($file_base)) {
        $GLOBALS['activedapps'][] = $theme_name;

        // 读主题版本信息
        $GLOBALS['zbp']->themeapp = $GLOBALS['zbp']->LoadApp('theme', $theme_name);
        $GLOBALS['zbp']->themeinfo = $GLOBALS['zbp']->themeapp->GetInfoArray();

        if ($GLOBALS['zbp']->themeapp->isloaded && is_readable($theme_include)) {
            include $theme_include;
        }
    }

    $aps = $GLOBALS['zbp']->GetPreActivePlugin();
    $aps_preset = GetVarsFromEnv('ZBP_PRESET_PLUGINS');
    if ($aps_preset != '') {
        $aps = array();
        $aps_preset = explode('|', $aps_preset);
        foreach ($aps as $ap) {
            $aps[] = trim($ap);
        }
        $aps = array_unique($aps);
    }

    foreach ($aps as $ap) {
        if (is_readable($file_base = $GLOBALS['usersdir'] . 'plugin/' . $ap . '/plugin.xml')) {
            $GLOBALS['activedapps'][] = $ap;
        }
        if (is_readable($file_base = $GLOBALS['usersdir'] . 'plugin/' . $ap . '/include.php')) {
            include $file_base;
        }
    }

    foreach ($GLOBALS['plugins'] as &$fn) {
        if (function_exists($fn)) {
            $fn();
        }
    }
}

unset($file_base, $aps, $aps_preset, $fn, $ap, $opk, $opv, $preset_bloghost, $preset_cookiespath);
unset($theme_name, $theme_include, $theme_preset, $style_preset);

//1.7新加入的
$GLOBALS['zbp']->PreLoad();
