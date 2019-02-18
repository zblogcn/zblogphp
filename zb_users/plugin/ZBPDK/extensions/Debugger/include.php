<?php
/**
 * ZBPDK子扩展
 * Z-BlogPHP调试器
 * 
 * @author 心扬 <chrishyze@gmail.com>
 */

/**
 * 子扩展信息
 * url:后台文件
 * description:描述
 * id:ID
 */
$GLOBALS['zbpdk']->add_extension(array(
    'url' => 'main.php',
    'description' => 'Z-BlogPHP调试器，可在前后台查看各类调试信息。',
    'id' => 'Debugger'
));

/**
 * 子扩展菜单列表
 * url:相对后台地址
 * float:菜单停靠位置
 * id:ID
 * title:标题
 */
$GLOBALS['zbpdk']->submenu->add(array(
    'url' => 'Debugger/main.php',
    'float' => 'left',
    'id' => 'Debugger',
    'title' => 'Debugger'
));

/**
 * 子扩展在插件激活时的执行函数
 */
function ActivePlugin_Debugger() {
    Add_Filter_Plugin('Filter_Plugin_DbSql_Filter', 'record_sql_debugger');
    Add_Filter_Plugin('Filter_Plugin_Index_End', 'output_front_debugger');
    Add_Filter_Plugin('Filter_Plugin_Search_End', 'output_front_debugger');
    Add_Filter_Plugin('Filter_Plugin_Admin_Footer', 'output_admin_debugger');
}

/**
 * 子扩展在插件安装时的执行函数
 */
function InstallPlugin_Debugger() {
    global $zbp;
    if (!$zbp->HasConfig('ZBPDK_Debugger')) {
        $zbp->Config('ZBPDK_Debugger')->show_in_front = '1';
        $zbp->Config('ZBPDK_Debugger')->show_in_admin = '1';
        $zbp->SaveConfig('ZBPDK_Debugger');
    }
}

/**
 * 子扩展在插件卸载时的执行函数
 */
function UnInstallPlugin_Debugger() {
    global $zbp;
    if ($zbp->HasConfig('ZBPDK_Debugger')) {
        $zbp->DelConfig('ZBPDK_Debugger');
    }
}

/**
 * SQL语句记录
 * @param string $sql
 */
function record_sql_debugger($sql) {
    if (!isset($GLOBALS['_query_logs'])) {
        $GLOBALS['_query_logs'] = array();
    }
    $GLOBALS['_query_logs'][] = $sql;
}

/**
 * 前台调试信息输出
 */
function output_front_debugger() {
    global $zbp;
    if ($zbp->Config('ZBPDK_Debugger')->show_in_front == '1') {
        output_content_debugger(false);
    }
}

/**
 * 后台调试信息输出
 */
function output_admin_debugger() {
    global $zbp;
    if ($zbp->Config('ZBPDK_Debugger')->show_in_admin == '1') {
        output_content_debugger(true);
    }
}

/**
 * 输出的信息内容
 * @param boolean $is_admin 
 */
function output_content_debugger($is_admin) {
    global $zbp;

    //当前内存占用
    $memory_usage = function_exists('memory_get_usage') ? memory_get_usage() : -1;
    if ($memory_usage < 1024) {
        $memory_usage =  $memory_usage . ' B';
    } elseif ($memory_usage < 1048576) {
        $memory_usage = round($memory_usage / 1024, 2) . ' KB';
    } else {
        $memory_usage = round($memory_usage / 1048576, 2) . ' MB';
    }

    //引用文件
    $include_files = '';
    $include_count = 0;
    foreach (get_included_files() as $key => $value) {
        $include_count = $key + 1;
        $include_files .= '<tr><td class="center">' .
            $include_count . '</td><td>' . $value . '</td></tr>';
    }

    //页面执行时间
    $runtime = number_format(1000 * (microtime(true) - $_SERVER['_start_time']), 2);

    //数据库查询
    $sql_query = '';
    $sql_count = $_SERVER['_query_count'] - 1;
    foreach ($GLOBALS['_query_logs'] as $key => $value) {
        $sql_query .= '<tr><td class="center">' .
            ($key + 1) . '</td><td>' . htmlentities($value) . '</td></tr>';
    }

    $type = $is_admin ? '后台' : '前台';

    $html = '
<div id="debug-container" class="debug-container">
    <div class="debug-header">
        <span class="debug-brand">Z-BlogPHP调试器</span>
        <ul id="debug-tabs">
            <li>运行信息</li>
            <li>请求数据</li>
            <li>引用文件</li>
            <li>SQL查询</li>
            <li>活动接口</li>
        </ul>
        <span class="debug-ctl-btn" id="debug-ctl-show" title="固定">○</span>
        <span class="debug-ctl-btn" id="debug-ctl-size" title="展开">&and;</span>
        <span class="debug-ctl-btn" id="debug-ctl-close" title="关闭">&times;</span>
    </div>
    <div class="debug-body">
        <ul id="debug-content">
            <li>
                <p><strong>页面执行时间</strong>：<span class="debug-data" style="background:#1e90ff">' .
                $runtime . ' ms</span></p>
                <p><strong>当前内存占用</strong>：<span class="debug-data" style="background:#32cd32">' .
                $memory_usage . '</span></p>
                <p><strong>引用的文件数</strong>：<span class="debug-data" style="background:#ff8c00">' .
                $include_count . '</span></p>
                <p><strong>数据库查询数</strong>：<span class="debug-data" style="background:#ff2b30">' .
                $sql_count . '</span></p>
            </li>
            <li>
                <p><strong>URL参数</strong>: ' . $_SERVER['QUERY_STRING'] . '</p>
                <p><strong>请求方式</strong>: ' . $_SERVER['REQUEST_METHOD'] . '</p>
                <p><strong>请求参数</strong>: </p>
                <pre class="debug-pre">$_GET: ' .
                    print_r(htmlspecialchars_array($_GET), true)
                    . '$_POST: ' .
                    print_r(htmlspecialchars_array($_POST), true)
                    . '$_COOKIE: ' .
                    print_r(htmlspecialchars_array($_COOKIE), true)
                    . '</pre>
                <p><strong>User-Agent</strong>: ' . $_SERVER['HTTP_USER_AGENT'] . '</p>
            </li>
            <li>
                <table class="debug-table">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>物理路径</th>
                        </tr>
                    </thead>
                    <tbody>' .
                        $include_files
                    . '</tbody>
                </table>
            </li>
            <li>
                <table class="debug-table">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>SQL 语句</th>
                        </tr>
                    </thead>
                    <tbody>' .
                        $sql_query
                    . '</tbody>
                </table>
            </li>
            <li>
                <p>
                    当前查看的是影响<strong>' . $type . '</strong>的已挂载接口
                    <a href="' . $zbp->host . 'zb_system/admin/index.php?act=PluginMng" style="float:right;margin-left:10px">[后台插件管理]</a>
                    <a href="' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/Debugger/main.php?act=show_all_interface" style="float:right">[查看所有的已挂载接口]</a>
                </p>
                <table class="debug-table">
                    <thead>
                        <tr>
                            <th>接口</th>
                            <th>挂载函数</th>
                            <th>对应插件</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>' .
                        get_actived_plugins_debugger($is_admin)
                    . '</tbody>
                </table>
            </li>
        </ul>
    </div>
</div>
<div id="debug-ctl-open">调试信息</div>
<style>
@import url(' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/Debugger/style.css);
</style>
<script src="' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/Debugger/script.js" type="text/javascript"></script>';
    echo $html;

    //阻止系统RunTime()的输出
    $zbp->option['ZC_RUNINFO_DISPLAY'] = false;

    unset($memory_usage, $include_files, $include_count, $runtime, $sql_query,
        $sql_count, $type, $GLOBALS['_query_logs']);
}

/**
 * 获取影响前后台的已挂载接口
 * 
 * TODO:
 * 查看对当前页面产生影响的接口
 * 
 * 已知BUG:
 * 一些从系统挂载的接口无法获取详情
 * 
 * @param boolean $is_admin
 * @return string
 */
function get_actived_plugins_debugger($is_admin) {
    global $zbp;
    
    //前后台对应的接口列表
    $interfaces = $is_admin ? array(
        'Filter_Plugin_Admin_LeftMenu',
        'Filter_Plugin_Admin_TopMenu',
        'Filter_Plugin_Admin_SiteInfo_SubMenu',
        'Filter_Plugin_Admin_ArticleMng_SubMenu',
        'Filter_Plugin_OutputOptionItemsOfType',
        'Filter_Plugin_OutputOptionItemsOfCategories',
        'Filter_Plugin_OutputOptionItemsOfMemberLevel',
        'Filter_Plugin_Admin_ArticleMng_Table',
        'Filter_Plugin_Admin_PageMng_SubMenu',
        'Filter_Plugin_Admin_PageMng_Table',
        'Filter_Plugin_Admin_CategoryMng_SubMenu',
        'Filter_Plugin_Admin_CategoryMng_Table',
        'Filter_Plugin_Admin_CommentMng_SubMenu',
        'Filter_Plugin_Admin_CommentMng_Table',
        'Filter_Plugin_Admin_MemberMng_SubMenu',
        'Filter_Plugin_Admin_MemberMng_Table',
        'Filter_Plugin_Admin_UploadMng_SubMenu',
        'Filter_Plugin_Admin_UploadMng_Table',
        'Filter_Plugin_Admin_TagMng_SubMenu',
        'Filter_Plugin_Admin_TagMng_Table',
        'Filter_Plugin_Admin_ThemeMng_SubMenu',
        'Filter_Plugin_Admin_ModuleMng_SubMenu',
        'Filter_Plugin_Admin_PluginMng_SubMenu',
        'Filter_Plugin_Admin_SettingMng_SubMenu',
        'Filter_Plugin_Admin_Footer',
        'Filter_Plugin_Admin_Header',
        'Filter_Plugin_Category_Edit_SubMenu',
        'Filter_Plugin_Category_Edit_Response',
        'Filter_Plugin_Edit_Begin',
        'Filter_Plugin_Edit_SubMenu',
        'Filter_Plugin_Edit_Response4',
        'Filter_Plugin_Edit_Response5',
        'Filter_Plugin_Edit_Response',
        'Filter_Plugin_Edit_Response2',
        'Filter_Plugin_Edit_Response3',
        'Filter_Plugin_Edit_End',
        'Filter_Plugin_Admin_Begin',
        'Filter_Plugin_Admin_End',
        'Filter_Plugin_Member_Edit_SubMenu',
        'Filter_Plugin_Member_Edit_Response',
        'Filter_Plugin_Module_Edit_SubMenu',
        'Filter_Plugin_Module_Edit_Response',
        'Filter_Plugin_Tag_Edit_SubMenu',
        'Filter_Plugin_Tag_Edit_Response',
        'Filter_Plugin_PostArticle_Core',
        'Filter_Plugin_PostArticle_Succeed',
        'Filter_Plugin_DelArticle_Succeed',
        'Filter_Plugin_PostTag_Core',
        'Filter_Plugin_PostTag_Succeed',
        'Filter_Plugin_PostPage_Core',
        'Filter_Plugin_PostPage_Succeed',
        'Filter_Plugin_DelPage_Succeed',
        'Filter_Plugin_DelComment_Succeed',
        'Filter_Plugin_PostCategory_Core',
        'Filter_Plugin_PostCategory_Succeed',
        'Filter_Plugin_DelCategory_Succeed',
        'Filter_Plugin_PostTag_Core',
        'Filter_Plugin_PostTag_Succeed',
        'Filter_Plugin_DelTag_Succeed',
        'Filter_Plugin_PostMember_Core',
        'Filter_Plugin_DelMember_Succeed',
        'Filter_Plugin_PostModule_Core',
        'Filter_Plugin_PostModule_Succeed',
        'Filter_Plugin_DelModule_Succeed'
    ) : array(
        'Filter_Plugin_ViewIndex_Begin',
        'Filter_Plugin_ViewFeed_Begin',
        'Filter_Plugin_ViewFeed_Core',
        'Filter_Plugin_ViewFeed_End',
        'Filter_Plugin_Index_Begin',
        'Filter_Plugin_Index_End',
        'Filter_Plugin_Html_Js_Add',
        'Filter_Plugin_ViewSearch_Begin',
        'Filter_Plugin_ViewSearch_Core',
        'Filter_Plugin_Search_Begin',
        'Filter_Plugin_Search_End',
        'Filter_Plugin_Feed_Begin',
        'Filter_Plugin_Feed_End',
        'Filter_Plugin_VerifyLogin_Succeed',
        'Filter_Plugin_Logout_Succeed',
        'Filter_Plugin_ViewAuto_Begin',
        'Filter_Plugin_ViewAuto_End',
        'Filter_Plugin_ViewList_Begin',
        'Filter_Plugin_ViewList_Core',
        'Filter_Plugin_ViewPost_Begin',
        'Filter_Plugin_ViewList_Template',
        'Filter_Plugin_ViewPost_Template',
        'Filter_Plugin_ViewComment_Template',
        'Filter_Plugin_ViewComments_Template',
        'Filter_Plugin_Zbp_ShowError',
        'Filter_Plugin_PostComment_Core',
        'Filter_Plugin_PostComment_Succeed',
        'Filter_Plugin_PostMember_Core',
        'Filter_Plugin_PostMember_Succeed'
    );

    $str = '';
    foreach ($GLOBALS as $key => $value) {
        if (preg_match("/^Filter/i", $key, $matches)) {
            foreach ($interfaces as $val) {
                if ($key == $val) {
                    foreach ($GLOBALS[$key] as $k => $v) {
                        if (function_exists($k)) {
                            $str .= '<tr><td>' . $key . '</td><td>' . $k
                                . '</td><td class="center">' .
                                get_plugin_name_debugger($k)
                                . '</td><td class="center">
                                <span class="debug-plg-detail"
                                interface="' . $key . '" func="' . $k . '"
                                title="查看/隐藏详情">ⅰ</span></td></tr>
                                <tr style="display:none"><td colspan="4"></td></tr>';
                        }
                    }
                }
            }
        }
    }

    return $str;
}

/**
 * 获取插件名称
 * @param string $func_name 接口绑定的函数名
 * @return string
 */
function get_plugin_name_debugger($func_name = '') {
    try {
        $func = new ReflectionFunction($func_name);
    } catch (ReflectionException $e) {
        return '非法函数名';
    }
    if (stripos($func->GetFileName(), 'zb_system/')) {
        return '系统';
    } elseif (stripos($func->GetFileName(), 'zb_users/plugin/')) {
        $app = new App;
        $app->LoadInfoByXml('plugin', basename(dirname($func->GetFileName())));
        return empty($app->name) ? '其他' : $app->name;
    } elseif (stripos($func->GetFileName(), 'zb_users/theme/')) {
        $app = new App;
        $app->LoadInfoByXml('theme', basename(dirname($func->GetFileName())));
        return empty($app->name) ? '其他' : $app->name;
    } else {
        return '非法路径';
    }
}

//EOF
