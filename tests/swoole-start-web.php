<?php
//zblog web浏览 示范
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
require_once __DIR__ . '/zblog/zb_system/function/c_system_base.php';

$http = new Swoole\Http\Server('127.0.0.1', 9999);

$zbp->Load();

$http->on('Request', function ($request, $response)
{
    $zbp = \ZBlogPHP::GetInstance();
    http_request_convert_to_global($request);
    //$_ENV['ZBP_PRESET_BLOGPATH'] = 'http://localhost';
    $_SERVER['_start_time'] = microtime(true); //RunTime
    $GLOBALS['currenturl'] = GetRequestUri();
    $GLOBALS['bloghost'] = GetCurrentHost($GLOBALS['blogpath'], $GLOBALS['cookiespath']);
    try {
        Clear_Filter_Plugin('Filter_Plugin_Zbp_ShowError');
        ob_start();
        ViewAuto();
        $r = ob_get_clean();
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->write($r);
    }
    catch (Error $e) {
        $rt = RunTime(false);
        $r = print_r(array($e->getCode(), $e->getMessage(), $rt), true);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->status(500);
        $response->end($r);
    }
    catch (Exception $e) {
        $rt = RunTime(false);
        $r = print_r(array($e->getCode(), $e->getMessage(), $rt), true);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->status(500);
        $response->end($r);
    }

});

$http->start();