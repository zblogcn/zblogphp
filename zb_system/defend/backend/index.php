<?php exit(); ?>
<!DOCTYPE html>
<html lang="{$language}">

<head>
    <meta charset="utf-8" />
    <meta name="generator" content="{$zblogphp}" />
    <meta name="csrfToken" content="{$zbp.GetCSRFToken()}" />
    <meta name="csrfExpiration" content="{$zbp.csrfExpiration}" />
    <title>{$name} - {$title}</title>
    <link rel="stylesheet" href="{$host}zb_system/admin2/{$backendtheme}/style/{$backendtheme}.css?v={$version}">
    <link rel="stylesheet" href="{$host}zb_system/image/icon/icon.css?v={$version}">
    <link rel="stylesheet" href="{$host}zb_system/css/jquery-ui.custom.css?v={$version}" />
    <script src="{$host}zb_system/script/jquery-latest.min.js?v={$version}"></script>
    <script src="{$host}zb_system/script/jquery-ui.custom.min.js?v={$version}"></script>
    <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
    <script>
        window.__ADMIN_JS_CONFIG_URL__ = '{$zbp.ajaxurl}admin2';
    </script>
    <script src="{$host}zb_system/admin2/script/c_admin_js_add.js?v={$version}"></script>
    <script src="{$host}zb_system/admin2/{$backendtheme}/script/{$backendtheme}.js?v={$version}"></script>
    {$header}
    {php}HookFilterPlugin('Filter_Plugin_Admin_Header');{/php}
</head>

<body class="admin admin-{$action}">
    <!-- <p>title: {$title}</p> -->
    <!-- <p>action: {$action}</p> -->
    {template:layout_top}
    {template:layout_left}
    {template:layout_main}
    {template:layout_footer}
</body>

</html>
