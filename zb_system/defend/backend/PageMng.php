<?php exit(); ?>
<!-- update: 2026-01-04 -->
<form method="post" action="{$zbp.cmdurl}?act=PostBat&type={$post_type}">
    <table class="tableFull tableBorder thCenter table_hover table_striped">
        <tr>
            <th data-field="id">{$zbp.lang['msg']['id']}{$button_id_html}</th>
            <th data-field="author">{$zbp.lang['msg']['author']}{$button_authorid_html}</th>
            <th data-field="title">{$zbp.lang['msg']['title']}</th>
            <th data-field="date">{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
            <th data-field="comment">{$zbp.lang['msg']['comment']}</th>
            <th data-field="status">{$zbp.lang['msg']['status']}</th>
            <th data-field="actions"></th>
            {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
            <th data-field="select"><a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a></th>
            {/if}
        </tr>

        {foreach $pages as $page}
        <tr data-id="{$page.ID}">
            <td class="td5" data-field="id">{$page.ID}</td>
            <td class="td10" data-field="author">{$page.Author.Name}</td>
            <td data-field="title">
                <a href="{$page.Url}" target="_blank"><i class="icon-link-45deg"></i></a> {$page.Title}
            </td>
            <td class="td20" data-field="date">{$page.Time()}</td>
            <td class="td5" data-field="comment">{$page.CommNums}</td>
            <td class="td5" data-field="status">{$page.StatusName}</td>
            <td class="td10 tdCenter" data-field="actions">
                <a href="{$zbp.cmdurl}?act=PageEdt&amp;id={$page.ID}"><i class="icon-pencil-square"></i></a>
                <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=PageDel&amp;id=' . $page->ID)}"><i class="icon-trash"></i></a>
            </td>
            {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
            <td class="td5 tdCenter" data-field="select">
                <input type="checkbox" id="id{$page.ID}" name="id[]" value="{$page.ID}">
            </td>
            {/if}
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

        <!-- 批量删除按钮 -->
        {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
        <input type="submit" class="button pull-right" value="{$zbp.lang['msg']['all_del']}" name="all_del" onclick="return confirmDelete();" />
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
</script>
