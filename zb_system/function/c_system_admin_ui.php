<?php

/**
 * 后台界面切换辅助.
 *
 * 最终验收通过前默认使用旧后台；只有显式设置 ZC_ADMIN_UI = next
 * 才启用新版 SPA（admin-next）。也可通过 URL 追加 ?ui=legacy 临时回退。
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

    return ($zbp->option['ZC_ADMIN_UI'] ?? 'legacy') === 'next';
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
