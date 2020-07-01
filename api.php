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
    ApiResponse(null, null, 503, $GLOBALS['lang']['error']['95']);
}

$mods = array();

// 从 zb_system/api/ 目录中载入 mods
foreach (GetFilesInDir(ZBP_PATH . 'zb_system/api/', 'php') as $mod => $file) {
    $mods[$mod] = $file;
}

// 增加插件自定义 mod
foreach ($GLOBALS['hooks']['Filter_Plugin_API_Add_Mod'] as $fpname => &$fpsignal) {
    $add_mods = $fpname();
    foreach ($add_mods as $mod => $file) {
        if (array_key_exists($mod, $mods)) {
            continue;
        }

        $mods[$mod] = $file;
    }
}

$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

if (empty($act)) {
    $act = 'get';
}

if (isset($mods[$mod]) && file_exists($mod_file = $mods[$mod])) {
    include $mod_file;
    $func = 'api_' . $mod . '_' . $act;
    if (function_exists($func)) {
        call_user_func($func);
    }
}
ApiResponse(null, null, 404, $GLOBALS['lang']['96']);
