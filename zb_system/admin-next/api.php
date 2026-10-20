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
require __DIR__ . '/bootstrap/api-compat.php';

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

    echo json_encode([
        'code'    => $status,
        'message' => $message,
        'data'    => null,
        'error'   => null,
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!$zbp->islogin || !$zbp->CheckRights('admin')) {
    ZbpAdminNext_JsonExit(401, 'Unauthorized');
}

// 后台 SPA 标记：只有在登录与 admin 校验通过后才会写入。
// ApiVerifyCSRF() 据此区分「后台同源会话」与「公开 API 调用」，
// 不从请求参数读取，避免被外部伪造。
if (
    isset($_SERVER['HTTP_X_ZBP_ADMIN_SPA'])
    && $_SERVER['HTTP_X_ZBP_ADMIN_SPA'] === '1'
) {
    $GLOBALS['zbp_admin_spa_verified'] = true;
}

HookFilterPlugin('Filter_Plugin_API_Begin');

// 后台内部调用按用户维度限流
ApiThrottle('admin-spa-' . $zbp->user->ID, 600, 60);

$mod = strtolower((string) GetVars('mod', 'GET', ''));
$act = strtolower((string) GetVars('act', 'GET', ''));
$GLOBALS['mod'] = $mod;
$GLOBALS['act'] = $act;

ApiLoadMods();
ApiCheckMods();
ApiLoadPostData();

ApiVerifyCSRF();

// 写操作统一用 JSON body，删除接口却只读 URL 的 id，这里补齐。
// 必须放在 CSRF 校验之后：先校验再回填，避免同名字段冲突导致 419。
ZbpAdminNext_NormalizeIdParam();

$api_public_mods = $GLOBALS['api_public_mods'];

ApiDispatch($api_public_mods, $mod, $act);
