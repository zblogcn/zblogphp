{* Template Name:404错误页 * Template Type:404 *}
<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
    <meta charset="utf-8">
    {if isset($lang['tpure']['theme'])}<meta name="theme" content="{$lang['tpure']['theme']}">
{/if}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <title>{$lang['tpure']['error404']} - {$name}</title>
    {if $zbp->Config('tpure')->PostFAVICONON}<link rel="shortcut icon" href="{$zbp->Config('tpure')->PostFAVICON}" type="image/x-icon" />
{/if}
    <meta name="generator" content="{$zblogphp}">
{if $zbp->Config('tpure')->PostSHAREARTICLEON == '1' || $zbp->Config('tpure')->PostSHAREPAGEON == '1'}
    <link rel="stylesheet" href="{$host}zb_users/theme/tpure/plugin/share/share.css" />
    <script src="{$host}zb_users/theme/tpure/plugin/share/share.js"></script>
{/if}
{if $zbp->Config('tpure')->PostSLIDEON == '1'}
    <script src="{$host}zb_users/theme/{$theme}/plugin/swiper/swiper.min.js"></script>
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/plugin/swiper/swiper.min.css" type="text/css" media="all"/>
{/if}
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/style/{$style}.css" type="text/css" media="all" />
{if $zbp->Config('tpure')->PostCOLORON == '1'}
    <link rel="stylesheet" rev="stylesheet" href="{$host}zb_users/theme/{$theme}/include/skin.css" type="text/css" media="all" />
{/if}
    <script src="{$host}zb_system/script/jquery-2.2.4.min.js"></script>
    <script src="{$host}zb_system/script/zblogphp.js"></script>
    <script src="{$host}zb_system/script/c_html_js_add.php"></script>
    <script src="{$host}zb_users/theme/{$theme}/script/common.js"></script>
    <script>window.tpure={{if $zbp->Config('tpure')->PostSLIDEON=='1'}slideon:'on',{/if}{if $zbp->Config('tpure')->PostSLIDEDISPLAY=='1'}slidedisplay:'on',{/if}{if $zbp->Config('tpure')->PostSLIDETIME}slidetime:{$zbp->Config('tpure')->PostSLIDETIME},{/if}{if $zbp->Config('tpure')->PostSLIDEPAGETYPE=='1'}slidepagetype:'on',{/if}{if $zbp->Config('tpure')->PostSLIDEEFFECTON=='1'}slideeffect:'on',{/if}{if $zbp->Config('tpure')->PostBANNERDISPLAYON=='1'}bannerdisplay:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLON=='1'}viewall:'on',{/if}{if $zbp->Config('tpure')->PostVIEWALLSTYLE}viewallstyle:'1',{else}viewallstyle:'0',{/if}{if $zbp->Config('tpure')->PostVIEWALLHEIGHT}viewallheight:'{$zbp->Config('tpure')->PostVIEWALLHEIGHT}',{/if}{if $zbp->Config('tpure')->PostAJAXON=='1'}ajaxpager:'on',{/if}{if $zbp->Config('tpure')->PostLOADPAGENUM}loadpagenum:'{$zbp->Config('tpure')->PostLOADPAGENUM}',{/if}{if $zbp->Config('tpure')->PostLAZYLOADON=='1'}lazyload:'on',{/if}{if $zbp->Config('tpure')->PostLAZYLINEON=='1'}lazyline:'on',{/if}{if $zbp->Config('tpure')->PostLAZYNUMON=='1'}lazynum:'on',{/if}{if $zbp->Config('tpure')->PostSETNIGHTON}night:'on',{/if}{if $zbp->Config('tpure')->PostSETNIGHTAUTOON}setnightauto:'on',{/if}{if $zbp->Config('tpure')->PostSETNIGHTSTART}setnightstart:'{$zbp->Config('tpure')->PostSETNIGHTSTART}',{/if}{if $zbp->Config('tpure')->PostSETNIGHTOVER}setnightover:'{$zbp->Config('tpure')->PostSETNIGHTOVER}',{/if}{if $zbp->Config('tpure')->PostSELECTON=='1'}selectstart:'on',{/if}{if $zbp->Config('tpure')->PostSINGLEKEY=='1'}singlekey:'on',{/if}{if $zbp->Config('tpure')->PostPAGEKEY=='1'}pagekey:'on',{/if}{if $zbp->Config('tpure')->PostTFONTSIZEON=='1'}tfontsize:'on',{/if}{if $zbp->Config('tpure')->PostFIXSIDEBARON=='1'}fixsidebar:'on',{/if}{if $zbp->Config('tpure')->PostFIXSIDEBARSTYLE}fixsidebarstyle:'1',{else}fixsidebarstyle:'0',{/if}{if $zbp->Config('tpure')->PostREMOVEPON=='1'}removep:'on',{/if}{if $zbp->Config('tpure')->PostBACKTOTOPON=='1'}backtotop:'on'{/if},version:{$zbp->themeapp->version}}</script>
{if $zbp->Config('tpure')->PostBLANKSTYLE == '1'}
    <base target="_blank" />
{/if}
{if $zbp->Config('tpure')->PostGREYON == '1'}
    {if ($zbp->Config('tpure')->PostGREYDAY && tpure_IsToday($zbp->Config('tpure')->PostGREYDAY) == true)}
        {if $zbp->Config('tpure')->PostGREYSTATE == '0'}
            {if $type == 'index'}<style>html { filter:grayscale(100%); } * { filter:gray; }</style>{/if}
        {else}
            <style>html { filter:grayscale(100%); } * { filter:gray; }</style>
        {/if}
    {elseif !$zbp->Config('tpure')->PostGREYDAY}
        {if $zbp->Config('tpure')->PostGREYSTATE == '0'}
            {if $type == 'index'}<style>html { filter:grayscale(100%); } * { filter:gray; }</style>{/if}
        {else}
            <style>html { filter:grayscale(100%); } * { filter:gray; }</style>
        {/if}
    {/if}
{/if}
{if $type == 'article'}
    <link rel="canonical" href="{$article.Url}" />
{/if}
    {$header}
{if $type == 'index' && $page == '1'}
    <link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="{$host}zb_system/xml-rpc/?rsd" />
    <link rel="wlwmanifest" type="application/wlwmanifest+xml" href="{$host}zb_system/xml-rpc/wlwmanifest.xml" />
{/if}
</head>
<body class="{$type}{if GetVars('night','COOKIE') } night{/if}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON == '1'} fixed{/if}">
        {if $zbp->Config('tpure')->PostBANNERON == '1' && $zbp->Config('tpure')->PostBANNERALLON == '1'}
            <div class="banner" data-type="display" data-speed="2" style="{if !tpure_isMobile()}height:{$zbp->Config('tpure')->PostBANNERPCHEIGHT}px;{else}height:{$zbp->Config('tpure')->PostBANNERMHEIGHT}px;{/if} background-image:url({$zbp->Config('tpure')->PostBANNER});">
                <div class="wrap">
                    <div class="hellotip">
                    {$zbp->Config('tpure')->PostBANNERFONT}
                    {if $zbp->Config('tpure')->PostBANNERSEARCHON}
                        <div class="hellosch{if !$zbp->Config('tpure')->PostBANNERFONT} alone{/if}">
                            <form name="search" method="post" action="{$host}zb_system/cmd.php?act=search">
                                <input type="text" name="q" placeholder="{$zbp->Config('tpure')->PostSCHTXT}" class="helloschinput" />
                                <button type="submit" class="helloschbtn"></button>
                            </form>
                            <div class="schwords">
                                <div class="schwordsinfo">
                                    {if $zbp->Config('tpure')->PostBANNERSEARCHLABEL}
                                        <h5>{$zbp->Config('tpure')->PostBANNERSEARCHLABEL}</h5>
                                    {/if}
                                    {$schwords = explode('|',$zbp->Config('tpure')->PostBANNERSEARCHWORDS)}
                                    {if is_array($schwords)}
                                        {foreach $schwords as $schval}
                                            <a href="{$host}search.php?q={$schval}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}>{$schval}</a>
                                        {/foreach}
                                    {/if}
                                </div>
                                <div class="ajaxresult"></div>
                            </div>
                        </div>
                    {/if}
                    </div>
                </div>
            </div>
        {/if}
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
				<a class="goback" href="{$host}">{$lang['tpure']['back']}{$lang['tpure']['index']}</a>
			</div>
        </div>
    </div>
</div>
{template:footer}
</body>
</html>