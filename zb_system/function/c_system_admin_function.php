<?php

/**
 * 后台管理辅助函数相关
 * @package Z-BlogPHP
 * @subpackage System/Administrator 后台管理
 * @author Z-BlogPHP Team
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

//###############################################################################################################

/**
 * 添加页面管理子菜单(内置插件函数).
 */
function Include_Admin_Addpagesubmenu()
{
    global $zbp;
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_page'], $zbp->cmdurl . '?act=PageEdt', 'm-left', null, null, null, 'icon-file-plus-fill');
}

/**
 * 添加标签管理子菜单(内置插件函数).
 */
function Include_Admin_Addtagsubmenu()
{
    global $zbp;
    $type = (int) GetVars('type');
    $typeurl = $type > 0 ? ('&type=' . $type) : '';
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_tag'], $zbp->cmdurl . '?act=TagEdt' . $typeurl, 'm-left', null, null, null, 'icon-tag-fill');
}

/**
 * 添加分类管理子菜单(内置插件函数).
 */
function Include_Admin_Addcatesubmenu()
{
    global $zbp;
    $type = (int) GetVars('type');
    $typeurl = $type > 0 ? ('&type=' . $type) : '';
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_category'], $zbp->cmdurl . '?act=CategoryEdt' . $typeurl, 'm-left', null, null, null, 'icon-folder-plus');
}

/**
 * 添加用户管理子菜单(内置插件函数).
 */
function Include_Admin_Addmemsubmenu()
{
    global $zbp;
    if ($zbp->CheckRights('MemberNew')) {
        echo MakeSubMenu($GLOBALS['lang']['msg']['new_member'], $zbp->cmdurl . '?act=MemberNew', 'm-left', null, null, null, 'icon-person-plus-fill');
    }
    echo MakeSubMenu($GLOBALS['lang']['msg']['view_rights'], $zbp->cmdurl . '?act=misc&amp;type=vrs', 'm-left', null, null, null, 'icon-person-check-fill');
}

/**
 * 添加模块管理子菜单(内置插件函数).
 */
function Include_Admin_Addmodsubmenu()
{
    global $zbp;
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_module'], $zbp->cmdurl . '?act=ModuleEdt', 'm-left', null, null, null, 'icon-subtract');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_navbar'], $zbp->cmdurl . '?act=ModuleEdt&amp;filename=navbar');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_link'], $zbp->cmdurl . '?act=ModuleEdt&amp;filename=link');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_favorite'], $zbp->cmdurl . '?act=ModuleEdt&amp;filename=favorite');
}

/**
 * 添加评论管理子菜单(内置插件函数).
 */
function Include_Admin_Addcmtsubmenu()
{
    global $zbp;
    if ($zbp->CheckRights('CommentAll')) {
        $n = ($zbp->cache->all_comment_nums - $zbp->cache->normal_comment_nums);
        if ($n != 0) {
            $n = ' (' . max(0, $n) . ')';
        } else {
            $n = '';
        }
        echo MakeSubMenu($GLOBALS['lang']['msg']['check_comment'] . $n, $zbp->cmdurl . '?act=CommentMng&amp;ischecking=1', 'm-left ' . (GetVars('ischecking') ? 'm-now' : ''), null, null, null, 'icon-shield-shaded');
    }
}

/**
 * 添加网站设置子菜单(内置插件函数).
 */
function Include_Admin_Addsettingsubmenu()
{
    echo MakeSubMenu($GLOBALS['lang']['msg']['clear_thumb_cache'], 'javascript:window.confirm(\'' . $GLOBALS['lang']['msg']['confirm_clear_thumb_cache'] . '\') && (window.location.href = \'' . BuildSafeCmdURL('act=misc&type=clearthumbcache') . '\');', 'm-right', null, null, null, 'icon-trash-fill');
}

/**
 * 升级数据库
 */
function Include_Admin_UpdateDB()
{
    global $zbp;

    if ($zbp->version >= ZC_LAST_VERSION && (int) $zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION) {
        if (substr(GetValueInArray(get_included_files(), 0), -9) == 'index.php') {
            $zbp->SetHint('tips', '<a href="#" onclick="$.get(bloghost+\'zb_system/admin/updatedb.php\', function(data){alert(JSON.parse(data).data);window.location.reload();});">' . @$zbp->langs->msg->update_db . '</a>', 10000);
        }
    }
}

/**
 * Check Weak PassWord
 */
function Include_Admin_CheckWeakPassWord()
{
    global $zbp, $action;

    if ($zbp->user->Password != Member::GetPassWordByGuid('zblogger', $zbp->user->Guid)) {
        return;
    }

    if ($action !== 'MemberEdt') {
        Redirect302($zbp->cmdurl . '?act=MemberEdt&id=' . $zbp->user->ID);
    }

    $zbp->ShowHint('bad', $zbp->langs->msg->change_default_password, 9999);
}

/**
 * Check Http 304OK
 */
function Include_Admin_CheckHttp304OK()
{
    global $zbp, $action;
    if ($action != 'admin') {
        return;
    }
    if (!$zbp->CheckRights('root')) {
        return;
    }
    //原因是不能输出304状态的，发现输出500状态也是错的，所以检测500用于304上
    if (GetVars('http304ok', 'COOKIE') !== '1' && GetVars('http304ok', 'COOKIE') !== '0') {
        echo '<script>
         var exp = new Date();
         exp.setTime(exp.getTime() + 365*24*3600*1000);
         $(function () {  $.ajax({type: "GET",url: "' . $zbp->cmdurl . '?act=checkhttp304ok",success: function(msg){ 
            document.cookie="http304ok=0; path=' . $zbp->cookiespath . '" + "; expires=" + exp.toGMTString();
         },statusCode: {500: function() {
            document.cookie="http304ok=1; path=' . $zbp->cookiespath . '" + "; expires=" + exp.toGMTString();
         }}}); }); </script>';
    }
    if (GetVars('http304ok', 'COOKIE') === '0') {
        if ($zbp->option['ZC_JS_304_ENABLE'] == true) {
            $zbp->option['ZC_JS_304_ENABLE'] = false;
            $zbp->SaveOption();
        }
    } elseif (GetVars('http304ok', 'COOKIE') === '1') {
        if ($zbp->option['ZC_JS_304_ENABLE'] == false) {
            $zbp->option['ZC_JS_304_ENABLE'] = true;
            $zbp->SaveOption();
        }
    }
}

/**
 * Check Moblie and Response Style
 */
function Include_Admin_CheckMoblie()
{
    if (function_exists('CheckIsMobile') && CheckIsMobile()) {
        echo '<style>@media screen{body{font-size:16px}}@media screen and (max-width: 768px) {#divMain{padding:0 1px;overflow:scroll;}}@media screen and (max-width: 540px) {body{font-size:18px}}</style>';
    }
}

function Include_Admin_UpdateAppAfter()
{
    global $zbp;
    if ($zbp->cache->success_updated_app !== '') {
        echo '<script src="' . $zbp->cmdurl . '?act=misc&type=updatedapp"></script>';
    }
}

$topmenus = array();

$leftmenus = array();

/**
 * 后台管理左侧导航菜单.
 */
function ResponseAdmin_LeftMenu()
{
    global $zbp;
    global $leftmenus;

    $leftmenus['nav_new'] = MakeLeftMenu("ArticleEdt", $zbp->lang['msg']['new_article'], $zbp->cmdurl . "?act=ArticleEdt", "nav_new", "aArticleEdt", "", "icon-pencil-square-fill");
    $leftmenus['nav_article'] = MakeLeftMenu("ArticleMng", $zbp->lang['msg']['article_manage'], $zbp->cmdurl . "?act=ArticleMng", "nav_article", "aArticleMng", "", "icon-stickies");
    $leftmenus['nav_page'] = MakeLeftMenu("PageMng", $zbp->lang['msg']['page_manage'], $zbp->cmdurl . "?act=PageMng", "nav_page", "aPageMng", "", "icon-stickies-fill");

    $leftmenus[] = "<li class='split'><hr/></li>";

    $leftmenus['nav_category'] = MakeLeftMenu("CategoryMng", $zbp->lang['msg']['category_manage'], $zbp->cmdurl . "?act=CategoryMng", "nav_category", "aCategoryMng", "", "icon-folder-fill");
    $leftmenus['nav_tags'] = MakeLeftMenu("TagMng", $zbp->lang['msg']['tag_manage'], $zbp->cmdurl . "?act=TagMng", "nav_tags", "aTagMng", "", "icon-tags-fill");
    $leftmenus['nav_comment1'] = MakeLeftMenu("CommentMng", $zbp->lang['msg']['comment_manage'], $zbp->cmdurl . "?act=CommentMng", "nav_comment", "aCommentMng", "", "icon-chat-text-fill");
    $leftmenus['nav_upload'] = MakeLeftMenu("UploadMng", $zbp->lang['msg']['upload_manage'], $zbp->cmdurl . "?act=UploadMng", "nav_upload", "aUploadMng", "", "icon-inboxes-fill");
    $leftmenus['nav_member'] = MakeLeftMenu("MemberMng", $zbp->lang['msg']['member_manage'], $zbp->cmdurl . "?act=MemberMng", "nav_member", "aMemberMng", "", "icon-people-fill");

    $leftmenus[] = "<li class='split'><hr/></li>";

    $leftmenus['nav_theme'] = MakeLeftMenu("ThemeMng", $zbp->lang['msg']['theme_manage'], $zbp->cmdurl . "?act=ThemeMng", "nav_theme", "aThemeMng", "", "icon-grid-1x2-fill");
    $leftmenus['nav_module'] = MakeLeftMenu("ModuleMng", $zbp->lang['msg']['module_manage'], $zbp->cmdurl . "?act=ModuleMng", "nav_module", "aModuleMng", "", "icon-grid-3x3-gap-fill");
    $leftmenus['nav_plugin'] = MakeLeftMenu("PluginMng", $zbp->lang['msg']['plugin_manage'], $zbp->cmdurl . "?act=PluginMng", "nav_plugin", "aPluginMng", "", "icon-puzzle-fill");

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_LeftMenu'] as $fpname => &$fpsignal) {
        $fpname($leftmenus);
    }

    foreach ($leftmenus as $m) {
        echo $m;
    }
}

/**
 * 后台管理顶部菜单.
 */
function ResponseAdmin_TopMenu()
{
    global $zbp;
    global $topmenus;

    $topmenus[] = MakeTopMenu("admin", $zbp->lang['msg']['dashboard'], $zbp->cmdurl . "?act=admin", "", "", "icon-house-door-fill");
    $topmenus[] = MakeTopMenu("SettingMng", @$zbp->lang['msg']['web_settings'], $zbp->cmdurl . "?act=SettingMng", "", "", "icon-gear-fill");

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu'] as $fpname => &$fpsignal) {
        $fpname($topmenus);
    }

    $u = "https://www.zblogcn.com/";
    if (defined('APPCENTRE_DOMAIN') && constant('APPCENTRE_DOMAIN') == 'app.zblogcn.net') {
        $u = "https://www.zblogcn.net/";
    }

    $topmenus[] = MakeTopMenu("misc", $zbp->lang['msg']['official_website'], $u, "_blank", "", "icon-zblog-circle-fill");

    foreach ($topmenus as $m) {
        echo $m;
    }
}

/**
 * 添加子菜单项.
 *
 * @param $strName
 * @param $strUrl
 * @param $strClass
 * @param $strTarget
 * @param $strId
 * @param $strTitle
 *
 * @return null|string
 */
function MakeSubMenu($strName, $strUrl, $strClass = 'm-left', $strTarget = '', $strId = '', $strTitle = '', $strIconClass = '')
{
    $s = '<a href="' . $strUrl . '" ';
    if ($strTarget) {
        $s .= 'target="' . $strTarget . '"';
    }
    if ($strId) {
        $s .= 'id="' . $strId . '"';
    }
    if ($strTitle) {
        $s .= 'title="' . $strTitle . '" ' . 'alt="' . $strTitle . '" ';
    }
    $s .= '>';
    $strIconElem = $strIconClass !== "" ? "<i class=\"" . $strIconClass . "\" style=\"line-height: 1em;\"></i> " : "";
    $s .= '<span class="' . $strClass . '">' . $strIconElem . $strName . '</span></a>';

    return $s;
}

/**
 * 添加顶部菜单项.
 *
 * @param $requireAction
 * @param $strName
 * @param $strUrl
 * @param $strTarget
 * @param $strLiId
 * @param $strIconClass
 *
 * @return null|string
 */
function MakeTopMenu($requireAction, $strName, $strUrl, $strTarget, $strLiId, $strIconClass = "")
{
    global $zbp;

    static $AdminTopMenuCount = 0;
    if ($zbp->CheckRights($requireAction) == false) {
        return '';
    }

    $tmp = null;
    if ($strTarget == "") {
        $strTarget = "_self";
    }
    $AdminTopMenuCount = ($AdminTopMenuCount + 1);
    if ($strLiId == "") {
        $strLiId = "topmenu" . $AdminTopMenuCount;
    }
    $strIconElem = $strIconClass !== "" ? "<i class=\"" . $strIconClass . "\"></i><span>" : "<span>";
    $tmp = "<li id=\"" . $strLiId . "\"><a href=\"" . $strUrl . "\" target=\"" . $strTarget . "\" title=\"" . htmlspecialchars($strName) . "\">" . $strIconElem . $strName . "</span></a></li>";

    return $tmp;
}

/**
 * 添加左侧菜单项.
 *
 * @param $requireAction
 * @param $strName
 * @param $strUrl
 * @param $strLiId
 * @param $strAId
 * @param $strImgUrl
 *
 * @return null|string
 */
function MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl, $strIconClass = "")
{
    global $zbp;

    static $AdminLeftMenuCount = 0;
    if ($zbp->CheckRights($requireAction) == false) {
        return '';
    }

    $AdminLeftMenuCount = ($AdminLeftMenuCount + 1);
    $tmp = null;

    if ($strIconClass != "") {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span><i class=\"" . $strIconClass . "\"></i>" . $strName . "</span></a></li>";
    } elseif ($strImgUrl != "") {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span class=\"bgicon\" style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . "</span></a></li>";
    } else {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span><i class=\"icon-window-fill\"></i>" . $strName . "</span></a></li>";
    }

    return $tmp;
}

//###############################################################################################################

/**
 * 生成通用表单的option列表.
 *
 * @param $default
 * @param $array
 * @param $name
 *
 * @return null|string
 */
function OutputOptionItemsOfCommon($default, $array, $name = 'Common')
{
    global $zbp;
    $s = null;
    $tz = $array;
    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCommon'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, $name);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成分类select表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfCategories($default, $type = 0)
{
    global $zbp;

    $s = null;
    $tz = array();
    foreach ($zbp->categoriesbyorder_type[$type] as $id => $cate) {
        $tz[$cate->ID] = $cate->SymbolName;
    }
    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCategories'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, $type);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成模板select表单.
 *
 * @param $default
 * @param $refuse_file_filter
 * @param $accept_type
 *
 * @return null|string
 */
function OutputOptionItemsOfTemplate($default, $refuse_file_filter = array(), $accept_type = array())
{
    global $zbp;
    $testRegExp = "/^(\.|post-|module|header|footer|comment|sidebar|pagebar|[a-zA-Z]\_)/si";
    $s = null;
    $tz = array();
    $tz[''] = $zbp->lang['msg']['none'];

    //type = list,single,article,page,category,tag,author,date，可以并列多个

    foreach ($zbp->template->templates as $key => $value) {
        if (preg_match($testRegExp, $key)) {
            continue;
        }

        $b = false;
        foreach ($refuse_file_filter as $key2 => $value2) {
            $testRegExp2 = "/.*($value2)/si";
            if (preg_match($testRegExp2, $key)) {
                $b = true;
            }
        }
        if ($b == true) {
            continue;
        }

        $name = $zbp->template->templates_Name[$key];
        $type = $zbp->template->templates_Type[$key];
        $typeArray = explode('|', $type);

        if (strtolower($type) == 'none') {
            continue;
        }

        //判断主题是否对模板进行了Template Type标注
        if ($zbp->template->isuse_nameandtype == true) { //用$accept_type去检查$typeArray，为真$c就是true就可以放入列表
            $c = false;
            foreach ($accept_type as $k1 => $v1) {
                foreach ($typeArray as $k2 => $v2) {
                    if (strtolower(trim($v1)) == strtolower(trim($v2))) {
                        $c = true;
                    }
                }
            }
            if ($c) {
                if ($default == $key) {
                    $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
                    $tz[$key] = '[' . $zbp->lang['msg']['current_template'] . '] ' . $key . $s2;
                } else {
                    $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
                    $tz[$key] = $key . $s2;
                }
            }
        } else { //没有标注就用传统方法
            if ($default == $key) {
                $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
                $tz[$key] = '[' . $zbp->lang['msg']['current_template'] . '] ' . $key . $s2;
            } else {
                $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
                $tz[$key] = $key . $s2;
            }
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfTemplate'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, $refuse_file_filter, $accept_type);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成用户等级select表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfMemberLevel($default)
{
    global $zbp;

    $s = null;
    $tz = array();
    if (!$zbp->CheckRights('MemberAll')) {
        $tz[$default] = $zbp->lang['user_level_name'][$default];
    } else {
        for ($i = 1; $i < (count($zbp->lang['user_level_name']) + 1); $i++) {
            $tz[$i] = $zbp->lang['user_level_name'][$i];
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfMemberLevel'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成用户select表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfMember($default, $posttype = 0, $checkaction = 'edit')
{
    global $zbp;

    $s = null;
    $tz = array();
    $max_level = (int) $zbp->option['ZC_OUTPUT_OPTION_MEMBER_MAX_LEVEL'];

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfMember_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $posttype, $checkaction, $tz);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    $actions = $zbp->GetPostType($posttype, 'actions');

    if (!$zbp->CheckRights($actions['all'])) {
        if (!isset($zbp->members[$default])) {
            $tz[0] = '';
        } else {
            $tz[$default] = $zbp->members[$default]->Name;
        }
    } else {
        for ($i = 1; $i < (count($zbp->lang['user_level_name']) + 1); $i++) {
            if ($zbp->CheckRightsByLevel($actions[$checkaction], $i) == false) {
                $i = ($i - 1);
                break;
            }
        }
        if ($max_level > 0 && $i > $max_level) {
            $i = $max_level;
        }
        if ($i > 0) {
            $zbp->LoadMembers($i);
        }
        $memberbyname = array();
        foreach ($zbp->members as $key => $value) {
            if ($zbp->CheckRightsByLevel($actions[$checkaction], $zbp->members[$key]->Level)) {
                $memberbyname[$zbp->members[$key]->Name] = $zbp->members[$key]->ID;
            }
        }
        if (!empty($default)) {
            $m = $zbp->GetMemberByID($default);
            if (!empty($m->ID)) {
                $memberbyname[$m->Name] = $m->ID;
            }
        }
        ksort($memberbyname);
        foreach ($memberbyname as $key => $value) {
            $tz[$value] = $key;
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfMember'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成文章IsTop状态select表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfIsTop($default)
{
    global $zbp;

    $s = null;
    $tz = array();
    $tz[0] = $zbp->lang['msg']['none'];
    $tz[2] = $zbp->lang['msg']['top_index'];
    $tz[1] = $zbp->lang['msg']['top_global'];
    $tz[4] = $zbp->lang['msg']['top_categorys'];
    //$tz[8] = $zbp->lang['msg']['top_category'];

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfIsTop'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }

    return $s;
}

/**
 * 生成文章发布状态select表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfPostStatus($default)
{
    global $zbp;

    $s = '';
    $tz = array();
    if (!$zbp->CheckRights('ArticlePub') && $default == 2) {
        $tz[2] = $zbp->lang['post_status_name']['2'];
    } elseif (!$zbp->CheckRights('ArticleAll') && $default == 2) {
        $tz[2] = $zbp->lang['post_status_name']['2'];
    } else {
        $tz[0] = $zbp->lang['post_status_name']['0'];
        $tz[1] = $zbp->lang['post_status_name']['1'];
        if ($zbp->CheckRights('ArticleAll')) {
            $tz[2] = $zbp->lang['post_status_name']['2'];
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfPostStatus'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }

    return $s;
}

/**
 * 创建Div模块.
 *
 * @param $m
 * @param bool $button
 */
function CreateModuleDiv($m, $button = true)
{
    global $zbp;

    echo '<div class="widget widget_source_' . $m->SourceType . ' widget_id_' . $m->FileName . '">';
    echo '<div class="widget-title"><i class="icon-layout-wtf module-icon"></i>' . (($m->SourceType != 'themeinclude') ? $m->Name : $m->FileName) . '';

    if ($button) {
        echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;id=' . $m->ID . '"><i class="icon-pencil-square"></i></a>';

        if ($m->SourceType == 'user' || $m->SourceType == 'themeinclude') {
            echo '&nbsp;<a onclick="return window.confirm(\'' . str_replace(array('"', '\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=ModuleDel&amp;id=' . $m->ID) . '"><i class="icon-trash"></i></a>';
        }
        echo '</span>';
    }

    echo '</div>';
    echo '<div class="funid" style="display:none">' . $m->FileName . '</div>';
    echo '</div>';
}

/**
 * 生成TYPEselect表单.
 *
 * @param $default
 *
 * @return null|string
 */
function OutputOptionItemsOfPostType($default)
{
    global $zbp;
    $s = null;
    $tz = array();

    foreach ($zbp->posttype as $key => $value) {
        $tz[$key] = $value['name'];
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCommon'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, 'PostType');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成时区select表单.
 *
 * @param $default
 *
 * @return string
 */
function CreateOptionsOfTimeZone($default)
{
    $s = '';
    $tz = array(
        'Etc/GMT+12'                     => '-12:00',
        'Pacific/Midway'                 => '-11:00',
        'Pacific/Honolulu'               => '-10:00',
        'America/Anchorage'              => '-09:00',
        'America/Los_Angeles'            => '-08:00',
        'America/Denver'                 => '-07:00',
        'America/Tegucigalpa'            => '-06:00',
        'America/New_York'               => '-05:00',
        'America/Halifax'                => '-04:00',
        'America/Argentina/Buenos_Aires' => '-03:00',
        'Atlantic/South_Georgia'         => '-02:00',
        'Atlantic/Azores'                => '-01:00',
        'UTC'                            => '00:00',
        'Europe/Berlin'                  => '+01:00',
        'Europe/Sofia'                   => '+02:00',
        'Africa/Nairobi'                 => '+03:00',
        'Europe/Moscow'                  => '+04:00',
        'Asia/Karachi'                   => '+05:00',
        'Asia/Dhaka'                     => '+06:00',
        'Asia/Bangkok'                   => '+07:00',
        'Asia/Shanghai'                  => '+08:00',
        'Asia/Tokyo'                     => '+09:00',
        'Pacific/Guam'                   => '+10:00',
        'Australia/Sydney'               => '+11:00',
        'Pacific/Fiji'                   => '+12:00',
        'Pacific/Tongatapu'              => '+13:00',
    );

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCommon'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, 'TimeZone');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $key . ' ' . $value . '</option>';
    }

    return $s;
}

/**
 * 生成语言select表单.
 *
 * @param $default
 *
 * @return string
 */
function CreateOptionsOfLang($default)
{
    global $zbp;
    $s = '';
    $dir = $zbp->usersdir . 'language/';
    $files = GetFilesInDir($dir, 'php');
    $tz = array();
    foreach ($files as $f) {
        if (substr($f, 0, 1) !== '.' && substr($f, 0, 1) !== '_') {
            $n = basename($f, '.php');
            //fix 1.3 to 1.4 warning
            if ('SimpChinese' == $n) {
                continue;
            }

            if ('TradChinese' == $n) {
                continue;
            }

            $t = include $f;
            $tz[$n] = $t['lang_name'] . ' (' . $n . ')';
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCommon'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, 'Lang');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $key . ' ' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成GuestType表单.
 *
 * @param $default
 *
 * @return string
 */
function CreateOptionsOfGuestIPType($default)
{
    global $zbp;
    $s = '';
    $tz = array(
        'REMOTE_ADDR'                    => 'REMOTE_ADDR (' . $zbp->lang['msg']['default'] . ') ' . GetVars('REMOTE_ADDR', 'SERVER'),
        'HTTP_X_FORWARDED_FOR'           => 'HTTP_X_FORWARDED_FOR (腾讯云,阿里云,七牛) ' . GetVars('HTTP_X_FORWARDED_FOR', 'SERVER'),
        'HTTP_X_REAL_IP'                 => 'HTTP_X_REAL_IP (又拍云,百度CDN)' . GetVars('HTTP_X_REAL_IP', 'SERVER'),
        'HTTP_CF_CONNECTING_IP'          => 'HTTP_CF_CONNECTING_IP (CloudFlare) ' . GetVars('HTTP_CF_CONNECTING_IP', 'SERVER'),
        'HTTP_CLIENT_IP'                 => 'HTTP_CLIENT_IP ' . GetVars('HTTP_CLIENT_IP', 'SERVER'),
    );

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCommon'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz, 'GuestIPType');
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    foreach ($tz as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }
    return $s;
}

/**
 * 生成排序按钮
 */
function MakeOrderButton($id, $urlrule, $order_get, $default = 'asc')
{
    $button_order_id_class = '';
    if ($order_get == $id . '_asc' || $order_get == $id . '_desc') {
        $button_order_id_class = 'element-visibility-visible-always ';
    }
    if ($order_get == $id . '_asc') {
        $button_order_id_order = $id . '_desc';
        $button_order_id_icon = 'icon-arrow-up-short';
        $button_order_id_class .= 'element-visibility-hidden ';
    } elseif ($order_get == $id . '_desc') {
        $button_order_id_order = $id . '_asc';
        $button_order_id_icon = 'icon-arrow-down-short';
        $button_order_id_class .= 'element-visibility-hidden ';
    } else {
        if ($default == 'asc') {
            $button_order_id_order = $id . '_asc';
            $button_order_id_icon = 'icon-arrow-down-short';
            $button_order_id_class .= 'element-visibility-hidden ';
        } else {
            $button_order_id_order = $id . '_desc';
            $button_order_id_icon = 'icon-arrow-up-short';
            $button_order_id_class .= 'element-visibility-hidden ';
        }
    }
    $urlrule->Rules['{%order%}'] = $button_order_id_order;
    $button_order_id = ' <a class="order_button ' . $button_order_id_class . '" href="' . $urlrule->Make() . '"><i style="font-size:0.75em;" class="' . $button_order_id_icon . '"></i></a>';

    return array($button_order_id);
}
