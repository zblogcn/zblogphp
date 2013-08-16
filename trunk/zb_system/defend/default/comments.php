<!--评论输出-->
<label style="display:none;" id="AjaxCommentBegin"></label>
{foreach $comments as $comment}
{template:comment}
{/foreach}
<label style="display:none;" id="AjaxCommentEnd"></label>

<!--评论框-->
{template:commentpost}