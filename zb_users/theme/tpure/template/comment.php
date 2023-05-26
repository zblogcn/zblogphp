<div class="cmtsitem">
    {if $comment.Author.HomePage}<a href="{$comment.Author.HomePage}" rel="nofollow" class="avatar"><img src="{tpure_MemberAvatar($comment.Author)}" alt="{$comment.Author.StaticName}" /></a>{else}<span class="avatar"><img src="{tpure_MemberAvatar($comment.Author)}" alt="{$comment.Author.StaticName}" /></span>{/if}
    <div class="cmtscon">
        <div class="cmtshead">
            <div class="cmtsname">{if $comment.Author.HomePage}<a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.StaticName}</a>{else}{$comment.Author.StaticName}{/if} {if $zbp->Config('tpure')->PostCMTIPON == '1'}<em>IP:{tpure_IP($comment.IP)}</em>{/if}</div>
            <div class="cmtsdate">{tpure_TimeAgo($comment->Time())}</div>
        </div>
        <div class="cmtsbody" data-commentid="{$comment.ID}"><p>{$comment.Content}</p></div>
        {foreach $comment.Comments as $comment}
            {template:commentreply}
        {/foreach}
        <div class="cmtsfoot"><a href="#comment" onclick="zbp.comment.reply({if $comment.RootID}{$comment.RootID}{else}{$comment.ID}{/if})" class="reply">{$lang['tpure']['reply']}</a></div>
    </div>
</div>