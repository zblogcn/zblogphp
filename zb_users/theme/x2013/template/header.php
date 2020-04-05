<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$language}" lang="{$language}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="Content-Language" content="{$language}" />
	<title>{if $type=='article' || $type=='page'}{$title}-{$name}{else}{$name}-{$title}{/if}</title>
<meta name="generator" content="{$zblogphp}" />
	<link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/style/style.css" type="text/css" media="screen" />
	<script src="{$host}zb_system/script/jquery-1.8.3.min.js" type="text/javascript"></script>
	<script src="{$host}zb_system/script/zblogphp.js" type="text/javascript"></script>
	<script src="{$host}zb_system/script/c_html_js_add.php" type="text/javascript"></script>
	<script src="{$host}zb_users/theme/{$theme}/script/custom.js" type="text/javascript"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
<!--[if lt IE 9]><script src="{$host}zb_users/theme/{$theme}/script/html5.js"></script><![endif]-->
{$header}
{if $type=='index' && $page=='1'}
	<link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd" />
	<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml" />
{/if}



