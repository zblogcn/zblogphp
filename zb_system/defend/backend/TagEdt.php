<?php exit(); ?>
<!-- update: 2026-01-04 -->

<form id="edit" name="edit" method="post" action="#">
    <input id="edtID" name="ID" type="hidden" value="{$tag->ID}" />
    <input id="edtType" name="Type" type="hidden" value="{$tag->Type}" />
    <p>
        <label for="edtName" class="block">
            <span class="title">{$zbp->lang['msg']['name']}:</span>
            <span class="star">(*)</span>
        </label>
        <input id="edtName" class="edit" size="40" name="Name" maxlength="{$option['ZC_TAGS_NAME_MAX']}" type="text" value="{$tag->Name}" autocomplete="off" />
    </p>
    <p>
        <label for="edtAlias" class="block">
            <span class="title">{$zbp->lang['msg']['alias']}:</span>
        </label>
        <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="{$tag->Alias}" />
    </p>
    <p>
        <label for="cmbTemplate" class="block">
            <span class="title">{$zbp->lang['msg']['template']}:</span>
        </label>
        <select class="edit" size="1" name="Template" id="cmbTemplate">
            {OutputOptionItemsOfTemplate($tag->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'tag'))}
        </select>
    </p>
    <p>
        <label for="edtIntro" class="block">
            <span class='title'>{$zbp->lang['msg']['intro']}:</span>
        </label>
        <textarea rows="6" id="edtIntro" name="Intro">{$tag->Intro}</textarea>
    </p>
    <p>
        <label>
            <span class="title">{$zbp->lang['msg']['add_to_navbar']}:</span>
            <input type="text" name="AddNavbar" id="edtAddNavbar" value="{$zbp->CheckItemToNavbar('tag', $tag->ID)}" class="checkbox" />
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
        $("#edit").attr("action", "{BuildSafeCmdURL('act=TagPst')}");
        if (!$("#edtName").val()) {
            alert("{$zbp->lang['error']['72']}");
            return false;
        }
    }
</script>
