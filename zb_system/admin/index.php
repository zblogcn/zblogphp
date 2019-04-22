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

$action = GetVars('act', 'GET');

$admin_action_add = null;
$admin_function = null;

if (($action == '') || ($action == null)) {
    $action = 'admin';
}

if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

switch ($action) {
    case 'ArticleMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_ArticleMng';
            $blogtitle = $lang['msg']['article_manage'];
        }
        break;
    case 'PageMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_PageMng';
            $blogtitle = $lang['msg']['page_manage'];
        }
        break;
    case 'CategoryMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_CategoryMng';
            $blogtitle = $lang['msg']['category_manage'];
        }
        break;
    case 'CommentMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_CommentMng';
            $blogtitle = $lang['msg']['comment_manage'];
        }
        break;
    case 'MemberMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_MemberMng';
            $blogtitle = $lang['msg']['member_manage'];
        }
        break;
    case 'UploadMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_UploadMng';
            $blogtitle = $lang['msg']['upload_manage'];
        }
        break;
    case 'TagMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_TagMng';
            $blogtitle = $lang['msg']['tag_manage'];
        }
        break;
    case 'PluginMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_PluginMng';
            $blogtitle = $lang['msg']['plugin_manage'];
        }
        break;
    case 'ThemeMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_ThemeMng';
            $blogtitle = $lang['msg']['theme_manage'];
        }
        break;
    case 'ModuleMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_ModuleMng';
            $blogtitle = $lang['msg']['module_manage'];
        }
        break;
    case 'SettingMng':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_SettingMng';
            $blogtitle = $lang['msg']['settings'];
        }
        break;
    case 'admin':
        if (is_null($admin_function)) {
            $admin_function = 'Admin_SiteInfo';
            $blogtitle = $lang['msg']['dashboard'];
        }
        break;
    case $admin_action_add:
        break;
    default:
        $zbp->ShowError(6, __FILE__, __LINE__);
        die();
        break;
}

require ZBP_PATH . 'zb_system/admin/admin_header.php';
require ZBP_PATH . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php
$admin_function();
?>
</div>
<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_End'] as $fpname => &$fpsignal) {
    $fpname();
}

RunTime();
