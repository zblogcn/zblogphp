<?php
//zblog web浏览 示范
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
require_once __DIR__ . '/vendor/autoload.php';
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
require  __DIR__ . '/zblog/zb_system/function/c_system_base.php';

// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker("http://127.0.0.1:8888");

// 启动4个进程对外提供服务
$http_worker->count = 4;

$http_worker->onWorkerStart = function(Worker $worker)
{
    echo "Worker starting...\n";
    $zbp = \ZBlogPHP::GetInstance();
    //$zbp->Initialize();
    $zbp->Load();
};

// 接收到浏览器发送的数据时回复hello world给浏览器
$http_worker->onMessage = function(TcpConnection $connection, Request $request)
{
    $zbp = \ZBlogPHP::GetInstance();
    http_request_convert_to_global($request, $connection);
    //$_ENV['ZBP_PRESET_BLOGPATH'] = 'http://localhost';
    $_SERVER['_start_time'] = microtime(true); //RunTime
    $GLOBALS['currenturl'] = GetRequestUri();
    $GLOBALS['bloghost'] = GetCurrentHost($GLOBALS['blogpath'], $GLOBALS['cookiespath']);

    try {
        Clear_Filter_Plugin('Filter_Plugin_Zbp_ShowError');
        ob_start();
        ViewAuto();
        $r = ob_get_clean();
        $response = new Workerman\Protocols\Http\Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ], $r);
        $connection->send($response);
    }
    catch (Error $e) {
        $rt = RunTime(false);
        $r = print_r(array($e->getCode(), $e->getMessage(), $rt), true);
        $response = new Workerman\Protocols\Http\Response(500, [
            'Content-Type' => 'text/html; charset=utf-8',
        ], $r);
        $response->withStatus(500);
        $connection->send($response);
    }
    catch (Exception $e) {
        $rt = RunTime(false);
        $r = print_r(array($e->getCode(), $e->getMessage(), $rt), true);
        $response = new Workerman\Protocols\Http\Response(500, [
            'Content-Type' => 'text/html; charset=utf-8',
        ], $r);
        $response->withStatus(500);
        $connection->send($response);
    }
};

// 运行worker
Worker::runAll();