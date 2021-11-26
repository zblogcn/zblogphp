<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 2.0 2013-07-05
 */
require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Load();

$action = 'ModuleEdt';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$blogtitle = $lang['msg']['module_edit'];

require ZBP_PATH . 'zb_system/admin/admin_header.php';
require ZBP_PATH . 'zb_system/admin/admin_top.php';

?>
<?php
$modid = null;
$mod = null;

if (isset($_GET['source']) && isset($_GET['filename'])) {
    if (GetVars('source', 'GET') == 'themeinclude_' . $zbp->theme) {
        $mod = $zbp->GetModuleByFileName(GetVars('filename', 'GET'));
        if ($mod->ID == 0 || $mod->SourceType != 'themeinclude') {
            $zbp->ShowError(61);
        }
    } else {
        $zbp->ShowError(61);
    }
} elseif (isset($_GET['filename'])) {
    $mod = $zbp->GetModuleByFileName(GetVars('filename', 'GET'));
    if ($mod->ID == 0 || $mod->SourceType == 'themeinclude') {
        $zbp->ShowError(69);
    }
} else {
    if (isset($_GET['id'])) {
        $modid = (int) GetVars('id', 'GET');
    } else {
        $modid = 0;
    }

    $mod = $zbp->GetModuleByID($modid);
}
if ($mod->Type == 'ul') {
    $mod->Content = str_replace("</li>", "</li>\r\n", $mod->Content);
}

$islock = '';
if ($mod->Source != 'user') {
    $islock = 'readonly="readonly"';
}
if ($mod->FileName == '') {
    $islock = '';
    $mod->Name = 'newmodule';
    $mod->HtmlID = 'newmodule';
}
$ishide = '';
if ($mod->SourceType == 'themeinclude') {
    $ishide = 'style="display:none;"';
}
?>
<div id="divMain">
    <div class="divHeader2">
        <?php echo $lang['msg']['module_edit']; ?></div>
    <div class="SubMenu">
        <?php
        HookFilterPlugin('Filter_Plugin_Module_Edit_SubMenu');
        ?>
    </div>
    <div id="divMain2" class="edit tag_edit">
        <form id="edit" name="edit" method="post" action="#">
            <input id="edtID" name="ID" type="hidden" value="<?php echo $mod->ID; ?>" />
            <input id="edtSource" name="Source" type="hidden" value="<?php echo $mod->Source; ?>" />
            <p <?php echo $ishide; ?>>
                <span class="title">
                    <?php echo $lang['msg']['name']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtName" class="edit" size="40" name="Name" maxlength="<?php echo $option['ZC_MODULE_NAME_MAX']; ?>" type="text" value="<?php echo FormatString($mod->Name, '[html-format]'); ?>" />
                (
                <?php echo $lang['msg']['hide_title']; ?>
                :
                <input type="text" id="IsHideTitle" name="IsHideTitle" class="checkbox" value="<?php echo $mod->IsHideTitle; ?>" />)</p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['filename']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtFileName" <?php echo $islock; ?> class="edit" size="40" name="FileName" type="text" value="<?php echo FormatString($mod->FileName, '[html-format]'); ?>" /></p>
            <p <?php echo $ishide; ?>>
                <span class="title">
                    <?php echo $lang['msg']['htmlid']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtHtmlID" class="edit" size="40" name="HtmlID" type="text" value="<?php echo FormatString($mod->HtmlID, '[html-format]'); ?>" /></p>
            <p <?php echo $ishide; ?>>
                <span class='title'>
                    <?php echo $lang['msg']['type']; ?>:</span>
                <br />
                <input id="Type_DIV" name="Type" type="radio" class="radio" value="div" <?php echo $mod->Type == 'div' ? 'checked="checked"' : ''; ?> onclick="$('#pMaxLi').css('display','none');" />
                <label for="Type_DIV">DIV</label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input id="Type_UL" type="radio" class="radio" name="Type" value="ul" <?php echo $mod->Type == 'div' ? '' : 'checked="checked"'; ?> onclick="$('#pMaxLi').css('display','block');" />
                <label for="Type_UL">UL</label>
            </p>
            <p id="pMaxLi" style="<?php echo $mod->Type == 'div' ? 'display:none;' : ''; ?>">
                <span class='title'>
                    <?php echo $lang['msg']['max_li_in_ul']; ?>:</span>
                <br />
                <input type="text" name="MaxLi" value="<?php echo $mod->MaxLi; ?>" size="40" /></p>
            <?php
            if ($mod->FileName == 'catalog') {
                ?>
                <p>
                    <span class='title'>
                        <?php echo $lang['msg']['style']; ?>:</span>
                    &nbsp;&nbsp;
                        <input id="catalog_style_normal" name="catalog_style" type="radio" class="radio" value="0" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '0' ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="catalog_style_normal"><?php echo $lang['msg']['catalog_style_normal']; ?></label>
                    &nbsp;&nbsp;
                        <input id="catalog_style_tree" name="catalog_style" type="radio" class="radio" value="1" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '1' ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="catalog_style_tree"><?php echo $lang['msg']['catalog_style_tree']; ?></label>
                    &nbsp;&nbsp;
                        <input id="catalog_style_ul" name="catalog_style" type="radio" class="radio" value="2" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE'] == '2' ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="catalog_style_ul"><?php echo $lang['msg']['catalog_style_ul']; ?></label>
                    &nbsp;&nbsp;
                </p>
                <?php
            }
            if ($mod->FileName == 'archives') {
                if ($zbp->option['ZC_MODULE_ARCHIVES_STYLE'] == '1') {
                    ?>
                    <label><input name="archives_style" type="checkbox" value="<?php echo $zbp->option['ZC_MODULE_ARCHIVES_STYLE']; ?>" checked="checked" /><?php echo $lang['msg']['archives_style_select']; ?></label></label>
                    <?php
                } else {
                    ?>
                    <label><input name="archives_style" type="checkbox" value="<?php echo $zbp->option['ZC_MODULE_ARCHIVES_STYLE']; ?>" /><?php echo $lang['msg']['archives_style_select']; ?></label></label>
                    <?php
                }
            }
            ?>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['content']; ?>:</span>
                <br />
                <textarea name="Content" id="Content" cols="80" rows="12"><?php echo htmlspecialchars($mod->Content); ?></textarea>
            </p>
            <p <?php echo $ishide; ?>>
                <span class='title'>
                    <?php echo $lang['msg']['no_refresh_content']; ?>:</span>
                <input type="text" id="NoRefresh" name="NoRefresh" class="checkbox" value="<?php echo $mod->NoRefresh; ?>" /></p>
            <!-- 1号输出接口 -->
            <div id='response' class='editmod2'>
                <?php
                HookFilterPlugin('Filter_Plugin_Module_Edit_Response');
                ?>
            </div>
            <p>

                <input type="submit" class="button" value="<?php echo $lang['msg']['submit']; ?>" id="btnPost" onclick="return checkInfo();" /></p>
        </form>
        <script>
            function checkInfo() {
                document.getElementById("edit").action = "<?php echo BuildSafeCmdURL('act=ModulePst'); ?>";

                if (!$("#edtName").val()) {
                    alert("<?php echo $lang['error']['72']; ?>");
                    return false
                }
                if (!$("#edtFileName").val()) {
                    alert("<?php echo $lang['error']['75']; ?>");
                    return false
                }
                if (!$("#edtHtmlID").val()) {
                    alert("<?php echo $lang['error']['76']; ?>");
                    return false
                }
            }
        </script>
        <script>
            ActiveLeftMenu("aModuleMng");
        </script>
        <script>
            AddHeaderFontIcon("icon-grid-fill");
        </script>
    </div>
</div>

<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

RunTime();
