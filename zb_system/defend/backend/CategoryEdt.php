<?php exit(); ?>
<!-- update: 2026-01-04 -->
<form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$cate->ID}" />
    <input id="edtType" name="Type" type="hidden" value="{$cate->Type}" />
    <!-- 名称 -->
    <p>
        <label for="edtName" class="block">
            <span class="title">{$zbp->lang['msg']['name']}:</span>
            <span class="star">(*)</span>
        </label>
        <input id="edtName" class="edit" size="40" name="Name" maxlength="{$option['ZC_CATEGORY_NAME_MAX']}" type="text" value="{$cate->Name}" autocomplete="off" />
    </p>
    <!-- 别名 -->
    <p>
        <label for="edtAlias" class="block">
            <span class="title">{$zbp->lang['msg']['alias']}:</span>
        </label>
        <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$cate->Alias}" />
    </p>
    <!-- 排序 -->
    <p>
        <label for="edtOrder" class="block">
            <span class="title">{$zbp->lang['msg']['order']}:</span>
        </label>
        <input id="edtOrder" class="edit" size="40" name="Order" type="text" value="{$cate->Order}" />
    </p>
    <!-- 父分类 -->
    <p>
        <label for="edtParentID" class="block">
            <span class="title">{$zbp->lang['msg']['parent_category']}:</span>
        </label>
        <select id="edtParentID" name="ParentID" class="edit" size="1">
            {$p}
        </select>
    </p>
    <!-- 模板 -->
    <p>
        <label for="cmbTemplate" class="block">
            <span class="title">{$zbp->lang['msg']['template']}:</span>
        </label>
        <select class="edit" size="1" name="Template" id="cmbTemplate">
            {OutputOptionItemsOfTemplate($cate->Template, array('single', '404', 'search', 'module', 'lm-'), array('list', 'category'))}
        </select>
        <input type="hidden" name="edtTemplate" id="edtTemplate" value="{$cate->Template}" />
    </p>
    <!-- 分类内文章模板 -->
    <p>
        <label for="cmbLogTemplate" class="block">
            <span class="title">{$zbp->lang['msg']['category_aritles_default_template']}:</span>
        </label>
        <select class="edit" size="1" name="LogTemplate" id="cmbLogTemplate">
            {OutputOptionItemsOfTemplate($cate->LogTemplate, array('index', '404', 'search', 'module', 'lm-'), array('single', $zbp->GetPostType($cate->Type, 'name')))}
        </select>
    </p>
    <!-- 描述 -->
    <p>
        <label for="edtIntro" class="block">
            <span class='title'>{$zbp->lang['msg']['intro']}:</span>
        </label>
        <textarea rows="6" id="edtIntro" name="Intro">{$cate->Intro}</textarea>
    </p>
    <!-- 添加到导航栏 -->
    <p>
        <label>
            <span class="title">{$zbp->lang['msg']['add_to_navbar']}:</span>
            <input type="text" name="AddNavbar" id="edtAddNavbar" value="{$zbp->CheckItemToNavbar('category', $cate->ID)}" class="checkbox" />
        </label>
    </p>
    <!-- 接口输出 -->
    <div id='response' class='editmod2'>
        {php}
        HookFilterPlugin('Filter_Plugin_Category_Edit_Response');
        {/php}
    </div>
    <!-- 提交按钮 -->
    <p>
        <input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" onclick="return checkInfo();" />
    </p>
</form>
<script>
    function checkInfo() {
        $("#edit").attr("action", "{BuildSafeCmdURL('act=CategoryPst')}");
        if (!$("#edtName").val()) {
            alert("{$zbp->lang['error']['72']}");
            return false;
        }
    }
</script>
