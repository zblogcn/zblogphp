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
        if (!empty($zbp->user->ID) && GetVars('redirect', 'GET')) {
            Redirect(GetVars('redirect', 'GET'));
        }
        if ($zbp->CheckRights('admin')) {
            Redirect('cmd.php?act=admin');
        }
        if (empty($zbp->user->ID) && GetVars('redirect', 'GET')) {
            setcookie("redirect", GetVars('redirect', 'GET'), 0, $zbp->cookiespath);
        }
        Redirect('login.php');
        break;
    case 'logout':
        CheckIsRefererValid();
        Logout();
        Redirect('../');
        break;
    case 'admin':
        Redirect('admin/index.php?act=admin');
        break;
    case 'verify':
        /*
         * 考虑兼容原因，此处不加CSRF验证。logout加的原因是主题的退出无大碍。
         */
        if (VerifyLogin()) {
            if (!empty($zbp->user->ID) && GetVars('redirect', 'COOKIE')) {
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
                CheckIsRefererValid();
                if (!$zbp->CheckRights('admin')) {
                    echo $zbp->ShowError(6, __FILE__, __LINE__);
                    die();
                }
                misc_statistic();
                break;
            case 'updateinfo':
                CheckIsRefererValid();
                if (!$zbp->CheckRights('root')) {
                    echo $zbp->ShowError(6, __FILE__, __LINE__);
                    die();
                }
                misc_updateinfo();
                break;
            case 'showtags':
                $zbp->csrfExpiration = 48;
                CheckIsRefererValid();
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
                misc_phpif();
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
        CheckIsRefererValid();
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
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostArticle();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        RedirectByScript('cmd.php?act=ArticleMng');
        break;
    case 'PageEdt':
        Redirect('admin/edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'PageDel':
        CheckIsRefererValid();
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
        $zbp->csrfExpiration = 48;
        CheckIsRefererValid();
        PostPage();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        echo '<script>localStorage.removeItem("zblogphp_article_" + decodeURIComponent(' . urlencode(GetVars('ID', 'POST')) . '));</script>';
        RedirectByScript('cmd.php?act=PageMng');
        break;
    case 'CategoryMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryEdt':
        Redirect('admin/category_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'CategoryPst':
        CheckIsRefererValid();
        PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=CategoryMng');
        break;
    case 'CategoryDel':
        CheckIsRefererValid();
        DelCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=CategoryMng');
        break;
    case 'CommentDel':
        CheckIsRefererValid();
        DelComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentChk':
        CheckIsRefererValid();
        CheckComment();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect($_SERVER["HTTP_REFERER"]);
        break;
    case 'CommentBat':
        CheckIsRefererValid();
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
        CheckIsRefererValid();
        PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=MemberMng');
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
            Redirect('cmd.php?act=MemberMng');
        break;
    case 'UploadMng':
        Redirect('admin/index.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'UploadPst':
        CheckIsRefererValid();
        PostUpload();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=UploadMng');
        break;
    case 'UploadDel':
        CheckIsRefererValid();
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
        CheckIsRefererValid();
        PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=TagMng');
        break;
    case 'TagDel':
        CheckIsRefererValid();
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
        CheckIsRefererValid();
        $disableResult = DisablePlugin(GetVars('name', 'GET'));
        if (is_object($disableResult)) {
            // 本来应该用ShowError的，但是不太方便，算了
            // 姑且先用SetHint放在这里
            $hint = $lang['error']['84'];
            $hint = str_replace('%s', "【$disableResult->name ($disableResult->id)】", $hint);
            $zbp->SetHint('bad', $hint);
        } else {
            $zbp->BuildModule();
            $zbp->SaveCache();
            $zbp->SetHint('good');
        }
        Redirect('cmd.php?act=PluginMng');
        break;
    case 'PluginEnb':
        CheckIsRefererValid();
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
        CheckIsRefererValid();
        $install = '&install=';
        $install .= SetTheme(GetVars('theme', 'POST'), GetVars('style', 'POST'));
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ThemeMng' . $install);
        break;
    case 'SidebarSet':
        CheckIsRefererValid();
        SetSidebar();
        $zbp->BuildModule();
        $zbp->SaveCache();
        break;
    case 'ModuleEdt':
        Redirect('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
        break;
    case 'ModulePst':
        CheckIsRefererValid();
        PostModule();
        $zbp->BuildModule();
        $zbp->SaveCache();
        $zbp->SetHint('good');
        Redirect('cmd.php?act=ModuleMng');
        break;
    case 'ModuleDel':
        CheckIsRefererValid();
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
        CheckIsRefererValid();
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
