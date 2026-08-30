<?php exit(); ?>
<div class="sub">
  <form class="search" id="search" method="post" action="#">
      {$zbp.lang['msg']['search']}：
      <input name="search" type="text" value="">
      <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}">
  </form>
</div>
<div class="postlist">
  <div class="tr thead">
    <div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
    <div class="td-5 td-title">{$zbp.lang['msg']['order']}{$button_order_html}</div>
    <div class="td-full td-alias">{$zbp.lang['msg']['name']}{$button_name_html}</div>
    <div class="td-20 td-intro">{$zbp.lang['msg']['alias']}</div>
    <div class="td-10 td-post-count">{$zbp.lang['msg']['post_count']}</div>
    <div class="td-10 td-action">操作</div>
  </div>
	{foreach $categories as $category}
	<div class="tr">
			<div class="td-5 td-id">{$category.ID}</div>
			<div class="td-5 td-title">{$category.Order}</div>
			<div class="td-full td-alias"><a href="{$category.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
      {$category.Name}</div>
			<div class="td-20 td-intro">{$category.Alias}</div>
      <div class="td-10 td-post-count">{$category.Count}</div>
			<div class="td-10 td-action">
				<a href="{$zbp.host}zb_system/cmd.php?act=CategoryEdt&amp;id={$category.ID}" class="edit">编辑</a>
        {if count($category.SubCategories) == 0}
				<a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=CategoryDel&amp;id=' . $category->ID)}" class="del">删除</a>
        {/if}
			</div>
	</div>
	{/foreach}
</div>


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
