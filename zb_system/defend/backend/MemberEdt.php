<?php exit(); ?>
<!-- update: 2026-01-04 -->
<form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$member->ID}" />
    <!-- 用户等级 -->
    <p>
        <label for="cmbLevel" class="block">
            <span class="title">{$zbp->lang['msg']['member_level']}:</span>
        </label>
        <select class="edit" size="1" name="Level" id="cmbLevel">
            {OutputOptionItemsOfMemberLevel($member->Level)}
        </select>
        {if $zbp->CheckRights('MemberAll') && $zbp->user->ID != $member->ID}
        <span class="title">{$zbp->lang['msg']['status']}:</span>

        {for $index = 0; $index < 3; $index++}
            <label for="user_staus_{$index}">
            <input id="user_staus_{$index}" name="Status" type="radio" class="radio" value="{$index}" {if $member->Status == $index}checked="checked"{/if}/>&nbsp;{$zbp->lang['user_status_name'][$index]}
            </label>
        {/for}

        {/if}
    </p>
    <!-- 用户名 -->
    <p>
        <label for="edtName" class="block">
            <span class="title">{$zbp->lang['msg']['name']}:</span>
            <span class="star">(*)</span>
        </label>
        <input id="edtName" class="edit" size="40" name="Name" maxlength="{$zbp->option['ZC_USERNAME_MAX']}" type="text" value="{$member->Name}" autocomplete="off" {if !$zbp->CheckRights('MemberAll')}readonly="readonly"{/if} />
    </p>
    <!-- 用户密码 -->
    <p>
        <label for="edtPassword" class="block">
            <span class="title">{$zbp->lang['msg']['password']}:</span>
        </label>
        <input id="edtPassword" class="edit" size="40" name="Password" type="password" value="" autocomplete="off" />
    </p>
    <!-- 确认密码 -->
    <p>
        <label for="edtPasswordRe" class="block">
            <span class="title">{$zbp->lang['msg']['re_password']}:</span>
        </label>
        <input id="edtPasswordRe" class="edit" size="40" name="PasswordRe" type="password" value="" autocomplete="off" />
    </p>
    <!-- 电子邮箱 -->
    <p>
        <label for="edtEmail" class="block">
            <span class="title">{$zbp->lang['msg']['email']}:</span>
            <span class="star">(*)</span>
        </label>
        <input id="edtEmail" class="edit" size="40" name="Email" type="text" value="{$member->Email}" />
    </p>
    <!-- 别名 -->
    <p>
        <label for="edtAlias" class="block">
            <span class="title">{$zbp->lang['msg']['alias']}:</span>
        </label>
        <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$member->Alias}" />
    </p>
    <!-- 主页 -->
    <p>
        <label for="edtHomePage" class="block">
            <span class="title">{$zbp->lang['msg']['homepage']}:</span>
        </label>
        <input id="edtHomePage" class="edit" size="40" name="HomePage" type="text" value="{$member->HomePage}" />
    </p>
    <!-- 介绍 -->
    <p>
        <label for="edtIntro" class="block">
            <span class="title">{$zbp->lang['msg']['intro']}:</span>
        </label>
        <textarea rows="6" id="edtIntro" name="Intro">{htmlspecialchars($member->Intro)}</textarea>
    </p>
    <!-- 模板 -->
    <p>
        <label for="cmbTemplate" class="block">
            <span class="title">{$zbp->lang['msg']['template']}:</span>
        </label>
        <select class="edit" size="1" name="Template" id="cmbTemplate">
            {OutputOptionItemsOfTemplate($member->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'author'))}
        </select>
    </p>
    <!-- 插件接口 -->
    <div id='response' class='editmod2'>
        {php}
        HookFilterPlugin('Filter_Plugin_Member_Edit_Response');
        {/php}
    </div>
    <!-- 默认头像 -->
    <p>
        <span class="title">{$zbp->lang['msg']['default_avatar']}:</span>&nbsp;<br />{$member->Avatar}
    </p>
    <!-- 提交按钮 -->
    <p>
        <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
</form>
<script>
    function checkInfo() {
        $("#edit").attr("action", "{BuildSafeCmdURL('act=MemberPst')}");
        if (!$("#edtEmail").val()) {
            alert("{$zbp->lang['error']['29']}");
            return false
        }
        if (!$("#edtName").val()) {
            alert("{$zbp->lang['error']['72']}");
            return false
        }
        if ($("#edtPassword").val() !== $("#edtPasswordRe").val()) {
            alert("{$zbp->lang['error']['73']}");
            return false
        }
    }
</script>
