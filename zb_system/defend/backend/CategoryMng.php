<?php exit(); ?>
<!-- update: 2026-01-04 -->
<form class="search" id="search" method="post" action="#">
    <p>
        {$zbp.lang['msg']['search']}:
        <input aria-label="search" name="search" type="text" value="" />
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    </p>
</form>

<table class="tableFull tableBorder thCenter table_hover table_striped">
    <tr>
        <th data-field="id">{$zbp.lang['msg']['id']}{$button_id_html}</th>
        <th data-field="order">{$zbp.lang['msg']['order']}{$button_order_html}</th>
        <th data-field="name">{$zbp.lang['msg']['name']}{$button_name_html}</th>
        <th data-field="alias">{$zbp.lang['msg']['alias']}</th>
        <th data-field="post_count">{$zbp.lang['msg']['post_count']}</th>
        <th data-field="actions"></th>
    </tr>
    {foreach $categories as $category}
    <tr data-id="{$category.ID}">
        <td class="td5" data-field="id">{$category.ID}</td>
        <td class="td5" data-field="order">{$category.Order}</td>
        <td class="td25" data-field="name">
            <a href="{$category.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
            {$category.Name}
        </td>
        <td class="td20" data-field="alias">{$category.Alias}</td>
        <td class="td10" data-field="post_count">{$category.Count}</td>
        <td class="td10 tdCenter" data-field="actions">
            <a href="{$zbp.cmdurl}?act=CategoryEdt&amp;id={$category.ID}"><i class="icon-pencil-square"></i></a>
            {if count($category.SubCategories) == 0}
            <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=CategoryDel&amp;id=' . $category->ID)}"><i class="icon-trash"></i></a>
            {/if}
        </td>
    </tr>
    {/foreach}
</table>

{if !$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']}
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
{/if}

<script>
    function confirmDelete() {
        return window.confirm("{$zbp.lang['msg']['confirm_operating']}");
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });
</script>
