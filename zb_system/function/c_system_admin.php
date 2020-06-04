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

$zbp->ismanage = true;

/**
 * 添加页面管理子菜单(内置插件函数).
 */
function Include_Admin_Addpagesubmenu()
{
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_page'], '../cmd.php?act=PageEdt');
}

/**
 * 添加标签管理子菜单(内置插件函数).
 */
function Include_Admin_Addtagsubmenu()
{
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_tag'], '../cmd.php?act=TagEdt');
}

/**
 * 添加分类管理子菜单(内置插件函数).
 */
function Include_Admin_Addcatesubmenu()
{
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_category'], '../cmd.php?act=CategoryEdt');
}

/**
 * 添加用户管理子菜单(内置插件函数).
 */
function Include_Admin_Addmemsubmenu()
{
    global $zbp;
    if ($zbp->CheckRights('MemberNew')) {
        echo MakeSubMenu($GLOBALS['lang']['msg']['new_member'], '../cmd.php?act=MemberNew');
    }
    echo MakeSubMenu($GLOBALS['lang']['msg']['view_rights'], '../cmd.php?act=misc&amp;type=vrs');
}

/**
 * 添加模块管理子菜单(内置插件函数).
 */
function Include_Admin_Addmodsubmenu()
{
    echo MakeSubMenu($GLOBALS['lang']['msg']['new_module'], '../cmd.php?act=ModuleEdt');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_navbar'], '../cmd.php?act=ModuleEdt&amp;filename=navbar');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_link'], '../cmd.php?act=ModuleEdt&amp;filename=link');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_favorite'], '../cmd.php?act=ModuleEdt&amp;filename=favorite');
    echo MakeSubMenu($GLOBALS['lang']['msg']['module_misc'], '../cmd.php?act=ModuleEdt&amp;filename=misc');
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
            $n = ' (' . $n . ')';
        } else {
            $n = '';
        }
        echo MakeSubMenu($GLOBALS['lang']['msg']['check_comment'] . $n, '../cmd.php?act=CommentMng&amp;ischecking=1', 'm-left ' . (GetVars('ischecking') ? 'm-now' : ''));
    }
}

/**
 * 升级数据库
 */
function Include_Admin_UpdateDB()
{
    global $zbp;

    if ($zbp->version >= 172330 && (int) $zbp->option['ZC_LAST_VERSION'] < 172330) {
        if (substr(GetValueInArray(get_included_files(), 0), -9) == 'index.php') {
            $zbp->SetHint('tips', '<a href="#" onclick="$.get(bloghost+\'zb_system/admin/updatedb.php\', function(data){alert(JSON.parse(data).data);window.location.reload();});">' . @$zbp->langs->msg->update_db . '</a>');
        }
    }
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
    if (GetVars('http304ok', 'COOKIE') !== '1') {
        echo '<script>$(function () { $.ajax({type: "GET",url: "' . $zbp->host . 'zb_system/cmd.php?act=checkhttp304ok",success: function(msg){zbp.cookie.set(\'http304ok\',\'0\',365);},statusCode: {500: function() {zbp.cookie.set(\'http304ok\',\'1\',365);}}}); });</script>';
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

$topmenus = array();

$leftmenus = array();

/**
 * 后台管理左侧导航菜单.
 */
function ResponseAdmin_LeftMenu()
{
    global $zbp;
    global $leftmenus;

    $leftmenus['nav_new'] = MakeLeftMenu("ArticleEdt", $zbp->lang['msg']['new_article'], $zbp->host . "zb_system/cmd.php?act=ArticleEdt", "nav_new", "aArticleEdt", "");
    $leftmenus['nav_article'] = MakeLeftMenu("ArticleMng", $zbp->lang['msg']['article_manage'], $zbp->host . "zb_system/cmd.php?act=ArticleMng", "nav_article", "aArticleMng", "");
    $leftmenus['nav_page'] = MakeLeftMenu("PageMng", $zbp->lang['msg']['page_manage'], $zbp->host . "zb_system/cmd.php?act=PageMng", "nav_page", "aPageMng", "");

    $leftmenus[] = "<li class='split'><hr/></li>";

    $leftmenus['nav_category'] = MakeLeftMenu("CategoryMng", $zbp->lang['msg']['category_manage'], $zbp->host . "zb_system/cmd.php?act=CategoryMng", "nav_category", "aCategoryMng", "");
    $leftmenus['nav_tags'] = MakeLeftMenu("TagMng", $zbp->lang['msg']['tag_manage'], $zbp->host . "zb_system/cmd.php?act=TagMng", "nav_tags", "aTagMng", "");
    $leftmenus['nav_comment1'] = MakeLeftMenu("CommentMng", $zbp->lang['msg']['comment_manage'], $zbp->host . "zb_system/cmd.php?act=CommentMng", "nav_comment", "aCommentMng", "");
    $leftmenus['nav_upload'] = MakeLeftMenu("UploadMng", $zbp->lang['msg']['upload_manage'], $zbp->host . "zb_system/cmd.php?act=UploadMng", "nav_upload", "aUploadMng", "");
    $leftmenus['nav_member'] = MakeLeftMenu("MemberMng", $zbp->lang['msg']['member_manage'], $zbp->host . "zb_system/cmd.php?act=MemberMng", "nav_member", "aMemberMng", "");

    $leftmenus[] = "<li class='split'><hr/></li>";

    $leftmenus['nav_theme'] = MakeLeftMenu("ThemeMng", $zbp->lang['msg']['theme_manage'], $zbp->host . "zb_system/cmd.php?act=ThemeMng", "nav_theme", "aThemeMng", "");
    $leftmenus['nav_module'] = MakeLeftMenu("ModuleMng", $zbp->lang['msg']['module_manage'], $zbp->host . "zb_system/cmd.php?act=ModuleMng", "nav_module", "aModuleMng", "");
    $leftmenus['nav_plugin'] = MakeLeftMenu("PluginMng", $zbp->lang['msg']['plugin_manage'], $zbp->host . "zb_system/cmd.php?act=PluginMng", "nav_plugin", "aPluginMng", "");

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

    $topmenus[] = MakeTopMenu("admin", $zbp->lang['msg']['dashboard'], $zbp->host . "zb_system/cmd.php?act=admin", "", "");
    $topmenus[] = MakeTopMenu("SettingMng", @$zbp->lang['msg']['web_settings'], $zbp->host . "zb_system/cmd.php?act=SettingMng", "", "");

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu'] as $fpname => &$fpsignal) {
        $fpname($topmenus);
    }

    $topmenus[] = MakeTopMenu("misc", $zbp->lang['msg']['official_website'], "https://www.zblogcn.com/", "_blank", "");

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
function MakeSubMenu($strName, $strUrl, $strClass = 'm-left', $strTarget = '', $strId = '', $strTitle = '')
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
    $s .= '<span class="' . $strClass . '">' . $strName . '</span></a>';

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
 *
 * @return null|string
 */
function MakeTopMenu($requireAction, $strName, $strUrl, $strTarget, $strLiId)
{
    global $zbp;

    static $AdminTopMenuCount = 0;
    if ($zbp->CheckRights($requireAction) == false) {
        return;
    }

    $tmp = null;
    if ($strTarget == "") {
        $strTarget = "_self";
    }
    $AdminTopMenuCount = ($AdminTopMenuCount + 1);
    if ($strLiId == "") {
        $strLiId = "topmenu" . $AdminTopMenuCount;
    }
    $tmp = "<li id=\"" . $strLiId . "\"><a href=\"" . $strUrl . "\" target=\"" . $strTarget . "\" title=\"" . htmlspecialchars($strName) . "\">" . $strName . "</a></li>";

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
function MakeLeftMenu($requireAction, $strName, $strUrl, $strLiId, $strAId, $strImgUrl)
{
    global $zbp;

    static $AdminLeftMenuCount = 0;
    if ($zbp->CheckRights($requireAction) == false) {
        return;
    }

    $AdminLeftMenuCount = ($AdminLeftMenuCount + 1);
    $tmp = null;
    if ($strImgUrl != "") {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . "</span></a></li>";
    } else {
        $tmp = "<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\" title=\"" . strip_tags($strName) . "\"><span>" . $strName . "</span></a></li>";
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
function OutputOptionItemsOfCategories($default)
{
    global $zbp;

    $s = null;
    $tz = array();
    foreach ($zbp->categoriesbyorder as $id => $cate) {
        $tz[$cate->ID] = $cate->SymbolName;
    }
    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCategories'] as $fpname => &$fpsignal) {
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
    $testRegExp = "/.*(\.|post-|module|header|footer|comment|sidebar|pagebar|[a-zA-Z]\_)/si";
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

        if ($default == $key) {
            $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
            $tz[$key] = '[' . $zbp->lang['msg']['current_template'] . '] ' . $key . $s2;
        } else {
            $s2 = ($name !== '') ? ' (' . $name . ')' : $name;
            $tz[$key] = $key . $s2;
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
function OutputOptionItemsOfMember($default)
{
    global $zbp;

    $s = null;
    $tz = array();

    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfMember_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default, $tz);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    if (!$zbp->CheckRights('ArticleAll')) {
        if (!isset($zbp->members[$default])) {
            $tz[0] = '';
        } else {
            $tz[$default] = $zbp->members[$default]->Name;
        }
    } else {
        for ($i = 1; $i < (count($zbp->lang['user_level_name']) + 1); $i++) {
            if ($zbp->CheckRightsByLevel('ArticleEdt', $i) == false) {
                break;
            }
        }
        $zbp->LoadMembers($i);
        $memberbyname = array();
        foreach ($zbp->members as $key => $value) {
            if ($zbp->CheckRightsByLevel('ArticleEdt', $zbp->members[$key]->Level)) {
                $memberbyname[$zbp->members[$key]->Name] = $zbp->members[$key]->ID;
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
    $tz[4] = $zbp->lang['msg']['top_category'];

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
    echo '<div class="widget-title"><img class="more-action" width="16" src="../image/admin/brick.png" alt="" />' . (($m->SourceType != 'theme' || $m->Source == 'plugin_' . $zbp->theme) ? $m->Name : $m->FileName) . '';

    if ($button) {
        if (!$m->IsIncludeFile) {
            echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;id=' . $m->ID . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>';
        } else {
            echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;source=theme&amp;filename=' . $m->FileName . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>';
            echo '&nbsp;<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=ModuleDel&amp;source=theme&amp;filename=' . $m->FileName) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>';
        }
        if ($m->SourceType != 'system'
            && $m->SourceType != 'theme'
            && !($m->SourceType == 'plugin'
            && CheckRegExp($m->Source, '/plugin_(' . $zbp->option['ZC_USING_PLUGIN_LIST'] . ')/i'))
        ) {
            echo '&nbsp;<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=ModuleDel&amp;id=' . $m->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>';
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
        $tz[$key] = $value[0];
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

    echo '<table class="tableFull tableBorder table_striped table_hover" id="tbStatistic"><tr><th colspan="4"  scope="col">&nbsp;' . $zbp->lang['msg']['site_analyze'];
    if ($zbp->CheckRights('root')) {
        echo '&nbsp;<a href="javascript:statistic(\'' . BuildSafeCmdURL('act=misc&type=statistic&forced=1') . '\');" id="statistic">[' . $zbp->lang['msg']['refresh_cache'] . ']</a>';
    }
    echo ' <img id="statloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';

    if ((time() - (int) $zbp->cache->reload_statistic_time) > (6 * 24 * 60 * 60)) {
        echo '<script>$(document).ready(function(){ statistic(\'' . BuildSafeCmdURL('act=misc&type=statistic') . '\'); });</script>';
    } else {
        $echoStatistic = true;
        $r = $zbp->cache->reload_statistic;
        $r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
        $r = str_replace('{$zbp->theme}', $zbp->theme, $r);
        $r = str_replace('{$zbp->style}', $zbp->style, $r);
        $r = str_replace('{$system_environment}', GetEnvironment(), $r);
        $r = str_replace('{$zbp->version}', ZC_VERSION_FULL, $r);
        $r = str_replace('{$theme_version}', '(v' . $zbp->themeapp->version . ')', $r);
        if ($zbp->option['ZC_DEBUG_MODE']) {
            $r = str_replace('<!--debug_mode_note-->', "<tr><td colspan='4' style='text-align: center'>{$zbp->lang['msg']['debugging_warning']}</td></tr>", $r);
        }
        echo $r;
    }

    echo '</table>';

    echo '<table class="tableFull tableBorder table_striped table_hover" id="tbUpdateInfo"><tr><th>&nbsp;' . $zbp->lang['msg']['latest_news'];
    if ($zbp->CheckRights('root')) {
        echo '&nbsp;<a href="javascript:updateinfo(\'' . BuildSafeCmdURL('act=misc&type=updateinfo') . '\');">[' . $zbp->lang['msg']['refresh'] . ']</a>';
    }
    echo ' <img id="infoloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';

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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/home_32.png' . '");</script>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&page=%page%}{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%category%}'] = GetVars('category');
    $p->UrlRule->Rules['{%search%}'] = rawurlencode(GetVars('search'));
    $p->UrlRule->Rules['{%status%}'] = GetVars('status');
    $p->UrlRule->Rules['{%istop%}'] = (bool) GetVars('istop');

    $w = array();
    if (!$zbp->CheckRights('ArticleAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }
    if (GetVars('search')) {
        $w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', GetVars('search'));
    }
    if (GetVars('istop')) {
        $w[] = array('<>', 'log_Istop', '0');
    }
    if (GetVars('status')) {
        $w[] = array('=', 'log_Status', GetVars('status'));
    }
    if (GetVars('category')) {
        $w[] = array('=', 'log_CateID', GetVars('category'));
    }

    $s = '';
    $or = array('log_PostTime' => 'DESC');
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    $type = null;
    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Article'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op, $type);
    }

    $array = $zbp->GetArticleList(
        $s,
        $w,
        $or,
        $l,
        $op,
        false
    );

    echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=PostBat&type=' . ZC_POST_TYPE_ARTICLE . '">';
    echo '<table border="1" class="tableFull tableBorder table_hover table_striped tableBorder-thcenter">';

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['category'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['author'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['title'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . '</th>';
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
        $tabletds[] = '<td><a href="' . $article->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $article->Title . '</td>';
        $tabletds[] = '<td class="td20">' . $article->Time() . '</td>';
        $tabletds[] = '<td class="td5">' . $article->CommNums . '</td>';
        $tabletds[] = '<td class="td5">' . ($article->IsTop ? $zbp->lang['msg']['top'] . '|' : '') . $article->StatusName . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=ArticleEdt&amp;id=' . $article->ID . '"><img src="../image/admin/page_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=ArticleDel&amp;id=' . $article->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .
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

    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        echo '<input  style="float:right;" type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" value="' . $zbp->lang['msg']['all_del'] . '">';
    }
    echo '</p></form></div>';
    echo '<script>ActiveLeftMenu("aArticleMng");</script>';
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/article_32.png' . '");</script>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=PageMng{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $w = array();
    if (!$zbp->CheckRights('PageAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }

    $s = '';
    $or = array('log_PostTime' => 'DESC');
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Page'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetPageList(
        $s,
        $w,
        $or,
        $l,
        $op
    );

    echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=PostBat&type=' . ZC_POST_TYPE_PAGE . '">';
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['author'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['title'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . '</th>';
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
        $tabletds[] = '<td><a href="' . $article->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $article->Title . '</td>';
        $tabletds[] = '<td class="td20">' . $article->Time() . '</td>';
        $tabletds[] = '<td class="td5">' . $article->CommNums . '</td>';
        $tabletds[] = '<td class="td5">' . $article->StatusName . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=PageEdt&amp;id=' . $article->ID . '"><img src="../image/admin/page_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=PageDel&id=' . $article->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .
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
    foreach ($p->Buttons as $key => $value) {
        if ($p->PageNow == $key) {
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';
        } else {
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';
        }
    }
    if ($zbp->CheckRights('PostBat') && $zbp->option['ZC_POST_BATCH_DELETE']) {
        echo '<input  style="float:right;" type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" value="' . $zbp->lang['msg']['all_del'] . '">';
    }
    echo '</p><form></div>';
    echo '<script>ActiveLeftMenu("aPageMng");</script>';
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/page_32.png' . '");</script>';
}

//###############################################################################################################

/**
 * 后台分类管理.
 */
function Admin_CategoryMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['category_manage'] . '</div>';
    echo '<div class="SubMenu">';
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CategoryMng_SubMenu'] as $fpname => &$fpsignal) {
        $fpname();
    }
    echo '</div>';
    echo '<div id="divMain2">';
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';

    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['order'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['post_count'] . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '</tr>';

    foreach ($zbp->categoriesbyorder as $category) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $category->ID . '</td>';
        $tabletds[] = '<td class="td5">' . $category->Order . '</td>';
        $tabletds[] = '<td class="td25"><a href="' . $category->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $category->Symbol . $category->Name . '</td>';
        $tabletds[] = '<td class="td20">' . $category->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . $category->Count . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=CategoryEdt&amp;id=' . $category->ID . '"><img src="../image/admin/folder_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            ((count($category->SubCategories) == 0) ? '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=CategoryDel&amp;id=' . $category->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' : '') .
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
    echo '</div>';
    echo '<script>ActiveLeftMenu("aCategoryMng");</script>';
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/category_32.png' . '");</script>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=CommentMng{&page=%page%}{&ischecking=%ischecking%}{&search=%search%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%search%}'] = rawurlencode(GetVars('search'));
    $p->UrlRule->Rules['{%ischecking%}'] = (bool) GetVars('ischecking');

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
    $or = array('comm_ID' => 'DESC');
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
    $op = array('pagebar' => $p);

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

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['parend_id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['content'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['article'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . '</th>';
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

        $tabletds[] = '<td class="td10"><span class="cmt-note" title="' . $zbp->lang['msg']['email'] . ':' . htmlspecialchars($cmt->Email) . '"><a href="mailto:' . htmlspecialchars($cmt->Email) . '">' . $cmt->Author->Name . '</a></span></td>';
        $tabletds[] = '<td><div style="overflow:hidden;max-width:500px;">' .
            (
                ($article) ? '<a href="' . $article->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' : '<a href="javascript:;"><img src="../image/admin/delete.png" alt="no exists" title="no exists" width="16" /></a>') .
            $cmt->Content . '<div></td>';
        $tabletds[] = '<td class="td5">' . $cmt->LogID . '</td>';
        $tabletds[] = '<td class="td15">' . $cmt->Time() . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=CommentDel&amp;id=' . $cmt->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            (!GetVars('ischecking', 'GET') ? '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><img src="../image/admin/minus-shield.png" alt="' . $zbp->lang['msg']['audit'] . '" title="' . $zbp->lang['msg']['audit'] . '" width="16" /></a>' : '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><img src="../image/admin/ok.png" alt="' . $zbp->lang['msg']['pass'] . '" title="' . $zbp->lang['msg']['pass'] . '" width="16" /></a>') .
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
        echo '<input type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '<input type="submit" name="all_pass"  value="' . $zbp->lang['msg']['all_pass'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    } else {
        echo '<input type="submit" name="all_del" onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/comments_32.png' . '");$(".cmt-note").tooltip();</script>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=MemberMng{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

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
    $array = $zbp->GetMemberList(
        '',
        $w,
        array('mem_ID' => 'ASC'),
        array(($p->PageNow - 1) * $p->PageCount, $p->PageCount),
        array('pagebar' => $p)
    );

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['member_level'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . '</th>';
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
        $tabletds[] = '<td class="td10">' . $member->LevelName . ($member->Status > 0 ? '(' . $zbp->lang['user_status_name'][$member->Status] . ')' : '') . '</td>';
        $tabletds[] = '<td><a href="' . $member->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $member->Name . '</td>';
        $tabletds[] = '<td class="td15">' . $member->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . $member->Articles . '</td>';
        $tabletds[] = '<td class="td10">' . $member->Pages . '</td>';
        $tabletds[] = '<td class="td10">' . $member->Comments . '</td>';
        $tabletds[] = '<td class="td10">' . $member->Uploads . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=MemberEdt&amp;id=' . $member->ID . '"><img src="../image/admin/user_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>' .
            (($zbp->CheckRights('MemberDel') && ($member->IsGod !== true)) ? '&nbsp;&nbsp;&nbsp;&nbsp;' .
                '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=MemberDel&amp;id=' . $member->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' : '') .
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/user_32.png' . '");</script>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=UploadMng{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $array = $zbp->GetUploadList(
        '',
        $w,
        array('ul_PostTime' => 'DESC'),
        array(($p->PageNow - 1) * $p->PageCount, $p->PageCount),
        array('pagebar' => $p)
    );

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableHeaders = array();
    $tableHeaders[] = '<tr>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['author'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['date'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['size'] . '</th>';
    $tableHeaders[] = '<th>' . $zbp->lang['msg']['type'] . '</th>';
    $tableHeaders[] = '<th></th>';
    $tableHeaders[] = '</tr>';

    foreach ($array as $upload) {
        $ret = array(); //table string
        $ret[] = '<tr>';
        $ret[] = '<td class="td5">' . $upload->ID . '</td>';
        $ret[] = '<td class="td10">' . htmlspecialchars($upload->Author->Name) . '</td>';
        $ret[] = '<td><a href="' . htmlspecialchars($upload->Url) . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . htmlspecialchars($upload->Name) . '</td>';
        $ret[] = '<td class="td15">' . $upload->Time() . '</td>';
        $ret[] = '<td class="td10">' . $upload->Size . '</td>';
        $ret[] = '<td class="td20">' . htmlspecialchars($upload->MimeType) . '</td>';
        $ret[] = '<td class="td10 tdCenter">' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=UploadDel&amp;id=' . $upload->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/accessories_32.png' . '");</script>';
}

//###############################################################################################################

/**
 * 后台标签管理.
 */
function Admin_TagMng()
{
    global $zbp;

    echo '<div class="divHeader">' . $zbp->lang['msg']['tag_manage'] . '</div>';
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

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=TagMng&page={%page%}{&search=%search%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }

    $p->UrlRule->Rules['{%search%}'] = rawurlencode(GetVars('search'));

    $w = array();
    if (GetVars('search')) {
        $w[] = array('search', 'tag_Name', 'tag_Alias', 'tag_Intro', GetVars('search'));
    }

    $array = $zbp->GetTagList(
        '',
        $w,
        array('tag_ID' => 'ASC'),
        array(($p->PageNow - 1) * $p->PageCount, $p->PageCount),
        array('pagebar' => $p)
    );

    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
    $tables = '';
    $tableths = array();
    $tableths[] = '<tr>';
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['alias'] . '</th>';
    $tableths[] = '<th>' . $zbp->lang['msg']['post_count'] . '</th>';
    $tableths[] = '<th></th>';
    $tableths[] = '</tr>';

    foreach ($array as $tag) {
        $tabletds = array(); //table string
        $tabletds[] = '<tr>';
        $tabletds[] = '<td class="td5">' . $tag->ID . '</td>';
        $tabletds[] = '<td class="td25"><a href="' . $tag->Url . '" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $tag->Name . '</td>';
        $tabletds[] = '<td class="td20">' . $tag->Alias . '</td>';
        $tabletds[] = '<td class="td10">' . $tag->Count . '</td>';
        $tabletds[] = '<td class="td10 tdCenter">' .
            '<a href="../cmd.php?act=TagEdt&amp;id=' . $tag->ID . '"><img src="../image/admin/tag_blue_edit.png" alt="' . $zbp->lang['msg']['edit'] . '" title="' . $zbp->lang['msg']['edit'] . '" width="16" /></a>' .
            '&nbsp;&nbsp;&nbsp;&nbsp;' .
            '<a onclick="return window.confirm(\'' . str_replace(array('"','\''), '', $zbp->lang['msg']['confirm_operating']) . '\');" href="' . BuildSafeCmdURL('act=TagDel&amp;id=' . $tag->ID) . '"><img src="../image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/tag_32.png' . '");</script>';
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
    echo '<div id="divMain2"><form id="frmTheme" method="post" action="../cmd.php?act=ThemeSet">';
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

        if ($theme->IsUsed() && $theme->path) {
            echo '<a href="' . $theme->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '" class="button"><img width="16" title="" alt="" src="../image/admin/setting_tools.png"/></a>&nbsp;&nbsp;';
        } else {
            echo '<img width="16" title="" alt="" src="../image/admin/layout.png"/>&nbsp;&nbsp;';
        }
        echo '<a target="_blank" href="' . htmlspecialchars($theme->url) . '" title=""><strong style="display:none;">' . htmlspecialchars($theme->id) . '</strong>';
        echo '<b>' . htmlspecialchars($theme->name) . '</b></a></div>';
        echo '<div><img src="' . $theme->GetScreenshot() . '" title="' . htmlspecialchars($theme->name) . '" alt="' . htmlspecialchars($theme->name) . '" width="200" height="150" /></div>';
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/themes_32.png' . '");</script>';
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
    echo '<div id="divMain2">';

    $sm = array();
    $um = array();
    $tm = array();
    $pm = array();

    foreach ($zbp->modules as $m) {
        if ($m->SourceType == 'system') {
            $sm[] = $m;
        } elseif ($m->SourceType == 'user') {
            $um[] = $m;
        } elseif ($m->SourceType == 'theme') {
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

    echo '<hr/>';
    echo "\r\n";
    echo '<form id="edit" method="post" action="' . BuildSafeCmdURL('act=SidebarSet') . '">';
    echo '<input type="hidden" id="strsidebar" name="edtSidebar" value="' . $zbp->option['ZC_SIDEBAR_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar2" name="edtSidebar2" value="' . $zbp->option['ZC_SIDEBAR2_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar3" name="edtSidebar3" value="' . $zbp->option['ZC_SIDEBAR3_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar4" name="edtSidebar4" value="' . $zbp->option['ZC_SIDEBAR4_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar5" name="edtSidebar5" value="' . $zbp->option['ZC_SIDEBAR5_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar6" name="edtSidebar6" value="' . $zbp->option['ZC_SIDEBAR6_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar7" name="edtSidebar7" value="' . $zbp->option['ZC_SIDEBAR7_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar8" name="edtSidebar8" value="' . $zbp->option['ZC_SIDEBAR8_ORDER'] . '"/>';
    echo '<input type="hidden" id="strsidebar9" name="edtSidebar9" value="' . $zbp->option['ZC_SIDEBAR9_ORDER'] . '"/>';
    echo '</form>';
    echo "\r\n";
    echo '<div class="clear"></div></div>';
    echo '</div>';
    //widget-list end
    echo "\r\n";
    //siderbar-list begin
    echo '<div class="siderbar-list">';
    echo '<div class="siderbar-drop" id="siderbar"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar2"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar2'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar2), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar2 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar3"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar3'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar3), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar3 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar4"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar4'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar4), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar4 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar5"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar5'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar5), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar5 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar6"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar6'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar6), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar6 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar7"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar7'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar7), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar7 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar8"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar8'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar8), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar8 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

    echo '<div class="siderbar-drop" id="siderbar9"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar9'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
    echo '<div class="siderbar-note" >' . str_replace('%s', count($zbp->template->sidebar9), $zbp->lang['msg']['sidebar_module_count']) . '</div>';
    foreach ($zbp->template->sidebar9 as $m) {
        CreateModuleDiv($m, false);
    }
    echo '</div></div>';
    echo "\r\n";

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
                var s1 = "";
                $("#siderbar").find("div.funid").each(function(i) {
                    s1 += $(this).html() + "|";
                });

                var s2 = "";
                $("#siderbar2").find("div.funid").each(function(i) {
                    s2 += $(this).html() + "|";
                });

                var s3 = "";
                $("#siderbar3").find("div.funid").each(function(i) {
                    s3 += $(this).html() + "|";
                });

                var s4 = "";
                $("#siderbar4").find("div.funid").each(function(i) {
                    s4 += $(this).html() + "|";
                });

                var s5 = "";
                $("#siderbar5").find("div.funid").each(function(i) {
                    s5 += $(this).html() + "|";
                });

                var s6 = "";
                $("#siderbar6").find("div.funid").each(function(i) {
                    s6 += $(this).html() + "|";
                });

                var s7 = "";
                $("#siderbar7").find("div.funid").each(function(i) {
                    s7 += $(this).html() + "|";
                });

                var s8 = "";
                $("#siderbar8").find("div.funid").each(function(i) {
                    s8 += $(this).html() + "|";
                });

                var s9 = "";
                $("#siderbar9").find("div.funid").each(function(i) {
                    s9 += $(this).html() + "|";
                });

                $("#strsidebar").val(s1);
                $("#strsidebar2").val(s2);
                $("#strsidebar3").val(s3);
                $("#strsidebar4").val(s4);
                $("#strsidebar5").val(s5);
                $("#strsidebar6").val(s6);
                $("#strsidebar7").val(s7);
                $("#strsidebar8").val(s8);
                $("#strsidebar9").val(s9);

                $.post($("#edit").attr("action"), {
                        "sidebar": s1,
                        "sidebar2": s2,
                        "sidebar3": s3,
                        "sidebar4": s4,
                        "sidebar5": s5,
                        "sidebar6": s6,
                        "sidebar7": s7,
                        "sidebar8": s8,
                        "sidebar9": s9
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
                    var c = ui.item.find(".funid").html();
                    if (ui.item.parent().find(".widget:contains(" + c + ")").length > 1) {
                        ui.item.remove();
                    };
                },
                stop: function(event, ui) {
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/link_32.png' . '");</script>';
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
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';
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
                echo '<a href="' . BuildSafeCmdURL('act=PluginDis&amp;name=' . htmlspecialchars($plugin->id)) . '" title="' . $zbp->lang['msg']['disable'] . '"><img width="16" alt="' . $zbp->lang['msg']['disable'] . '" src="../image/admin/control-power.png"/></a>';
            } else {
                echo '<a href="' . BuildSafeCmdURL('act=PluginEnb&amp;name=' . htmlspecialchars($plugin->id)) . '" title="' . $zbp->lang['msg']['enable'] . '"><img width="16" alt="' . $zbp->lang['msg']['enable'] . '" src="../image/admin/control-power-off.png"/></a>';
            }
        }
        if ($plugin->IsUsed() && $plugin->CanManage()) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $plugin->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '"><img width="16" alt="' . $zbp->lang['msg']['manage'] . '" src="../image/admin/setting_tools.png"/></a>';
        }

        echo '</td>';

        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<script>ActiveLeftMenu("aPluginMng");';
    echo 'AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/plugin_32.png' . '");$(".plugin-note").tooltip();</script>';
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
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['blog_host'] . '</b><br/><span class="note">' . $zbp->lang['msg']['blog_host_add'] . '</span></p></td><td><p><input id="ZC_BLOG_HOST" name="ZC_BLOG_HOST" style="width:600px;" type="text" value="' . $decodedBlogHost . '" ' . ($zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] ? '' : 'readonly="readonly" ') . 'oninput="disableSubmit($(this).val())" />&nbsp;&nbsp;<span class="js-tip"></span>';
                    echo '<p><label onclick="$(\'#ZC_BLOG_HOST\').prop(\'readonly\', $(\'#ZC_PERMANENT_DOMAIN_ENABLE\').val()==0?true:false);   if($(\'#ZC_PERMANENT_DOMAIN_ENABLE\').val()==0){enableSubmit();$(this).parent().next().hide();$(\'.js-tip\').html(\'\');}else {disableSubmit();$(this).parent().next().show();}"><input type="text" id="ZC_PERMANENT_DOMAIN_ENABLE" name="ZC_PERMANENT_DOMAIN_ENABLE" class="checkbox" value="' . $zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] . '"/></label>' . $zbp->lang['msg']['permanent_domain'] . '<span style="display:none;">&nbsp;&nbsp;<input type="text" id="ZC_PERMANENT_DOMAIN_WITH_ADMIN" name="ZC_PERMANENT_DOMAIN_WITH_ADMIN" class="checkbox" value="' . $zbp->option['ZC_PERMANENT_DOMAIN_WITH_ADMIN'] . '"/></label>' . $zbp->lang['msg']['permanent_domain_with_admin'] . '</span></p>';
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
                    echo '</td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_name'] . '</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="' . htmlspecialchars($zbp->option['ZC_BLOG_NAME']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_subname'] . '</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="' . htmlspecialchars($zbp->option['ZC_BLOG_SUBNAME']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['copyright'] . '</b><br/><span class="note">' . $zbp->lang['msg']['copyright_add'] . '</span></p></td><td><p><textarea cols="3" rows="6" id="ZC_BLOG_COPYRIGHT" name="ZC_BLOG_COPYRIGHT" style="width:600px;">' . htmlspecialchars($zbp->option['ZC_BLOG_COPYRIGHT']) . '</textarea></p></td></tr>';

                    echo '</table>';
                    echo '</div>';

                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['blog_timezone'] . '</b></p></td><td><p><select id="ZC_TIME_ZONE_NAME" name="ZC_TIME_ZONE_NAME" style="width:600px;" >';
                    echo CreateOptionsOfTimeZone($zbp->option['ZC_TIME_ZONE_NAME']);
                    echo '</select></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['blog_language'] . '</b></p></td><td><p><select id="ZC_BLOG_LANGUAGEPACK" name="ZC_BLOG_LANGUAGEPACK" style="width:600px;" >';
                    echo CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK']);
                    echo '</select></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['debug_mode'] . '</b></p></td><td><p><input id="ZC_DEBUG_MODE" name="ZC_DEBUG_MODE" type="text" value="' . $zbp->option['ZC_DEBUG_MODE'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->show_warning_error . '</b></p></td><td><p><input id="ZC_DEBUG_MODE_WARNING" name="ZC_DEBUG_MODE_WARNING" type="text" value="' . $zbp->option['ZC_DEBUG_MODE_WARNING'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['additional_security'] . '</b></p></td><td><p><input id="ZC_ADDITIONAL_SECURITY" name="ZC_ADDITIONAL_SECURITY" type="text" value="' . $zbp->option['ZC_ADDITIONAL_SECURITY'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['using_cdn_guest_type'] . '</b></p></td><td><p><select id="ZC_USING_CDN_GUESTIP_TYPE" name="ZC_USING_CDN_GUESTIP_TYPE" style="width:600px;" >';
                    echo CreateOptionsOfGuestIPType($zbp->option['ZC_USING_CDN_GUESTIP_TYPE']);
                    echo '</select></p></td></tr>';

                    echo '<tr><td><p><b>' . $zbp->lang['msg']['close_site'] . '</b></p></td><td><p><input id="ZC_CLOSE_SITE" name="ZC_CLOSE_SITE" type="text" value="' . $zbp->option['ZC_CLOSE_SITE'] . '" class="checkbox"/></p></td></tr>';

                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td><p><b>' . $zbp->lang['msg']['display_count'] . '</b></p></td><td><p><input id="ZC_DISPLAY_COUNT" name="ZC_DISPLAY_COUNT" style="width:600px;" type="text" value="' . $zbp->option['ZC_DISPLAY_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['display_subcategorys'] . '</b></p></td><td><p><input id="ZC_DISPLAY_SUBCATEGORYS" name="ZC_DISPLAY_SUBCATEGORYS" type="text" value="' . $zbp->option['ZC_DISPLAY_SUBCATEGORYS'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['pagebar_count'] . '</b></p></td><td><p><input id="ZC_PAGEBAR_COUNT" name="ZC_PAGEBAR_COUNT" style="width:600px;" type="text" value="' . $zbp->option['ZC_PAGEBAR_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['search_count'] . '</b></p></td><td><p><input id="ZC_SEARCH_COUNT" name="ZC_SEARCH_COUNT" style="width:600px;" type="text" value="' . $zbp->option['ZC_SEARCH_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['syntax_high_lighter'] . '</b></p></td><td><p><input id="ZC_SYNTAXHIGHLIGHTER_ENABLE" name="ZC_SYNTAXHIGHLIGHTER_ENABLE" type="text" value="' . $zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'] . '" class="checkbox"/></p></td></tr>';
                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';

                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['comment_turnoff'] . '</b></p></td><td><p><input id="ZC_COMMENT_TURNOFF" name="ZC_COMMENT_TURNOFF" type="text" value="' . $zbp->option['ZC_COMMENT_TURNOFF'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td class="td25"><p><b>' . $zbp->lang['msg']['comment_audit'] . '</b><br/><span class="note">' . $zbp->lang['msg']['comment_audit_comment'] . '</span></p></td><td><p><input id="ZC_COMMENT_AUDIT" name="ZC_COMMENT_AUDIT" type="text" value="' . $zbp->option['ZC_COMMENT_AUDIT'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comment_reverse_order'] . '</b></p></td><td><p><input id="ZC_COMMENT_REVERSE_ORDER" name="ZC_COMMENT_REVERSE_ORDER" type="text" value="' . $zbp->option['ZC_COMMENT_REVERSE_ORDER'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comments_display_count'] . '</b></p></td><td><p><input id="ZC_COMMENTS_DISPLAY_COUNT" name="ZC_COMMENTS_DISPLAY_COUNT" type="text" value="' . $zbp->option['ZC_COMMENTS_DISPLAY_COUNT'] . '"  style="width:600px;" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['comment_verify_enable'] . '</b></p></td><td><p><input id="ZC_COMMENT_VERIFY_ENABLE" name="ZC_COMMENT_VERIFY_ENABLE" type="text" value="' . $zbp->option['ZC_COMMENT_VERIFY_ENABLE'] . '" class="checkbox"/></p></td></tr>';

                    echo '</table>';
                    echo '</div>';
                    echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab5">';
                    echo '<table style="padding:0px;margin:0px;width:100%;" class="table_hover table_striped">';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['allow_upload_type'] . '</b></p></td><td><p><input id="ZC_UPLOAD_FILETYPE" name="ZC_UPLOAD_FILETYPE" style="width:600px;" type="text" value="' . htmlspecialchars($zbp->option['ZC_UPLOAD_FILETYPE']) . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['allow_upload_size'] . '</b><br/><span class="note">upload_max_filesize=' . ini_get('upload_max_filesize') . '<br/>post_max_size=' . ini_get('post_max_size') . '</span></p></td><td><p><input id="ZC_UPLOAD_FILESIZE" name="ZC_UPLOAD_FILESIZE" style="width:600px;" type="text" value="' . $zbp->option['ZC_UPLOAD_FILESIZE'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->get_text_intro . '</b></p></td><td><p><input id="ZC_ARTICLE_INTRO_WITH_TEXT" name="ZC_ARTICLE_INTRO_WITH_TEXT" type="text" value="' . $zbp->option['ZC_ARTICLE_INTRO_WITH_TEXT'] . '" class="checkbox"/></p></td></tr>';
                    echo '<tr><td><p><b>' . $zbp->lang['msg']['manage_count'] . '</b></p></td><td><p><input id="ZC_MANAGE_COUNT" name="ZC_MANAGE_COUNT" style="width:600px;" type="text" value="' . $zbp->option['ZC_MANAGE_COUNT'] . '" /></p></td></tr>';
                    echo '<tr><td><p><b>' . @$zbp->langs->msg->enable_post_batch_delete . '</b></p></td><td><p><input id="ZC_POST_BATCH_DELETE" name="ZC_POST_BATCH_DELETE" type="text" value="' . $zbp->option['ZC_POST_BATCH_DELETE'] . '" class="checkbox"/></p></td></tr>';
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
    echo '<script>AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/setting_32.png' . '");</script>';
}
