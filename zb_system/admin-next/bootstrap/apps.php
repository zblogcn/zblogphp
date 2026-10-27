<?php

/**
 * 采集已启用插件与当前主题的 admin-next 前端声明.
 *
 * 只读 XML，不加载任何应用代码；声明不合法时整条忽略并记录日志，
 * 保证坏插件不会让后台白屏。
 */

/**
 * 校验插件/主题 id，防止路径穿越.
 */
function ZbpAdminNext_ValidAppId(string $id): bool
{
    return $id !== ''
        && preg_match('/^[A-Za-z0-9_\\-]+$/', $id) === 1
        && strlen($id) <= 100;
}

/**
 * 组装单个应用的声明，失败返回 null.
 *
 * @param array $admin App::$admin
 * @param array $info  应用基础信息
 */
function ZbpAdminNext_BuildManifest(array $admin, array $info): ?array
{
    $entry = (string) ($admin['entry'] ?? '');

    // 允许纯菜单声明（无前端产物），用于纯后端插件
    if ($entry !== '') {
        if (str_contains($entry, '..') || str_starts_with($entry, '/')) {
            ZbpAdminNext_Log('admin-next: 非法 entry ' . $entry . ' (' . $info['id'] . ')');
            return null;
        }

        $file = $info['path'] . $entry;
        if (!is_readable($file)) {
            ZbpAdminNext_Log(
                'admin-next: entry 不存在 ' . $info['id'] . ' -> ' . $entry
            );
            return null;
        }
    }

    $menu = [];
    foreach ($admin['menu'] as $item) {
        $route = (string) ($item['route'] ?? '');
        if ($route === '') {
            $route = '/plugin/' . $info['id'];
        }

        // 路由必须落在本应用的命名空间下
        if (!str_starts_with($route, '/plugin/' . $info['id'])) {
            ZbpAdminNext_Log(
                'admin-next: 路由越界 ' . $info['id'] . ' -> ' . $route
            );
            continue;
        }

        $menu[] = [
            'id'    => $info['id'] . '_' . $item['id'],
            'title' => $item['title'],
            'route' => $route,
            'icon'  => $item['icon'] !== '' ? $item['icon'] : 'puzzle',
            'group' => $item['group'] !== '' ? $item['group'] : ($info['type'] === 'theme' ? 'theme' : 'extend'),
            'order' => $item['order'],
            'require' => $item['require'],
        ];
    }

    if ($entry === '' && count($menu) === 0) {
        return null;
    }

    return [
        'appId'   => $info['id'],
        'type'    => $info['type'],
        'name'    => $info['name'],
        'version' => $info['version'],
        'baseUrl' => $info['baseUrl'],
        'entry'   => $entry,
        'style'   => (string) ($admin['style'] ?? ''),
        'legacy'  => (string) ($admin['legacy'] ?? ''),
        'menu'    => $menu,
    ];
}

/**
 * 解析 <admin><editor> 声明为可注入编辑器的资源描述.
 *
 * 与页面清单分开处理：编辑器不占菜单，也不要求 mount() 入口。
 *
 * @param array $admin  App::$admin
 * @param array $info   应用基础信息
 *
 * @return array|null
 */
function ZbpAdminNext_BuildEditor(array $admin, array $info): ?array
{
    $editor = $admin['editor'] ?? null;
    if (!is_array($editor) || empty($editor['asset'])) {
        return null;
    }

    $asset = (string) $editor['asset'];
    $style = (string) ($editor['style'] ?? '');

    foreach ([$asset, $style] as $path) {
        if ($path === '') {
            continue;
        }
        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            ZbpAdminNext_Log('admin-next: 非法编辑器资源 ' . $path . ' (' . $info['id'] . ')');
            return null;
        }
    }

    if (!is_readable($info['path'] . $asset)) {
        ZbpAdminNext_Log('admin-next: 编辑器资源不存在 ' . $info['id'] . ' -> ' . $asset);
        return null;
    }

    return [
        'appId'        => $info['id'],
        'name'         => $info['name'],
        'version'      => $info['version'],
        'asset'        => $info['baseUrl'] . $asset,
        'style'        => $style !== '' ? $info['baseUrl'] . $style : '',
        'capabilities' => $editor['capabilities'] ?? [],
    ];
}

/**
 * 记录一条声明错误，避免静默失败.
 */
function ZbpAdminNext_Log(string $message): void
{
    if (function_exists('Logs')) {
        Logs($message, 'WARN');
    }
}

/**
 * 全部可用的 admin-next 应用声明.
 *
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_AppManifests(): array
{
    global $zbp;

    $manifests = [];

    // 插件：仅启用的
    foreach ($zbp->GetPreActivePlugin() as $id) {
        $id = trim((string) $id);
        if (!ZbpAdminNext_ValidAppId($id)) {
            continue;
        }

        $app = $zbp->LoadApp('plugin', $id);
        if (!$app->isloaded || $app->admin === null) {
            continue;
        }

        $manifest = ZbpAdminNext_BuildManifest($app->admin, [
            'id'      => $app->id,
            'type'    => 'plugin',
            'name'    => $app->name,
            'version' => $app->version,
            'path'    => $app->app_path,
            'baseUrl' => $app->app_url,
        ]);

        if ($manifest !== null) {
            $manifests[] = $manifest;
        }
    }

    // 当前主题
    if (ZbpAdminNext_ValidAppId((string) $zbp->theme)) {
        $theme = $zbp->LoadApp('theme', $zbp->theme);
        if ($theme->isloaded && $theme->admin !== null) {
            $manifest = ZbpAdminNext_BuildManifest($theme->admin, [
                'id'      => $theme->id,
                'type'    => 'theme',
                'name'    => $theme->name,
                'version' => $theme->version,
                'path'    => $theme->app_path,
                'baseUrl' => $theme->app_url,
            ]);

            if ($manifest !== null) {
                $manifests[] = $manifest;
            }
        }
    }

    return $manifests;
}

/**
 * 收集已启用插件声明的编辑器实现.
 *
 * 同一时间只允许一个编辑器生效：先声明者优先，其余忽略并记录日志。
 *
 * @return array|null
 */
function ZbpAdminNext_EditorManifest(): ?array
{
    global $zbp;

    foreach ($zbp->GetPreActivePlugin() as $id) {
        $id = trim((string) $id);
        if (!ZbpAdminNext_ValidAppId($id)) {
            continue;
        }

        $app = $zbp->LoadApp('plugin', $id);
        if (!$app->isloaded || $app->admin === null) {
            continue;
        }

        $editor = ZbpAdminNext_BuildEditor($app->admin, [
            'id'      => $app->id,
            'type'    => 'plugin',
            'name'    => $app->name,
            'version' => $app->version,
            'path'    => $app->app_path,
            'baseUrl' => $app->app_url,
        ]);

        if ($editor !== null) {
            return $editor;
        }
    }

    return null;
}

/**
 * 把应用声明转换成导航项，追加到导航树.
 *
 * @param array<int, array<string, mixed>> $items
 * @param array<int, array<string, mixed>> $manifests
 */
function ZbpAdminNext_AppendManifestNav(array &$items, array $manifests): void
{
    foreach ($manifests as $manifest) {
        if (count($manifest['menu']) === 0) {
            continue;
        }

        foreach ($manifest['menu'] as $menu) {
            $items[] = [
                'id'       => $menu['id'],
                'title'    => $menu['title'],
                'url'      => '#' . $menu['route'],
                'icon'     => $menu['icon'],
                'group'    => $menu['group'],
                'order'    => $menu['order'] > 0 ? $menu['order'] : 200,
                'require'  => $menu['require'],
                'external' => false,
                'legacy'   => false,
                'app'      => $manifest['appId'],
            ];
        }
    }
}
