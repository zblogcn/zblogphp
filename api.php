<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-01
 */

// 标记为 API 运行模式
define('ZBP_IN_API', true);

require 'zb_system/function/c_system_base.php';

$zbp->Load();

if (!$GLOBALS['option']['ZC_API_ENABLE']) {
    ApiResponse(null, null, 'Web API is disabled!');
}

$mods = array();

foreach (GetFilesInDir(ZBP_PATH . 'zb_system/api/', 'php') as $sortname => $fullname) {
    $mods[$sortname] = $fullname;
}

foreach ($GLOBALS['hooks']['Filter_Plugin_API_Mod'] as $fpname => &$fpsignal) {
    $fpname($mods);
}

$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

if (isset($mods[$mod]) && file_exists($mod_file = $mods[$mod])) {
    include $mod_file;
    $func = 'api_' . $mod . '_' . $act;
    if (function_exists($func)) {
        ApiResponse(call_user_func($func));
    }
}

ApiResponse(null, null, 'API is not available!');
