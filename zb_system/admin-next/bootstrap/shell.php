<?php

/**
 * 组装首屏引导数据并输出 HTML 外壳.
 */

/**
 * 安全地序列化到 <script> 内.
 *
 * 不能复用 JsonEncode()：它会先做 htmlspecialchars，把 URL 里的 & 变成 &amp;。
 */
function ZbpAdminNext_JsonForScript(mixed $data): string
{
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    if (defined('JSON_HEX_TAG')) {
        $flags |= JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    }

    return json_encode($data, $flags) ?: 'null';
}

/**
 * 后台 SPA 使用的 API CSRF 令牌.
 *
 * GetCSRFToken() 在「附加安全」开启时会把浏览器 UA 算进哈希，
 * 浏览器升级或换客户端后令牌就失效。后台是同源已登录会话，
 * 这里生成不绑定 UA 的令牌，并由 ApiVerifyCSRF() 对 SPA 请求放行。
 */
function ZbpAdminNext_ApiCsrfToken(): string
{
    global $zbp;

    $security = $zbp->option['ZC_ADDITIONAL_SECURITY'];
    $zbp->option['ZC_ADDITIONAL_SECURITY'] = false;
    $token = $zbp->GetCSRFToken('api');
    $zbp->option['ZC_ADDITIONAL_SECURITY'] = $security;

    return (string) $token;
}

/**
 * 前端需要的语言片段.
 */
function ZbpAdminNext_Lang(): array{
    global $zbp;

    return [
        'msg'               => $zbp->lang['msg'] ?? [],
        'post_status_name'  => $zbp->lang['post_status_name'] ?? [],
        'lang_bcp47'        => $zbp->lang['lang_bcp47'] ?? 'zh-CN',
        'locale'            => $zbp->lang['lang'] ?? '',
    ];
}

/**
 * 组装 window.__ZB__.
 */
function ZbpAdminNext_BootData(): array
{
    global $zbp;

    return [
        'host'    => $zbp->host,
        'entry'   => $zbp->host . 'zb_system/admin-next/index.php',
        'api'     => $zbp->host . 'zb_system/admin-next/api.php',
        'legacy'  => $zbp->cmdurl . '?act=admin',
        'version' => ZC_VERSION_FULL,
        'debug'   => (bool) $zbp->isdebug,
        'user'    => [
            'id'         => (int) $zbp->user->ID,
            'name'       => $zbp->user->Name,
            'staticName' => $zbp->user->StaticName,
            'level'      => (int) $zbp->user->Level,
            'levelName'  => $zbp->user->LevelName,
            'avatar'     => $zbp->user->Avatar,
        ],
        'rights'    => ZbpAdminNext_Rights(),
        'nav'       => ZbpAdminNext_Nav(),
        'apps'      => ZbpAdminNext_AppManifests(),
        // 插件声明的编辑器（<admin><editor>），无则前端回退内置编辑器
        'editor'    => ZbpAdminNext_EditorManifest(),
        'lang'      => ZbpAdminNext_Lang(),
        'csrf'      => [
            // 网站开启「附加安全」时，令牌会绑定浏览器 UA，
            // 浏览器升级或页面来自不同 UA 客户端后旧令牌会失效；
            // 后台是同源会话，这里只提供不绑定 UA 的令牌，避免写操作误报 419。
            'token'      => ZbpAdminNext_ApiCsrfToken(),
            'admin'      => $zbp->GetCSRFToken(),
            'expiration' => (int) $zbp->csrfExpiration,
        ],
        'options'   => [
            'manageCount'  => (int) $zbp->option['ZC_MANAGE_COUNT'],
            'pagebarCount' => (int) $zbp->option['ZC_PAGEBAR_COUNT'],
            'blogName'     => $zbp->name,
            'blogSubname'  => $zbp->subname,
        ],
    ];
}

/**
 * 输出页面.
 */
function ZbpAdminNext_RenderShell(): void
{
    global $zbp;

    $assets = ZbpAdminNext_Assets();
    $boot = ZbpAdminNext_BootData();

    if ($zbp->option['ZC_ADDITIONAL_SECURITY']) {
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Content-Security-Policy: ' . GetBackendCSPHeader());
        if ($zbp->isHttps) {
            header('Upgrade-Insecure-Requests: 1');
        }
    }

    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');

    $title = htmlspecialchars($zbp->name, ENT_QUOTES, 'UTF-8');
    $langCode = htmlspecialchars($boot['lang']['lang_bcp47'] ?: 'zh-CN', ENT_QUOTES, 'UTF-8');
    $blogversion = htmlspecialchars($GLOBALS['blogversion'] ?? '', ENT_QUOTES, 'UTF-8');

    echo '<!doctype html>' . "\n";
    echo '<html lang="' . $langCode . '">' . "\n<head>\n";
    echo '<meta charset="utf-8" />' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />' . "\n";
    echo '<meta name="robots" content="none" />' . "\n";
    echo '<meta name="generator" content="Z-BlogPHP ' . $blogversion . '" />' . "\n";
    echo '<meta name="csrfToken" content="' . htmlspecialchars($boot['csrf']['token'], ENT_QUOTES, 'UTF-8') . '" />' . "\n";
    echo '<title>' . $title . '</title>' . "\n";

    foreach ($assets['css'] as $href) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" />' . "\n";
    }

    // 插件复用的 Tailwind 预设只生成插件可能用到的工具类，命名空间限于插件根节点
    echo '<style>@layer base{}.zbp-plugin-root .tw-hidden{display:none}</style>' . "\n";

    foreach ($assets['styles'] as $appId => $href) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8')
            . '" data-plugin="' . htmlspecialchars($appId, ENT_QUOTES, 'UTF-8') . '" />' . "\n";
    }

    // 插件提供的编辑器样式（<admin><editor> 声明）
    if (!empty($boot['editor']['style'])) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars($boot['editor']['style'], ENT_QUOTES, 'UTF-8')
            . '" data-editor="' . htmlspecialchars($boot['editor']['appId'], ENT_QUOTES, 'UTF-8') . '" />' . "\n";
    }

    // 主题色在首屏前应用，避免闪白
    echo '<script>(function(){try{var t=localStorage.getItem("zbp-admin-theme");'
        . 'if(t==="dark"||(t!=="light"&&window.matchMedia("(prefers-color-scheme: dark)").matches))'
        . '{document.documentElement.classList.add("dark")}}catch(e){}})();</script>' . "\n";

    // 插件共享运行时：裸模块名 react 指向同一份实例，避免多份 React
    if ($assets['runtime'] !== '') {
        $runtime = htmlspecialchars($assets['runtime'], ENT_QUOTES, 'UTF-8');
        echo '<script type="importmap">'
            . '{"imports":{"react":"' . $runtime . '","react-dom":"' . $runtime . '","react-dom/client":"' . $runtime . '"}}'
            . '</script>' . "\n";
    }

    echo "</head>\n<body>\n";
    echo '<div id="zb-admin-root"></div>' . "\n";
    echo '<script>window.__ZB__=' . ZbpAdminNext_JsonForScript($boot) . ';</script>' . "\n";

    if ($assets['missing']) {
        echo '<script>window.__ZB_ASSETS_MISSING__=true;</script>' . "\n";
    }

    // 先加载共享运行时，再加载主入口，确保 window.__ZB_PLUGIN_RUNTIME__ 已就绪
    if ($assets['runtime'] !== '') {
        echo '<script type="module" src="' . htmlspecialchars($assets['runtime'], ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    }

    foreach ($assets['js'] as $src) {
        echo '<script type="module" src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    }

    echo "</body>\n</html>\n";
}
