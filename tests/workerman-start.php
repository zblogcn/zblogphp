<?php
//zblog api 示范
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
require_once __DIR__ . '/vendor/autoload.php';
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
//$_ENV['ZBP_PRESET_BLOGPATH'] = 'http://localhost';
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
    RunTime_Begin();

    try {
        Clear_Filter_Plugin('Filter_Plugin_Zbp_ShowError');

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
        //echo $r;
        $response = new Workerman\Protocols\Http\Response(200, [
            'Content-Type' => 'text/json; charset=utf-8',
        ], $r);
        $connection->send($response);
    }
    catch (\Throwable $e) {
        $r = ApiResponse(null, $e, '500', '', false);
        $response = new Workerman\Protocols\Http\Response(500, [
            'Content-Type' => 'text/json; charset=utf-8',
        ], $r);
        $response->withStatus(500);
        $connection->send($response);
    }
};

// 运行worker
Worker::runAll();