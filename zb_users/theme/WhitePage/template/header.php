<!DOCTYPE html>
<html lang="{$lang['lang_bcp47']}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title>{$name}-{$title}</title>
	<meta name="generator" content="{$zblogphp}" />
	<meta name="viewport" content="width=device-width" />
{if $zbp.Config('WhitePage').SuperFast}
	<script src="{$host}zb_system/script/c_html_js_add.php?{if isset($html_js_hash)}hash={$html_js_hash}&{/if}v={$version}"></script>
	<link rel="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css?{$themeinfo['modified']}" type="text/css" media="all"/>
{else}
	<link rel="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css?{$themeinfo['modified']}" type="text/css" media="all"/>
	<script src="{$host}zb_system/script/jquery-latest.min.js?v={$version}"></script>
	<script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script>
	<script src="{$host}zb_system/script/c_html_js_add.php?{if isset($html_js_hash)}hash={$html_js_hash}&{/if}v={$version}"></script>
{/if}
{$header}
{if $type=='index'&&$page=='1'&&$option['ZC_XMLRPC_ENABLE']}
	<link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd" />
	<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml" /> 
{/if}
</head>
