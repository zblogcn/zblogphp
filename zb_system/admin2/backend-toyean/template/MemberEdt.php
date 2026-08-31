<?php die(); ?>
<div class="edit member_edit">
  <form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$member->ID}" />
    <p>
      <span class="title">{$zbp->lang['msg']['member_level']}:</span>
      <br/>
      <select class="edit" size="1" name="Level" id="cmbLevel">
        {php}echo OutputOptionItemsOfMemberLevel($member->Level);{/php}
      </select>
      {if $zbp->CheckRights('MemberAll') && $zbp->user->ID != $member->ID}
      &nbsp;(
      <span class="title">{$zbp->lang['msg']['status']}:</span>
      <input id="user_status_0" name="Status" type="radio" class="radio" value="0" {if $member->Status == 0}checked="checked"{/if}/>&nbsp;
      <label for="user_status_0">{$zbp->lang['user_status_name'][0]}</label>
      &nbsp;&nbsp;
      <input id="user_status_1" name="Status" type="radio" class="radio" value="1" {if $member->Status == 1}checked="checked"{/if}/>&nbsp;
      <label for="user_status_1">{$zbp->lang['user_status_name'][1]}</label>
      &nbsp;&nbsp;
      <input id="user_status_2" name="Status" type="radio" class="radio" value="2" {if $member->Status == 2}checked="checked"{/if}/>&nbsp;
      <label for="user_status_2">{$zbp->lang['user_status_name'][2]}</label>
      )
      {/if}
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['name']}:</span><span class="star">(*)</span><br/>
      <input id="edtName" class="edit" size="40" name="Name" maxlength="{$zbp->option['ZC_USERNAME_MAX']}" type="text" value="{$member->Name}" {if !$zbp->CheckRights('MemberAll')}readonly="readonly"{/if}/>
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['password']}:</span><br/>
      <input id="edtPassword" class="edit" size="40" name="Password" type="password" value="" autocomplete="off"/>
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['re_password']}:</span><br/>
      <input id="edtPasswordRe" class="edit" size="40" name="PasswordRe" type="password" value="" autocomplete="off"/>
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['email']}:</span><span class="star">(*)</span><br/>
      <input id="edtEmail" class="edit" size="40" name="Email" type="text" value="{$member->Email}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['alias']}:</span><br/>
      <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$member->Alias}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['homepage']}:</span><br/>
      <input id="edtHomePage" class="edit" size="40" name="HomePage" type="text" value="{$member->HomePage}" />
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['intro']}:</span><br/>
      <textarea cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;">{htmlspecialchars($member->Intro)}</textarea>
    </p>
    <p>
      <span class="title">{$zbp->lang['msg']['template']}:</span><br/>
      <select class="edit" size="1" name="Template" id="cmbTemplate">
        {php}echo OutputOptionItemsOfTemplate($member->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'author'));{/php}
      </select>
    </p>
    <div id='response' class='editmod2'>
      {php}
        HookFilterPlugin('Filter_Plugin_Member_Edit_Response');
      {/php}
    </div>
    <p>
      <span class="title">{$zbp->lang['msg']['default_avatar']}:</span>&nbsp;<br/>{$member->Avatar}
    </p>
    <p>
      <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
  </form>
  <script>
    function checkInfo(){
      document.getElementById("edit").action = "{php}echo BuildSafeCmdURL('act=MemberPst');{/php}";

      if(!$("#edtEmail").val()){
        alert("{$zbp->lang['error']['29']}");
        return false
      }
      if(!$("#edtName").val()){
        alert("{$zbp->lang['error']['72']}");
        return false
      }
      if($("#edtPassword").val()!==$("#edtPasswordRe").val()){
        alert("{$zbp->lang['error']['73']}");
        return false
      }
    }
  </script>
</div>
