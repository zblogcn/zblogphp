<?php exit(); ?>

<div class="sub">
	<form class="search" id="search" method="post" action="#">
		{$zbp.lang['msg']['search']}：
		<input type="text" name="search" value="">
		<input type="submit" class="button" value="{$zbp.lang['msg']['submit']}">
	</form>
</div>

<!-- 标签列表 -->
<form method="post" action="{$zbp.host}zb_system/cmd.php?act=TagBat">
	<input type="hidden" name="csrfToken" value="{$zbp.GetCSRFToken()}">
	<div class="postlist">
		<div class="tr thead">
			<div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
			<div class="td-25 td-title">{$zbp.lang['msg']['name']}{$button_name_html}</div>
			<div class="td-20 td-alias">{$zbp.lang['msg']['alias']}</div>
			<div class="td-full td-intro">{$zbp.lang['msg']['intro']}</div>
			<div class="td-10 td-action">操作</div>
			<div class="td-10 td-order"><a href="javascript:;" onclick="BatchSelectAll();return false;">全选</a></div>
		</div>
		{foreach $tags as $tag}
		<div class="tr">
			<div class="td-5 td-id">{$tag.ID}</div>
			<div class="td-25 td-title">{$tag.Name}</div>
			<div class="td-20 td-alias">{$tag.Alias}</div>
			<div class="td-full td-intro">{$tag.Intro}</div>
			<div class="td-10 td-action">
				<a href="{$zbp.host}zb_system/cmd.php?act=TagEdt&amp;id={$tag.ID}" class="edit">编辑</a>
				<a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=TagDel&amp;id=' . $tag->ID)}" class="del">删除</a>
			</div>
			<div class="td-10 td-order"><input type="checkbox" id="id{$tag.ID}" name="id[]" value="{$tag.ID}"></div>
		</div>
		{/foreach}
	</div>

	<!-- 分页和批量操作按钮 -->
	<p class="pagebar">
		{foreach $p->buttons as $k => $v}
		{if $k == $p->PageNow}
		<span class="now-page">{$k}</span>
		{else}
		<a href="{$v}">{$k}</a>
		{/if}
		{/foreach}
	</p>
	<input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}" class="button" style="float:right;">

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
