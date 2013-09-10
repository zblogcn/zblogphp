{if $socialcomment}
{$socialcomment}
{else}

<label id="AjaxCommentBegin"></label>
<!--评论输出-->
{foreach $comments as $key => $comment}
{template:comment}
{/foreach}

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
{template:pagebar}
</div>
<label id="AjaxCommentEnd"></label>

<!--评论框-->
{template:commentpost}

{/if}