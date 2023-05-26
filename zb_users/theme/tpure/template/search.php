{* Template Name:搜索页模板 * Template Type:search *}
<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
{template:header}
</head>
<body class="{$type}{if GetVars('night','COOKIE') } night{/if}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
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
            {if $zbp->Config('tpure')->PostSITEMAPON=='1'}
            <div class="sitemap">{$lang['tpure']['sitemap']}<a href="{$host}">{$zbp->Config('tpure')->PostSITEMAPTXT ? $zbp->Config('tpure')->PostSITEMAPTXT : $lang['tpure']['index']}</a> > {$title}
            </div>
            {/if}
            <div{if $zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '0'} id="sticky"{/if}>
                <div class="content listcon">
                    <div class="block custom{if $zbp->Config('tpure')->PostBIGPOSTIMGON=='1'} large{/if}{if tpure_JudgeListTemplate($zbp->Config('tpure')->PostSEARCHSTYLE)} {tpure_JudgeListTemplate($zbp->Config('tpure')->PostSEARCHSTYLE)}{/if}">

                        {if count((array)$articles)}
                            {if $zbp->Config('tpure')->PostSEARCHSTYLE == '1'}
                                {foreach $articles as $article}
                                {template:post-forummulti}
                                {/foreach}
                            {elseif $zbp->Config('tpure')->PostSEARCHSTYLE == '2'}
                                <div class="albumlist">
                                {foreach $articles as $article}
                                {template:post-albummulti}
                                {/foreach}
                                </div>
                            {elseif $zbp->Config('tpure')->PostSEARCHSTYLE == '4'}
                                {foreach $articles as $article}
                                {template:post-hotspotmulti}
                                {/foreach}
                            {else}
                                {foreach $articles as $article}
                                {template:post-multi}
                                {/foreach}
                            {/if}
                        {else}
                            <div class="searchnull">{$lang['tpure']['searchnulltip']} <a href="https://www.baidu.com/s?wd={$_GET['q']}" target="_blank" rel="nofollow">{$_GET['q']}</a> {$lang['tpure']['searchnullcon']}</div>
                        {/if}
                    </div>
                    {if $pagebar && $pagebar.PageAll > 1}
                    <div class="pagebar">
                        {template:pagebar}
                    </div>
                    {/if}
                </div>
                <div class="sidebar{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}{if tpure_isMobile() && $zbp->Config('tpure')->PostSIDEMOBILEON=='1'} show{/if}">
                    {template:sidebar5}
                </div>
            </div>
        </div>
    </div>
</div>
{template:footer}
</body>
</html>