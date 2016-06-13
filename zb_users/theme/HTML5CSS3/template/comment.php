<blockquote id="cmt{$comment.ID}">
	<figure><img src="{$comment.Author.Avatar}" alt="{$comment.Author.StaticName}" /></figure>
    <cite><b><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.StaticName}</a></b> <time>发表时间 {$comment.Time()}</time></cite>
	<q>{$comment.Content}
&nbsp;&nbsp;<i class="revertcomment"><a href="#reply" onclick="zbp.comment.reply('{$comment.ID}')">回复</a></i>
{foreach $comment.Comments as $comment}
{template:comment}
{/foreach}
	</q>
</blockquote>