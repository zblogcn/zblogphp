<?php exit(); ?>


<div class="sub">
  <form class="search" name="upload" id="upload" method="post" enctype="multipart/form-data" action="{BuildSafeCmdURL('act=UploadPst')}">

    {$zbp.lang['msg']['upload_file']}：

    <input type="file" name="file" size="60">
    <label><input type="checkbox" name="auto_rename" checked>{$zbp.lang['msg']['auto_rename_uploadfile']}</label>
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}">
    <input class="button" type="reset" value="{$zbp.lang['msg']['reset']}">
  </form>
</div>

<div class="postlist">
  <div class="tr thead">
    <div class="td-5 td-id">{$zbp.lang['msg']['id']}{$button_id_html}</div>
    <div class="td-10">{$zbp.lang['msg']['author']}{$button_authorid_html}</div>
    <div class="td-30">{$zbp.lang['msg']['name']}</div>
    <div class="td-15">{$zbp.lang['msg']['date']}{$button_posttime_html}</div>
    <div class="td-10">{$zbp.lang['msg']['size']}{$button_size_html}</div>
    <div class="td-10">{$zbp.lang['msg']['type']}</div>
    <div class="td-10 td-action"></div>
  </div>
</div>

{foreach $uploads as $upload}
<div class="tr">
  <div class="td-5 td-id">{$upload.ID}</div>
  <div class="td-10">{$upload.Author.Name}</div>
  <div class="td-30"><a href="{$upload.Url}" target="_blank">{$upload.Name}</a></div>
  <div class="td-15">{$upload.Time()}</div>
  <div class="td-10">{$upload.Size}</div>
  <div class="td-10">{$upload.MimeType}</div>
  <div class="td-10 td-action">
    <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=UploadDel&amp;id=' . $upload->ID)}"
      class="del">删除</a>
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