<?php die(); ?>

<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
  <p>
    {$zbp.lang['msg']['search']}:
    <input name="search" style="width:450px;" type="text" value="" />
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
  </p>
</form>

<!-- 标签列表 -->
<form method="post" action="{$zbp.host}zb_system/cmd.php?act=TagBat">
  <input type="hidden" name="csrfToken" value="{$zbp.GetCSRFToken()}">

  <table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
    <tr>
      <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
      <th>{$zbp.lang['msg']['name']}{$button_name_html}</th>
      <th>{$zbp.lang['msg']['alias']}</th>
      <th>{$zbp.lang['msg']['intro']}</th>
      <th>{$zbp.lang['msg']['order']}{$button_order_html}</th>
      <th></th>
      <th><a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a></th>
    </tr>

    {foreach $tags as $tag}
    <tr>
      <td class="td5">{$tag.ID}</td>
      <td class="td10">{$tag.Name}</td>
      <td class="td10">{$tag.Alias}</td>
      <td>{$tag.Intro}</td>
      <td class="td5">{$tag.Order}</td>
      <td class="td10 tdCenter">
        <a href="../cmd.php?act=TagEdt&amp;id={$tag.ID}"><i class="icon-pencil-square"></i></a>
        <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=TagDel&amp;id=' . $tag->ID)}"><i class="icon-trash"></i></a>
      </td>
      <td class="td5 tdCenter">
        <input type="checkbox" id="id{$tag.ID}" name="id[]" value="{$tag.ID}" />
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

    <input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}" class="button" style="float:right;" />
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
