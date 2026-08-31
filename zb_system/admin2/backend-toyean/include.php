<?php

function ActivePlugin_backend_toyean()
{
    global $zbp;
    $zbp->lang['msg']['first_button'] = '首页';
    $zbp->lang['msg']['prev_button'] = '上一页';
    $zbp->lang['msg']['next_button'] = '下一页';
    $zbp->lang['msg']['last_button'] = '尾页';
}

//覆盖ResponseAdmin_TopMenu
function ResponseAdmin_TopMenu()
{
    global $zbp;
    global $topmenus;

    if (false === $zbp->isbackend_ui) {
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

function backend_toyean_MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass = '')
{
    global $zbp;

    static $AdminLeftMenuCount = 0;
    if (false == $zbp->CheckRights($requireAction)) {
        return '';
    }

    $AdminLeftMenuCount = ($AdminLeftMenuCount + 1);
    $tmp = null;

    if ('' != $strIconClass) {
        $tmp = '<li id="' . $strLiId . '"><a id="' . $strAId . '" href="' . $strUrl . '" data-title="' . strip_tags($strName) . '"><span><i class="ico ' . $strIconClass . '"></i>' . $strName . '</span></a></li>';
    } elseif ('' != $strImgUrl) {
        $tmp = '<li id="' . $strLiId . '"><a id="' . $strAId . '" href="' . $strUrl . '" data-title="' . strip_tags($strName) . "\"><span class=\"bgicon\" style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . '</span></a></li>';
    } else {
        $tmp = '<li id="' . $strLiId . '"><a id="' . $strAId . '" href="' . $strUrl . '" data-title="' . strip_tags($strName) . '"><span><i class="ico icon-window-fill"></i>' . $strName . '</span></a></li>';
    }

    return $tmp;
}

function MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass = '')
{
    global $zbp;
    if (false === $zbp->isbackend_ui) {
        return _MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass);
    }

    return backend_toyean_MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass);
}

//覆盖ResponseAdmin_LeftMenu
function ResponseAdmin_LeftMenu()
{
    global $zbp;
    global $leftmenus;

    if (false === $zbp->isbackend_ui) {
        return _ResponseAdmin_LeftMenu();
    }

    $leftmenus[] = '<li class="menutitle">概览</li>';

    $leftmenus[] = '<li id="nav_dashboard"><a id="aDashboard" href="' . $zbp->cmdurl . '?act=admin' . '" data-title="仪表盘"><span><i class="ico ico-home"></i>仪表盘</span></a></li>';

    $leftmenus[] = '<li class="menutitle">内容管理</li>';

    $leftmenus['nav_new'] = backend_toyean_MakeLeftMenu('ArticleEdt', '创作', $zbp->cmdurl . '?act=ArticleEdt', 'nav_new', 'aArticleEdt', '', 'ico ico-newpost');
    $leftmenus['nav_article'] = backend_toyean_MakeLeftMenu('ArticleMng', '文章', $zbp->cmdurl . '?act=ArticleMng', 'nav_article', 'aArticleMng', '', 'ico ico-article');
    $leftmenus['nav_page'] = backend_toyean_MakeLeftMenu('PageMng', '页面', $zbp->cmdurl . '?act=PageMng', 'nav_page', 'aPageMng', '', 'ico ico-page');

    $leftmenus['nav_category'] = backend_toyean_MakeLeftMenu('CategoryMng', '分类', $zbp->cmdurl . '?act=CategoryMng', 'nav_category', 'aCategoryMng', '', 'ico ico-category');
    $leftmenus['nav_tags'] = backend_toyean_MakeLeftMenu('TagMng', '标签', $zbp->cmdurl . '?act=TagMng', 'nav_tags', 'aTagMng', '', 'ico ico-tag');
    $leftmenus['nav_comment1'] = backend_toyean_MakeLeftMenu('CommentMng', '评论', $zbp->cmdurl . '?act=CommentMng', 'nav_comment', 'aCommentMng', '', 'ico ico-cmt');
    $leftmenus['nav_upload'] = backend_toyean_MakeLeftMenu('UploadMng', '附件', $zbp->cmdurl . '?act=UploadMng', 'nav_upload', 'aUploadMng', '', 'ico ico-file');

    $leftmenus[] = '<li class="menutitle">系统管理</li>';

    $leftmenus['nav_setting'] = backend_toyean_MakeLeftMenu('SettingMng', '设置', $zbp->cmdurl . '?act=SettingMng', 'nav_setting', 'aSettingMng', '', 'ico ico-setting');
    $leftmenus['nav_member'] = backend_toyean_MakeLeftMenu('MemberMng', '用户', $zbp->cmdurl . '?act=MemberMng', 'nav_member', 'aMemberMng', '', 'ico ico-user');
    $leftmenus['nav_module'] = backend_toyean_MakeLeftMenu('ModuleMng', '模块', $zbp->cmdurl . '?act=ModuleMng', 'nav_module', 'aModuleMng', '', 'ico ico-module');
    $leftmenus['nav_theme'] = backend_toyean_MakeLeftMenu('ThemeMng', '主题', $zbp->cmdurl . '?act=ThemeMng', 'nav_theme', 'aThemeMng', '', 'ico ico-theme');

    $leftmenus['nav_plugin'] = backend_toyean_MakeLeftMenu('PluginMng', '插件', $zbp->cmdurl . '?act=PluginMng', 'nav_plugin', 'aPluginMng', '', 'ico ico-plugin');
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
