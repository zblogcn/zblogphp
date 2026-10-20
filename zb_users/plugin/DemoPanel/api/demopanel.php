<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 演示插件 API：继承宿主的登录、权限、CSRF 与限流.
 *
 * @return array
 */
function api_demopanel_stats()
{
    global $zbp;

    ApiCheckAuth(true, 'admin');

    return [
        'data' => [
            'plugin'    => 'DemoPanel',
            'site'      => $zbp->name,
            'version'   => $zbp->version,
            'articles'  => (int) $zbp->cache->all_article_nums,
            'server'    => PHP_VERSION,
            'timestamp' => time(),
        ],
    ];
}

/**
 * 演示插件 API：回显提交内容，用于验证写操作链路.
 *
 * @return array
 */
function api_demopanel_echo()
{
    ApiCheckAuth(true, 'admin');

    return [
        'data'    => ['message' => (string) GetVars('message', 'POST', '')],
        'message' => '已收到',
    ];
}
