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

$action = 'TagPst';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$type = (int) GetVars('type');
$typetitle = $type > 0 ? (ucfirst($zbp->GetPostType($type, 'name')) . '-') : '';

$blogtitle = $typetitle . $lang['msg']['tag_edit'];

require ZBP_PATH . 'zb_system/admin/admin_header.php';
require ZBP_PATH . 'zb_system/admin/admin_top.php';

?>
<?php
$tagid = null;
if (isset($_GET['id'])) {
    $tagid = (int) GetVars('id', 'GET');
    $action = 'TagEdt';
    if (!$zbp->CheckRights($action)) {
        $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }
} else {
    $tagid = 0;
    $action = 'TagNew';
    if (!$zbp->CheckRights($action)) {
        $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }
}

$tag = $zbp->GetTagByID($tagid);
if ($tag->ID == 0) {
    $tag->Type = (int) GetVars('type', 'GET');
}
?>
<div id="divMain">
    <div class="divHeader2">
        <?php echo $typetitle . $lang['msg']['tag_edit']; ?></div>
    <div class="SubMenu">
        <?php
        HookFilterPlugin('Filter_Plugin_Tag_Edit_SubMenu');
        ?>
    </div>
    <div id="divMain2" class="edit tag_edit">
        <form id="edit" name="edit" method="post" action="#">
            <input id="edtID" name="ID" type="hidden" value="
            <?php
            echo $tag->ID;
            ?>
            " />
            <input id="edtType" name="Type" type="hidden" value="
            <?php
            echo $tag->Type;
            ?>
            " />
            <p>
                <span class="title">
                    <?php echo $lang['msg']['name']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtName" class="edit" size="40" name="Name" maxlength="<?php echo $option['ZC_TAGS_NAME_MAX']; ?>" type="text" value="<?php echo $tag->Name; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['alias']; ?>:</span>
                <br />
                <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $tag->Alias; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['template']; ?>:</span>
                <br />
                <select class="edit" size="1" name="Template" id="cmbTemplate">
                    <?php echo OutputOptionItemsOfTemplate($tag->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'tag')); ?></select>
            </p>
            <p>
                <span class='title'>
                    <?php echo $lang['msg']['intro']; ?>:</span>
                <br />
                <textarea cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;"><?php echo htmlspecialchars($tag->Intro); ?></textarea>
            </p>
            <p>
                <label>
                    <span class="title">
                        <?php echo $lang['msg']['add_to_navbar']; ?>:</span>
                    <input type="text" name="AddNavbar" id="edtAddNavbar" value="<?php echo (int) $zbp->CheckItemToNavbar('tag', $tag->ID); ?>" class="checkbox" /></label>
            </p>
            <div id='response' class='editmod2'>
                <?php
                HookFilterPlugin('Filter_Plugin_Tag_Edit_Response');
                ?>
            </div>
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit']; ?>" id="btnPost" onclick="return checkInfo();" /></p>
        </form>
        <script>
            function checkInfo() {
                document.getElementById("edit").action = "<?php echo BuildSafeCmdURL('act=TagPst'); ?>";

                if (!$("#edtName").val()) {
                    alert("<?php echo $lang['error']['72']; ?>");
                    return false
                }

            }
        </script>
        <script>
            ActiveLeftMenu("aTagMng");
        </script>
        <script>
            AddHeaderIcon("<?php echo $zbp->host . 'zb_system/image/common/tag_32.png'; ?>");
        </script>
        <script>
            AddHeaderFontIcon("icon-tags-fill");
        </script>
    </div>
</div>

<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

RunTime();
