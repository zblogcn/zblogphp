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
 * Origin 优先，缺失时使用 Referer；两者都缺失或无法解析时拒绝。
 * 按 scheme/host/port 元组比较，避免前缀匹配和默认端口误判。
 */
function ZbpAdminNext_IsSameOriginRequest(): bool
{
    global $zbp;

    $source = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (!is_string($source)) {
        return false;
    }
    $source = trim($source);

    if ($source === '') {
        $source = $_SERVER['HTTP_REFERER'] ?? '';
        if (!is_string($source)) {
            return false;
        }
        $source = trim($source);
    }

    if ($source === '' || preg_match('/[\x00-\x20\x7f]/', $source) === 1) {
        return false;
    }

    $normalize = static function ($parts): ?array {
        if (!is_array($parts)) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '' || !in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        return [$scheme, $host, (int) $port];
    };

    $expected = $normalize(parse_url(rtrim((string) $zbp->host, '/')));
    $actual = $normalize(parse_url($source));

    return $expected !== null && $actual === $expected;
}
