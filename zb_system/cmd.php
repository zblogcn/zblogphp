<?php
require './function/c_system_base.php';
$zbp->Load();
$action = GetVars('act', 'GET');

if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

foreach ($GLOBALS['hooks']['Filter_Plugin_Cmd_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

switch ($action) {
    case 'login':
        if ($zbp->user->ID > 0 && GetVars('redirect', 'GET')) {
            Redirect(GetVars('redirect', 'GET'));
        }
        if ($zbp->CheckRights('admin')) {
            Redirect('cmd.php?act=admin');
        }
        if ($zbp->user->ID == 0 && GetVars('redirect', 'GET')) {
            setcookie("redirect", GetVars('redirect', 'GET'), 0, $zbp->cookiespath);
        }
        Redirect('login.php');
        break;
    case 'logout':
        Logout();
        Redirect('../');
        break;
    case 'admin':
        Redirect('admin/index.php?act=admin');
        break;
    case 'verify':
        if (VerifyLogin()) {
            if ($zbp->user->ID > 0 && GetVars('redirect', 'COOKIE')) {
                Redirect(GetVars('redirect', 'COOKIE'));
            }
            Redirect('admin/index.php?act=admin');
        } else {
            Redirect('../');
        }
        break;
    case 'search':
        $q = rawurlencode(trim(strip_tags(GetVars('q', 'POST'))));
        Redirect($zbp->searchurl . '?q=' . $q);
        break;
    case 'misc':
        include './function/c_system_misc.php';
        ob_clean();

        $miscType = GetVars('type', 'GET');

        foreach ($GLOBALS['hooks']['Filter_Plugin_Misc_Begin'] as $fpname => &$fpsignal) {
            $fpname($miscType);
        }

        switch ($miscType) {
            case 'statistic':
                if (!$zbp->CheckRights('admin')) {
                    echo $zbp->ShowError(6, __FILE__, __LINE__);
                    die();
                }
                misc_statistic();
                break;
            case 'updateinfo':
                if (!$zbp->CheckRights('root')) {
                    echo $zbp->ShowError(6, __FILE__, __LINE__);
                    die();
                }
                misc_updateinfo();
                break;
            case 'showtags':
                if (!$zbp->CheckRights('ArticleEdt')) {
                    Http404();
                    die();
                }
                misc_showtags();
                break;
            case 'vrs':
                if (!$zbp->CheckRights('misc')) {
                    $zbp->ShowError(6, __FILE__, __LINE__);
                }
                misc_viewrights();
                break;
            case 'phpinfo':
                if (!$zbp->CheckRights('root')) {
                    echo $zbp->ShowError(6, __FILE__, __LINE__);
                    die();
                }
                misc_phpinfo();
                break;
            default:
                break;
        }

        break;
    case 'cmt':
        $die = false;
        if (GetVars('isajax', 'POST')) {
            // 兼容老版本的评论前端
            Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'RespondError', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        } elseif (GetVars('format', 'POST') == "json") {
            // 1.5之后的评论以json形式加载给前端
            Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);
            $die = true;
        }

        PostComment();
        $zbp->BuildModule();
        $zbp->SaveCache();

        if ($die) {
            exit;
        }

        Redirect(GetVars('HTTP_REFERER', 'SERVER'));

        break;
    case 'getcmt':
        ViewComments((int) GetVars('postid', 'GET'), (int) GetVars('page', 'GET'));
        die();
    break;
    case 'ArticleEdt':
        Redirect('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ArticleDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ArticleMng');
        break;
    case 'ArticleMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ArticlePst':
        PostArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ArticleMng');
        break;
    case 'PageEdt':
        Redirect('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PageDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=PageMng');
        break;
    case 'PageMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PagePst':
        PostPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=PageMng');
        break;
    case 'CategoryMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryEdt':
        Redirect('admin/category_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryPst':
        PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=CategoryMng');
        break;
    case 'CategoryDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=CategoryMng');
        break;
    case 'CommentDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentChk':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        CheckComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentBat':
        if (isset($_POST['id']) == false) {
            Redirect($_SERVER["HTTP_REFERER"]);
        }
        BatchComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberEdt':
        Redirect('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberNew':
        Redirect('admin/member_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'MemberPst':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=MemberMng');
        break;
    case 'MemberDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        if (DelMember()) {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        } else {
            $zbp->SetHint('bad');
        }
            Redirect('cmd.php?act=MemberMng');
        break;
    case 'UploadMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'UploadPst':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        PostUpload();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=UploadMng');
        break;
    case 'UploadDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelUpload();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=UploadMng');
        break;
    case 'TagMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'TagEdt':
        Redirect('admin/tag_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'TagPst':
        PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=TagMng');
        break;
    case 'TagDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=TagMng');
        break;
    case 'PluginMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
            $zbp->BuildModule();
            $zbp->SaveCache();
        }
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PluginDis':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        UninstallPlugin(GetVars('name', 'GET'));
        DisablePlugin(GetVars('name', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=PluginMng');
        break;
    case 'PluginEnb':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        $install = '&install=';
        $install .= EnablePlugin(GetVars('name', 'GET'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=PluginMng' . $install);
        break;
    case 'ThemeMng':
        if (GetVars('install', 'GET')) {
            InstallPlugin(GetVars('install', 'GET'));
        }
        if (GetVars('install', 'GET') !== null) {
            $zbp->BuildTemplate();
        }
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ThemeSet':
        $install = '&install=';
        $install .= SetTheme(GetVars('theme', 'POST'), GetVars('style', 'POST'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ThemeMng' . $install);
        break;
    case 'SidebarSet':
        SetSidebar();
        $zbp->BuildModule();
        $zbp->SaveCache();
        break;
    case 'ModuleEdt':
        Redirect('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ModulePst':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        PostModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ModuleMng');
        break;
    case 'ModuleDel':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        DelModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ModuleMng');
        break;
    case 'ModuleMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'SettingMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'SettingSav':
        if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
            $zbp->ShowError(5, __FILE__, __LINE__);
            die();
        }
        SaveSetting();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=SettingMng');
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
