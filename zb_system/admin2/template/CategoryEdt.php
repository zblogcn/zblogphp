<?php die(); ?>
<div class="edit category_edit">
  <form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$cate->ID}" />
    <input id="edtType" name="Type" type="hidden" value="{$cate->Type}" />
    <p>
      <span class="title">{$zbp->lang['msg']['name']}:</span>
      <span class="star">(*)</span>
      <br />
      <input id="edtName" class="edit" size="40" name="Name" maxlength="{$option['ZC_CATEGORY_NAME_MAX']}" type="text" value="{$cate->Name}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['alias']}:</span>
      <br />
      <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$cate->Alias}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['order']}:</span>
      <br />
      <input id="edtOrder" class="edit" size="40" name="Order" type="text" value="{$cate->Order}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['parent_category']}:</span>
      <br />
      <select id="edtParentID" name="ParentID" class="edit" size="1">
        {$p}
      </select>
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['template']}:</span>
      <br />
      <select class="edit" size="1" name="Template" id="cmbTemplate">
        {php}
          echo OutputOptionItemsOfTemplate($cate->Template, array('single', '404', 'search', 'module', 'lm-'), array('list', 'category'));
        {/php}
      </select>
      <input type="hidden" name="edtTemplate" id="edtTemplate" value="{$cate->Template}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['category_aritles_default_template']}:</span>
      <br />
      <select class="edit" size="1" name="LogTemplate" id="cmbLogTemplate">
        {php}
          echo OutputOptionItemsOfTemplate($cate->LogTemplate, array('index', '404', 'search', 'module', 'lm-'), array('single', $zbp->GetPostType($cate->Type, 'name')));
        {/php}
      </select>
    </p>
    <p>
      <span class='title'>{$zbp->lang['msg']['intro']}:</span>
      <br />
      <textarea cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;">{$cate->Intro}</textarea>
    </p>
    <p>
      <label>
        <span class="title">{$zbp->lang['msg']['add_to_navbar']}:</span>
        <input type="text" name="AddNavbar" id="edtAddNavbar" value="{php}echo (int) $zbp->CheckItemToNavbar('category', $cate->ID);{/php}" class="checkbox" />
      </label>
    </p>
    <!-- 1号输出接口 -->
    <div id='response' class='editmod2'>
      {php}
        HookFilterPlugin('Filter_Plugin_Category_Edit_Response');
      {/php}
    </div>
    <p>
      <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
  </form>
  <script>
    function checkInfo() {
      document.getElementById("edit").action = "{php}echo BuildSafeCmdURL('act=CategoryPst');{/php}";
      if (!$("#edtName").val()) {
        alert("{$zbp->lang['error']['72']}");
        return false;
      }
    }
  </script>
</div>
