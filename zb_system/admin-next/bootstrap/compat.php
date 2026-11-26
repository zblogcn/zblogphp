<?php

/**
 * 旧后台插件兼容诊断.
 *
 * 只读取已注册钩子，不执行插件回调，避免在 SPA 外壳中输出旧后台 HTML。
 */

/**
 * 文章/页面编辑页依赖的旧钩子.
 *
 * @return string[]
 */
function ZbpAdminNext_LegacyEditHookNames(): array
{
    return [
        'Filter_Plugin_Edit_Begin',
        'Filter_Plugin_Edit_End',
        'Filter_Plugin_Edit_SubMenu',
        'Filter_Plugin_Edit_Response',
        'Filter_Plugin_Edit_Response2',
        'Filter_Plugin_Edit_Response3',
        'Filter_Plugin_Edit_Response4',
        'Filter_Plugin_Edit_Response5',
    ];
}

/**
 * 按已加载插件的注册名推断钩子回调归属.
 *
 * 回调名通常以插件 ID 开头；无法确定归属时保留 null，仍会触发兼容回退。
 */
function ZbpAdminNext_PluginForHookCallback(string $callback): ?string
{
    global $plugins;

    $ids = array_keys((array) ($plugins ?? []));
    usort($ids, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

    $callbackOwner = preg_split('/(::|@)/', $callback, 2)[0] ?? $callback;
    foreach ($ids as $id) {
        $id = (string) $id;
        $prefix = $id . '_';
        if (strncasecmp($callback, $prefix, strlen($prefix)) === 0
            || strncasecmp($callbackOwner, $prefix, strlen($prefix)) === 0) {
            return $id;
        }
    }

    return null;
}

/**
 * 采集一组钩子的已注册回调.
 *
 * @param string[] $hookNames
 * @return array<int, array{hook: string, callback: string, plugin: string|null, signal: string}>
 */
function ZbpAdminNext_HookDiagnostics(array $hookNames): array
{
    $diagnostics = [];

    foreach ($hookNames as $hook) {
        foreach (List_Filter_Plugin($hook) as $callback => $signal) {
            $callback = (string) $callback;
            $diagnostics[] = [
                'hook'     => $hook,
                'callback' => $callback,
                'plugin'   => ZbpAdminNext_PluginForHookCallback($callback),
                'signal'   => (string) $signal,
            ];
        }
    }

    return $diagnostics;
}

/**
 * 写入 window.__ZB__.compat 的兼容诊断数据.
 */
function ZbpAdminNext_CompatData(): array
{
    $legacyEditHooks = ZbpAdminNext_HookDiagnostics(ZbpAdminNext_LegacyEditHookNames());
    $adminHeaderHooks = ZbpAdminNext_HookDiagnostics(['Filter_Plugin_Admin_Header']);
    $topMenuHooks = ZbpAdminNext_HookDiagnostics(['Filter_Plugin_Admin_TopMenu']);

    $legacyEditPlugins = [];
    foreach ($legacyEditHooks as $diagnostic) {
        if ($diagnostic['plugin'] !== null && !in_array($diagnostic['plugin'], $legacyEditPlugins, true)) {
            $legacyEditPlugins[] = $diagnostic['plugin'];
        }
    }
    sort($legacyEditPlugins);

    return [
        'legacyEditRequired' => count($legacyEditHooks) > 0,
        'legacyEditHooks'    => $legacyEditHooks,
        'legacyEditPlugins'  => $legacyEditPlugins,
        'adminHeaderHooks'   => $adminHeaderHooks,
        'topMenuHooks'       => $topMenuHooks,
        'legacyTopMenu'      => count($topMenuHooks) > 0,
    ];
}
