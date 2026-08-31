<?php die();?>
<!DOCTYPE html>
<html lang="{$language}">

<head>
  <meta charset="utf-8" />
  <meta name="generator" content="{$zblogphp}" />
  <title>{$name} - {$title}</title>
  <link rel="stylesheet" href="{$host}zb_system/admin2/style/admin2.css?v={$version}">
  <link rel="stylesheet" href="{$host}zb_system/image/icon/icon.css?v={$version}">
  <script src="{$host}zb_system/script/jquery-2.2.4.min.js?v={$version}"></script>
  <script src="{$host}zb_system/script/jquery-ui.custom.min.js?v={$version}"></script>
  <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
  <script src="{$host}zb_system/script/c_admin_js_add.php?v={$version}"></script>
  {$header}
</head>

<body class="admin admin-{$action}">
  <!-- <p>title: {$title}</p> -->
  <!-- <p>action: {$action}</p> -->
  {template:layout_top}
  {template:layout_left}
  {template:layout_main}
  {$footer}
</body>

</html>
