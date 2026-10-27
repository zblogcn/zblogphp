<?php

/**
 * admin-next API 兼容层.
 *
 * Z-BlogPHP 的删除接口只认 URL 参数 ?id=，而写操作统一走 POST body，
 * 前端传的是 { id }。这里在派发前把 body 里的 id 回填到 $_GET，
 * 避免每个删除接口各自处理。
 *
 * 注意：必须在 ApiVerifyCSRF() 之后调用。校验只会读取名为 csrf_token 的
 * 参数，但同时出现在 body 与 URL 里的 id 会让校验拿到数组，从而误判为非法
 * 请求（419）。先校验、后回填，两边都安全。
 */


function ZbpAdminNext_NormalizeIdParam(): void
{
    if (!empty($_GET['id'])) {
        return;
    }

    foreach (['id', 'ID'] as $key) {
        if (!empty($_POST[$key])) {
            $_GET['id'] = $_REQUEST['id'] = $_POST[$key];

            return;
        }
    }
}

/**
 * 当前请求是否来自本站同源页面.
 *
 * 浏览器对跨站 fetch 一定会带 Origin（旧浏览器退化为 Referer），
 * 因此只要两者之一存在就必须与当前站点一致；都缺失时才退回
 * 信任 X-ZBP-Admin-Spa 头（例如非浏览器客户端）。
 */
function ZbpAdminNext_IsSameOriginRequest(): bool
{
    global $zbp;

    $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
    if ($source === '') {
        return true;
    }

    $expected = rtrim($zbp->host, '/');
    $actual = rtrim(preg_match('#^[a-z][a-z0-9+.\-]*://#i', $source) ? parse_url(
        $source,
        PHP_URL_SCHEME
    ) . '://' . parse_url($source, PHP_URL_HOST) . (parse_url($source, PHP_URL_PORT) ? ':' . parse_url($source, PHP_URL_PORT) : '') : $source, '/');

    return strcasecmp($actual, $expected) === 0;
}
