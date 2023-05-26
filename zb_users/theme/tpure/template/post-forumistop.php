{* Template Name:社区列表页置顶文章 *}
{if $page != '1' && $zbp->Config('tpure')->PostISTOPINDEXON == '1'}
{else}
<div class="item">
    <h2 class="istop"><a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}>{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '0'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}{$article.Title}<em>{$lang['tpure']['istop']}</em>{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '1'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}</a></h2>
    <div class="forumdate">{tpure_TimeAgo($article->Time())}</div>
</div>
{/if}