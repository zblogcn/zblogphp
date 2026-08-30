<?php

//自定义函数

//覆盖ResponseAdmin_TopMenu
function ResponseAdmin_TopMenu()
{
    global $zbp;
    global $topmenus;

    if ($zbp->isbackend_ui === false) {
        return _ResponseAdmin_TopMenu();
    }

    //$topmenus[] = MakeTopMenu("admin", $zbp->lang['msg']['dashboard'], $zbp->cmdurl . "?act=admin", "", "", "icon-house-door-fill");
    $topmenus[] = '<li><a href="" class="rebuild">清空缓存、编译模板</a></li>';

    //$topmenus[] = MakeTopMenu("SettingMng", @$zbp->lang['msg']['web_settings'], $zbp->cmdurl . "?act=SettingMng", "", "topmenu_setting", "icon-gear-fill");

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu'] as $fpname => &$fpsignal) {
        $fpname($topmenus);
    }

    foreach ($topmenus as $m) {
        echo $m;
    }
}



function backend_toyean_MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass = "")
{
    global $zbp;

    static $AdminLeftMenuCount = 0;
    if ($zbp->CheckRights($requireAction) == false) {
        return '';
    }

    $AdminLeftMenuCount = ($AdminLeftMenuCount + 1);
    $tmp = null;

    if ($strIconClass != "") {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span><i class=\"ico " . $strIconClass . "\"></i>" . $strName . "</span></a></li>";
    } elseif ($strImgUrl != "") {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span class=\"bgicon\" style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . "</span></a></li>";
    } else {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span><i class=\"ico icon-window-fill\"></i>" . $strName . "</span></a></li>";
    }

    return $tmp;
}

function MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass = "")
{
    global $zbp;
    if ($zbp->isbackend_ui === false) {
        return _MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass);
    }

    return backend_toyean_MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass);
}

//覆盖ResponseAdmin_LeftMenu
function ResponseAdmin_LeftMenu()
{
    global $zbp;
    global $leftmenus;

    if ($zbp->isbackend_ui === false) {
        return _ResponseAdmin_LeftMenu();
    }

    $leftmenus[] = '<li class="menutitle">概览</li>';

    $leftmenus[] = '<li id="nav_dashboard"><a id="aDashboard" href="' . $zbp->cmdurl . "?act=admin" . '" data-title="仪表盘"><span><i class="ico ico-home"></i>仪表盘</span></a></li>';

    $leftmenus[] = '<li class="menutitle">内容管理</li>';

    $leftmenus['nav_new'] = backend_toyean_MakeLeftMenu("ArticleEdt", $zbp->lang['msg']['new_article'], $zbp->cmdurl . "?act=ArticleEdt", "nav_new", "aArticleEdt", "", "ico ico-newpost");
    $leftmenus['nav_article'] = backend_toyean_MakeLeftMenu("ArticleMng", $zbp->lang['msg']['article_manage'], $zbp->cmdurl . "?act=ArticleMng", "nav_article", "aArticleMng", "", "ico ico-article");
    $leftmenus['nav_page'] = backend_toyean_MakeLeftMenu("PageMng", $zbp->lang['msg']['page_manage'], $zbp->cmdurl . "?act=PageMng", "nav_page", "aPageMng", "", "ico ico-page");

    //$leftmenus[] = "<li class='split'><hr/></li>";

    $leftmenus['nav_category'] = backend_toyean_MakeLeftMenu("CategoryMng", $zbp->lang['msg']['category_manage'], $zbp->cmdurl . "?act=CategoryMng", "nav_category", "aCategoryMng", "", "ico ico-category");
    $leftmenus['nav_tags'] = backend_toyean_MakeLeftMenu("TagMng", $zbp->lang['msg']['tag_manage'], $zbp->cmdurl . "?act=TagMng", "nav_tags", "aTagMng", "", "ico ico-tag");
    $leftmenus['nav_comment1'] = backend_toyean_MakeLeftMenu("CommentMng", $zbp->lang['msg']['comment_manage'], $zbp->cmdurl . "?act=CommentMng", "nav_comment", "aCommentMng", "", "ico ico-cmt");
    $leftmenus['nav_upload'] = backend_toyean_MakeLeftMenu("UploadMng", $zbp->lang['msg']['upload_manage'], $zbp->cmdurl . "?act=UploadMng", "nav_upload", "aUploadMng", "", "ico ico-file");

    $leftmenus[] = '<li class="menutitle">系统管理</li>';

    $leftmenus['nav_setting'] = backend_toyean_MakeLeftMenu("SettingMng", $zbp->lang['msg']['web_settings'], $zbp->cmdurl . "?act=SettingMng", "nav_setting", "aSettingMng", "", "ico ico-setting");
    $leftmenus['nav_member'] = backend_toyean_MakeLeftMenu("MemberMng", $zbp->lang['msg']['member_manage'], $zbp->cmdurl . "?act=MemberMng", "nav_member", "aMemberMng", "", "ico ico-user");

    //$leftmenus[] = "<li class='split'><hr/></li>";
    $leftmenus['nav_theme'] = backend_toyean_MakeLeftMenu("ThemeMng", $zbp->lang['msg']['theme_manage'], $zbp->cmdurl . "?act=ThemeMng", "nav_theme", "aThemeMng", "", "ico ico-theme");
    $leftmenus['nav_module'] = backend_toyean_MakeLeftMenu("ModuleMng", $zbp->lang['msg']['module_manage'], $zbp->cmdurl . "?act=ModuleMng", "nav_module", "aModuleMng", "", "ico ico-module");
    $leftmenus['nav_plugin'] = backend_toyean_MakeLeftMenu("PluginMng", $zbp->lang['msg']['plugin_manage'], $zbp->cmdurl . "?act=PluginMng", "nav_plugin", "aPluginMng", "", "ico ico-plugin");
    $leftmenus[] = '<li class="menutitle">其它</li>';

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_LeftMenu'] as $fpname => &$fpsignal) {
        $fpname($leftmenus);
    }

    foreach ($leftmenus as $m) {
        //$m = str_replace('<li', '<dd', $m);
        //$m = str_replace('</li>', '</dd>', $m);
        echo $m;
    }
}