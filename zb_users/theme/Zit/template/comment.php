<blockquote id="cmt{$comment.ID}" class="cmt pane">
  <img src="{$comment.Author.Avatar}" alt="{$comment.Author.Name}" class="avatar">
  <cite><b><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank" title="{$comment.Author.Name}">{$comment.Author.Name}</a></b><small>{$comment.Time()}{if $comment.Level<3} · <a href="#postcmt" onclick="zbp.comment.reply({$comment.ID});" class="kico-reply">{$lang['Zit']['reply']}</a>{/if}</small></cite>
  <q>{$comment.Content}</q>
  {foreach $comment.Comments as $comment}
  {template:comment}
  {/foreach}
</blockquote>