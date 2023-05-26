{* Template Name:首页模板(勿选) * Template Type:index *}
<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
{template:header}
</head>
<body class="{$type}{if GetVars('night','COOKIE') } night{/if}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON == '1'} fixed{/if}">
    {if $type == 'index' && $page == '1' && $zbp->Config('tpure')->PostSLIDEON == '1' && $zbp->Config('tpure')->PostSLIDEPLACE == '1'}
        {php}$slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);{/php}
        <div class="slide topslide swiper-container{if $zbp->Config('tpure')->PostSLIDEDISPLAY=='1'} display{/if}">
            <div class="swiper-wrapper">
                {if isset($slidedata)}
                {foreach $slidedata as $value}
                    {if $value['isused']}
                        <a href="{$value['url']}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if} class="swiper-slide" style="background-color:#{$value['color']};"><img src="{$value['img']}" alt="{$value['title']}" /></a>
                    {/if}
                {/foreach}
                {/if}
            </div>
            {if count((array)$slidedata) > 1}
                {if $zbp->Config('tpure')->PostSLIDEPAGEON == '1'}
                <div class="swiper-pagination"></div>
                {/if}
                {if $zbp->Config('tpure')->PostSLIDEBTNON == '1'}
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                {/if}
            {/if}
        </div>
    {elseif $zbp->Config('tpure')->PostBANNERON == '1'}
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
        <div class="indexcon">
            <div class="wrap">
                <div{if $zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '0'} id="sticky"{/if}>
                    <div class="content listcon">
                        {php}$slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);{/php}
                        {if $type == 'index' && $page == '1' && $zbp->Config('tpure')->PostSLIDEON == '1' && $zbp->Config('tpure')->PostSLIDEPLACE == '0' && count((array)$slidedata)>0}
                        <div class="slideblock">
                        <div class="slide swiper-container{if $zbp->Config('tpure')->PostSLIDETITLEON == '1'} hastitle{/if}">
                            <div class="swiper-wrapper">
                                {if isset($slidedata)}
                                {foreach $slidedata as $value}
                                    {if $value['isused']}
                                        <a href="{$value['url']}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if} class="swiper-slide" style="background-color:#{$value['color']};"><img src="{$value['img']}" alt="{$value['title']}" /><span>{$value['title']}</span></a>
                                    {/if}
                                {/foreach}
                                {/if}
                            </div>
                            {if count((array)$slidedata) > 1}
                                {if $zbp->Config('tpure')->PostSLIDEPAGEON == '1'}
                                <div class="swiper-pagination"></div>
                                {/if}
                                {if $zbp->Config('tpure')->PostSLIDEBTNON == '1'}
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                                {/if}
                            {/if}
                        </div>
                        </div>
                        {/if}
                        <div class="block custom{if $zbp->Config('tpure')->PostBIGPOSTIMGON == '1'} large{/if}{if $zbp->Config('tpure')->PostINDEXSTYLE == '1'} forum{elseif $zbp->Config('tpure')->PostINDEXSTYLE == '2'} album{elseif $zbp->Config('tpure')->PostINDEXSTYLE == '3'} sticker{elseif $zbp->Config('tpure')->PostINDEXSTYLE == '4'} hotspot{/if}">

                            {if $zbp->Config('tpure')->PostINDEXSTYLE == '1'}
                            {foreach $articles as $article}
                                {if $article.IsTop}
                                {template:post-forumistop}
                                {else}
                                {template:post-forummulti}
                                {/if}
                            {/foreach}
                            {elseif $zbp->Config('tpure')->PostINDEXSTYLE == '2'}
                            <div class="albumlist">
                            {foreach $articles as $article}
                                {if $article.IsTop}
                                {template:post-albumistop}
                                {else}
                                {template:post-albummulti}
                                {/if}
                            {/foreach}
                            </div>
                            {elseif $zbp->Config('tpure')->PostINDEXSTYLE == '4'}
                            {foreach $articles as $article}
                                {if $article.IsTop}
                                {template:post-hotspotistop}
                                {else}
                                {template:post-hotspotmulti}
                                {/if}
                            {/foreach}
                            {else}
                            {foreach $articles as $article}
                                {if $article.IsTop}
                                {template:post-istop}
                                {else}
                                {template:post-multi}
                                {/if}
                            {/foreach}
                            {/if}

                        </div>
                        {if $pagebar && $pagebar.PageAll > 1}
                        <div class="pagebar">
                            {template:pagebar}
                        </div>
                        {/if}
                        {if $type == 'index' && $page == '1' && $zbp->Config('tpure')->PostFRIENDLINKON == '1' && !tpure_isMobile()}
                            <div class="friendlink">
                                <span>{$lang['tpure']['friendlink']}</span>
                                <ul>{module:link}</ul>
                            </div>
                            {elseif tpure_isMobile() && $zbp->Config('tpure')->PostFRIENDLINKMON == '1'}
                            <div class="friendlink">
                                <span>{$lang['tpure']['friendlink']}</span>
                                <ul>{module:link}</ul>
                            </div>
                        {/if}
                    </div>
                    <div class="sidebar{if $zbp->Config('tpure')->PostFIXMENUON == '1'} fixed{/if}{if tpure_isMobile() && $zbp->Config('tpure')->PostSIDEMOBILEON=='1'} show{/if}">
                        {template:sidebar}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{template:footer}
</body>
</html>