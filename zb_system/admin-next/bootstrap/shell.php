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
 * 前端需要的语言片段.
 */
function ZbpAdminNext_Lang(): array
{
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
        'lang'      => ZbpAdminNext_Lang(),
        'csrf'      => [
            'token'      => $zbp->GetCSRFToken('api'),
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

    // 主题色在首屏前应用，避免闪白
    echo '<script>(function(){try{var t=localStorage.getItem("zbp-admin-theme");'
        . 'if(t==="dark"||(t!=="light"&&window.matchMedia("(prefers-color-scheme: dark)").matches))'
        . '{document.documentElement.classList.add("dark")}}catch(e){}})();</script>' . "\n";

    echo "</head>\n<body>\n";
    echo '<div id="zb-admin-root"></div>' . "\n";
    echo '<script>window.__ZB__=' . ZbpAdminNext_JsonForScript($boot) . ';</script>' . "\n";

    if ($assets['missing']) {
        echo '<script>window.__ZB_ASSETS_MISSING__=true;</script>' . "\n";
    }

    foreach ($assets['js'] as $src) {
        echo '<script type="module" src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    }

    echo "</body>\n</html>\n";
}
