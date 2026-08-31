<?php exit(); ?>
<!-- update: 2026-01-04 -->

<!-- 上传表单 -->
<form class="search" name="upload" id="upload" method="post" enctype="multipart/form-data" action="{BuildSafeCmdURL('act=UploadPst')}">
    <p>{$zbp.lang['msg']['upload_file']}: </p>
    <p>
        <input type="file" name="file" aria-label="file" size="60" />
        <label class="label-flex"><input type="checkbox" name="auto_rename" checked />{$zbp.lang['msg']['auto_rename_uploadfile']}</label>
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
        <input class="button" type="reset" value="{$zbp.lang['msg']['reset']}" />
    </p>
</form>

<!-- 文件列表 -->
<table class="tableFull tableBorder thCenter table_hover table_striped">
    <tr>
        <th data-field="id">{$zbp.lang['msg']['id']}{$button_id_html}</th>
        <th data-field="author">{$zbp.lang['msg']['author']}{$button_authorid_html}</th>
        <th data-field="name">{$zbp.lang['msg']['name']}</th>
        <th data-field="date">{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
        <th data-field="size">{$zbp.lang['msg']['size']}{$button_size_html}</th>
        <th data-field="type">{$zbp.lang['msg']['type']}</th>
        <th data-field="actions"></th>
    </tr>

    {foreach $uploads as $upload}
    <tr data-id="{$upload.ID}">
        <td class="td5" data-field="id">{$upload.ID}</td>
        <td class="td10" data-field="author">{$upload.Author.Name}</td>
        <td data-field="name">
            <a href="{$upload.Url}" target="_blank"><i class="icon-link-45deg"></i></a>
            {$upload.Name}
        </td>
        <td class="td15" data-field="date">{$upload.Time()}</td>
        <td class="td10" data-field="size">{$upload.Size}</td>
        <td class="td20" data-field="type">{$upload.MimeType}</td>
        <td class="td10 tdCenter" data-field="actions">
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
        return window.confirm("{$zbp.lang['msg']['confirm_operating']}");
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });
</script>
