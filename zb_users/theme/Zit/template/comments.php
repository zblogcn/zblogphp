{if !$article.IsLock}
<div id="cmts">
<h3 class="zit kico-lao">{$cfg.CommentTitle}</h3>
{if $socialcomment}
  {$socialcomment}
{else}
{template:commentpost}
  <label id="AjaxCommentBegin"></label>
  {foreach $comments as $key => $comment}
  {template:comment}
  {/foreach}
  {template:pagebar}
  <label id="AjaxCommentEnd"></label>
{/if}
</div>
{/if}
