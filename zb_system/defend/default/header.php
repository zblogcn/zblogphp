{* Template Name:公共头部 * Template Type:none *}
<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<meta name="generator" content="{$zblogphp}" />
	<meta name="renderer" content="webkit">
	<title>{$name}-{$title}</title>
	<link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css" type="text/css" media="all"/>
	<script src="{$host}zb_system/script/jquery-latest.min.js?v={$version}"></script>
	<script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
	<script src="{$host}zb_system/script/c_html_js_add.php?hash={$html_js_hash}&v={$version}"></script>
{$header}
{if $type=='index'&&$page=='1'}
	<link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd" />
	<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml" />
{/if}
</head>
