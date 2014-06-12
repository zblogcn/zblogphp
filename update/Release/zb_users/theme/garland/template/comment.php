<div class="comment even" id="cmt{$comment.ID}">
	<div class="clear-block">
		<span class="submitted">{$comment.Time()} — <a href="{$comment.Author.HomePage}" title="{$comment.Author.HomePage}"  rel="nofollow" >{$comment.Author.StaticName}</a>  <a href="#cmt{$comment.ID}" onclick="RevertComment('{$comment.ID}')" class="comment_reply" title="回复{$comment.Author.StaticName}">回复</a></span>
		<h3><a href="#cmt{$comment.ID}" class="active">{$key+1}</a> .</h3>
		<div class="content">
		  {$comment.Content}
{foreach $comment.Comments as $comment}
{template:comment}
{/foreach}
		</div>
	</div>
</div>
