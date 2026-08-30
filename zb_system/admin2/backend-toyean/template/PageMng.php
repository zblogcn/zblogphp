<?php exit(); ?>

<form method="post" action="{$zbp.host}zb_system/cmd.php?act=PostBat&type={$post_type}">

<div class="postlist">
	<div class="tr thead">
		<div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
		<div class="td-10 td-author">{$zbp.lang['msg']['author']}{$button_authorid_html}</div>
		<div class="td-full td-title">{$zbp.lang['msg']['title']}</div>
		<div class="td-20 td-date">{$zbp.lang['msg']['date']}{$button_posttime_html}</div>
		<div class="td-5 td-comment">{$zbp.lang['msg']['comment']}</div>
		<div class="td-5 td-status">{$zbp.lang['msg']['status']}</div>
		<div class="td-10 td-action">操作</div>
		{if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
		<div class="td-5 td-order"><a href="javascript:;" onclick="BatchSelectAll();return false;">全选</a></div>
		{/if}
	</div>
	{foreach $pages as $page}
	<div class="tr">
		<div class="td-5 td-id">{$page.ID}</div>
		<div class="td-10 td-author">{$page.Author.Name}</div>
		<div class="td-full td-title">
			<a href="{$page.Url}" target="_blank"><i class="icon-link-45deg"></i></a> {$page.Title}
		</div>
		<div class="td-20 td-date">{$page.Time()}</div>
		<div class="td-5 td-comment">{$page.CommNums}</div>
		<div class="td-5 td-status">{$page.StatusName}</div>
		<div class="td-10 td-action">
			<a href="{$zbp.host}zb_system/cmd.php?act=PageEdt&amp;id={$page.ID}" class="edit">编辑</a>
			<a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=PageDel&amp;id=' . $page->ID)}" class="del">删除</a>
		</div>
		{if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
		<div class="td-5 td-order">
			<input type="checkbox" id="id{$page.ID}" name="id[]" value="{$page.ID}">
		</div>
		{/if}
	</div>
	{/foreach}

</div>

<p class="pagebar">
	{foreach $p->buttons as $k => $v}
	{if $k == $p->PageNow}
	<span class="now-page">{$k}</span>
	{else}
	<a href="{$v}">{$k}</a>
	{/if}
	{/foreach}
	{if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
	<input type="submit" class="button pull-right" value="{$zbp.lang['msg']['all_del']}" name="all_del" onclick="return confirmDelete();">
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
