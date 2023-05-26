{* Template Name:标签云无侧栏模板 * Template Type:page *}
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
            <div class="sitemap">{$lang['tpure']['sitemap']}<a href="{$host}">{$zbp->Config('tpure')->PostSITEMAPTXT ? $zbp->Config('tpure')->PostSITEMAPTXT : $lang['tpure']['index']}</a> &gt; 
                {if $type=='article'}{if is_object($article.Category) && $article.Category.ParentID}<a href="{$article.Category.Parent.Url}">{$article.Category.Parent.Name}</a> &gt;{/if} <a href="{$article.Category.Url}">{$article.Category.Name}</a> &gt; {/if}{$article.Title}
            </div>
            {/if}
            <div{if $zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '0'} id="sticky"{/if}>
                <div class="content wide">
                    <div class="block">
                        <div class="post">
                            <h1>{$article.Title}</h1>
                            <div class="single{if (($type=='article' && $article.Metas.viewall != '1' && $zbp->Config('tpure')->PostVIEWALLSINGLEON)||($type=='page' && $article.Metas.viewall != '1' && $zbp->Config('tpure')->PostVIEWALLPAGEON))} viewall{/if}">
                                {tpure_GetTagCloudList()}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{template:footer}
</body>
</html>