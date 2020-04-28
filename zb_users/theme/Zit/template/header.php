<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
  <meta charset="utf-8">
  <title>{$title} - {$name}</title>
{template:seo}
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme" content="吉光片羽,jgpy.cn">
  <meta name="generator" content="{$zblogphp}">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
  <meta http-equiv="Cache-Control" content="no-siteapp">
  <link rel="shortcut icon" href="{$host}favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css?v={$cfg->Custom}" type="text/css" media="all">
  <script src="{$host}zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
  <script src="{$host}zb_system/script/zblogphp.js" type="text/javascript"></script>
  <script src="{$host}zb_system/script/c_html_js_add.php" type="text/javascript"></script>
  <script src="{$host}zb_users/theme/{$theme}/script/custom.js?v={$cfg->Custom}" type="text/javascript"></script>
{$header}
{if $type=='index'&&$page=='1'}
  <link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}">
  <link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd">
  <link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml">
{/if}
</head>

<body class="{$type}">
<header id="face">
  <div class="inner">
    <h2 id="logo"><a href="{$host}" title="{$cfg.Logo?$cfg.Logo:$name}" class="zit">{$cfg.Logo?$cfg.Logo:$name}</a></h2>
    <nav id="menu">
      <ul>
        {module:navbar}
      </ul>
    </nav>
    <div id="seek" class="invis">
        <form name="search" method="post" action="{$host}zb_system/cmd.php?act=search">
          <input name="q" type="text" placeholder="{$msg.keyword}"><button type="submit" class="kico-magnify"><dfn>{$msg.search}</dfn></button>
          <p>{module:zit-searchtag} <i id="shuts" class="kico">&times;</i></p>
        </form>
    </div>
  </div>
</header>
<section id="banner">
  <b id="backdrop"></b>
  <div class="inner">
    <h2 id="motto" class="zit">{$motto}</h2>
  </div>
</section>