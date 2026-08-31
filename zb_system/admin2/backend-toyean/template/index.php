<?php die();?>
{php}<?php if ($zbp->option['ZC_ADDITIONAL_SECURITY']) {
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Content-Security-Policy: ' . GetBackendCSPHeader());
    if ($zbp->isHttps) {
        header('Upgrade-Insecure-Requests: 1');
    }
}?>{/php}
<!DOCTYPE html>
<html lang="{$language}">
<head>
  <meta charset="utf-8" />
  <meta name="generator" content="{$zblogphp}" />
  <title>{$name} - {$title}</title>
  <link rel="stylesheet" href="{$host}zb_system/admin2/{$backendtheme}/style/style.css?v={$version}">
  <!--<link rel="stylesheet" href="{$host}zb_system/image/icon/icon.css?v={$version}">-->
  <script src="{$host}zb_system/script/jquery-2.2.4.min.js?v={$version}"></script>
  <!--<script src="{$host}zb_system/script/jquery-ui.custom.min.js?v={$version}"></script>-->
  <script src="{$host}zb_system/admin2/{$backendtheme}/script/common.js"></script>
  <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
  <script src="{$host}zb_system/script/c_admin_js_add.php?v={$version}"></script>
<script>window.toyean = Object.assign(window.toyean || {}, { night: true, setnightstart: '22', setnightover: '6', backtotop: true, backtotopvalue: 500, version: '1.0' });</script>
  {$header}
{php}HookFilterPlugin('Filter_Plugin_Admin_Header');{/php}
</head>
<body class="admin admin-{$action}">
  <div class="wrapper">
  <!-- <p>title: {$title}</p> -->
  <!-- <p>action: {$action}</p> -->
  {template:layout_left}
  <div class="main">
  {template:layout_top}
  {template:layout_main}
  </div>
  {template:layout_footer}
  </div>
</body>

</html>
