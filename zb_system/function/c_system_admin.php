<?php

/**
 * 后台管理相关
 * @package Z-BlogPHP
 * @subpackage System/Administrator 后台管理
 * @author Z-BlogPHP Team
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

require ZBP_PATH . 'zb_system/function/c_system_admin_function.php';

$zbp->ismanage = true;

//###############################################################################################################

/**
 * 后台管理显示网站信息.
 */
function Admin_SiteInfo()
{
    global $zbp;

    $echoStatistic = false;

    echo '<div class="divHeader">' . $zbp->lang['msg']['info_intro'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_SiteInfo_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';

    echo '<table class="tableFull tableBorder table_striped table_hover" id="tbStatistic"><tr><th colspan="4"  scope="col"><i class="icon-info-circle-fill"></i>&nbsp;' . $zbp->lang['msg']['site_analyze'];
    if ($zbp->CheckRights('root')) {
        echo '&nbsp;&nbsp;<a href="javascript:statistic(\'' . BuildSafeCmdURL('act=misc&type=statistic&forced=1') . '\');" id="statistic"><i class="icon-arrow-repeat" style="font-size:small; margin-right: 0.2em;"  alt="' . $zbp->lang['msg']['refresh_cache'] . '" title="' . $zbp->lang['msg']['refresh_cache'] . '"></i><small>' . $zbp->lang['msg']['refresh_cache'] . '</small>' . '</a>';
    }
    echo ' </th></tr>';

    if ((time() - (int) $zbp->cache->reload_statistic_time) > (23 * 60 * 60)) {
        echo '<script>$(document).ready(function(){ statistic(\'' . BuildSafeCmdURL('act=misc&type=statistic') . '\'); });</script>';
    } else {
        $echoStatistic = true;
        $r = $zbp->cache->reload_statistic;
        if (!$zbp->CheckRights('root')) {
            $a = explode('<!--debug_mode_moreinfo-->', $r);
            $r = $a[0];
        }
        $r = str_replace('{$zbp->user->IsGod}', '', $r);
        $r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
        $r = str_replace('{$zbp->theme}', $zbp->theme, $r);
        $r = str_replace('{$zbp->style}', $zbp->style, $r);
        $r = str_replace('{$system_environment}', GetEnvironment(), $r);
        $app = $zbp->LoadApp('plugin', 'AppCentre');
        $sv = ZC_VERSION_FULL;
        if ($app->isloaded == true && $app->IsUsed()) {
            $sv .= '; AppCentre' . $app->version;
        }
        if ($zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION) {
            $sv .= '; Db' . ZC_LAST_VERSION;
        }
        $r = str_replace('{$zbp->version}', $sv, $r);
        $r = str_replace('{$theme_version}', '(v' . $zbp->themeapp->version . ')', $r);
        if ($zbp->isdebug) {
            $r = str_replace('<!--debug_mode_note-->', "<tr><td colspan='4' style='text-align: center'>{$zbp->lang['msg']['debugging_warning']}</td></tr>", $r);
        }
        echo $r;
    }

    echo '</table>';

    echo '<table class="tableFull tableBorder table_striped table_hover" id="tbUpdateInfo"><tr><th><i class="icon-flower2"></i>&nbsp;' . $zbp->lang['msg']['latest_news'];
    if ($zbp->CheckRights('root')) {
        echo '&nbsp;&nbsp;<a href="javascript:updateinfo(\'' . BuildSafeCmdURL('act=misc&type=updateinfo') . '\');"><i class="icon-arrow-repeat" style="font-size:small; margin-right: 0.2em;" alt="' . $zbp->lang['msg']['refresh'] . '" title="' . $zbp->lang['msg']['refresh'] . '"></i><small>' . $zbp->lang['msg']['refresh'] . '</small></a>';
    }
    echo ' </th></tr>';

    if ((time() - (int) $zbp->cache->reload_updateinfo_time) > (47 * 60 * 60) && $zbp->CheckRights('root') && $echoStatistic == true) {
        echo '<script>$(document).ready(function(){ updateinfo(\'' . BuildSafeCmdURL('act=misc&type=updateinfo') . '\'); });</script>';
    } else {
        echo $zbp->cache->reload_updateinfo;
    }

    echo '</table>';

    echo '</div>';

    $s = file_get_contents($zbp->path . "zb_system/defend/thanks.html");
    $s = str_replace('Z-BlogPHP网站和程序开发', $zbp->lang['msg']['develop_intro'], $s);
    $s = str_replace('程序', $zbp->lang['msg']['program'], $s);
    $s = str_replace('界面', $zbp->lang['msg']['interface'], $s);
    $s = str_replace('支持', $zbp->lang['msg']['support'], $s);
    $s = str_replace('感谢', $zbp->lang['msg']['thanks'], $s);
    $s = str_replace('相关链接', $zbp->lang['msg']['website'], $s);
    echo $s;
    echo '<script>ActiveTopMenu("topmenu1");</script>';
    echo '<script>AddHeaderFontIcon("icon-house-door-fill");</script>';
}

//###############################################################################################################

/**
 * 后台文章管理.
 */
function Admin_ArticleMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['article_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ArticleMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    echo '<form class="search" id="search" method="post" action="#">';

    echo '<p>' . $zbp->lang['msg']['search'] . ':&nbsp;&nbsp;' . $zbp->lang['msg']['category'] . ' <select class="edit" size="1" name="category" style="width:140px;" ><option value="">' . $zbp->lang['msg']['any'] . '</option>';
    foreach ($zbp->categoriesbyorder as $id => $cate) {
        echo '<option value="' . $cate->ID . '">' . $cate->SymbolName . '</option>';
    }
    echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;' . $zbp->lang['msg']['type'] . ' <select class="edit" size="1" name="status" style="width:100px;" ><option value="">' . $zbp->lang['msg']['any'] . '</option> <option value="0" >' . $zbp->lang['post_status_name']['0'] . '</option><option value="1" >' . $zbp->lang['post_status_name']['1'] . '</option><option value="2" >' . $zbp->lang['post_status_name']['2'] . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;
    <label><input type="checkbox" name="istop" value="True"/>&nbsp;' . $zbp->lang['msg']['top'] . '</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
    echo '</form>';

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%category%}'] = GetVars('category');
    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%status%}'] = GetVars('status');
    $p->UrlRule->Rules['{%istop%}'] = (bool) GetVars('istop');
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = array();
    $w[] = array('=', 'log_Type', 0);

    if (!$zbp->CheckRights('ArticleAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }
    if (GetVars('search')) {
        $w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $search);
    }
    if (GetVars('istop')) {
        $w[] = array('<>', 'log_Istop', '0');
    }
    if (GetVars('status') !== null && GetVars('status') !== '') {
        $w[] = array('=', 'log_Status', (int) GetVars('status'));
    }
    if (GetVars('category')) {
        $w[] = array('=', 'log_CateID', GetVars('category'));
    }

    $s = '';

    if ($order_get == 'id_desc') {
        $or = array('log_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('log_ID' => 'ASC');
    } elseif ($order_get == 'cateid_desc') {
        $or = array('log_CateID' => 'DESC');
    } elseif ($order_get == 'cateid_asc') {
        $or = array('log_CateID' => 'ASC');
    } elseif ($order_get == 'authorid_desc') {
        $or = array('log_AuthorID' => 'DESC');
    } elseif ($order_get == 'authorid_asc') {
        $or = array('log_AuthorID' => 'ASC');
    } elseif ($order_get == 'posttime_desc') {
        $or = array('log_PostTime' => 'DESC');
    } elseif ($order_get == 'posttime_asc') {
        $or = array('log_PostTime' => 'ASC');
    } elseif ($order_get == 'updatetime_desc') {
        $or = array('log_UpdateTime' => 'DESC');
    } elseif ($order_get == 'updatetime_asc') {
        $or = array('log_UpdateTime' => 'ASC');
    } else {
        $or = array($zbp->manageorder => 'DESC');
    }
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    $type = null;

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ArticleMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Article'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op, $type);
    }

    $array = $zbp->GetPostList(
        $s,
        $w,
        $or,
        $l,
        $op
    );

    echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=PostBat&type=' . ZC_POST_TYPE_ARTICLE . '">';
    echo '<table border="1" class="tableFull tableBorder table_hover table_striped tableBorder-thcenter">';

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get);
    list($button_posttime_html) = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    list($button_cateid_html) = MakeOrderButton('cateid', $p->UrlRule, $order_get);
    list($button_authorid_html) = MakeOrderButton('authorid', $p->UrlRule, $order_get);

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['category'] . $button_cateid_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['author'] . $button_authorid_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['title'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . $button_posttime_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['comment'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['status'] . '</th>';
    $tableths[] = '<th></th>';
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        $tableths[] = '<th><a href="" onclick="BatchSelectAll();return false;">' . $zbp->lang['msg']['select_all'] . '</a></th>';
    }
    $tableths[] = '</tr>';

    foreach ($array as $article) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $article->ID . '</td>';
        $tabletds[] = '<td class="td10">' . $article->Category->Name . '</td>';
        $tabletds[] = '<td class="td10">' . $article->Author->Name . '</td>';
        $tabletds[] = '<td><a href="' . $article->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . $article->Title . '</td>';
        $tabletds[] = '<td class="td20">' . $article->Time() . '</td>';
        $tabletds[] = '<td class="td5">' . $article->CommNums . '</td>';
        $tabletds[] = '<td class="td5">' . ($article->IsTop ? $zbp->lang['msg']['top'] . '|' : '') . $article->StatusName . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=ArticleEdt&amp;id=' . $article->ID . '"><i class="icon-pencil-square"></i></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=ArticleDel&amp;id=' . $article->ID) . '"><i class="icon-trash"></i></a>' .
            '</td>';
        if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
            $tabletds[] = '<td class="td5 tdCenter"><input type="checkbox" id="id' . $article->ID . '" name="id[]" value="' . $article->ID . '"></td>';
        }
        $tabletds[] = '</tr>';

        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ArticleMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前post，当前行，表头
            $fpreturn = $fpname($article, $tabletds, $tableths);
        }

        $tables .= implode($tabletds);
    }

    echo implode($tableths) . $tables;

    echo '</table>';
    echo '<hr/><p class="pagebar">';

    $p->UrlRule->Rules['{%order%}'] = GetVars('order', 'GET');
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        echo '<input  style="float:right;" type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" value="' . $zbp->lang['msg']['all_del'] . '">';
    }
    echo '</p></form></div>';
    echo '<script>ActiveLeftMenu("aArticleMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-stickies");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台页面管理.
 */
function Admin_PageMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['page_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_PageMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    echo '<!--<form class="search" id="search" method="post" action="#"></form>-->';

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=PageMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = array();
    $w[] = array('=', 'log_Type', 1);

    if (!$zbp->CheckRights('PageAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }

    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('log_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('log_ID' => 'ASC');
    } elseif ($order_get == 'cateid_desc') {
        $or = array('log_CateID' => 'DESC');
    } elseif ($order_get == 'cateid_asc') {
        $or = array('log_CateID' => 'ASC');
    } elseif ($order_get == 'authorid_desc') {
        $or = array('log_AuthorID' => 'DESC');
    } elseif ($order_get == 'authorid_asc') {
        $or = array('log_AuthorID' => 'ASC');
    } elseif ($order_get == 'posttime_desc') {
        $or = array('log_PostTime' => 'DESC');
    } elseif ($order_get == 'posttime_asc') {
        $or = array('log_PostTime' => 'ASC');
    } else {
        $or = array('log_PostTime' => 'DESC');
    }
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_PageMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Page'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetPostList(
        $s,
        $w,
        $or,
        $l,
        $op
    );

    echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=PostBat&type=' . ZC_POST_TYPE_PAGE . '">';
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get);
    list($button_posttime_html) = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    list($button_cateid_html) = MakeOrderButton('cateid', $p->UrlRule, $order_get);
    list($button_authorid_html) = MakeOrderButton('authorid', $p->UrlRule, $order_get);

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['author'] . $button_authorid_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['title'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . $button_posttime_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['comment'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['status'] . '</th>';
    $tableths[] = '<th></th>';
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        $tableths[] = '<th><a href="" onclick="BatchSelectAll();return false;">' . $zbp->lang['msg']['select_all'] . '</a></th>';
    }
    $tableths[] = '</tr>';

    foreach ($array as $article) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $article->ID . '</td>';
        $tabletds[] = '<td class="td10">' . $article->Author->Name . '</td>';
        $tabletds[] = '<td><a href="' . $article->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . $article->Title . '</td>';
        $tabletds[] = '<td class="td20">' . $article->Time() . '</td>';
        $tabletds[] = '<td class="td5">' . $article->CommNums . '</td>';
        $tabletds[] = '<td class="td5">' . $article->StatusName . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=PageEdt&amp;id=' . $article->ID . '"><i class="icon-pencil-square"></i></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=PageDel&amp;id=' . $article->ID) . '"><i class="icon-trash"></i></a>' .
            '</td>';
        if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
            $tabletds[] = '<td class="td5 tdCenter"><input type="checkbox" id="id' . $article->ID . '" name="id[]" value="' . $article->ID . '"></td>';
        }
        $tabletds[] = '</tr>';

        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_PageMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前post，当前行，表头
            $fpreturn = $fpname($article, $tabletds, $tableths);
        }

        $tables .= implode($tabletds);
    }
    echo implode($tableths) . $tables;

    echo '</table>';
    echo '<hr/><p class="pagebar">';
    $p->UrlRule->Rules['{%order%}'] = $order_get;
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        echo '<input  style="float:right;" type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" value="' . $zbp->lang['msg']['all_del'] . '">';
    }
    echo '</p><form></div>';
    echo '<script>ActiveLeftMenu("aPageMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-stickies-fill");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台分类管理.
 */
function Admin_CategoryMng()
{
    global $zbp;

    $posttype = (int) GetVars('type');
    $typetitle = $posttype > 0 ? (ucfirst($zbp->GetPostType($posttype, 'name')) . '-') : '';
    echo '<div class="divHeader">' . $typetitle . $zbp->lang['msg']['category_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CategoryMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    if (!$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']) {
        echo '<form class="search" id="edit" method="post" action="#">';
        echo '<p>' . $zbp->lang['msg']['search'] . ':&nbsp;&nbsp;
        <input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
        echo '</form>';
    }

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=CategoryMng{&type=%type%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;
    //$p->UrlRule->Rules['{%type%}'] = GetVars('type', 'GET');
    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);

    $w = array();
    $w[] = array('=', 'cate_Type', $posttype);

    if ($search) {
        $w[] = array('search', 'cate_Name', 'cate_Alias', 'cate_Intro', $search);
    }

    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('cate_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('cate_ID' => 'ASC');
    } elseif ($order_get == 'order_desc') {
        $or = array('cate_Order' => 'DESC');
    } elseif ($order_get == 'order_asc') {
        $or = array('cate_Order' => 'ASC');
    } elseif ($order_get == 'name_desc') {
        $or = array('cate_Name' => 'DESC');
    } elseif ($order_get == 'name_asc') {
        $or = array('cate_Name' => 'ASC');
    } elseif ($order_get == 'alias_desc') {
        $or = array('cate_Alias' => 'DESC');
    } elseif ($order_get == 'alias_asc') {
        $or = array('cate_Alias' => 'ASC');
    } else {
        $or = array('cate_ID' => 'ASC');
    }

    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CategoryMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    if (!$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']) {
        $array = $zbp->GetCategoryList(
            $s,
            $w,
            $or,
            $l,
            $op
        );
    } else {
        $array = $zbp->categoriesbyorder_type[$posttype];
    }

    //Array_Isset($zbp->categoriesbyorder_type, $posttype, array());
    //$array = $zbp->categoriesbyorder_type[$posttype];
    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get, 'desc');
    list($button_order_html) = MakeOrderButton('order', $p->UrlRule, $order_get);
    list($button_name_html) = MakeOrderButton('name', $p->UrlRule, $order_get);
    list($button_alias_html) = MakeOrderButton('alias', $p->UrlRule, $order_get);

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['order'] . $button_order_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . $button_name_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . $button_alias_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['post_count'] . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '</tr>';

    foreach ($array as $category) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $category->ID . '</td>';
        $tabletds[] = '<td class="td5">' . $category->Order . '</td>';
        $tabletds[] = '<td class="td25"><a href="' . $category->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . ($zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY'] ? $category->Symbol : '') . $category->Name . '</td>';
        $tabletds[] = '<td class="td20">' . $category->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . $category->Count . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=CategoryEdt&amp;id=' . $category->ID . '"><i class="icon-pencil-square"></i></a>' .
            ((count($category->SubCategories) == 0) ? '&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=CategoryDel&amp;id=' . $category->ID) . '"><i class="icon-trash"></i></a>' : '') .
            '</td>';

        $tabletds[] = '</tr>';
        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CategoryMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前$category，当前行，表头
            $fpreturn = $fpname($category, $tabletds, $tableths);
        }

        $tables .= implode($tabletds);
    }

    echo implode($tableths) . $tables;

    echo '</table>';
    if (!$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']) {
        echo '<hr/><p class="pagebar">';
        foreach ($p->Buttons as $key => $value) {
            if ($p->PageNow == $key) {
                echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
            } else {
                echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
            }
        }
        echo '</p>';
    }
    echo '</div>';
    echo '<script>ActiveLeftMenu("aCategoryMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-folder-fill");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台评论管理.
 */
function Admin_CommentMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['comment_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CommentMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';

    echo '<form class="search" id="search" method="post" action="#">';
    echo '<p>' . $zbp->lang['msg']['search'] . '&nbsp;&nbsp;&nbsp;&nbsp;<input name="search" style="width:450px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
    echo '</form>';
    echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=CommentBat">';
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=CommentMng{&ischecking=%ischecking%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $search = GetVars('search', '', '');

    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%ischecking%}'] = (bool) GetVars('ischecking');
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = array();
    if (!$zbp->CheckRights('CommentAll')) {
        $w[] = array('=', 'comm_AuthorID', $zbp->user->ID);
    }
    if (GetVars('search')) {
        $w[] = array('search', 'comm_Content', 'comm_Name', GetVars('search'));
    }
    if (GetVars('id')) {
        $w[] = array('=', 'comm_ID', GetVars('id'));
    }

    $w[] = array('=', 'comm_Ischecking', (int) GetVars('ischecking'));

    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('comm_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('comm_ID' => 'ASC');
    } elseif ($order_get == 'posttime_desc') {
        $or = array('comm_PostTime' => 'DESC');
    } elseif ($order_get == 'posttime_asc') {
        $or = array('comm_PostTime' => 'ASC');
    } elseif ($order_get == 'logid_desc') {
        $or = array('comm_LogID' => 'DESC');
    } elseif ($order_get == 'logid_asc') {
        $or = array('comm_LogID' => 'ASC');
    } elseif ($order_get == 'authorid_desc') {
        $or = array('comm_AuthorID' => 'DESC');
    } elseif ($order_get == 'authorid_asc') {
        $or = array('comm_AuthorID' => 'ASC');
    } elseif ($order_get == 'parentid_desc') {
        $or = array('comm_ParentID' => 'DESC');
    } elseif ($order_get == 'parentid_asc') {
        $or = array('comm_ParentID' => 'ASC');
    } else {
        $or = array('comm_ID' => 'DESC');
    }
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CommentMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Comment'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetCommentList(
        $s,
        $w,
        $or,
        $l,
        $op
    );

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get, 'desc');
    list($button_posttime_html) = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    list($button_logid_html) = MakeOrderButton('logid', $p->UrlRule, $order_get);
    list($button_authorid_html) = MakeOrderButton('authorid', $p->UrlRule, $order_get);
    list($button_parentid_html) = MakeOrderButton('parentid', $p->UrlRule, $order_get);

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['parend_id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . $button_authorid_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['content'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['article'] . $button_logid_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . $button_posttime_html . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '<th><a href="" onclick="BatchSelectAll();return false;">' . $zbp->lang['msg']['select_all'] . '</a></th>';
    $tableths[] = '</tr>';

    foreach ($array as $cmt) {
        $article = $zbp->GetPostByID($cmt->LogID);
        if ($article->ID == 0) {
            $article = null;
        }

        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5"><a href="?act=CommentMng&id=' . $cmt->ID . '" title="' . $zbp->lang['msg']['jump_comment'] . $cmt->ID . '">' . $cmt->ID . '</a></td>';
        if ($cmt->ParentID > 0) {
            $tabletds[] = '<td class="td5"><a href="?act=CommentMng&id=' . $cmt->ParentID . '" title="' . $zbp->lang['msg']['jump_comment'] . $cmt->ParentID . '">' . $cmt->ParentID . '</a></td>';
        } else {
            $tabletds[] = '<td class="td5"></td>';
        }

        $tabletds[] = '<td class="td10"><span class="cmt-note" title="' . $zbp->lang['msg']['email'] . ':' . htmlspecialchars($cmt->Email) . '"><a href="mailto:' . htmlspecialchars($cmt->Email) . '">' . $cmt->Author->StaticName . '</a></span></td>';
        $tabletds[] = '<td><div style="overflow:hidden;max-width:500px;">' .
            (
                ($article) ? '<a href="' . $article->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' : '<a href="javascript:;"><i class="icon-trash"></i></a>') .
            $cmt->Content . '<div></td>';
        $tabletds[] = '<td class="td5">' . $cmt->LogID . '</td>';
        $tabletds[] = '<td class="td15">' . $cmt->Time() . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=CommentDel&amp;id=' . $cmt->ID) . '"><i class="icon-trash" title="' . $zbp->lang['msg']['del'] . '"></i></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            (!GetVars('ischecking', 'GET') ? '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><i class="icon-shield-fill-x" title="' . $zbp->lang['msg']['audit'] . '"></i></a>' : '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><i class="icon-shield-fill-check" title="' . $zbp->lang['msg']['pass'] . '"></i></a>') .
            '</td>';
        $tabletds[] = '<td class="td5 tdCenter">' . '<input type="checkbox" id="id' . $cmt->ID . '" name="id[]" value="' . $cmt->ID . '"/>' . '</td>';

        $tabletds[] = '</tr>';
        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CommentMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前$cmt，当前行，表头
            $fpreturn = $fpname($cmt, $tabletds, $tableths, $article);
        }

        $tables .= implode($tabletds);
    }

    echo implode($tableths) . $tables;
    echo '</table>';
    echo '<hr/>';

    echo '<p style="float:right;">';

    if ((bool) GetVars('ischecking')) {
        echo '<input type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" name="all_pass"  value="' . $zbp->lang['msg']['all_pass'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    } else {
        echo '<input type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" name="all_audit"  value="' . $zbp->lang['msg']['all_audit'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }

    echo '</p>';

    echo '<p class="pagebar">';

    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }

    echo '</p>';

    echo '<hr/></form>';

    echo '</div>';
    echo '<script>ActiveLeftMenu("aCommentMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-chat-text-fill"); $(".cmt-note").tooltip();</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台用户管理.
 */
function Admin_MemberMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['member_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_MemberMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    echo '<form class="search" id="search" method="post" action="#">';

    echo '<p>' . $zbp->lang['msg']['search'] . ':&nbsp;&nbsp;' . $zbp->lang['msg']['member_level'] . ' <select class="edit" size="1" name="level" style="width:140px;" ><option value="">' . $zbp->lang['msg']['any'] . '</option>';
    foreach ($zbp->lang['user_level_name'] as $id => $name) {
        echo '<option value="' . $id . '">' . $name . '</option>';
    }
    echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
    echo '</form>';

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=MemberMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = array();
    if (!$zbp->CheckRights('MemberAll')) {
        $w[] = array('=', 'mem_ID', $zbp->user->ID);
    }
    if (GetVars('level')) {
        $w[] = array('=', 'mem_Level', GetVars('level'));
    }
    if (GetVars('search')) {
        $w[] = array('search', 'mem_Name', 'mem_Alias', 'mem_Email', GetVars('search'));
    }

    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('mem_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('mem_ID' => 'ASC');
    } elseif ($order_get == 'level_desc') {
        $or = array('mem_Level' => 'DESC');
    } elseif ($order_get == 'level_asc') {
        $or = array('mem_Level' => 'ASC');
    } elseif ($order_get == 'name_desc') {
        $or = array('mem_Name' => 'DESC');
    } elseif ($order_get == 'name_asc') {
        $or = array('mem_Name' => 'ASC');
    } elseif ($order_get == 'alias_desc') {
        $or = array('mem_Alias' => 'DESC');
    } elseif ($order_get == 'alias_asc') {
        $or = array('mem_Alias' => 'ASC');
    } else {
        $or = array('mem_ID' => 'ASC');
    }
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_MemberMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetMemberList(
        '',
        $w,
        $or,
        $l,
        $op
    );

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get, 'desc');
    list($button_level_html) = MakeOrderButton('level', $p->UrlRule, $order_get);
    list($button_name_html) = MakeOrderButton('name', $p->UrlRule, $order_get);
    list($button_alias_html) = MakeOrderButton('alias', $p->UrlRule, $order_get);

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['member_level'] . $button_level_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . $button_name_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . $button_alias_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['all_artiles'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['all_pages'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['all_comments'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['all_uploads'] . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '</tr>';

    foreach ($array as $member) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $member->ID . '</td>';
        $tabletds[] = '<td class="td10">' . $member->LevelName . ($member->Status > 0 ? '(' . $zbp->lang['user_status_name'][$member->Status] . ')' : '') . ($member->IsGod ? ' <span title="root">#</span>' : '') . '</td>';
        $tabletds[] = '<td><a href="' . $member->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . $member->Name . '</td>';
        $tabletds[] = '<td class="td15">' . $member->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . max(0, $member->Articles) . '</td>';
        $tabletds[] = '<td class="td10">' . max(0, $member->Pages) . '</td>';
        $tabletds[] = '<td class="td10">' . max(0, $member->Comments) . '</td>';
        $tabletds[] = '<td class="td10">' . max(0, $member->Uploads) . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=MemberEdt&amp;id=' . $member->ID . '"><i class="icon-pencil-square"></i></a>' .
            (($zbp->CheckRights('MemberDel') && ($member->IsGod !== true)) ? '&nbsp;&nbsp;&nbsp;&nbsp;' .
                '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=MemberDel&amp;id=' . $member->ID) . '"><i class="icon-trash"></i></a>' : '') .
            '</td>';

        $tabletds[] = '</tr>';

        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_MemberMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前$member，当前行，表头
            $fpreturn = $fpname($member, $tabletds, $tableths);
        }

        $tables .= implode($tabletds);
    }

    echo implode($tableths) . $tables;

    echo '</table>';
    echo '<hr/><p class="pagebar">';
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    echo '</p></div>';
    echo '<script>ActiveLeftMenu("aMemberMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-people-fill");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 *  后台上传附件管理.
 */
function Admin_UploadMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['upload_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_UploadMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';

    echo '<form class="search" name="upload" id="upload" method="post" enctype="multipart/form-data" action="' . BuildSafeCmdURL('act=UploadPst') . '">';
    echo '<p>' . $zbp->lang['msg']['upload_file'] . ': </p>';
    echo '<p><input type="file" name="file" size="60" />&nbsp;&nbsp;';
    echo '<label><input type="checkbox" name="auto_rename" checked/>' . $zbp->lang['msg']['auto_rename_uploadfile'] . '</label>&nbsp;&nbsp;';
    echo '<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '" onclick="" />&nbsp;&nbsp;';
    echo '<input class="button" type="reset" value="' . $zbp->lang['msg']['reset'] . '" /></p>';
    echo '</form>';

    $w = array();
    if (!$zbp->CheckRights('UploadAll')) {
        $w[] = array('=', 'ul_AuthorID', $zbp->user->ID);
    }

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=UploadMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('ul_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('ul_ID' => 'ASC');
    } elseif ($order_get == 'size_desc') {
        $or = array('ul_Size' => 'DESC');
    } elseif ($order_get == 'size_asc') {
        $or = array('ul_Size' => 'ASC');
    } elseif ($order_get == 'authorid_desc') {
        $or = array('ul_AuthorID' => 'DESC');
    } elseif ($order_get == 'authorid_asc') {
        $or = array('ul_AuthorID' => 'ASC');
    } elseif ($order_get == 'logid_desc') {
        $or = array('ul_LogID' => 'DESC');
    } elseif ($order_get == 'logid_asc') {
        $or = array('ul_LogID' => 'ASC');
    } elseif ($order_get == 'posttime_desc') {
        $or = array('ul_PostTime' => 'DESC');
    } elseif ($order_get == 'posttime_asc') {
        $or = array('ul_PostTime' => 'ASC');
    } else {
        $or = array('ul_PostTime' => 'DESC');
    }
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_UploadMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetUploadList($s, $w, $or, $l, $op);

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get);
    list($button_size_html) = MakeOrderButton('size', $p->UrlRule, $order_get);
    list($button_authorid_html) = MakeOrderButton('authorid', $p->UrlRule, $order_get);
    list($button_logid_html) = MakeOrderButton('logid', $p->UrlRule, $order_get);
    list($button_posttime_html) = MakeOrderButton('posttime', $p->UrlRule, $order_get);

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableHeaders = array();
    $tableHeaders[] = '<tr>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['author'] . $button_authorid_html . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['date'] . $button_posttime_html . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['size'] . $button_size_html . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['type'] . '</th>';
    $tableHeaders[] = '<th></th>';
    $tableHeaders[] = '</tr>';

    foreach ($array as $upload) {
        $ret = array(); //table string
        $ret[] = '<tr>';
        $ret[] = '<td class="td5">' . $upload->ID . '</td>';
        $ret[] = '<td class="td10">' . htmlspecialchars($upload->Author->Name) . '</td>';
        $ret[] = '<td><a href="' . htmlspecialchars($upload->Url) . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . htmlspecialchars($upload->Name) . '</td>';
        $ret[] = '<td class="td15">' . $upload->Time() . '</td>';
        $ret[] = '<td class="td10">' . $upload->Size . '</td>';
        $ret[] = '<td class="td20">' . htmlspecialchars($upload->MimeType) . '</td>';
        $ret[] = '<td class="td10 tdCenter">' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=UploadDel&amp;id=' . $upload->ID) . '"><i class="icon-trash"></i></a>' .
            '</td>';

        $ret[] = '</tr>';
        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_UploadMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前$upload，当前行，表头
            $fpreturn = $fpname($upload, $ret, $tableHeaders);
        }

        $tables .= implode($ret);
    }

    echo implode($tableHeaders) . $tables;
    echo '</table>';
    echo '<hr/><p class="pagebar">';
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    echo '</p></div>';
    echo '<script>ActiveLeftMenu("aUploadMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-inboxes-fill");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台标签管理.
 */
function Admin_TagMng()
{
    global $zbp;

    $posttype = (int) GetVars('type');
    $typetitle = $posttype > 0 ? (ucfirst($zbp->GetPostType($posttype, 'name')) . '-') : '';

    echo '<div class="divHeader">' . $typetitle . $zbp->lang['msg']['tag_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TagMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';

    echo '<div id="divMain2">';
    echo '<form class="search" id="edit" method="post" action="#">';
    echo '<p>' . $zbp->lang['msg']['search'] . ':&nbsp;&nbsp;
    <input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
    echo '</form>';

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=TagMng{&type=%type%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');

    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%type%}'] = GetVars('type', 'GET');

    $w = array();
    if ($search) {
        $w[] = array('search', 'tag_Name', 'tag_Alias', 'tag_Intro', $search);
    }
    $w[] = array('=', 'tag_Type', $posttype);


    $s = '';
    if ($order_get == 'id_desc') {
        $or = array('tag_ID' => 'DESC');
    } elseif ($order_get == 'id_asc') {
        $or = array('tag_ID' => 'ASC');
    } elseif ($order_get == 'name_desc') {
        $or = array('tag_Name' => 'DESC');
    } elseif ($order_get == 'name_asc') {
        $or = array('tag_Name' => 'ASC');
    } elseif ($order_get == 'alias_desc') {
        $or = array('tag_Alias' => 'DESC');
    } elseif ($order_get == 'alias_asc') {
        $or = array('tag_Alias' => 'ASC');
    } else {
        $or = array('tag_ID' => 'ASC');
    }

    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    //1.7新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TagMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetTagList($s, $w, $or, $l, $op);

    list($button_id_html) = MakeOrderButton('id', $p->UrlRule, $order_get, 'desc');
    list($button_name_html) = MakeOrderButton('name', $p->UrlRule, $order_get);
    list($button_alias_html) = MakeOrderButton('alias', $p->UrlRule, $order_get);

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . $button_id_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . $button_name_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . $button_alias_html . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['post_count'] . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '</tr>';

    foreach ($array as $tag) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $tag->ID . '</td>';
        $tabletds[] = '<td class="td25"><a href="' . $tag->Url . '" target="_blank"><i class="icon-link-45deg"></i></a> ' . $tag->Name . '</td>';
        $tabletds[] = '<td class="td20">' . $tag->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . $tag->Count . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=TagEdt&amp;id=' . $tag->ID . '"><i class="icon-pencil-square"></i></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=TagDel&amp;id=' . $tag->ID) . '"><i class="icon-trash"></i></a>' .
            '</td>';

        $tabletds[] = '</tr>';

        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TagMng_Table'] as $fpname => &$fpsignal) {
            //传入 当前$tag，当前行，表头
            $fpreturn = $fpname($tag, $tabletds, $tableths);
        }

        $tables .= implode($tabletds);
    }

    echo implode($tableths) . $tables;
    echo '</table>';
    echo '<hr/><p class="pagebar">';
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    echo '</p></div>';

    echo '<script>ActiveLeftMenu("aTagMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-tags-fill");</script>';
    echo '<script>$(\'a.order_button\').parent().bind(\'mouseenter mouseleave\', function() {$(this).find(\'a.order_button\').toggleClass(\'element-visibility-hidden\');});</script>';
}

//###############################################################################################################

/**
 * 后台主题管理.
 */
function Admin_ThemeMng()
{
    global $zbp;

    $allthemes = $zbp->LoadThemes();

    echo '<div class="divHeader">' . $zbp->lang['msg']['theme_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ThemeMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2" style="min-width:550px;"><form id="frmTheme" method="post" action="../cmd.php?act=ThemeSet">';
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
    echo '<input type="hidden" name="theme" id="theme" value="" />';
    echo '<input type="hidden" name="style" id="style" value="" />';

    foreach ($allthemes as $theme) {
        echo "\n\n";

        echo '<div class="theme ' . ($theme->IsUsed() ? 'theme-now' : 'theme-other') . '"';
        echo ' data-themeid="' . htmlspecialchars($theme->id) . '"';
        echo ' data-themename="' . htmlspecialchars($theme->name) . '"';
        echo '>';
        echo '<div class="theme-name">';

        if (isset($zbp->lang[$theme->id]['theme_name'])) {
            $theme->name = $zbp->lang[$theme->id]['theme_name'];
        }

        if ($theme->IsUsed() && $theme->path && !in_array('AppCentre', $zbp->GetPreActivePlugin())) {
            echo '<a href="' . $theme->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '"><i class="icon-tools"></i></a>&nbsp;&nbsp;';
        } else {
            echo '<i class="icon-layout-text-sidebar-reverse"></i>&nbsp;&nbsp;';
        }
        echo '<a target="_blank" href="' . htmlspecialchars($theme->url) . '" title=""><strong style="display:none;">' . htmlspecialchars($theme->id) . '</strong>';
        echo '<b>' . htmlspecialchars($theme->name) . '</b></a></div>';
        echo '<div class="theme-img"><span><img src="' . $theme->GetScreenshot() . '" title="' . htmlspecialchars($theme->name) . '" alt="' . htmlspecialchars($theme->name) . '" /></span></div>';
        echo '<div class="theme-author">' . $zbp->lang['msg']['author'] . ': <a target="_blank" href="' . htmlspecialchars($theme->author_url) . '">' . htmlspecialchars($theme->author_name) . '</a></div>';
        echo '<div class="theme-style">';
        echo '<select class="edit" size="1" title="' . $zbp->lang['msg']['style'] . '">';
        foreach ($theme->GetCssFiles() as $key => $value) {
            echo '<option value="' . htmlspecialchars($key) . '" ' . ($theme->IsUsed() ? ($key == $zbp->style ? 'selected="selected"' : '') : '') . '>' . basename($value) . '</option>';
        }
        echo '</select>';
        echo '<input type="button" onclick="$(\'#style\').val($(this).prev().val());$(\'#theme\').val(\'' . $theme->id . '\');$(\'#frmTheme\').submit();" class="theme-activate button" value="' . $zbp->lang['msg']['enable'] . '">';
        echo '</div>';
        echo '</div>';
    }

    echo '</form></div>';
    echo '<script>ActiveLeftMenu("aThemeMng");</script>';
    echo '<script>AddHeaderFontIcon("icon-grid-1x2-fill");</script>';
}

//###############################################################################################################

/**
 * 后台模块管理.
 */
function Admin_ModuleMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['module_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ModuleMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2" style="min-width:550px;">';

    $sm = array();
    $um = array();
    $tm = array();
    $pm = array();

    foreach ($zbp->modules as $m) {
        if ($m->SourceType == 'system') {
            $sm[] = $m;
        } elseif ($m->SourceType == 'user') {
            $um[] = $m;
        } elseif ($m->SourceType == 'theme' || $m->SourceType == 'themeinclude') {
            //判断模块归属当前主题
            if ($m->Source == 'theme' || (substr($m->Source, (-1 - strlen($zbp->theme)))) == ('_' . $zbp->theme)) {
                $tm[] = $m;
            }
        } else {
            $pm[] = $m;
        }
    }

    //widget-list begin
    echo '<div class="widget-left">';
    echo '<div class="widget-list">';

    echo '<script>';
    echo 'var functions = {';
    foreach ($zbp->modules as $key => $value) {
        echo "'" . $value->FileName . "':'" . $value->Source . "' ,";
    }
    echo "'':''};";
    echo '</script>';
    echo "\r\n";
    echo '<div class="widget-list-header">' . $zbp->lang['msg']['system_module'] . '</div>';
    echo '<div class="widget-list-note">' . $zbp->lang['msg']['drag_module_to_sidebar'] . '</div>';
    echo "\r\n";
    foreach ($sm as $m) {
        CreateModuleDiv($m);
    }

    echo '<div class="widget-list-header">' . $zbp->lang['msg']['user_module'] . '</div>';
    echo "\r\n";
    foreach ($um as $m) {
        CreateModuleDiv($m);
    }

    echo '<div class="widget-list-header">' . $zbp->lang['msg']['plugin_module'] . '</div>';
    echo "\r\n";
    foreach ($pm as $m) {
        CreateModuleDiv($m);
    }

    echo '<div class="widget-list-header">' . $zbp->lang['msg']['theme_module'] . '</div>';
    echo "\r\n";
    foreach ($tm as $m) {
        CreateModuleDiv($m);
    }

    $sideids = array(1 => '', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9');

    echo '<hr/>';
    echo "\r\n";
    echo '<form id="edit" method="post" action="' . BuildSafeCmdURL('act=SidebarSet') . '">';

    foreach ($sideids as $key => $value) {
        echo '<input type="hidden" id="strsidebar' . $value . '" name="edtSidebar' . $value . '" value="' . $zbp->option['ZC_SIDEBAR' . $value . '_ORDER'] . '"/>';
    }
    echo '</form>';
    echo "\r\n";
    echo '<div class="clear"></div></div>';
    echo '</div>';
    //widget-list end
    echo "\r\n";
    //siderbar-list begin
    foreach ($sideids as $key => $value) {
        $id = 'sidebar' . $value;
        echo '<div class="siderbar-list">';
        echo '<div class="siderbar-drop" id="siderbar' . $value . '"><div class="siderbar-header">' . $zbp->lang['msg'][$id] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
        echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->$id), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
        foreach ($zbp->template->$id as $m) {
            CreateModuleDiv($m, false);
        }
        echo '</div></div>';
        echo "\r\n";
    }

    echo '<div class="clear"></div></div>';
    //siderbar-list end
    echo "\r\n";
    echo '<div class="clear"></div>';

    echo '</div>';
    echo "\r\n";

    echo '<script>ActiveLeftMenu("aModuleMng");</script>'; ?>
    <script>
        $(function() {
            function sortFunction() {
                <?php
                foreach ($sideids as $key => $value) {
                    echo '
        var s' . $key . ' = "";
        $("#siderbar' . $value . '").find("div.funid").each(function(i) {
            s' . $key . ' += $(this).html() + "|";
        });
        ';
                    echo '$("#strsidebar' . $value . '").val(s' . $key . ');';
                }
                ?>

                $.post($("#edit").attr("action"), {
                    <?php
                    foreach ($sideids as $key => $value) {
                        echo '"sidebar' . $value . '": s' . $key . ',';
                    }
                    ?>
                    },
                    function(data) {
                        //alert("Data Loaded: " + data);
                    });

            };

            var t, f = 1;

            function hideWidget(item) {
                item.find(".ui-icon").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-w");
                t = item.next();
                t.find(".widget").hide("fast").end().show();
                t.find(".siderbar-note>span").text(t.find(".widget").length);
            }

            function showWidget(item) {
                item.find(".ui-icon").removeClass("ui-icon-triangle-1-w").addClass("ui-icon-triangle-1-s");
                t = item.next();
                t.find(".widget").show("fast");
                t.find(".siderbar-note>span").text(t.find(".widget").length);
            }

            $(".siderbar-header").click(function() {
                if ($(this).hasClass("clicked")) {
                    showWidget($(this));
                    $(this).removeClass("clicked");
                } else {
                    hideWidget($(this));
                    $(this).addClass("clicked");
                }
            });

            $(".siderbar-sort-list").sortable({
                items: '.widget',
                start: function(event, ui) {
                    showWidget(ui.item.parent().prev());
                },
                stop: function(event, ui) {
                    var c = ui.item.find(".funid").html();
                    var siderbarName = [];
                    ui.item.parent().find(".funid").each(function(item, element) {
                        var c = $(element).html();
                        if (siderbarName[c] !== undefined) {
                            siderbarName[c] += 1
                        } else {
                            siderbarName[c] = 1
                        }
                    })
                    if (siderbarName[c] > 1) {
                        ui.item.remove();
                    };

                    $(this).parent().find(".roll").show("slow");
                    sortFunction();
                    $(this).parent().find(".roll").hide("slow");
                    showWidget($(this).parent().prev());
                }
            }).disableSelection();

            $(".widget-list>.widget").draggable({
                connectToSortable: ".siderbar-sort-list",
                revert: "invalid",
                containment: "document",
                helper: "clone",
                cursor: "move"
            }).disableSelection();

            $(".widget-list").droppable({
                accept: ".siderbar-sort-list>.widget",
                drop: function(event, ui) {
                    ui.draggable.remove();
                }
            });

        });
    </script>
    <?php
    echo '<script>AddHeaderFontIcon("icon-grid-3x3-gap-fill");</script>';
}

//###############################################################################################################

/**
 * 后台插件管理.
 */
function Admin_PluginMng()
{
    global $zbp;

    $allplugins = $zbp->LoadPlugins();

    echo '<div class="divHeader">' . $zbp->lang['msg']['plugin_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_PluginMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped plugin-list">';
    echo '<tr>

    <th></th>
    <th>' . $zbp->lang['msg']['name'] . '</th>
    <th>' . $zbp->lang['msg']['author'] . '</th>
    <th>' . $zbp->lang['msg']['date'] . '</th>
    <th></th>
    </tr>';

    $plugins = array();

    $app = new App();
    if ($app->LoadInfoByXml('theme', $zbp->theme) == true) {
        if ($app->HasPlugin()) {
            array_unshift($plugins, $app);
        }
    }

    $pl = $zbp->option['ZC_USING_PLUGIN_LIST'];
    $apl = explode('|', $pl);
    $apl = array_unique($apl);
    foreach ($apl as $name) {
        foreach ($allplugins as $plugin) {
            if ($name == $plugin->id) {
                $plugins[] = $plugin;
            }
        }
    }
    foreach ($allplugins as $plugin) {
        if (!$plugin->IsUsed()) {
            $plugins[] = $plugin;
        }
    }

    foreach ($plugins as $plugin) {
        echo '<tr>';
        echo '<td class="td5 tdCenter' . ($plugin->type == 'plugin' ? ' plugin' : '') . ($plugin->IsUsed() ? ' plugin-on' : '') . '" data-pluginid="' . htmlspecialchars($plugin->id) . '"><img ' . ($plugin->IsUsed() ? '' : 'style="opacity:0.2"') . ' src="' . $plugin->GetLogo() . '" alt="" width="32" height="32" /></td>';
        echo '<td class="td25"><span class="plugin-note" title="' . htmlspecialchars($plugin->note) . '">' . htmlspecialchars($plugin->name) . ' ' . htmlspecialchars($plugin->version) . '</span></td>';
        echo '<td class="td20"><a href="' . htmlspecialchars($plugin->author_url) . '" target="_blank">' . htmlspecialchars($plugin->author_name) . '</a></td>';
        echo '<td class="td20">' . htmlspecialchars($plugin->modified) . '</td>';
        echo '<td class="td10 tdCenter">';

        if ($plugin->type == 'plugin') {
            if ($plugin->IsUsed()) {
                echo '<a href="' . BuildSafeCmdURL('act=PluginDis&amp;name=' . htmlspecialchars($plugin->id)) . '" title="' . $zbp->lang['msg']['disable'] . '" class="btn-icon btn-disable" data-pluginid="' . htmlspecialchars($plugin->id) . '"><i class="icon-cancel on"></i></a>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            } else {
                echo '<a href="' . BuildSafeCmdURL('act=PluginEnb&amp;name=' . htmlspecialchars($plugin->id)) . '" title="' . $zbp->lang['msg']['enable'] . '" class="btn-icon btn-enable" data-pluginid="' . htmlspecialchars($plugin->id) . '"><i class="icon-power off"></i></a>';
            }
        }

        if ($plugin->IsUsed() && $plugin->CanManage()) {
            echo '<a href="' . $plugin->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '" class="btn-icon btn-manage" data-pluginid="' . htmlspecialchars($plugin->id) . '"><i class="icon-tools"></i></a>';
        }

        echo '</td>';

        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<script>ActiveLeftMenu("aPluginMng");';
    echo 'AddHeaderFontIcon("icon-puzzle-fill"); $(".plugin-note").tooltip();</script>';
}

//###############################################################################################################

/**
 * 后台网站设置管理.
 */
function Admin_SettingMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['settings'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_SettingMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    ?>
    <form method="post" action="<?php echo BuildSafeCmdURL('act=SettingSav'); ?>" onsubmit="return checkDomain();">
        <div id="divMain2">
            <div class="content-box">
                <!-- Start Content Box -->

                <div class="content-box-header">
                    <ul class="content-box-tabs">
                        <li><a href="#tab1" class="default-tab"><span><?php echo $zbp->lang['msg']['basic_setting']; ?></span></a></li>
                        <li><a href="#tab2"><span><?php echo $zbp->lang['msg']['global_setting']; ?></span></a></li>
                        <li><a href="#tab3"><span><?php echo $zbp->lang['msg']['page_setting']; ?></span></a></li>
                        <li><a href="#tab4"><span><?php echo $zbp->lang['msg']['comment_setting']; ?></span></a></li>
                        <li><a href="#tab5"><span><?php echo @$zbp->langs->msg->backend_setting; ?></span></a></li>
                        <li><a href="#tab6"><span><?php echo $zbp->lang['msg']['api_setting']; ?></span></a></li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <!-- End .content-box-header -->

                <div class="content-box-content">
                    <?php
                    $decodedBlogHost = $zbp->option['ZC_BLOG_HOST'];
                    if (stripos($zbp->option['ZC_BLOG_HOST'], '/xn--') !== false && function_exists('mb_strtolower')) {
                        $Punycode = new Punycode();
                        $decodedBlogHost = $Punycode->decode($zbp->option['ZC_BLOG_HOST']);
                    }

                    echo '<div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['blog_host'] . '</b><br/>';
                    if ($zbp->ispermanent_domain) {
                        echo '<span class="note">' . $zbp->lang['msg']['permanent_domain_is_enable'] . '</span><br/>';
                    }
                    if ($zbp->ispermanent_domain && (Null2Empty(GetValueInArray($zbp->option, 'ZC_PERMANENT_DOMAIN_FORCED_URL')) == '') &&$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']) {
                        echo '<span class="note">' . $zbp->lang['msg']['blog_host_add'] . '</span>';
                    }
                    echo '</p></td><td><p><input id="ZC_BLOG_HOST" name="ZC_BLOG_HOST" style="max-width:600px;width:90%;" type="text" value="' . $decodedBlogHost . '" ' . (($zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] && (Null2Empty(GetValueInArray($zbp->option, 'ZC_PERMANENT_DOMAIN_FORCED_URL'))) == '') ? '' : 'readonly="readonly" ') . 'oninput="disableSubmit($(this).val())" />&nbsp;&nbsp;';
                    if ($zbp->ispermanent_domain && (Null2Empty(GetValueInArray($zbp->option, 'ZC_PERMANENT_DOMAIN_FORCED_URL')) == '') && $zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']) {
                        echo '<span class="js-tip"></span>';
                        echo '<p><label onclick="$(\'#ZC_BLOG_HOST\').prop(\'readonly\', $(\'#ZC_PERMANENT_DOMAIN_ENABLE\').val()==0?true:false);   if($(\'#ZC_PERMANENT_DOMAIN_ENABLE\').val()==0){enableSubmit();$(\'.js-tip\').html(\'\');}else {disableSubmit();}"><input type="text" id="ZC_PERMANENT_DOMAIN_ENABLE" name="ZC_PERMANENT_DOMAIN_ENABLE" class="checkbox" value="' . $zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] . '"/></label>' . $zbp->lang['msg']['permanent_domain'] . '<span style="display:none;"></span></p>';
                        echo '<script>
var bCheck = false;
function disableSubmit(newurl){
    bCheck = true;
}
function enableSubmit(newurl){
    bCheck = false;
}
function checkDomain(){
    if(bCheck === false)return true;
    if(bCheck === true){
        var i = changeDomain($(\'#ZC_BLOG_HOST\').val());
        if(i === true)
            return true;
        else
            return false;
    }
}
function changeDomain(newurl){
    var token = "' . CreateWebToken("", (time() + 3600)) . '";
    newurl = newurl.replace(" ","");
    if(newurl.substr(newurl.length-1,1) != "/" ){
        newurl = newurl + "/";
    }
    url = bloghost + "zb_system/cmd.php?act=misc&type=ping&token=" + token;
    $(".js-tip").html("<em>' . $zbp->lang['msg']['verifying'] . '</em>");
    $.getJSON(url,{url:newurl},function(data) {
        if (data) {
          $(".js-tip").html(data.err.msg);
          if(data.err.code == 0){
            enableSubmit();
            return true;
          }
          console.log(data);
          disableSubmit();
          return false;
        }
      }).fail(function() {
        $(".js-tip").html("<em>' . $zbp->lang['msg']['verify_fail'] . '</em>");
        console.log( "error" );
        disableSubmit();
        return false;
      });
}
    </script>';
                    } else {
                        echo '<script>
function checkDomain(){
    return true;
}
    </script>';
                    };
                    echo '</td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_name'] . '</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="max-width:600px;width:90%;" type="text" value="' . htmlspecialchars($zbp->option['ZC_BLOG_NAME']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_subname'] . '</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="max-width:600px;width:90%;"  type="text" value="' . htmlspecialchars($zbp->option['ZC_BLOG_SUBNAME']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['copyright'] . '</b><br/><span class="note">' . $zbp->lang['msg']['copyright_add'] . '</span></p></td><td><p><textarea cols="3" rows="6" id="ZC_BLOG_COPYRIGHT" name="ZC_BLOG_COPYRIGHT" style="max-width:600px;width:90%;">' . htmlspecialchars($zbp->option['ZC_BLOG_COPYRIGHT']) . '</textarea></p></td></tr>';

                    echo '</table>';
                    echo '</div>';

                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['blog_timezone'] . '</b></p></td><td><p><select id="ZC_TIME_ZONE_NAME" name="ZC_TIME_ZONE_NAME" style="max-width:600px;width:90%;" >';
                    echo CreateOptionsOfTimeZone($zbp->option['ZC_TIME_ZONE_NAME']);
                    echo '</select></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_language'] . '</b></p></td><td><p><select id="ZC_BLOG_LANGUAGEPACK" name="ZC_BLOG_LANGUAGEPACK" style="max-width:600px;width:90%;" >';
                    echo CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK']);
                    echo '</select></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['debug_mode'] . '</b></p></td><td><p><input id="ZC_DEBUG_MODE" name="ZC_DEBUG_MODE" type="text" value="' . $zbp->option['ZC_DEBUG_MODE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->show_warning_error . '</b></p></td><td><p><input id="ZC_DEBUG_MODE_WARNING" name="ZC_DEBUG_MODE_WARNING" type="text" value="' . $zbp->option['ZC_DEBUG_MODE_WARNING'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['additional_security'] . '</b></p></td><td><p><input id="ZC_ADDITIONAL_SECURITY" name="ZC_ADDITIONAL_SECURITY" type="text" value="' . $zbp->option['ZC_ADDITIONAL_SECURITY'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['using_cdn_guest_type'] . '</b></p></td><td><p><select id="ZC_USING_CDN_GUESTIP_TYPE" name="ZC_USING_CDN_GUESTIP_TYPE" style="max-width:600px;width:90%;" >';
                    echo CreateOptionsOfGuestIPType($zbp->option['ZC_USING_CDN_GUESTIP_TYPE']);
                    echo '</select></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['enable_xmlrpc'] . '</b></p></td><td><p><input id="ZC_XMLRPC_ENABLE" name="ZC_XMLRPC_ENABLE" type="text" value="' . $zbp->option['ZC_XMLRPC_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['close_site'] . '</b></p></td><td><p><input id="ZC_CLOSE_SITE" name="ZC_CLOSE_SITE" type="text" value="' . $zbp->option['ZC_CLOSE_SITE'] . '" class="checkbox"/></p></td></tr>';

                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['display_count'] . '</b></p></td><td><p><input id="ZC_DISPLAY_COUNT" name="ZC_DISPLAY_COUNT" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_DISPLAY_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['display_subcategorys'] . '</b></p></td><td><p><input id="ZC_DISPLAY_SUBCATEGORYS" name="ZC_DISPLAY_SUBCATEGORYS" type="text" value="' . $zbp->option['ZC_DISPLAY_SUBCATEGORYS'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['pagebar_count'] . '</b></p></td><td><p><input id="ZC_PAGEBAR_COUNT" name="ZC_PAGEBAR_COUNT" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_PAGEBAR_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['search_count'] . '</b></p></td><td><p><input id="ZC_SEARCH_COUNT" name="ZC_SEARCH_COUNT" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_SEARCH_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['syntax_high_lighter'] . '</b></p></td><td><p><input id="ZC_SYNTAXHIGHLIGHTER_ENABLE" name="ZC_SYNTAXHIGHLIGHTER_ENABLE" type="text" value="' . $zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['comment_turnoff'] . '</b></p></td><td><p><input id="ZC_COMMENT_TURNOFF" name="ZC_COMMENT_TURNOFF" type="text" value="' . $zbp->option['ZC_COMMENT_TURNOFF'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['comment_audit'] . '</b><br/><span class="note">' . $zbp->lang['msg']['comment_audit_comment'] . '</span></p></td><td><p><input id="ZC_COMMENT_AUDIT" name="ZC_COMMENT_AUDIT" type="text" value="' . $zbp->option['ZC_COMMENT_AUDIT'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comment_reverse_order'] . '</b></p></td><td><p><input id="ZC_COMMENT_REVERSE_ORDER" name="ZC_COMMENT_REVERSE_ORDER" type="text" value="' . $zbp->option['ZC_COMMENT_REVERSE_ORDER'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comments_display_count'] . '</b></p></td><td><p><input id="ZC_COMMENTS_DISPLAY_COUNT" name="ZC_COMMENTS_DISPLAY_COUNT" type="text" value="' . $zbp->option['ZC_COMMENTS_DISPLAY_COUNT'] . '"  style="max-width:600px;width:90%;" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comment_verify_enable'] . '</b></p></td><td><p><input id="ZC_COMMENT_VERIFY_ENABLE" name="ZC_COMMENT_VERIFY_ENABLE" type="text" value="' . $zbp->option['ZC_COMMENT_VERIFY_ENABLE'] . '" class="checkbox"/></p></td></tr>';

                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab5">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['allow_upload_type'] . '</b></p></td><td><p><input id="ZC_UPLOAD_FILETYPE" name="ZC_UPLOAD_FILETYPE" style="max-width:600px;width:90%;" type="text" value="' . htmlspecialchars($zbp->option['ZC_UPLOAD_FILETYPE']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['allow_upload_size'] . '</b><br/><span class="note">upload_max_filesize=' . ini_get('upload_max_filesize') . '<br/>post_max_size=' . ini_get('post_max_size') . '</span></p></td><td><p><input id="ZC_UPLOAD_FILESIZE" name="ZC_UPLOAD_FILESIZE" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_UPLOAD_FILESIZE'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->get_text_intro . '</b></p></td><td><p><input id="ZC_ARTICLE_INTRO_WITH_TEXT" name="ZC_ARTICLE_INTRO_WITH_TEXT" type="text" value="' . $zbp->option['ZC_ARTICLE_INTRO_WITH_TEXT'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['manage_count'] . '</b></p></td><td><p><input id="ZC_MANAGE_COUNT" name="ZC_MANAGE_COUNT" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_MANAGE_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->enable_post_batch_delete . '</b></p></td><td><p><input id="ZC_POST_BATCH_DELETE" name="ZC_POST_BATCH_DELETE" type="text" value="' . $zbp->option['ZC_POST_BATCH_DELETE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->delete_member_with_alldata . '</b></p></td><td><p><input id="ZC_DELMEMBER_WITH_ALLDATA" name="ZC_DELMEMBER_WITH_ALLDATA" type="text" value="' . $zbp->option['ZC_DELMEMBER_WITH_ALLDATA'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->category_legacy_display . '</b></p></td><td><p><input id="ZC_CATEGORY_MANAGE_LEGACY_DISPLAY" name="ZC_CATEGORY_MANAGE_LEGACY_DISPLAY" type="text" value="' . $zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY'] . '" class="checkbox"/></p></td></tr>';
                    //echo '<tr><td><p><b>' . @$zbp->langs->msg->enable_login_csrfcheck . '</b></p></td><td><p><input id="ZC_LOGIN_CSRFCHECK_ENABLE" name="ZC_LOGIN_CSRFCHECK_ENABLE" type="text" value="' . $zbp->option['ZC_LOGIN_CSRFCHECK_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->enable_login_verify . '</b></p></td><td><p><input id="ZC_LOGIN_VERIFY_ENABLE" name="ZC_LOGIN_VERIFY_ENABLE" type="text" value="' . $zbp->option['ZC_LOGIN_VERIFY_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '</table>';
                    echo '</div>';

                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab6">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['enable_api'] . '</b></p></td><td><p><input id="ZC_API_ENABLE" name="ZC_API_ENABLE" type="text" value="' . $zbp->option['ZC_API_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['enable_api_throttle'] . '</b><br/><span class="note">' . $zbp->lang['msg']['enable_api_throttle_note'] . '</span></p></td><td><p><input id="ZC_API_THROTTLE_ENABLE" name="ZC_API_THROTTLE_ENABLE" type="text" value="' . $zbp->option['ZC_API_THROTTLE_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['api_throttle_max_reqs_per_min'] . '</b><br/><span class="note">' . $zbp->lang['msg']['api_throttle_max_reqs_note'] . '</span></p></td><td><p><input id="ZC_API_THROTTLE_MAX_REQS_PER_MIN" name="ZC_API_THROTTLE_MAX_REQS_PER_MIN" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_API_THROTTLE_MAX_REQS_PER_MIN'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['api_display_count'] . '</b><br/><span class="note">' . $zbp->lang['msg']['api_throttle_max_reqs_note'] . '</span></p></td><td><p><input id="ZC_API_DISPLAY_COUNT" name="ZC_API_DISPLAY_COUNT" style="max-width:600px;width:90%;" type="text" value="' . $zbp->option['ZC_API_DISPLAY_COUNT'] . '" /></p></td></tr>';
                    echo '</table>';
                    echo '</div>';
                    ?>
                </div>
                <!-- End .content-box-content -->

            </div>
            <hr />
            <p><input type="submit" class="button" value="<?php echo $zbp->lang['msg']['submit']; ?>" id="btnPost" onclick="" /></p>
        </div>
    </form>
    <?php
    echo '<script>ActiveTopMenu("topmenu2");</script>';
    echo '<script>AddHeaderFontIcon("icon-gear-fill");</script>';
}
