<?php

/**
 * Z-BlogPHP admin-next SPA 入口.
 *
 * 与 zb_system/admin/index.php 并存；不修改旧后台任何行为。
 * 访问：zb_system/admin-next/index.php#/posts
 *
 * @package Z-BlogPHP
 * @subpackage System/Administrator
 */

if (!isset($_SERVER['SCRIPT_NAME'])) {
    exit('Access denied');
}

// PHP 版本闸门：低于 8.0 时给出可读提示，而不是白屏
if (PHP_VERSION_ID < 80000) {
    header('Content-Type: text/html; charset=utf-8', true, 500);
    echo '<!doctype html><html lang="zh-CN"><meta charset="utf-8">'
        . '<title>PHP 版本过低</title>'
        . '<body style="font:14px/1.7 system-ui;padding:48px;max-width:640px;margin:0 auto">'
        . '<h1 style="font-size:18px">无法加载新版后台</h1>'
        . '<p>新版后台需要 PHP 8.0 或更高版本，当前版本为 ' . htmlspecialchars(PHP_VERSION) . '。</p>'
        . '<p><a href="../cmd.php?act=admin">返回旧版后台</a></p>'
        . '</body></html>';
    exit;
}

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

// 必须在插件加载前定义，否则插件注册的钩子会被静默丢弃
DefinePluginFilter('Filter_Plugin_Admin_Menu_Items');

$zbp->Load();

// 显式回退到旧后台
if (GetVars('ui', 'GET') === 'legacy') {
    Redirect($zbp->cmdurl . '?act=admin');
}

if (!$zbp->islogin) {
    Redirect($zbp->cmdurl . '?act=login&redirect=' . rawurlencode($zbp->host . 'zb_system/admin-next/index.php'));
}

if (!$zbp->CheckRights('admin')) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$adminNextBase = $zbp->host . 'zb_system/admin-next/';
$action = 'admin-next';

require __DIR__ . '/bootstrap/assets.php';
require __DIR__ . '/bootstrap/rights.php';
require __DIR__ . '/bootstrap/apps.php';
require __DIR__ . '/bootstrap/nav.php';
require __DIR__ . '/bootstrap/shell.php';

ZbpAdminNext_RenderShell();
