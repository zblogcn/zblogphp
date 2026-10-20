<?php

/**
 * 后台界面切换辅助.
 *
 * 默认使用新版 SPA（admin-next），可通过以下方式回到旧后台：
 *  1. 系统设置 ZC_ADMIN_UI = legacy
 *  2. URL 追加 ?ui=legacy
 *
 * 旧后台仍可独立访问（zb_system/admin/index.php），本文件只负责
 * 决定「默认落地页」与登录后的跳转目标。
 */

/**
 * 是否使用新版后台界面.
 */
function AdminUiIsNext(): bool
{
    global $zbp;

    if (GetVars('ui', 'GET') === 'legacy') {
        return false;
    }

    return ($zbp->option['ZC_ADMIN_UI'] ?? 'next') !== 'legacy';
}

/**
 * 后台首页地址（相对 zb_system/ 目录）.
 */
function AdminHomeUrl(string $suffix = ''): string
{
    if (class_exists('App') && !AdminUiIsNext()) {
        return 'admin/index.php?act=admin' . $suffix;
    }

    return 'admin-next/index.php' . $suffix;
}

/**
 * 设置页下拉框选项（供旧后台设置页使用）.
 */
function AdminUiOptions(): array
{
    return [
        'next'   => '新版后台（SPA）',
        'legacy' => '旧版后台（表格）',
    ];
}
