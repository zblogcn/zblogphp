<?php die(); ?>
<div class="edit module_edit">
  <form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$mod->ID}" />
    <input id="edtSource" name="Source" type="hidden" value="{$mod->Source}" />
    <p {if $mod->SourceType == 'themeinclude'}style="display:none;"{/if}>
      <span class="title">{$zbp->lang['msg']['name']}:</span><span class="star">(*)</span><br/>
      <input id="edtName" class="edit" size="40" name="Name" maxlength="{$zbp->option['ZC_MODULE_NAME_MAX']}" type="text" value="{FormatString($mod->Name, '[html-format]')}" />
      ({$zbp->lang['msg']['hide_title']}: <input type="text" id="IsHideTitle" name="IsHideTitle" class="checkbox" value="{$mod->IsHideTitle}" />)
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['filename']}:</span><span class="star">(*)</span><br/>
      <input id="edtFileName" class="edit" size="40" name="FileName" type="text" value="{FormatString($mod->FileName, '[html-format]')}" {if $mod->Source != 'user'}readonly="readonly"{/if} />
    </p>
    <p {if $mod->SourceType == 'themeinclude'}style="display:none;"{/if}>
      <span class="title">{$zbp->lang['msg']['htmlid']}:</span><span class="star">(*)</span><br/>
      <input id="edtHtmlID" class="edit" size="40" name="HtmlID" type="text" value="{FormatString($mod->HtmlID, '[html-format]')}" />
    </p>
    <p {if $mod->SourceType == 'themeinclude'}style="display:none;"{/if}>
      <span class="title">{$zbp->lang['msg']['type']}:</span><br/>
      <input id="Type_DIV" name="Type" type="radio" class="radio" value="div" {if $mod->Type == 'div'}checked="checked"{/if} onclick="$('#pMaxLi').css('display','none');" />
      <label for="Type_DIV">DIV</label>&nbsp;&nbsp;&nbsp;&nbsp;
      <input id="Type_UL" type="radio" class="radio" name="Type" value="ul" {if $mod->Type != 'div'}checked="checked"{/if} onclick="$('#pMaxLi').css('display','block');" />
      <label for="Type_UL">UL</label>
    </p>
    <p id="pMaxLi" style="{if $mod->Type == 'div'}display:none;{/if}">
      <span class="title">{$zbp->lang['msg']['max_li_in_ul']}:</span><br/>
      <input type="text" name="MaxLi" value="{$mod->MaxLi}" size="40"/>
    </p>
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
    <p>
      <span class="title">{$zbp->lang['msg']['content']}:</span><br/>
      <textarea name="Content" id="Content" cols="80" rows="12">{htmlspecialchars($mod->Content)}</textarea>
    </p>
    <p {if $mod->SourceType == 'themeinclude'}style="display:none;"{/if}>
      <span class="title">{$zbp->lang['msg']['no_refresh_content']}:</span>
      <input type="text" id="NoRefresh" name="NoRefresh" class="checkbox" value="{$mod->NoRefresh}" />
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
    function checkInfo(){
      document.getElementById("edit").action = "{php}echo BuildSafeCmdURL('act=ModulePst');{/php}";

      if(!$("#edtName").val()){
        alert("{$zbp->lang['error']['72']}");
        return false
      }
      if(!$("#edtFileName").val()){
        alert("{$zbp->lang['error']['75']}");
        return false
      }
      if(!$("#edtHtmlID").val()){
        alert("{$zbp->lang['error']['76']}");
        return false
      }
    }
  </script>
</div>
