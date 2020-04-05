<ol class="commentlist" id="cmt{$comment.ID}">
	<li class="comment odd alt thread-odd thread-alt depth-{$comment.ID}" id="comment-{$comment.ID}">
		<div class="c-floor"><a href="#cmt{$comment.ID}">{if $comment.FloorID}#{$comment.FloorID}{/if}</span>
</a></div>
		<div class="c-avatar">
			<img class="avatar" src="{$comment.Author.Avatar}" width="36" height="36">
		</div>
		<div class="c-main" id="div-comment-{$comment.ID}">
			<div class="c-meta"><span class="c-author">{$comment.Author.Name}</span>{$comment.Time()} <a class='comment-reply-link' href='#respond' onclick="zbp.comment.reply('{$comment.ID}')">回复</a></div>
			<p>{$comment.Content}
{foreach $comment.Comments as $comment}
	{template:comment}
{/foreach}
			</p>
		</div>
	</li>
</ol>