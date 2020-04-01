{*Template Name:404错误页*}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="{$language}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{$lang['tpure']['error404']} - {$name}</title>
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css" type="text/css" media="all"/>
{if $zbp->Config('tpure')->PostCOLORON == '1'}
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/include/skin.css" type="text/css" media="all"/>
{/if}
    <script src="{$host}zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
    <script src="{$host}zb_system/script/zblogphp.js" type="text/javascript"></script>
    <script src="{$host}zb_system/script/c_html_js_add.php" type="text/javascript"></script>
    <script type="text/javascript" src="{$host}zb_users/theme/{$theme}/script/common.js"></script>
    <script type="text/javascript">window.tpure={{if $zbp->Config('tpure')->PostBANNERDISPLAYON=='1'}bannerdisplay:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLON=='1'}viewall:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLSTYLE}viewallstyle:'1',{else}viewallstyle:'0',{/if}{if $zbp->Config('tpure')->PostVIEWALLHEIGHT}viewallheight:'{$zbp->Config('tpure')->PostVIEWALLHEIGHT}',{/if}{if $zbp->Config('tpure')->PostSINGLEKEY=='1'}singlekey:'on',{/if}{if $zbp->Config('tpure')->PostPAGEKEY=='1'}pagekey:'on',{/if}{if $zbp->Config('tpure')->PostREMOVEPON=='1'}removep:'on',{/if}{if $zbp->Config('tpure')->PostBACKTOTOPON=='1'}backtotop:'on'{/if}}</script>
{if $zbp->Config('tpure')->PostBLANKON=='1'}
    <base target="_blank" />
{/if}
{if $zbp->Config('tpure')->PostGREYON=='1'}
<style>html {filter: grayscale(100%);}</style>
{/if}
    {$header}
</head>
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="mask"></div>
        <div class="wrap">
            <div class="errorpage">
				<h3>{$lang['tpure']['error404txt']}</h3>
				<h4>{$lang['tpure']['nopage']}</h4>
				<p>{$lang['tpure']['trysearch']}</p>
				<form name="search" method="post" action="{$host}zb_system/cmd.php?act=search" class="errorsearch">
				<input type="text" name="q" size="11" class="errschtxt"> 
				<input type="submit" value="{$lang['tpure']['search']}" class="errschbtn">
				</form>
				<a class="goback" href="{$host}">{$lang['tpure']['back']} {$name} {$lang['tpure']['index']}</a>
			</div>
        </div>
    </div>
    {template:footer}