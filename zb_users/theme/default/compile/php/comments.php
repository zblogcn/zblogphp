{* Template Name:所有评论模板 *}
{if $socialcomment}
{$socialcomment}
{else}

{if $article.CommNums>0}
<ul class="msg msghead">
	<li class="tbname">{$lang['default']['comment_list']}:</li>
</ul>
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

<!--评论框-->
{template:commentpost}

{/if}