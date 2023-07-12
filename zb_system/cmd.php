<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 */

// 标记为 CMD 运行模式
define('ZBP_IN_CMD', true);

if ((isset($_REQUEST['act']) && $_REQUEST['act'] == 'ajax') || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') == 0)) {
    define('ZBP_IN_AJAX', true);
}

require 'function/c_system_base.php';

$action = GetVars('act', 'GET');

$zbp->Load();

if (!$zbp->CheckRights($zbp->action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

HookFilterPlugin('Filter_Plugin_Cmd_Begin');

switch ($zbp->action) {
    case 'login':
        Redirect_cmd_from_args_with_loggedin(GetVars('redirect', 'GET'));
        if ($zbp->CheckRights('admin')) {
            Redirect_cmd_end('admin/index.php?act=admin');
        }
        if (empty($zbp->user->ID) && GetVars('redirect', 'GET')) {
            setcookie("redirect", GetVars('redirect', 'GET'), 0, $zbp->cookiespath);
        }
        Redirect_cmd_end('login.php');
        break;
    case 'logout':
        CheckIsRefererValid();
        Logout();
        Redirect_cmd_end('../');
        break;
    case 'admin':
        Redirect_cmd_end('admin/index.php?act=admin');
        break;
    case 'verify':
        if (VerifyLogin(true, false, false)) {
            Redirect_cmd_from_args_with_loggedin(GetVars('redirect', 'COOKIE'));
            Redirect_cmd_end('admin/index.php?act=admin');
        } else {
            Redirect_cmd_end('../');
        }
        break;
    case 'search':
        Redirect_cmd_to_search();
        break;
    case 'cmt':
        $die = false;
        if (GetVars('isajax', 'POST')) {
            // 兼容老版本的评论前端
            Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'RespondError', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        } elseif (GetVars('format', 'POST') == "json") {
            // 1.5之后的评论以json形式加载给前端
            Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        }
        PostComment();
        $zbp->BuildModule();
        $zbp->SaveCache();

        if ($die) {
            exit;
        } else {
            Redirect_cmd_end(GetVars('HTTP_REFERER', 'SERVER'));
        }
        break;
    case 'getcmt':
        ViewComments((int) GetVars('postid', 'GET'), (int) GetVars('page', 'GET'));
        break;
    case 'ArticleEdt':
        Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ArticleDel':
        CheckIsRefererValid();
        DelArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ArticleMng');
        break;
    case 'ArticleMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ArticlePst':
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        Redirect_cmd_end_by_script('cmd.php?act=ArticleMng');
        break;
    case 'PageEdt':
        Redirect_cmd_end('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PageDel':
        CheckIsRefererValid();
        DelPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=PageMng');
        break;
    case 'PageMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PagePst':
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        Redirect_cmd_end_by_script('cmd.php?act=PageMng');
        break;
    case 'CategoryMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryEdt':
        Redirect_cmd_end('admin/category_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryPst':
        CheckIsRefererValid();
        PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=CategoryMng');
        break;
    case 'CategoryDel':
        CheckIsRefererValid();
        DelCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=CategoryMng');
        break;
    case 'CommentDel':
        CheckIsRefererValid();
        DelComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentChk':
        CheckIsRefererValid();
        CheckComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentBat':
        CheckIsRefererValid();
        BatchComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberEdt':
        Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberNew':
        Redirect_cmd_end('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberPst':
        CheckIsRefererValid();
        $mem = PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
        //判断及提前跳转
        if (isset($_POST['Password'])
            && $mem->ID == $zbp->user->ID
            && !defined('ZBP_IN_AJAX')
            && !defined('ZBP_IN_API')
        ) {
            Redirect_cmd_end($zbp->host . 'zb_system/cmd.php?act=login');
        }
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=MemberMng');
        break;
    case 'MemberDel':
        CheckIsRefererValid();
        if (DelMember()) {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        } else {
            $zbp->SetHint('bad');
        }
        Redirect_cmd_end('cmd.php?act=MemberMng');
        break;
    case 'UploadMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'UploadPst':
        CheckIsRefererValid();
        if (PostUpload()) {
            $zbp->SetHint('good');
        } else {
            $zbp->SetHint('bad');
        }
        Redirect_cmd_end('cmd.php?act=UploadMng');
        break;
    case 'UploadDel':
        CheckIsRefererValid();
        DelUpload();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=UploadMng');
        break;
    case 'TagMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'TagEdt':
        Redirect_cmd_end('admin/tag_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'TagPst':
        CheckIsRefererValid();
        PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=TagMng');
        break;
    case 'TagDel':
        CheckIsRefererValid();
        DelTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=TagMng');
        break;
    case 'PluginMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
            $zbp->BuildModule();
            $zbp->SaveCache();
        }
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PluginDis':
        CheckIsRefererValid();
        $disableResult = DisablePlugin(GetVars('name', 'GET'));
        if ($disableResult == false) {
            $zbp->SetHint('bad');
        } else {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        }
        Redirect_cmd_end('cmd.php?act=PluginMng');
        break;
    case 'PluginEnb':
        CheckIsRefererValid();
        $install = '&install=';
        $install .= EnablePlugin(GetVars('name', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=PluginMng' . $install);
        break;
    case 'ThemeMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
        }
        if (GetVars('install', 'GET') !== null) {
            $zbp->BuildTemplate();
        }
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ThemeSet':
        CheckIsRefererValid();
        $install = '&install=';
        $install .= SetTheme(GetVars('theme', 'POST'), GetVars('style', 'POST'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ThemeMng' . $install);
        break;
    case 'SidebarSet':
        CheckIsRefererValid();
        SetSidebar();
        $zbp->BuildModule();
        $zbp->SaveCache();
        break;
    case 'ModuleEdt':
        Redirect_cmd_end('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ModulePst':
        CheckIsRefererValid();
        PostModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ModuleMng');
        break;
    case 'ModuleDel':
        CheckIsRefererValid();
        DelModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=ModuleMng');
        break;
    case 'ModuleMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'SettingMng':
        Redirect_cmd_end('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'SettingSav':
        CheckIsRefererValid();
        $oldHost = $zbp->option['ZC_BLOG_HOST'];
        SaveSetting();
        $zbp->BuildModule();
        $zbp->SaveCache();
        //判断及提前跳转
        if ($zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] == true) {
            if ($oldHost != $zbp->option['ZC_BLOG_HOST']) {
                Redirect_cmd_end($zbp->option['ZC_BLOG_HOST'] . 'zb_system/cmd.php?act=login');
            }
        }
        $zbp->SetHint('good');
        Redirect_cmd_end('cmd.php?act=SettingMng');
        break;
    case 'PostBat':
        BatchPost(GetVars('type', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect_cmd_end($_SERVER["HTTP_REFERER"]);
        break;
    case 'misc':
        include './function/c_system_misc.php';
        ob_clean();

        $miscType = GetVars('type', 'GET');
        $miscType = str_replace(array('<', '>', '&', ' ', '/', '"', "'"), '', $miscType);
        $miscType = ($miscType === 'php' . 'info') ? 'php_zbp_info' : $miscType;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Misc_Begin'] as $fpname => &$fpsignal) {
            $fpname($miscType);
        }

        $function = 'misc_' . $miscType;
        $function();
        break;
    case 'ajax':
        foreach ($GLOBALS['hooks']['Filter_Plugin_Cmd_Ajax'] as $fpname => &$fpsignal) {
            $fpname(GetVars('src', 'GET'));
        }

        break;
    default:
        // code...
        break;
}

HookFilterPlugin('Filter_Plugin_Cmd_End');
