{if $socialcomment}
{$socialcomment}
{else}
<!--评论输出-->
{foreach $comments as $key => $comment}
{template:comment}
{/foreach}

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
{if $pagebar}
{template:pagebar}
{/if}
</div>

<!--评论框-->
{template:commentpost}

{/if}