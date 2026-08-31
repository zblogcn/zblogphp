<?php
if (!defined('ZBP_PATH')) {
  exit('Access denied');
}
require __DIR__ . "/admin2_view.php";
require __DIR__ . "/admin2_misc.php";

$zbp->ismanage = true;
$zbp->isbackend_ui = true;

// admin2 后台主要函数 管理页面
function zbp_admin2_GetActionInfo($action)
{
  global $lang;
  $main = (object) array(
    "Title" => "",
    "Header" => "",
    "HeaderIcon" => "",
    "SubMenu" => "",
    "ActiveTopMenu" => "",
    "ActiveLeftMenu" => "",
    "Action" => $action,
  );
  switch ($action) {
    case 'admin':
      // $admin_function = 'Admin_SiteInfo';
      $blogtitle = $lang['msg']['dashboard'];
      $main->Content = zbp_admin2_SiteInfo();
      $main->Header = $lang['msg']['info_intro'];
      $main->HeaderIcon = 'icon-house-door-fill';
      $main->ActiveLeftMenu = 'aDashboard';
      $main->ActiveTopMenu = 'topmenu_dashboard';
      $main->Title = $blogtitle;
      break;
    case 'ArticleMng':
      // $admin_function = 'Admin_ArticleMng';
      $blogtitle = $lang['msg']['article_manage'];
      $main->ActiveLeftMenu = 'aArticleMng';
      $main->Content = zbp_admin2_ArticleMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-stickies';
      $main->Title = $blogtitle;
      break;
    case 'PageMng':
      // $admin_function = 'Admin_PageMng';
      $blogtitle = $lang['msg']['page_manage'];
      $main->ActiveLeftMenu = 'aPageMng';
      $main->Content = zbp_admin2_PageMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-stickies-fill';
      $main->Title = $blogtitle;
      break;
    case 'CategoryMng':
      // $admin_function = 'Admin_CategoryMng';
      $blogtitle = $lang['msg']['category_manage'];
      $main->ActiveLeftMenu = 'aCategoryMng';
      $main->Content = zbp_admin2_CategoryMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-folder-fill';
      $main->Title = $blogtitle;
      break;
    case 'TagMng':
      // $admin_function = 'Admin_TagMng';
      $blogtitle = $lang['msg']['tag_manage'];
      $main->ActiveLeftMenu = 'aTagMng';
      $main->Content = zbp_admin2_TagMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-tags-fill';
      $main->Title = $blogtitle;
      break;
    case 'CommentMng':
      // $admin_function = 'Admin_CommentMng';
      $blogtitle = $lang['msg']['comment_manage'];
      $main->ActiveLeftMenu = 'aCommentMng';
      $main->Content = zbp_admin2_CommentMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-chat-text-fill';
      $main->Title = $blogtitle;
      break;
    case 'UploadMng':
      // $admin_function = 'Admin_UploadMng';
      $blogtitle = $lang['msg']['upload_manage'];
      $main->ActiveLeftMenu = 'aUploadMng';
      $main->Content = zbp_admin2_UploadMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-inboxes-fill';
      $main->Title = $blogtitle;
      break;
    case 'MemberMng':
      // $admin_function = 'Admin_MemberMng';
      $blogtitle = $lang['msg']['member_manage'];
      $main->ActiveLeftMenu = 'aMemberMng';
      $main->Content = zbp_admin2_MemberMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-people-fill';
      $main->Title = $blogtitle;
      break;
    case 'ModuleMng':
      // $admin_function = 'Admin_ModuleMng';
      $blogtitle = $lang['msg']['module_manage'];
      $main->ActiveLeftMenu = 'aModuleMng';
      $main->Content = zbp_admin2_ModuleMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-3x3-gap-fill';
      $main->Title = $blogtitle;
      break;
    case 'ThemeMng':
      // $admin_function = 'Admin_ThemeMng';
      $blogtitle = $lang['msg']['theme_manage'];
      $main->ActiveLeftMenu = 'aThemeMng';
      $main->Content = zbp_admin2_ThemeMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-1x2-fill';
      $main->Title = $blogtitle;
      break;
    case 'PluginMng':
      // $admin_function = 'Admin_PluginMng';
      $blogtitle = $lang['msg']['plugin_manage'];
      $main->ActiveLeftMenu = 'aPluginMng';
      $main->Content = zbp_admin2_PluginMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-puzzle-fill';
      $main->Title = $blogtitle;
      break;
    case 'SettingMng':
      // $admin_function = 'Admin_SettingMng';
      $blogtitle = $lang['msg']['settings'];
      $main->ActiveLeftMenu = 'aSettingMng';
      $main->Content = zbp_admin2_SettingMng();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-gear-fill';
      $main->Title = $blogtitle;
      break;
    case 'ArticleEdt':
      $blogtitle = $lang['msg']['article_edit'];
      if (empty(GetVars('id'))) {
        $main->ActiveLeftMenu = 'aArticleEdt';
      } else {
        $main->ActiveLeftMenu = 'aArticleMng';
      }
      $main->Content = zbp_admin2_ArticleEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-pencil-square-fill';
      $main->Title = $blogtitle;
      break;
    case 'CategoryEdt':
      $blogtitle = $lang['msg']['category_edit'];
      $main->ActiveLeftMenu = 'aCategoryMng';
      $main->Content = zbp_admin2_CategoryEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-folder-fill';
      $main->Title = $blogtitle;
      break;

    case 'TagEdt':
      $blogtitle =  $lang['msg']['tag_edit'];
      $main->ActiveLeftMenu = 'aTagMng';
      $main->Content = zbp_admin2_TagEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-tags-fill';
      $main->Title = $blogtitle;
      break;

    case 'MemberNew':
    case 'MemberEdt':
      $blogtitle = $lang['msg']['member_edit'];
      $main->ActiveLeftMenu = 'aMemberMng';
      $main->Content = zbp_admin2_MemberEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-person-fill';
      $main->Title = $blogtitle;
      break;

    case 'ModuleEdt':
      $blogtitle = $lang['msg']['module_edit'];
      $main->ActiveLeftMenu = 'aModuleMng';
      $main->Content = zbp_admin2_ModuleEdt();
      $main->Header = $blogtitle;
      $main->HeaderIcon = 'icon-grid-fill';
      $main->Title = $blogtitle;
      break;

    default:
      break;
  }

  //返回原SubMenu接口设置的菜单
  $main->SubMenu = zbp_admin2_GenSubMenu($action);
  return $main;
}

