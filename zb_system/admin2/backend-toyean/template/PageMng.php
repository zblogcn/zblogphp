<?php die(); ?>

<form method="post" action="{$zbp.host}zb_system/cmd.php?act=PostBat&type={$post_type}">
<!--  -->
<table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
  <tr>
    <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
    <th>{$zbp.lang['msg']['author']}{$button_authorid_html}</th>
    <th>{$zbp.lang['msg']['title']}</th>
    <th>{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
    <th>{$zbp.lang['msg']['comment']}</th>
    <th>{$zbp.lang['msg']['status']}</th>
    <th></th>
    {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
    <th><a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a></th>
    {/if}
  </tr>

  {foreach $pages as $page}
  <tr>
    <td class="td5">{$page.ID}</td>
    <td class="td10">{$page.Author.Name}</td>
    <td>
      <a href="{$page.Url}" target="_blank"><i class="icon-link-45deg"></i></a> {$page.Title}
    </td>
    <td class="td20">{$page.Time()}</td>
    <td class="td5">{$page.CommNums}</td>
    <td class="td5">{$page.StatusName}</td>
    <td class="td10 tdCenter">
      <a href="../cmd.php?act=PageEdt&amp;id={$page.ID}"><i class="icon-pencil-square"></i></a>
      <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=PageDel&amp;id=' . $page->ID)}"><i class="icon-trash"></i></a>
    </td>
    {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
    <td class="td5 tdCenter">
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
    const message = "{$zbp.lang['msg']['confirm_operating']}";
    const confirmed = window.confirm(message);
    return confirmed;
  }

  $("a.order_button").parent().bind("mouseenter mouseleave", function() {
    $(this).find("a.order_button").toggleClass("element-visibility-hidden");
  });
</script>
