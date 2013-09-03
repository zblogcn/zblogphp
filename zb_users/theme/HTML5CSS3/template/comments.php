{if $socialcomment}
{$socialcomment}
{else}
<!--评论输出-->

<dl id="comment">
  <dt>留言列表</dt>
  <dd>
<label id="AjaxCommentBegin"></label>
{foreach $comments as $key => $comment}
{template:comment}
{/foreach}

<!--评论翻页条输出-->
<nav>
{template:pagebar}
</nav>
<label id="AjaxCommentEnd"></label>
  </dd>
</dl>


<!--评论框-->
{template:commentpost}

{/if}