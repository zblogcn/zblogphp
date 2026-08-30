<?php die(); ?>
<div class="edit tag_edit">
  <form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$tag->ID}" />
    <input id="edtType" name="Type" type="hidden" value="{$tag->Type}" />
    <p>
      <span class="title">{$zbp->lang['msg']['name']}:</span>
      <span class="star">(*)</span>
      <br />
      <input id="edtName" class="edit" size="40" name="Name" maxlength="{$option['ZC_TAGS_NAME_MAX']}" type="text" value="{$tag->Name}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['alias']}:</span>
      <br />
      <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$tag->Alias}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['template']}:</span>
      <br />
      <select class="edit" size="1" name="Template" id="cmbTemplate">
        {php}
          echo OutputOptionItemsOfTemplate($tag->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'tag'));
        {/php}
      </select>
    </p>
    <p>
      <span class='title'>{$zbp->lang['msg']['intro']}:</span>
      <br />
      <textarea cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;">{$tag->Intro}</textarea>
    </p>
    <p>
      <label>
        <span class="title">{$zbp->lang['msg']['add_to_navbar']}:</span>
        <input type="text" name="AddNavbar" id="edtAddNavbar" value="{php}echo (int) $zbp->CheckItemToNavbar('tag', $tag->ID);{/php}" class="checkbox" />
      </label>
    </p>
    <div id='response' class='editmod2'>
      {php}
        HookFilterPlugin('Filter_Plugin_Tag_Edit_Response');
      {/php}
    </div>
    <p>
      <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
  </form>
  <script>
    function checkInfo() {
      document.getElementById("edit").action = "{php}echo BuildSafeCmdURL('act=TagPst');{/php}";
      if (!$("#edtName").val()) {
        alert("{$zbp->lang['error']['72']}");
        return false;
      }
    }
  </script>
</div>
