<?php exit(); ?>

<div class="sub">
  <form class="search" id="search" method="post" action="#">
    {$zbp.lang['msg']['search']}： {$zbp.lang['msg']['member_level']}
    <select class="edit" size="1" name="level" style="width:140px;">
      <option value="">{$zbp.lang['msg']['any']}</option>
      {foreach $zbp.lang['user_level_name'] as $curId => $curName}
      <option value="{$curId}">{$curName}</option>
      {/foreach}
    </select>
    <input name="search" type="text" placeholder="请输入…" value="">
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}">
  </form>
</div>

<div class="postlist">
  <div class="tr thead">
    <div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
    <div class="td-10">{$zbp.lang['msg']['member_level']}{$button_level_html}</div>
    <div class="td-15">{$zbp.lang['msg']['name']}{$button_name_html}</div>
    <div class="td-15">{$zbp.lang['msg']['alias']}{$button_alias_html}</div>
    <div class="td-10">{$zbp.lang['msg']['all_artiles']}</div>
    <div class="td-10">{$zbp.lang['msg']['all_pages']}</div>
    <div class="td-10">{$zbp.lang['msg']['all_comments']}</div>
    <div class="td-10">{$zbp.lang['msg']['all_uploads']}</div>
    <div class="td-10 td-action">操作</div>
  </div>
</div>

{foreach $members as $member}
<div class="tr">
  <div class="td-5 td-id">{$member.ID}</div>
  <div class="td-10">{$member.LevelName}
    {if $member.Status > 0}({$zbp.lang['user_status_name'][$member.Status]}){/if}
    {if $member.IsGod}<span title="root">#</span>{/if}</div>
  <div class="td-15"><a href="{$member.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
    {$member.Name}</div>
  <div class="td-15">{$member.Alias}</div>
  <div class="td-10">{php}echo max(0, $member->Articles);{/php}</div>
  <div class="td-10">{php}echo max(0, $member->Pages);{/php}</div>
  <div class="td-10">{php}echo max(0, $member->Comments);{/php}</div>
  <div class="td-10">{php}echo max(0, $member->Uploads);{/php}</div>
  <div class="td-10 td-action">
    <a href="{$zbp.host}zb_system/cmd.php?act=MemberEdt&amp;id={$member.ID}" class="edit">编辑</a>
    {if $zbp.CheckRights('MemberDel') && !$member.IsGod}
    <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=MemberDel&amp;id=' . $member->ID)}"
      class="del">删除</a>
    {/if}
  </div>
</div>
{/foreach}

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
    const message = "{$zbp.lang['msg']['confirm_operating']}";
    const confirmed = window.confirm(message);
    return confirmed;
  }
  $("a.order_button").parent().bind("mouseenter mouseleave", function () {
    $(this).find("a.order_button").toggleClass("element-visibility-hidden");
  });
</script>