<div class="cmtsitem">
    <a href="" class="avatar"><img src="{$user.Avatar}" alt="{$comment.Author.StaticName}"></a>
    <div class="cmtscon">
        <div class="cmtshead">
            <div class="cmtsname"><a href="{$comment.Author.HomePage}" rel="nofollow" target="_blank">{$comment.Author.StaticName}</a></div>
            <div class="cmtsdate">{$comment.Time()}</div>
        </div>
        <div class="cmtsbody" data-commentid="{$comment.ID}"><p>{$comment.Content}</p></div>
        {foreach $comment.Comments as $comment}
            {template:commentreply}
        {/foreach}
        <div class="cmtsfoot"><a href="#comment" onclick="zbp.comment.reply('{$comment.ID}')" class="reply">{$lang['tpure']['reply']}</a></div>
    </div>
</div>