{* Template Name:用户文章列表模板 * Template Type:author *} 
<!DOCTYPE html>
<html xml:lang="{$lang['lang_bcp47']}" lang="{$lang['lang_bcp47']}">
<head>
{template:header}
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
            {if $zbp->Config('tpure')->PostSITEMAPON == '1'}
            <div class="sitemap">{$lang['tpure']['sitemap']}<a href="{$host}">{$zbp->Config('tpure')->PostSITEMAPTXT ? $zbp->Config('tpure')->PostSITEMAPTXT : $lang['tpure']['index']}</a>
{if $type=='category'}
{tpure_navcate($category.ID)}
{else}
> {$title}
{/if}
            </div>
            {/if}
            <div{if $zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '0'} id="sticky"{/if}>
                <div class="content listcon">
                    {if $type == 'author'}
                    <div class="block">
                        <div class="auth">
                            <div class="authimg">
                                {if $author.Metas.memberimg}
                                    <img src="{$author.Metas.memberimg}" alt="{$author.StaticName}" />
                                {else}
                                    <img src="{tpure_MemberAvatar($author)}" alt="{$author.StaticName}" />
                                {/if}
                                <em class="sex{if $author.Metas.membersex == '2'} female{else} male{/if}"></em>
                            </div>
                            <div class="authinfo">
                                <h1>{$author.StaticName} {if $type == 'author'}<span class="level">{if $author.Level == '1'}{$lang['tpure']['user_level_name']['1']}{elseif $author.Level == '2'}{$lang['tpure']['user_level_name']['2']}{elseif $author.Level == '3'}{$lang['tpure']['user_level_name']['3']}{elseif $author.Level == '4'}{$lang['tpure']['user_level_name']['4']}{elseif $author.Level == '5'}{$lang['tpure']['user_level_name']['5']}{else}{$lang['tpure']['user_level_name']['6']}{/if}</span>{/if}</h1>
                                <p{if $author.Intro} title="{$author.Intro}"{/if}>{$author.Intro ? $author.Intro : $lang['tpure']['intronull']}</p>
                                <span class="cate"> {$author.Articles} {$lang['tpure']['articles']}</span>
                                <span class="cmt"> {$author.Comments} {$lang['tpure']['comments']}</span>
                            </div>
                        </div>
                    </div>
                    {/if}
                    <div class="block custom{if $zbp->Config('tpure')->PostBIGPOSTIMGON == '1'} large{/if}">
                        {foreach $articles as $article}
                            {if $article.IsTop}
                            {template:post-istop}
                            {else}
                            {template:post-multi}
                            {/if}
                        {/foreach}
                    </div>
                    {if $pagebar && $pagebar.PageAll > 1}
                    <div class="pagebar">
                        {template:pagebar}
                    </div>
                    {/if}
                </div>
                <div class="sidebar{if $zbp->Config('tpure')->PostFIXMENUON == '1'} fixed{/if}{if tpure_isMobile() && $zbp->Config('tpure')->PostSIDEMOBILEON=='1'} show{/if}">
                    {template:sidebar8}
                </div>
            </div>
        </div>
    </div>
</div>
{template:footer}
</body>
</html>