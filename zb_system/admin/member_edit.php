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

$action = '';
if (GetVars('act', 'GET') == 'MemberEdt') {
    $action = 'MemberEdt';
}

if (GetVars('act', 'GET') == 'MemberNew') {
    $action = 'MemberNew';
}

if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}
$blogtitle = $lang['msg']['member_edit'];

require ZBP_PATH . 'zb_system/admin/admin_header.php';
require ZBP_PATH . 'zb_system/admin/admin_top.php';

$memberid = null;
if (isset($_GET['id'])) {
    $memberid = (int) GetVars('id', 'GET');
} else {
    $memberid = 0;
}

if (!$zbp->CheckRights('MemberAll')) {
    if ((int) $memberid != (int) $zbp->user->ID) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }
}

$member = $zbp->GetMemberByID($memberid);

?>
<div id="divMain">
    <div class="divHeader2">
        <?php echo $lang['msg']['member_edit']; ?></div>
    <div class="SubMenu">
        <?php
        foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Edit_SubMenu'] as $fpname => &$fpsignal) {
            $fpname();
        }
        ?>
    </div>
    <div id="divMain2" class="edit member_edit">
        <form id="edit" name="edit" method="post" action="#">
            <input id="edtID" name="ID" type="hidden" value="<?php echo $member->ID; ?>" />
            <p>
                <span class="title">
                    <?php echo $lang['msg']['member_level']; ?>:</span>
                <br />
                <select class="edit" size="1" name="Level" id="cmbLevel">
                    <?php echo OutputOptionItemsOfMemberLevel($member->Level); ?></select>
    <?php
    if ($zbp->CheckRights('MemberAll') && $zbp->user->ID != $member->ID) {
        ?>
                    &nbsp;(
                    <span class="title">
            <?php echo $lang['msg']['status']; ?>:</span>
                        <input id="user_status_0" name="Status" type="radio" class="radio" value="0" <?php echo $member->Status == 0 ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="user_status_0"><?php echo $lang['user_status_name'][0]; ?></label>
                    &nbsp;&nbsp;
                        <input id="user_status_1" name="Status" type="radio" class="radio" value="1" <?php echo $member->Status == 1 ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="user_status_1"><?php echo $lang['user_status_name'][1]; ?></label>
                    &nbsp;&nbsp;
                        <input id="user_status_2" name="Status" type="radio" class="radio" value="2" <?php echo $member->Status == 2 ? 'checked="checked"' : ''; ?> />&nbsp;
                        <label for="user_status_2"><?php echo $lang['user_status_name'][2]; ?></label>
                    )
                    <?php
    }
    ?>
            </p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['name']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtName" class="edit" size="40" name="Name" placeholder="<?php echo $zbp->lang['error']['77']; ?>" maxlength="<?php echo $zbp->option['ZC_USERNAME_MAX']; ?>" type="text" value="<?php echo $member->Name; ?>" 
                    <?php
                    if (!$zbp->CheckRights('MemberAll')) {
                        echo 'readonly="readonly"';
                    }
                    ?>
                                                                                                                                         /></p>
            <p>
                <span class='title'>
                    <?php echo $lang['msg']['password']; ?>:</span>
                <br />
                <input id="edtPassword" class="edit" size="40" name="Password" type="password" value="" autocomplete="off" />
            </p>
            <p>
                <span class='title'>
                    <?php echo $lang['msg']['re_password']; ?>:</span>
                <br />
                <input id="edtPasswordRe" class="edit" size="40" name="PasswordRe" type="password" value="" autocomplete="off" />
            </p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['email']; ?>:</span>
                <span class="star">(*)</span>
                <br />
                <input id="edtEmail" class="edit" size="40" name="Email" type="text" value="<?php echo $member->Email; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['alias']; ?>:</span>
                <br />
                <input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $member->Alias; ?>" /></p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['homepage']; ?>:</span>
                <br />
                <input id="edtHomePage" class="edit" size="40" name="HomePage" type="text" value="<?php echo $member->HomePage; ?>" /></p>
            <p>
                <span class='title'>
                    <?php echo $lang['msg']['intro']; ?>:</span>
                <br />
                <textarea cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;"><?php echo htmlspecialchars($member->Intro); ?></textarea>
            </p>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['template']; ?>:</span>
                <br />
                <select class="edit" size="1" name="Template" id="cmbTemplate">
                    <?php echo OutputOptionItemsOfTemplate($member->Template, array('single', '404', 'module', 'search', 'lm-'), array('list', 'author')); ?></select>
            </p>
            <div id='response' class='editmod2'>
                <?php
                foreach ($GLOBALS['hooks']['Filter_Plugin_Member_Edit_Response'] as $fpname => &$fpsignal) {
                    $fpname();
                }
                ?>
            </div>
            <p>
                <span class="title">
                    <?php echo $lang['msg']['default_avatar']; ?>:</span>
                &nbsp;
                <br />
                <?php echo $member->Avatar; ?></p>
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit']; ?>" id="btnPost" onclick="return checkInfo();" /></p>
        </form>
        <script>
            function checkInfo() {
                document.getElementById("edit").action = "<?php echo BuildSafeCmdURL('act=MemberPst'); ?>";


                if (!$("#edtEmail").val()) {
                    alert("<?php echo $lang['error']['29']; ?>");
                    return false
                }


                if (!$("#edtName").val()) {
                    alert("<?php echo $lang['error']['72']; ?>");
                    return false
                }

                if ($("#edtPassword").val() !== $("#edtPasswordRe").val()) {
                    alert("<?php echo $lang['error']['73']; ?>");
                    return false
                }

            }
        </script>
        <script>
            ActiveLeftMenu("aMemberMng");
        </script>
        <script>
            AddHeaderFontIcon("icon-person-fill");
        </script>
    </div>
</div>

<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

RunTime();
