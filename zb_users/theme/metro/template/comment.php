<div class="msg" id="cmt{$comment.ID}">
            <div class="msgtxt">
              <div class="msgtxtbogy">
              <div class="msgname"><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$key+1}<span class="dot">.</span>{$comment.Author.Name}</a>&nbsp;&nbsp;<span>{$comment.Time()}&nbsp;<a href="#cmt{$comment.ID}" onclick="ReComment('cmt{$comment.ID}','msg','msgarticle','comment','{$comment.ID}')">回复该评论</a></span></div>
              <div class="msgarticle">{$comment.Content}
{foreach $comment.Comments as $comment}
	{template:comment}
{/foreach}	
              </div>
            </div>
          </div>
          <div class="msgimg"><a name="cmt{$comment.ID}"><img class="avatar" src="{$comment.Author.Avatar}" alt=""/></a></div>
          <div class="clear"></div>
</div>