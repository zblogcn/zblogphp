<?php die(); ?>

<form class="search" id="search" method="post" action="#">
  <p>
    {$zbp.lang['msg']['search']}:
    <input name="search" style="width:250px;" type="text" value="" />
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
  </p>
</form>

<table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
  <tr>
    <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
    <th>{$zbp.lang['msg']['order']}{$button_order_html}</th>
    <th>{$zbp.lang['msg']['name']}{$button_name_html}</th>
    <th>{$zbp.lang['msg']['alias']}</th>
    <th>{$zbp.lang['msg']['post_count']}</th>
    <th></th>
  </tr>
  {foreach $categories as $category}
  <tr>
    <td class="td5">{$category.ID}</td>
    <td class="td5">{$category.Order}</td>
    <td class="td25">
      <a href="{$category.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
      {$category.Name}
    </td>
    <td class="td20">{$category.Alias}</td>
    <td class="td10">{$category.Count}</td>
    <td class="td10 tdCenter">
      <a href="../cmd.php?act=CategoryEdt&amp;id={$category.ID}"><i class="icon-pencil-square"></i></a>
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
