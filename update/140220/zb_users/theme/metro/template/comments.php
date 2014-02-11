{if $socialcomment}
{$socialcomment}
{else}

<div class="commentlist" style="overflow:hidden;">
{if $article.CommNums>0}
<h4>评论列表:</h4>
{/if}

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

</div>


<!--评论框-->
{template:commentpost}

{/if}