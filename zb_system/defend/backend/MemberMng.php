<?php exit(); ?>
<!-- update: 2026-01-04 -->

<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
    <p>
        {$zbp.lang['msg']['search']}:
        {$zbp.lang['msg']['member_level']}
        <select class="edit" size="1" name="level" style="width:140px;">
            <option value="">{$zbp.lang['msg']['any']}</option>
            {foreach $zbp.lang['user_level_name'] as $curId => $curName}
            <option value="{$curId}">{$curName}</option>
            {/foreach}
        </select>

        <input name="search" style="width:250px;" type="text" value="" />
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    </p>
</form>

<!-- 用户列表 -->
<table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
    <tr>
        <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
        <th>{$zbp.lang['msg']['member_level']}{$button_level_html}</th>
        <th>{$zbp.lang['msg']['name']}{$button_name_html}</th>
        <th>{$zbp.lang['msg']['alias']}{$button_alias_html}</th>
        <th>{$zbp.lang['msg']['all_artiles']}</th>
        <th>{$zbp.lang['msg']['all_pages']}</th>
        <th>{$zbp.lang['msg']['all_comments']}</th>
        <th>{$zbp.lang['msg']['all_uploads']}</th>
        <th></th>
    </tr>

    {foreach $members as $member}
    <tr>
        <td class="td5">{$member.ID}</td>
        <td class="td10">
            {$member.LevelName}
            {if $member.Status > 0}({$zbp.lang['user_status_name'][$member.Status]}){/if}
            {if $member.IsGod}<span title="root">#</span>{/if}
        </td>
        <td>
            <a href="{$member.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
            {$member.Name}
        </td>
        <td class="td15">{$member.Alias}</td>
        <td class="td10">{php}echo max(0, $member->Articles);{/php}</td>
        <td class="td10">{php}echo max(0, $member->Pages);{/php}</td>
        <td class="td10">{php}echo max(0, $member->Comments);{/php}</td>
        <td class="td10">{php}echo max(0, $member->Uploads);{/php}</td>
        <td class="td10 tdCenter">
            <a href="{$zbp.cmdurl}?act=MemberEdt&amp;id={$member.ID}">
                <i class="icon-pencil-square"></i>
            </a>
            {if $zbp.CheckRights('MemberDel') && !$member.IsGod}
            <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=MemberDel&amp;id=' . $member->ID)}">
                <i class="icon-trash"></i>
            </a>
            {/if}
        </td>
    </tr>
    {/foreach}

</table>

<!-- 分页 -->
<p class="pagebar">
    {foreach $p->buttons as $k => $v}
    {if $k == $p->PageNow}
    <span class="now-page">{$k}</span>
    {else}
    <a href="{$v}">{$k}</a>
    {/if}
    {/foreach}
</p>

<script>
    function confirmDelete() {
        const message = "{$zbp.lang['msg']['confirm_operating']}";
        const confirmed = window.confirm(message);
        return confirmed;
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });
</script>
