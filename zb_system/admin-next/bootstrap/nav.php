<?php

/**
 * 导航采集.
 *
 * 官方菜单结构化直出；旧插件的菜单项通过解析 ResponseAdmin_LeftMenu()
 * 的 HTML 产物补充（必须原样调用，部分插件依赖 $leftmenus 的既有 key）。
 */

/**
 * 旧图标字体类名 → lucide 图标名.
 */
function ZbpAdminNext_MapIcon(?string $legacy): string
{
    static $map = [
        'icon-house-door-fill'   => 'house',
        'icon-pencil-square-fill' => 'square-pen',
        'icon-stickies'          => 'files',
        'icon-stickies-fill'     => 'file-text',
        'icon-folder-fill'       => 'folder',
        'icon-tags-fill'         => 'tags',
        'icon-chat-text-fill'    => 'message-square',
        'icon-inboxes-fill'      => 'inbox',
        'icon-people-fill'       => 'users',
        'icon-grid-1x2-fill'     => 'layout-grid',
        'icon-grid-3x3-gap-fill' => 'layout-panel-left',
        'icon-puzzle-fill'       => 'puzzle',
        'icon-gear-fill'         => 'settings',
        'icon-window-fill'       => 'app-window',
        'icon-link-45deg'        => 'link',
        'icon-caret-left-fill'   => 'chevron-left',
        'icon-caret-right-fill'  => 'chevron-right',
    ];

    if ($legacy === null || $legacy === '') {
        return 'link';
    }

    $first = preg_split('/\s+/', trim($legacy))[0] ?? '';

    return $map[$first] ?? 'link';
}

/**
 * 已启用 AppCentre 的管理地址.
 *
 * 仅接受已安装、已启用且声明管理页面的 AppCentre；地址统一由 App 提供。
 */
function ZbpAdminNext_AppCentreUrl(): ?string
{
    global $zbp;

    if (!in_array('AppCentre', $zbp->GetPreActivePlugin(), true)) {
        return null;
    }

    $app = $zbp->LoadApp('plugin', 'AppCentre');
    if (!$app->isloaded || !$app->CanManage()) {
        return null;
    }

    return $app->GetManageUrl();
}

/**
 * SPA 与官方导航共用的应用管理入口.
 *
 * @return array{appCentre: array{enabled: bool, url: string|null}, legacyPluginManageUrl: string}
 */
function ZbpAdminNext_AppManagement(): array
{
    global $zbp;

    $appCentreUrl = ZbpAdminNext_AppCentreUrl();

    return [
        'appCentre' => [
            'enabled' => $appCentreUrl !== null,
            'url'     => $appCentreUrl,
        ],
        // force=1 防止旧版入口在 ZC_ADMIN_UI=next 时再次跳回 SPA。
        'legacyPluginManageUrl' => $zbp->host . 'zb_system/admin/index.php?act=PluginMng&force=1',
    ];
}

/**
 * 官方菜单项（结构化，不经过 HTML）.
 *
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_OfficialNav(): array
{
    global $zbp;

    $defs = [
        ['dashboard', 'main', 'house', 'admin', 0],
        ['posts', 'content', 'files', 'ArticleMng', 10],
        ['pages', 'content', 'file-text', 'PageMng', 20],
        ['categories', 'content', 'folder', 'CategoryMng', 30],
        ['tags', 'content', 'tags', 'TagMng', 40],
        ['comments', 'content', 'message-square', 'CommentMng', 50],
        ['uploads', 'content', 'inbox', 'UploadMng', 60],
        ['members', 'manage', 'users', 'MemberMng', 70],
        ['themes', 'extend', 'layout-grid', 'ThemeMng', 80],
        ['modules', 'extend', 'layout-panel-left', 'ModuleMng', 90],
        ['plugins', 'extend', 'puzzle', 'PluginMng', 100],
        ['settings', 'system', 'settings', 'SettingMng', 110],
    ];

    $titles = [
        'dashboard'  => $zbp->lang['msg']['dashboard'],
        'posts'      => $zbp->lang['msg']['article_manage'],
        'pages'      => $zbp->lang['msg']['page_manage'],
        'categories' => $zbp->lang['msg']['category_manage'],
        'tags'       => $zbp->lang['msg']['tag_manage'],
        'comments'   => $zbp->lang['msg']['comment_manage'],
        'uploads'    => $zbp->lang['msg']['upload_manage'],
        'members'    => $zbp->lang['msg']['member_manage'],
        'themes'     => $zbp->lang['msg']['theme_manage'],
        'modules'    => $zbp->lang['msg']['module_manage'],
        'plugins'    => $zbp->lang['msg']['plugin_manage'],
        'settings'   => $zbp->lang['msg']['web_settings'] ?? $zbp->lang['msg']['settings'],
    ];

    $items = [];
    foreach ($defs as [$id, $group, $icon, $require, $order]) {
        if (!$zbp->CheckRights($require)) {
            continue;
        }
        $items[] = [
            'id'      => $id,
            'title'   => $titles[$id],
            'url'     => '#/' . ($id === 'dashboard' ? '' : $id),
            'icon'    => $icon,
            'group'   => $group,
            'order'   => $order,
            'require' => $require,
            'external' => false,
            'legacy'  => false,
        ];
    }

    $appManagement = ZbpAdminNext_AppManagement();
    $appCentreUrl = $appManagement['appCentre']['url'];
    if ($zbp->CheckRights('PluginMng')) {
        $items[] = [
            'id'       => $appCentreUrl === null ? 'legacy_plugin_manage' : 'app_centre',
            'title'    => $appCentreUrl === null
                ? ($zbp->lang['msg']['legacy_plugin_manage'] ?? '旧版插件管理')
                : ($zbp->lang['msg']['app_center'] ?? '应用中心'),
            'url'      => $appCentreUrl ?? $appManagement['legacyPluginManageUrl'],
            'icon'     => $appCentreUrl === null ? 'layout-panel-left' : 'puzzle',
            'group'    => 'extend',
            'order'    => 105,
            'require'  => 'PluginMng',
            'external' => true,
            'legacy'   => true,
        ];
    }

    return $items;
}

/**
 * 解析旧后台左菜单 HTML，取出「非官方」的插件菜单项.
 *
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_ParseLegacyMenu(string $html): array
{
    $html = trim($html);
    if ($html === '') {
        return [];
    }

    $knownIds = [
        'nav_new', 'nav_article', 'nav_page', 'nav_category', 'nav_tags',
        'nav_comment', 'nav_comment1', 'nav_upload', 'nav_member',
        'nav_theme', 'nav_module', 'nav_plugin',
    ];

    $doc = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<?xml encoding="UTF-8"?><ul id="zbp-legacy-nav">' . $html . '</ul>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $items = [];
    $seen = [];

    foreach ($doc->getElementsByTagName('li') as $li) {
        $id = $li->getAttribute('id');
        if ($id !== '' && in_array($id, $knownIds, true)) {
            continue;
        }
        if (str_contains($li->getAttribute('class'), 'split')) {
            continue;
        }

        $anchor = null;
        foreach ($li->childNodes as $node) {
            if ($node->nodeName === 'a') {
                $anchor = $node;
                break;
            }
        }
        if ($anchor === null) {
            continue;
        }

        $href = trim($anchor->getAttribute('href'));
        if ($href === '' || str_starts_with($href, 'javascript:') || isset($seen[$href])) {
            continue;
        }
        $seen[$href] = true;

        $span = null;
        foreach ($anchor->childNodes as $node) {
            if ($node->nodeName === 'span') {
                $span = $node;
                break;
            }
        }

        $label = $span !== null ? trim($span->textContent) : trim($anchor->textContent);
        if ($label === '') {
            continue;
        }

        $title = trim($anchor->getAttribute('title'));
        if ($title === '') {
            $title = $label;
        }

        $iconClass = null;
        if ($span !== null) {
            foreach ($span->getElementsByTagName('i') as $i) {
                $iconClass = $i->getAttribute('class');
                break;
            }
        }

        $items[] = [
            'id'       => 'legacy_' . substr(md5($href), 0, 8),
            'title'    => $title,
            'url'      => $href,
            'icon'     => ZbpAdminNext_MapIcon($iconClass),
            'group'    => 'extend',
            'order'    => 900,
            'require'  => null,
            'external' => true,
            'legacy'   => true,
        ];
    }

    return $items;
}

/**
 * 旧菜单只允许页面地址和 SPA 哈希路由.
 */
function ZbpAdminNext_SafeMenuUrl(string $href): bool
{
    if ($href === '' || preg_match('/[\x00-\x1f\x7f]/', $href)) {
        return false;
    }

    $scheme = parse_url($href, PHP_URL_SCHEME);
    if ($scheme !== null && !is_string($scheme)) {
        return false;
    }

    return $scheme === null || in_array(strtolower($scheme), ['http', 'https'], true);
}

/**
 * 判断旧顶部菜单是否只是官方菜单的重复入口.
 */
function ZbpAdminNext_IsOfficialTopMenuUrl(string $href): bool
{
    $parts = parse_url($href);
    if (!is_array($parts)) {
        return false;
    }

    $host = strtolower((string) ($parts['host'] ?? ''));
    $path = (string) ($parts['path'] ?? '');
    if (in_array($host, ['www.zblogcn.com', 'www.zblogcn.net'], true)
        && ($path === '' || $path === '/')) {
        return true;
    }

    $query = [];
    parse_str((string) ($parts['query'] ?? ''), $query);
    $action = isset($query['act']) && is_string($query['act']) ? $query['act'] : '';

    return in_array($action, [
        'admin',
        'ArticleMng',
        'PageMng',
        'CategoryMng',
        'TagMng',
        'CommentMng',
        'UploadMng',
        'MemberMng',
        'ThemeMng',
        'ModuleMng',
        'PluginMng',
        'SettingMng',
    ], true);
}

/**
 * 单菜单应用可以安全映射到其 SPA 路由；多入口应用无法推断目标，保留旧页面。
 *
 * @param array<int, array<string, mixed>> $manifests
 */
function ZbpAdminNext_MapTopMenuRoute(string $href, array $manifests): ?string
{
    if (str_starts_with($href, '#/')) {
        return $href;
    }

    $path = (string) parse_url($href, PHP_URL_PATH);
    foreach ($manifests as $manifest) {
        $basePath = rtrim((string) parse_url((string) $manifest['baseUrl'], PHP_URL_PATH), '/');
        if ($basePath === '' || ($path !== $basePath && !str_starts_with($path, $basePath . '/'))) {
            continue;
        }
        if (count($manifest['menu']) === 1) {
            return '#' . $manifest['menu'][0]['route'];
        }
    }

    return null;
}

/**
 * 解析旧顶部菜单，跳过官方项；无法安全映射的插件项标记为 legacy.
 *
 * @param array<int, array<string, mixed>> $manifests
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_ParseLegacyTopMenu(string $html, array $manifests = []): array
{
    $html = trim($html);
    if ($html === '') {
        return [];
    }

    $doc = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<?xml encoding="UTF-8"?><ul id="zbp-legacy-top-nav">' . $html . '</ul>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $items = [];
    $seen = [];

    foreach ($doc->getElementsByTagName('li') as $li) {
        $anchor = null;
        foreach ($li->childNodes as $node) {
            if ($node->nodeName === 'a') {
                $anchor = $node;
                break;
            }
        }
        if ($anchor === null) {
            continue;
        }

        $href = trim($anchor->getAttribute('href'));
        if (!ZbpAdminNext_SafeMenuUrl($href)
            || ZbpAdminNext_IsOfficialTopMenuUrl($href)
            || isset($seen[$href])) {
            continue;
        }
        $seen[$href] = true;

        $label = trim($anchor->textContent);
        if ($label === '') {
            continue;
        }
        $title = trim($anchor->getAttribute('title'));
        if ($title === '') {
            $title = $label;
        }

        $iconClass = null;
        foreach ($anchor->getElementsByTagName('i') as $i) {
            $iconClass = $i->getAttribute('class');
            break;
        }

        $route = ZbpAdminNext_MapTopMenuRoute($href, $manifests);
        $legacy = $route === null;
        $items[] = [
            'id'       => 'legacy_top_' . substr(md5($href), 0, 8),
            'title'    => $title,
            'url'      => $route ?? $href,
            'icon'     => ZbpAdminNext_MapIcon($iconClass),
            'group'    => 'extend',
            'order'    => 910,
            'require'  => null,
            'external' => $legacy || strtolower($anchor->getAttribute('target')) === '_blank',
            'legacy'   => $legacy,
        ];
    }

    return $items;
}

/**
 * 最后按 id 和 URL 去重，官方结构化菜单优先保留.
 *
 * @param array<int, array<string, mixed>> $items
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_DedupeNav(array $items): array
{
    $result = [];
    $seenIds = [];
    $seenUrls = [];

    foreach ($items as $item) {
        $id = (string) ($item['id'] ?? '');
        $url = (string) ($item['url'] ?? '');
        if (($id !== '' && isset($seenIds[$id])) || ($url !== '' && isset($seenUrls[$url]))) {
            continue;
        }
        if ($id !== '') {
            $seenIds[$id] = true;
        }
        if ($url !== '') {
            $seenUrls[$url] = true;
        }
        $result[] = $item;
    }

    return $result;
}

/**
 * 完整导航树.
 *
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_Nav(): array
{
    $items = ZbpAdminNext_OfficialNav();
    $manifests = ZbpAdminNext_AppManifests();

    // 原样跑一遍旧菜单，保留插件的 key 依赖语义
    $GLOBALS['leftmenus'] = [];
    ob_start();
    ResponseAdmin_LeftMenu();
    $legacyHtml = (string) ob_get_clean();

    $declaredApps = [];
    foreach ($manifests as $manifest) {
        $declaredApps[$manifest['appId']] = true;
    }

    foreach (ZbpAdminNext_ParseLegacyMenu($legacyHtml) as $item) {
        // 已声明 admin-next 前端的应用，不再重复出现旧入口
        foreach ($declaredApps as $appId => $_) {
            if (stripos((string) $item['url'], '/plugin/' . $appId . '/') !== false
                || stripos((string) $item['url'], '/theme/' . $appId . '/') !== false) {
                continue 2;
            }
        }
        $items[] = $item;
    }

    ZbpAdminNext_AppendManifestNav($items, $manifests);

    // 顶部菜单同样必须调用原钩子，部分插件依赖 $topmenus 的既有内容。
    $GLOBALS['topmenus'] = [];
    ob_start();
    ResponseAdmin_TopMenu();
    $legacyTopHtml = (string) ob_get_clean();
    foreach (ZbpAdminNext_ParseLegacyTopMenu($legacyTopHtml, $manifests) as $item) {
        $items[] = $item;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Menu_Items'] ?? [] as $fn => $signal) {
        $fn($items);
    }

    $items = ZbpAdminNext_DedupeNav($items);

    usort($items, static function (array $a, array $b): int {
        return ($a['order'] ?? 500) <=> ($b['order'] ?? 500);
    });

    return $items;
}
