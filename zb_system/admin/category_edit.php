<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version 2.0 2013-07-05
 */
require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->CheckGzip();
$zbp->Load();

$action = 'CategoryEdt';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$blogtitle = $lang['msg']['category_edit'];

require ZBP_PATH . 'zb_system/admin/admin_header.php';
require ZBP_PATH . 'zb_system/admin/admin_top.php';

?>
<?php

$cateid = null;
if (isset($_GET['id'])) {
    $cateid = (int) GetVars('id', 'GET');
} else {
    $cateid = 0;
}

$cate = $zbp->GetCategoryByID($cateid);

$p = null;

$p .= '<option value="0">' . $lang['msg']['none'] . '</option>';

foreach ($zbp->categoriesbyorder as $k => $v) {
    if ($v->ID == $cate->ID) {
        continue;
    }
    if ($cate->ID > 0 && $v->RootID == $cate->ID) {
        continue;
    }
    if ($cate->RootID > 0) {
        if ($v->RootID == $cate->RootID && $v->Level >= $cate->Level) {
            continue;
        }
    }
    if ($v->Level < 3) {
        $p .= '<option ' . ($v->ID == $cate->ParentID ? 'selected="selected"' : '') . ' value="' . $v->ID . '">' . $v->SymbolName . '</option>';
    }
}

?>
<div id="divMain">
    <div class="divHeader2">
        <?php echo $lang['msg']['category_edit']?></div>
    <div class="SubMenu">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Edit_SubMenu'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
    </div>
    <div id="divMain2" class="edit category_edit">
        <form id="edit" name="edit" method="post" action="#">
            <input id="edtID" name="ID" type="hidden" value="<?php echo $cate->ID; ?>" />
            <p>
                <span class="title">
                    <?php echo $lang['msg']['name']?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtName" class="edit" size="40" name="Name" maxlength="<?php echo $option['ZC_CATEGORY_NAME_MAX']; ?>" type="text" value="<?php echo $cate->Name; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['alias']?>:</span>
                <br />
                <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $cate->Alias; ?>" /></p>

            <p>
                <span class="title">
                    <?php echo $lang['msg']['order']?>:</span>
                <br />
                <input id="edtOrder" class="edit" size="40" name="Order" type="text" value="<?php echo $cate->Order; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['parent_category']?>:</span>
                <br />
                <select id="edtParentID" name="ParentID" class="edit" size="1">
                    <?php echo $p; ?></select>
            </p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['template']?>:</span>
                <br />
                <select class="edit" size="1" name="Template" id="cmbTemplate">
                    <?php echo OutputOptionItemsOfTemplate($cate->Template); ?></select>
                <input type="hidden" name="edtTemplate" id="edtTemplate" value="<?php echo $cate->Template; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['category_aritles_default_template']?>:</span>
                <br />
                <select class="edit" size="1" name="LogTemplate" id="cmbLogTemplate">
                    <?php echo OutputOptionItemsOfTemplate($cate->LogTemplate); ?></select>
            </p>
            <p>
                <span class='title'>
                    <?php echo $lang['msg']['intro']?>:</span>
                <br/>
                <textarea  cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;"><?php echo htmlspecialchars($cate->Intro); ?></textarea>
            </p>
            <p>
                <label>
                    <span class="title">
                        <?php echo $lang['msg']['add_to_navbar']?>:</span>
                    <input type="text" name="AddNavbar" id="edtAddNavbar" value="<?php echo (int) $zbp->CheckItemToNavbar('category', $cate->ID)?>" class="checkbox" />
                </label>
            </p>
            <!-- 1号输出接口 -->
            <div id='response' class='editmod2'>
                <?php foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Edit_Response'] as $fpname => &$fpsignal) {
    $fpname();
}?>
            </div>
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" id="btnPost" onclick="return checkInfo();" /></p>
        </form>
        <script type="text/javascript">
function checkInfo(){
  document.getElementById("edit").action="<?php echo BuildSafeCmdURL('act=CategoryPst'); ?>";

  if(!$("#edtName").val()){
    alert("<?php echo $lang['error']['72']?>");
    return false
  }

}
    </script>
        <script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script>
        <script type="text/javascript">AddHeaderIcon("<?php echo $zbp->host . 'zb_system/image/common/category_32.png'; ?>");</script>
    </div>
</div>

<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

RunTime();
