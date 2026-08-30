<?php
if (!defined('ZBP_PATH')) {
  exit('Access denied');
}
require __DIR__ . "/admin2_view.php";
require __DIR__ . "/admin2_misc.php";

$zbp->ismanage = true;

// 管理页面
function zbp_admin2_GetActionInfo($action, $lang)
{
  $main = (object) array(
    "Header" => "",
    "HeaderIcon" => "",
    "SubMenu" => "",
    "ActiveTopMenu" => "",
    "ActiveLeftMenu" => "",
  );
  switch ($action) {
    case 'admin':
      // $admin_function = 'Admin_SiteInfo';
      $blogtitle = $lang['msg']['dashboard'];
      $main->Content = zbp_admin2_SiteInfo();
      $main->Header = $lang['msg']['info_intro'];
      $main->HeaderIcon = 'icon-house-door-fill';
      break;
    case 'ArticleMng':
      // $admin_function = 'Admin_ArticleMng';
      $blogtitle = $lang['msg']['article_manage'];
      $main->ActiveLeftMenu = 'aArticleMng';
      $main->Content = zbp_admin2_ArticleMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-stickies';
      break;
    case 'PageMng':
      // $admin_function = 'Admin_PageMng';
      $blogtitle = $lang['msg']['page_manage'];
      $main->ActiveLeftMenu = 'aPageMng';
      $main->Content = zbp_admin2_PageMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-stickies-fill';
      break;
    case 'CategoryMng':
      // $admin_function = 'Admin_CategoryMng';
      $blogtitle = $lang['msg']['category_manage'];
      $main->ActiveLeftMenu = 'aCategoryMng';
      $main->Content = zbp_admin2_CategoryMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-folder-fill';
      break;
    case 'TagMng':
      // $admin_function = 'Admin_TagMng';
      $blogtitle = $lang['msg']['tag_manage'];
      $main->ActiveLeftMenu = 'aTagMng';
      $main->Content = zbp_admin2_TagMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-tags-fill';
      break;
    case 'CommentMng':
      // $admin_function = 'Admin_CommentMng';
      $blogtitle = $lang['msg']['comment_manage'];
      $main->ActiveLeftMenu = 'aCommentMng';
      $main->Content = zbp_admin2_CommentMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-chat-text-fill';
      break;
    case 'UploadMng':
      // $admin_function = 'Admin_UploadMng';
      $blogtitle = $lang['msg']['upload_manage'];
      $main->ActiveLeftMenu = 'aUploadMng';
      $main->Content = zbp_admin2_UploadMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-inboxes-fill';
      break;
    case 'MemberMng':
      // $admin_function = 'Admin_MemberMng';
      $blogtitle = $lang['msg']['member_manage'];
      $main->ActiveLeftMenu = 'aMemberMng';
      $main->Content = zbp_admin2_MemberMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-people-fill';
      break;
    case 'ModuleMng':
      // $admin_function = 'Admin_ModuleMng';
      $blogtitle = $lang['msg']['module_manage'];
      $main->ActiveLeftMenu = 'aModuleMng';
      $main->Content = zbp_admin2_ModuleMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-3x3-gap-fill';
      break;
    case 'ThemeMng':
      // $admin_function = 'Admin_ThemeMng';
      $blogtitle = $lang['msg']['theme_manage'];
      $main->ActiveLeftMenu = 'aThemeMng';
      $main->Content = zbp_admin2_ThemeMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-1x2-fill';
      break;
    case 'PluginMng':
      // $admin_function = 'Admin_PluginMng';
      $blogtitle = $lang['msg']['plugin_manage'];
      $main->ActiveLeftMenu = 'aPluginMng';
      $main->Content = zbp_admin2_PluginMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-puzzle-fill';
      break;
    case 'SettingMng':
      // $admin_function = 'Admin_SettingMng';
      $blogtitle = $lang['msg']['settings'];
      $main->ActiveLeftMenu = 'aSettingMng';
      $main->Content = zbp_admin2_SettingMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-gear-fill';
      break;
    default:
      return zbp_admin2_GetEditInfo($action, $lang);
      break;
  }
  $main->SubMenu = zbp_admin2_GenSubMenu($action);
  return array($blogtitle, $main);
}

// 编辑页面
function zbp_admin2_GetEditInfo($action, $lang)
{
  $main = (object) array(
    "Header" => "",
    "HeaderIcon" => "",
    "SubMenu" => "",
    "ActiveTopMenu" => "",
    "ActiveLeftMenu" => "",
  );

  switch ($action) {
    case 'CategoryEdt':
      $blogtitle = $lang['msg']['category_edit'];
      $main->ActiveLeftMenu = 'aCategoryMng';
      $main->Content = zbp_admin2_CategoryEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-folder-fill';
      break;

    case 'TagEdt':
      $blogtitle =  $lang['msg']['tag_edit'];
      $main->ActiveLeftMenu = 'aTagMng';
      $main->Content = zbp_admin2_TagEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-tags-fill';
      break;

    case 'MemberNew':
    case 'MemberEdt':
      $blogtitle = $lang['msg']['member_edit'];
      $main->ActiveLeftMenu = 'aMemberMng';
      $main->Content = zbp_admin2_MemberEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-person-fill';
      break;

    case 'ModuleEdt':
      $blogtitle = $lang['msg']['module_edit'];
      $main->ActiveLeftMenu = 'aModuleMng';
      $main->Content = zbp_admin2_ModuleEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-fill';
      break;

    default:
      break;
  }

  $main->SubMenu = zbp_admin2_GenSubMenu($action);
  return array($blogtitle, $main);
}
