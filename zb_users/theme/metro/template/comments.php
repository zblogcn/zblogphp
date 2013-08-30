
       <div class="commentlist" style="overflow:hidden;">
       <h4>评论列表:</h4>

{if $socialcomment}
{$socialcomment}
{else}
<!--评论输出-->
{foreach $comments as $key => $comment}
{template:comment}
{/foreach}

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
{template:pagebar}
</div>

<!--评论框-->
{template:commentpost}

{/if}

</div>