<?php exit(); ?>

<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
    <p>
        {$zbp.lang['msg']['search']}:
        <input aria-label="search" name="search" type="text" value="" />
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    </p>
</form>

<!-- 评论列表 -->
<form method="post" action="{$zbp.cmdurl}?act=CommentBat">
    <input type="hidden" name="csrfToken" value="{$zbp.GetCSRFToken()}">

    <table class="tableFull tableBorder thCenter table_hover table_striped">
        <tr>
            <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
            <th>{$zbp.lang['msg']['parend_id']}</th>
            <th>{$zbp.lang['msg']['name']}{$button_authorid_html}</th>
            <th>{$zbp.lang['msg']['content']}</th>
            <th>{$zbp.lang['msg']['article']}{$button_logid_html}</th>
            <th>{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
            <th></th>
            <th><a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a></th>
        </tr>

        {foreach $comments as $comment}
        <tr>
            <td class="td5">
                <a href="?act=CommentMng&id={$comment.ID}" title="{$zbp.lang['msg']['jump_comment']}{$comment.ID}">{$comment.ID}</a>
            </td>
            <td class="td5">
                {if $comment.ParentID}
                <a href="?act=CommentMng&id={$comment.ParentID}" title="{$zbp.lang['msg']['jump_comment']}{$comment.ParentID}">{$comment.ParentID}</a>
                {/if}
            </td>
            <td class="td10">
                <span class="cmt-note" title="{$zbp.lang['msg']['email']}:{$comment.Email}">
                    <a href="mailto:{$comment.Email}">{$comment.Author.StaticName}</a>
                </span>
            </td>
            <td>
                <div class="overflow-hidden">
                    {if $comment.Post}
                    <a href="{$comment.Post.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
                    {else}
                    <a href="javascript:;"><i class="icon-trash"></i></a>
                    {/if}
                    {$comment.Content}
                </div>
            </td>
            <td class="td5">{$comment.LogID}</td>
            <td class="td15">{$comment.Time()}</td>
            <td class="td10 tdCenter">
                <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=CommentDel&amp;id=' . $comment->ID)}">
                    <i class="icon-trash" title="{$zbp.lang['msg']['del']}"></i>
                </a>
                {if !$ischecking}
                <a href="{BuildSafeCmdURL('act=CommentChk&amp;id=' . $comment->ID . '&amp;ischecking=' . (int)!$ischecking)}">
                    <i class="icon-shield-fill-x" title="{$zbp.lang['msg']['audit']}"></i>
                </a>
                {else}
                <a href="{BuildSafeCmdURL('act=CommentChk&amp;id=' . $comment->ID . '&amp;ischecking=' . (int)!$ischecking)}">
                    <i class="icon-shield-fill-check" title="{$zbp.lang['msg']['pass']}"></i>
                </a>
                {/if}
            </td>
            <td class="td5 tdCenter">
                <input type="checkbox" id="id{$comment.ID}" name="id[]" value="{$comment.ID}" />
            </td>
        </tr>
        {/foreach}

    </table>

    <!-- 分页和批量操作按钮 -->
    <p class="pagebar">
        {foreach $p->buttons as $k => $v}
        {if $k == $p->PageNow}
        <span class="now-page">{$k}</span>
        {else}
        <a href="{$v}">{$k}</a>
        {/if}
        {/foreach}

        {if $ischecking}
        <input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}" class="button pull-right" />
        <input type="submit" name="all_pass" value="{$zbp.lang['msg']['all_pass']}" class="button pull-right mr-10" />
        {else}
        <input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}" class="button pull-right" />
        <input type="submit" name="all_audit" value="{$zbp.lang['msg']['all_audit']}" class="button pull-right mr-10" />
        {/if}
    </p>

</form>

<script>
    function confirmDelete() {
        return window.confirm("{$zbp.lang['msg']['confirm_operating']}");
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });

    $(".cmt-note").tooltip();
</script>
