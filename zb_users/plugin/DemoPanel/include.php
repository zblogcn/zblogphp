<?php

// 演示插件：只注册一个 API mod，后台页面由 dist/admin.js 提供
RegisterPlugin('DemoPanel', 'ActivePlugin_DemoPanel');

function ActivePlugin_DemoPanel()
{
    // 与后台共享同一套鉴权、CSRF 与限流
    ApiAddMod('demopanel', dirname(__FILE__) . '/api/demopanel.php');
}

function InstallPlugin_DemoPanel()
{
}

function UninstallPlugin_DemoPanel()
{
}
