{* Template Name:社区列表页普通文章 *}
<div class="item">
    <h2><a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}>{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '0'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}{$article.Title}{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '1'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}</a></h2>
    <div class="forumdate">{tpure_TimeAgo($article->Time())}</div>
</div>