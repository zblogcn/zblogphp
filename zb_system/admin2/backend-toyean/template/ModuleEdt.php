<?php exit(); ?>
<div class="edit module_edit">
<form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$mod->ID}" />
    <input id="edtSource" name="Source" type="hidden" value="{$mod->Source}" />
    <!-- name -->
    <p {if $mod->SourceType == 'themeinclude'}class="hidden"{/if}>
        <span class="title">{$zbp->lang['msg']['title']}:</span><span class="star">(*)</span><br />
        <input id="edtName" class="edit" size="40" name="Name" maxlength="{$zbp->option['ZC_MODULE_NAME_MAX']}" type="text" value="{FormatString($mod->Name, '[html-format]')}" />
        ({$zbp->lang['msg']['hide_title']}: <input type="text" id="IsHideTitle" name="IsHideTitle" class="checkbox" value="{$mod->IsHideTitle}" />)
    </p>
    <!-- filename -->
    <p>
        <span class="title">{$zbp->lang['msg']['id']}:</span><span class="star">(*)</span><br />
        <input id="edtFileName" class="edit" size="40" name="FileName" type="text" value="{FormatString($mod->FileName, '[html-format]')}" {if $mod->Source != 'user'}readonly="readonly"{/if} />
    </p>
    <!-- htmlid -->
    <input id="edtHtmlID" class="edit" size="40" name="HtmlID" type="hidden" value="{FormatString($mod->HtmlID, '[html-format]')}" />
    <!-- type -->
    <input id="edtType" class="edit" size="40" name="Type" type="hidden" value="{FormatString($mod->Type, '[html-format]')}" />
    {if $mod->AutoContent == false && $mod->Type == 'ul'}
    <p>
        <span class="title">{$zbp->lang['msg']['link']}:</span><span class="star">(*)</span><br />
    </p>
{php}<?php
foreach ($mod->Links as $link) {
    ?>{/php}
    <p><input class="edit" size="50" name="href[]" type="text" placeholder="{$zbp->lang['msg']['href']}" value="{FormatString(@$link->href, '[html-format]')}" />
    <input class="edit" size="30" name="content[]" type="text" placeholder="{$zbp->lang['msg']['text']}" value="{FormatString(@$link->content, '[html-format]')}" />
    <input class="edit" size="30" name="target[]" type="text" placeholder="Target" value="{FormatString(@$link->target, '[html-format]')}" />
    {if $mod->FileName == 'navbar'}
    <input class="edit" size="30" name="li_id[]" type="hidden" value="{FormatString(@$link->li_id, '[html-format]')}" />
    {/if}
    </p>
    {php}<?php
}
?>{/php}
    <p><input class="edit" size="50" name="href[]" type="text" placeholder="{$zbp->lang['msg']['href']}" value="" />
    <input class="edit" size="30" name="content[]" type="text" placeholder="{$zbp->lang['msg']['text']}" value="" />
    <input class="edit" size="30" name="target[]" type="text" placeholder="Target" value="" />
    </p>
    {/if}
    {if $mod->AutoContent == false && $mod->Type == 'div'}
    <p>
        <span class="title">{$zbp->lang['msg']['content']}:</span><br />
        <textarea name="Content" id="Content" cols="80" rows="12">{htmlspecialchars($mod->Content)}</textarea>
    </p>  
    {/if}

    {if $mod->FileName == 'catalog'}
    <p>
        <span class="title">{$zbp->lang['msg']['style']}:</span>&nbsp;&nbsp;
        <input id="catalog_style_normal" name="catalog_style" type="radio" class="radio" value="0" {if $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '0'}checked="checked"{/if}/>&nbsp;
        <label for="catalog_style_normal">{$zbp->lang['msg']['catalog_style_normal']}</label>&nbsp;&nbsp;
        <input id="catalog_style_tree" name="catalog_style" type="radio" class="radio" value="1" {if $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '1'}checked="checked"{/if}/>&nbsp;
        <label for="catalog_style_tree">{$zbp->lang['msg']['catalog_style_tree']}</label>&nbsp;&nbsp;
        <input id="catalog_style_ul" name="catalog_style" type="radio" class="radio" value="2" {if $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '2'}checked="checked"{/if}/>&nbsp;
        <label for="catalog_style_ul">{$zbp->lang['msg']['catalog_style_ul']}</label>&nbsp;&nbsp;
    </p>
    {/if}
    {if $mod->FileName == 'archives'}
    <label>
        <input name="archives_style" type="checkbox" value="{$zbp->option['ZC_MODULE_ARCHIVES_STYLE']}" {if $zbp->option['ZC_MODULE_ARCHIVES_STYLE'] == '1'}checked="checked"{/if}/>{$zbp->lang['msg']['archives_style_select']}
    </label>
    {/if}
    <!-- maxli -->
    <input type="hidden" name="MaxLi" value="{$mod->MaxLi}" size="40" />
    <!-- no refresh content -->
    <p style="display:none;" {if $mod->SourceType == 'themeinclude'}class="hidden"{/if}>
        <span class="title">{$zbp->lang['msg']['no_refresh_content']}:</span>
        <input type="text" id="NoRefresh" name="NoRefresh" class="checkbox" value="{$mod->NoRefresh}" />
    </p>
    <p {if $mod->SourceType != 'user' && $mod->SourceType != 'plugin' && $mod->SourceType != 'theme'}class="hidden"{/if}>
        <span class="title">{$zbp->lang['msg']['custom_content']}:</span>
        <input type="text" id="custom_content" name="custom_content" class="checkbox" value="{if $mod->Type == 'div'}1{else}0{/if}" />
    </p>
    <div id='response' class='editmod2'>
        {php}
        HookFilterPlugin('Filter_Plugin_Module_Edit_Response');
        {/php}
    </div>
    <p>
        <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
</form>
<script>
    function checkInfo() {
        $("#edit").attr("action", "{BuildSafeCmdURL('act=ModulePst')}");
        if (!$("#edtName").val()) {
            alert("{$zbp->lang['error']['72']}");
            return false
        }
        if (!$("#edtFileName").val()) {
            alert("{$zbp->lang['error']['75']}");
            return false
        }
    }
</script>
</div>
