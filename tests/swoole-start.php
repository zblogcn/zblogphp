<?php
//zblog api 示范
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
//$_ENV['ZBP_PRESET_HOST'] = 'http://localhost';
require_once __DIR__ . '/zblog/zb_system/function/c_system_base.php';

$http = new Swoole\Http\Server('127.0.0.1', 9999);

$zbp->Load();

$http->on('Request', function ($request, $response)
{
    $response->header('Content-Type', 'text/html; charset=utf-8');
    $zbp = \ZBlogPHP::GetInstance();
    http_request_convert_to_global($request);
    RunTime_Begin();

    try {
        ClearFilterPlugin('Filter_Plugin_Zbp_ShowError');

        ApiCheckEnable();

        foreach ($GLOBALS['hooks']['Filter_Plugin_API_Begin'] as $fpname => &$fpsignal) {
            $fpname();
        }

        ApiCheckAuth(false, 'api');

        ApiCheckLimit();

        $GLOBALS['mods'] = array();
        $GLOBALS['mods_allow'] = array();
        $GLOBALS['mods_disallow'] = array();
        $GLOBALS['mod'] = GetVars('mod', 'GET');
        $GLOBALS['act'] = GetVars('act', 'GET');

        // 载入系统和应用的 mod
        ApiLoadMods($GLOBALS['mods']);

        //进行Api白名单和黑名单的检查
        ApiCheckMods($GLOBALS['mods_allow'], $GLOBALS['mods_disallow']);

        ApiLoadPostData();

        ApiVerifyCSRF();

        // 派发 API
        ob_start();
        $r = ApiDispatch($GLOBALS['mods'], $GLOBALS['mod'], $GLOBALS['act']);
        ob_end_clean();
        $response->header('Content-Type', 'text/json; charset=utf-8');
        $response->write($r);
    }
    catch (\Throwable $e) {
        //echo print_r($e, true);
        $r = ApiResponse(null, $e, '500', '', false);
        $response->header('Content-Type', 'text/json; charset=utf-8');
        $response->status(500);
        $response->end($r);
    }

});

$http->start();