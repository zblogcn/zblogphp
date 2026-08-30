<?php exit(); ?>
<!-- update: 2026-01-04 -->

<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
    <p>
        {$zbp.lang['msg']['search']}:
        <!-- 用户级别 -->
        <label>

            {$zbp.lang['msg']['member_level']}
            <select class="edit" size="1" name="level">
                <option value="">{$zbp.lang['msg']['any']}</option>
                {foreach $zbp.lang['user_level_name'] as $curId => $curName}
                <option value="{$curId}">{$curName}</option>
                {/foreach}
            </select>
        </label>

        <input aria-label="search" name="search" type="text" value="" />
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    </p>
</form>

<!-- 用户列表 -->
<table class="tableFull tableBorder thCenter table_hover table_striped">
    <tr>
        <th data-field="id">{$zbp.lang['msg']['id']}{$button_id_html}</th>
        <th data-field="member_level">{$zbp.lang['msg']['member_level']}{$button_level_html}</th>
        <th data-field="name">{$zbp.lang['msg']['name']}{$button_name_html}</th>
        <th data-field="alias">{$zbp.lang['msg']['alias']}{$button_alias_html}</th>
        <th data-field="all_articles">{$zbp.lang['msg']['all_artiles']}</th>
        <th data-field="all_pages">{$zbp.lang['msg']['all_pages']}</th>
        <th data-field="all_comments">{$zbp.lang['msg']['all_comments']}</th>
        <th data-field="all_uploads">{$zbp.lang['msg']['all_uploads']}</th>
        <th data-field="actions"></th>
    </tr>

    {foreach $members as $member}
    <tr data-id="{$member.ID}">
        <td class="td5" data-field="id">{$member.ID}</td>
        <td class="td10" data-field="member_level">
            {$member.LevelName}
            {if $member.Status > 0}({$zbp.lang['user_status_name'][$member.Status]}){/if}
            {if $member.IsGod}<span title="root">#</span>{/if}
        </td>
        <td data-field="name">
            <a href="{$member.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
            {$member.Name}
        </td>
        <td class="td15" data-field="alias">{$member.Alias}</td>
        <td class="td10" data-field="all_articles">{max(0, $member->Articles)}</td>
        <td class="td10" data-field="all_pages">{max(0, $member->Pages)}</td>
        <td class="td10" data-field="all_comments">{max(0, $member->Comments)}</td>
        <td class="td10" data-field="all_uploads">{max(0, $member->Uploads)}</td>
        <td class="td10 tdCenter" data-field="actions">
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
        return window.confirm("{$zbp.lang['msg']['confirm_operating']}");
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });
</script>
