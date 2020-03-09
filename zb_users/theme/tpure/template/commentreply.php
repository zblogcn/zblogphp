<div class="cmtsreply">
    <div class="cmtsreplyname"><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.StaticName}</a> {$lang['tpure']['replytxt']}</div>
    <div class="cmtsreplycon">{$comment.Content}</div>
    <div class="cmtsreplydate">{$comment.Time()}</div>
</div>
{foreach $comment.Comments as $comment}
    {template:commentreply}
{/foreach}