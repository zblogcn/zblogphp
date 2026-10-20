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
