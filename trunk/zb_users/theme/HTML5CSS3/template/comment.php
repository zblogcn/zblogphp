<blockquote id="cmt{$comment.ID}">
	<figure><img src="{$comment.Author.Avatar}" alt="{$comment.Author.Name}" /></figure>
    <cite><b><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.Name}</a></b> <time>发表时间 {$comment.Time()}</time></cite>
	<q>{$comment.Content}
{foreach $comment.Comments as $comment}
{template:comment}
{/foreach}
	</q>
</blockquote>