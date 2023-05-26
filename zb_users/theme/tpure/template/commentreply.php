<div class="cmtsreply">
    <div class="cmtsreplyname">{if $comment.Author.HomePage}<a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.StaticName}</a>{else}{$comment.Author.StaticName}{/if} {if $zbp->Config('tpure')->PostCMTIPON == '1'}<em>IP:{tpure_IP($comment.IP)}</em>{/if} {$lang['tpure']['replytxt']}</div>
    <div class="cmtsreplycon">{$comment.Content}</div>
    <div class="cmtsreplydate">{tpure_TimeAgo($comment->Time())}</div>
</div>
{foreach $comment.Comments as $comment}
    {template:commentreply}
{/foreach}