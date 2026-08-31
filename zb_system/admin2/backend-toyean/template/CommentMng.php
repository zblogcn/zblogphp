<?php exit(); ?>

<div class="sub">
  <form class="search" id="search" method="post" action="#">
    {$zbp.lang['msg']['search']}：
    <input name="search" type="text" placeholder="请输入…" value="">
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}">
  </form>
</div>

<!-- 评论列表 -->
<form method="post" action="{$zbp.host}zb_system/cmd.php?act=CommentBat">
  <input type="hidden" name="csrfToken" value="{$zbp.GetCSRFToken()}">
  <div class="postlist">
    <div class="tr thead">
      <div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
      <div class="td-10">{$zbp.lang['msg']['parend_id']}</div>
      <div class="td-15">{$zbp.lang['msg']['name']}{$button_authorid_html}</div>
      <div class="td-15">{$zbp.lang['msg']['content']}</div>
      <div class="td-10">{$zbp.lang['msg']['article']}{$button_logid_html}</div>
      <div class="td-10">{$zbp.lang['msg']['date']}{$button_posttime_html}</div>
      <div class="td-10 td-action">操作</div>
      <div class="td-5 tdCenter">
        <a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a>
      </div>
    </div>
  </div>

  <table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">

    {foreach $comments as $comment}
    <tr>
      <td class="td5">
        <a href="?act=CommentMng&id={$comment.ID}"
          title="{$zbp.lang['msg']['jump_comment']}{$comment.ID}">{$comment.ID}</a>
      </td>
      <td class="td5">
        {if $comment.ParentID}
        <a href="?act=CommentMng&id={$comment.ParentID}"
          title="{$zbp.lang['msg']['jump_comment']}{$comment.ParentID}">{$comment.ParentID}</a>
        {/if}
      </td>
      <td class="td10">
        <span class="cmt-note" title="{$zbp.lang['msg']['email']}:{$comment.Email}">
          <a href="mailto:{$comment.Email}">{$comment.Author.StaticName}</a>
        </span>
      </td>
      <td>
        <div style="overflow:hidden;max-width:500px;">
          {if $comment.Post}
          <a href="{$comment.Post.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
          {else}
          <a href="javascript:;"><i class="icon-trash"></i></a>
          {/if}
          {$comment.Content}
        </div>
      </td>
      <td class="td5">{$comment.LogID}</td>
      <td class="td15">{$comment.Time()}</td>
      <td class="td10 tdCenter">
        <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=CommentDel&amp;id=' . $comment->ID)}">
          <i class="icon-trash" title="{$zbp.lang['msg']['del']}"></i>
        </a>
        {if !$ischecking}
        <a href="{BuildSafeCmdURL('act=CommentChk&amp;id=' . $comment->ID . '&amp;ischecking=' . (int)!$ischecking)}">
          <i class="icon-shield-fill-x" title="{$zbp.lang['msg']['audit']}"></i>
        </a>
        {else}
        <a href="{BuildSafeCmdURL('act=CommentChk&amp;id=' . $comment->ID . '&amp;ischecking=' . (int)!$ischecking)}">
          <i class="icon-shield-fill-check" title="{$zbp.lang['msg']['pass']}"></i>
        </a>
        {/if}
      </td>
      <td class="td5 tdCenter">
        <input type="checkbox" id="id{$comment.ID}" name="id[]" value="{$comment.ID}">
      </td>
    </tr>
    {/foreach}
  </table>

  <p class="pagebar">
    {foreach $p->buttons as $k => $v}
    {if $k == $p->PageNow}
    <span class="now-page">{$k}</span>
    {else}
    <a href="{$v}">{$k}</a>
    {/if}
    {/foreach}
  </p>

  {if $ischecking}
  <input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}"
    class="button" style="float:right;">
  <input type="submit" name="all_pass" value="{$zbp.lang['msg']['all_pass']}" class="button"
    style="float:right;margin-right:10px;">
  {else}
  <input type="submit" name="all_del" onclick="return confirmDelete();" value="{$zbp.lang['msg']['all_del']}"
    class="button" style="float:right;">
  <input type="submit" name="all_audit" value="{$zbp.lang['msg']['all_audit']}" class="button"
    style="float:right;margin-right:10px;">
  {/if}

</form>

<script>
  function confirmDelete() {
    const message = "{$zbp.lang['msg']['confirm_operating']}";
    const confirmed = window.confirm(message);
    return confirmed;
  }

  $("a.order_button").parent().bind("mouseenter mouseleave", function () {
    $(this).find("a.order_button").toggleClass("element-visibility-hidden");
  });

  $(".cmt-note").tooltip();
</script>