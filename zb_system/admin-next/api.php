<?php

/**
 * Z-BlogPHP admin-next 内部 API 入口.
 *
 * 与 zb_system/api.php 的区别：
 *  - 不受 ZC_API_ENABLE 影响（那是「是否对外暴露 API」的开关）
 *  - 强制要求已登录且具备 admin 权限
 *  - 限流按用户维度
 *  - 不输出 runtime 调试块
 *
 * 其余流程（mod 注册、CSRF、JSON body、派发）完全复用系统实现。
 *
 * @package Z-BlogPHP
 * @subpackage System/Administrator
 */

if (!isset($_SERVER['SCRIPT_NAME'])) {
    exit('Access denied');
}

define('ZBP_IN_API', true);
define('ZBP_IN_ADMIN_SPA', true);

require '../function/c_system_base.php';

$zbp->Load();

/**
 * 直接输出 JSON 并结束.
 */
function ZbpAdminNext_JsonExit(int $status, string $message): void
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode(
        [
            'code'    => $status,
            'message' => $message,
            'data'    => null,
            'error'   => null,
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if (!$zbp->islogin || !$zbp->CheckRights('admin')) {
    ZbpAdminNext_JsonExit(401, 'Unauthorized');
}

HookFilterPlugin('Filter_Plugin_API_Begin');

// 后台内部调用按用户限流（ApiThrottle 内部仍会附加 IP 维度）
ApiThrottle('admin-spa-' . $zbp->user->ID, 600, 60);

$mod = strtolower((string) GetVars('mod', 'GET', ''));
$act = strtolower((string) GetVars('act', 'GET', ''));
$GLOBALS['mod'] = $mod;
$GLOBALS['act'] = $act;

ApiLoadMods();
ApiCheckMods();
ApiLoadPostData();
ApiVerifyCSRF();

$api_public_mods = $GLOBALS['api_public_mods'];

ApiDispatch($api_public_mods, $mod, $act);
