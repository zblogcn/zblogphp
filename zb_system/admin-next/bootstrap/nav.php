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
 * 完整导航树.
 *
 * @return array<int, array<string, mixed>>
 */
function ZbpAdminNext_Nav(): array
{
    $items = ZbpAdminNext_OfficialNav();

    // 原样跑一遍旧菜单，保留插件的 key 依赖语义
    $GLOBALS['leftmenus'] = [];
    ob_start();
    ResponseAdmin_LeftMenu();
    $legacyHtml = (string) ob_get_clean();

    foreach (ZbpAdminNext_ParseLegacyMenu($legacyHtml) as $item) {
        $items[] = $item;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Menu_Items'] ?? [] as $fn => $signal) {
        $fn($items);
    }

    usort($items, static function (array $a, array $b): int {
        return ($a['order'] ?? 500) <=> ($b['order'] ?? 500);
    });

    return $items;
}
