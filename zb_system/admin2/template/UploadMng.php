<?php die(); ?>

<!-- 上传表单 -->
<form class="search" name="upload" id="upload" method="post" enctype="multipart/form-data" action="{BuildSafeCmdURL('act=UploadPst')}">
  <p>{$zbp.lang['msg']['upload_file']}: </p>
  <p>
    <input type="file" name="file" size="60" />
    <label><input type="checkbox" name="auto_rename" checked/>{$zbp.lang['msg']['auto_rename_uploadfile']}</label>
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    <input class="button" type="reset" value="{$zbp.lang['msg']['reset']}" />
  </p>
</form>

<!-- 文件列表 -->
<table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">
  <tr>
    <th>{$zbp.lang['msg']['id']}{$button_id_html}</th>
    <th>{$zbp.lang['msg']['author']}{$button_authorid_html}</th>
    <th>{$zbp.lang['msg']['name']}</th>
    <th>{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
    <th>{$zbp.lang['msg']['size']}{$button_size_html}</th>
    <th>{$zbp.lang['msg']['type']}</th>
    <th></th>
  </tr>

  {foreach $uploads as $upload}
  <tr>
    <td class="td5">{$upload.ID}</td>
    <td class="td10">{$upload.Author.Name}</td>
    <td>
      <a href="{$upload.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
      {$upload.Name}
    </td>
    <td class="td15">{$upload.Time()}</td>
    <td class="td10">{$upload.Size}</td>
    <td class="td20">{$upload.MimeType}</td>
    <td class="td10 tdCenter">
      <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=UploadDel&amp;id=' . $upload->ID)}">
        <i class="icon-trash"></i>
      </a>
    </td>
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
</p>

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
