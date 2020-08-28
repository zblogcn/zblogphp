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

foreach ($GLOBALS['hooks']['Filter_Plugin_API_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

$mods = array();

// 载入系统和应用的 mod
ApiLoadMods($mods);

$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (! ($mod === 'member' && $act === 'login'))) {
    ApiVerifyCSRF();
}

// 派发 API
ApiDispatch($mods, $mod, $act);
