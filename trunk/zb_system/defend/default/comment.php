<ul class="msg" id="cmt{$comment->ID}">
	<li class="msgname"><img class="avatar" src="{$comment->Author->Avatar}" alt="" width="32"/>&nbsp;<span class="commentname"><a href="{$comment->HomePage}" rel="nofollow" target="_blank">{$comment->Name}</a></span><br/><small>&nbsp;发布于&nbsp;{$comment->Time()}&nbsp;&nbsp;<span class="revertcomment"><a href="#comment" onclick="RevertComment('{$comment->ID}')">回复该评论</a></span></small></li>
	<li class="msgarticle">{$comment->Content}
{foreach $comment->Comments as $comment}
{template:comment}
{/foreach}
	</li>
</ul>