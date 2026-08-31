<?php exit(); ?>
<!DOCTYPE html>
<html lang="{$language}">

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta name="generator" content="{$zblogphp}">
    <meta name="csrfToken" content="{$zbp.GetCSRFToken()}" />
    <meta name="csrfExpiration" content="{$zbp.csrfExpiration}" />
    <title>{$name} - {$title}</title>
    <link rel="stylesheet" href="{$host}zb_system/admin2/{$backendtheme}/style/{$backendtheme}.css?v={$version}">
    <!--<link rel="stylesheet" href="{$host}zb_system/image/icon/icon.css?v={$version}">-->
    <script src="{$host}zb_system/script/jquery-latest.min.js?v={$version}"></script>
    <script src="{$host}zb_system/script/jquery-ui.custom.min.js?v={$version}"></script>
    <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
    <script>
        window.__ADMIN_JS_CONFIG_URL__ = '{$zbp.ajaxurl}admin2';
    </script>
    <script src="{$host}zb_system/admin2/script/c_admin_js_add.js?v={$version}"></script>
    <script src="{$host}zb_system/admin2/{$backendtheme}/script/common.js"></script>
    <script>
        window.toyean = Object.assign(window.toyean || {}, {
            night: true,
            setnightstart: '22',
            setnightover: '6',
            backtotop: true,
            backtotopvalue: 500,
            version: '1.0'
        });
    </script>
    {$header}
    {php}HookFilterPlugin('Filter_Plugin_Admin_Header');{/php}
</head>

<body class="admin admin-{$action}{if GetVars('night','COOKIE')==1} night{/if}">
    <div class="wrapper">
        <!-- <p>title: {$title}</p> -->
        <!-- <p>action: {$action}</p> -->
        {template:layout_left}
        <div class="main{if GetVars('side','COOKIE')==1} on{/if}">
            {template:layout_top}
            {template:layout_main}
        </div>
        {template:layout_footer}
    </div>
</body>

</html>
