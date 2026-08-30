<?php die(); ?>
<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
  <p>
    {$zbp.lang['msg']['search']}: {$zbp.lang['msg']['category']}
    <select class="edit" size="1" name="category" style="width:140px;">
      <option value="">{$zbp.lang['msg']['any']}</option>
      {foreach $zbp.categoriesbyorder as $id => $cate}
      <option value="{$cate->ID}">{$cate->SymbolName}</option>
      {/foreach}
    </select>
    {$zbp.lang['msg']['type']}
    <select class="edit" size="1" name="status" style="width:100px;">
      <option value="">{$zbp.lang['msg']['any']}</option>
      <option value="0">{$zbp.lang['post_status_name']['0']}</option>
      <option value="1">{$zbp.lang['post_status_name']['1']}</option>
      <option value="2">{$zbp.lang['post_status_name']['2']}</option>
    </select>

    <label>
      <input type="checkbox" name="istop" value="True" />{$zbp.lang['msg']['top']}
    </label>

    <input name="search" style="width:250px;" type="text" value="" />
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
  </p>
</form>

<form method="post" action="{$zbp.host}zb_system/cmd.php?act=PostBat&type={$post_type}">
<!-- 文章列表 -->

<table class="tableFull tableBorder table_hover table_striped tableBorder-thcenter">

  <tr>
    <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
    <th>{$zbp.lang['msg']['category']}{$button_cateid_html}</th>
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

  {foreach $articles as $article}
  <tr>
    <td class="td5">{$article.ID}</td>
    <td class="td10">{$article.Category.Name}</td>
    <td class="td10">{$article.Author.Name}</td>
    <td>
      <a href="{$article.Url}" target="_blank"><i class="icon-link-45deg"></i></a> {$article.Title}
    </td>
    <td class="td20">{$article.Time()}</td>
    <td class="td5">{$article.CommNums}</td>
    <td class="td5">
      {if $article.IsTop}
      {$zbp.lang.msg.top}|
      {/if}
      {$article.StatusName}
    </td>
    <td class="td10 tdCenter">
      <a href="../cmd.php?act=ArticleEdt&amp;id={$article.ID}"><i class="icon-pencil-square"></i></a>
      <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=ArticleDel&amp;id=' . $article->ID)}"><i class="icon-trash"></i></a>
    </td>
    {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
    <td class="td5 tdCenter">
      <input type="checkbox" id="id{$article.ID}" name="id[]" value="{$article.ID}">
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