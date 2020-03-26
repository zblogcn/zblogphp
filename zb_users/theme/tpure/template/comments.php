{if $socialcomment}
{$socialcomment}
{else}
<div {if $article.CommNums == '0'}data-content="{$lang['tpure']['nocmt']}"{/if} class="cmts block{if $article.CommNums=='0'} nocmt{/if}">
    {if $article.CommNums>0}<div class="posttitle"><h4>{$lang['tpure']['cmtlist']}</h4></div>{/if}
    <label id="AjaxCommentBegin"></label>
	{foreach $comments as $key => $comment}
	{template:comment}
	{/foreach}
    {if $article.CommNums>0}
    <div class="cmtpagebar">{template:pagebar}</div>
    {/if}
	<label id="AjaxCommentEnd"></label>
</div>
{template:commentpost}
{/if}