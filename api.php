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

// 载入系统和应用的 mod
ApiLoadMods($mods);

$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

if (empty($act)) {
    $act = 'get';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (! ($mod === 'member' && $act === 'login'))) {
    ApiVerifyCSRF();
}

if (isset($mods[$mod]) && file_exists($mod_file = $mods[$mod])) {
    include_once $mod_file;
    $func = 'api_' . $mod . '_' . $act;
    if (function_exists($func)) {
        $result = call_user_func($func);

        ApiResponse(
            isset($result['data']) ? $result['data'] : null,
            isset($result['error']) ? $result['error'] : null,
            isset($result['code']) ? $result['code'] : 200,
            isset($result['message']) ? $result['message'] : 'OK'
        );
    }
}

ApiResponse(null, null, 404, $GLOBALS['lang']['error']['96']);
