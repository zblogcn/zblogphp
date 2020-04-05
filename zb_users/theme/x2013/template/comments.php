{if $socialcomment}
{$socialcomment}
{else}

<div id="postcomments">
	{if $article.CommNums>0}
	<h3 class="base-tit" id="comments">网友评论<b>{$article.CommNums}</b>条</h3>
	{/if}
	<label id="AjaxCommentBegin"></label>
	<!--评论输出-->
	{foreach $comments as $key => $comment}
	{template:comment}
	{/foreach}	
	<div class="pagenav">{template:pagebar}</div>
	<label id="AjaxCommentEnd"></label>
</div>  

<!--评论框-->
{if !$article.IsLock}
{template:commentpost}
{/if}

{/if}