<?php

//注册插件
RegisterPlugin("changyan", "ActivePlugin_changyan");

if (function_exists('ini_set')) {
    ini_set('max_execution_time', '0');
}
define('CHANGYAN_PLUGIN_PATH', dirname(__FILE__));
require CHANGYAN_PLUGIN_PATH . '/Synchronizer.php';
require CHANGYAN_PLUGIN_PATH . '/Handler.php';

$changyanPlugin = null;

//注册插件函数
function ActivePlugin_changyan()
{
    global $changyanPlugin,$zbp;
    $changyanPlugin = Changyan_Handler::getInstance();

    //add_action('init', 'changyan_init');
    Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'changyan_init');
    Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'changyan_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'changyan_socialcomment');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'changyan_view_post_template');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Template', 'changyan_view_list_template');
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'changyan_html_js_add');
}

function changyan_html_js_add()
{
    global $changyanPlugin,$zbp;
    if (!$changyanPlugin->getOption('changyan_script')) {
        return;
    }
    if (!$changyanPlugin->getOption('changyan_isCron')) {
        return;
    }
    if (time() - $changyanPlugin->getOption('changyan_lastSyncTime') > 3600) {
        $changyanPlugin->sync2Wordpress();
    }
    $zbp->AddBuildModule('comments');
    $zbp->BuildModule();
}

function changyan_view_list_template(&$template)
{
    global $changyanPlugin,$zbp;
    $posts = &$template->GetTags('articles');
    foreach ($posts as $post) {
        $post->CommNums = '<span id = "sourceId::' . $post->ID . '" class = "cy_cmt_count" ></span>';
    }
}
function changyan_view_post_template(&$template)
{
    global $changyanPlugin,$zbp;
    $post = &$template->GetTags('article');
    $post->CommNums = '<a href="#SOHUCS" id="changyan_count_unit"></a>';
}

function InstallPlugin_changyan()
{
    global $zbp;
    //@duoshuo_create_database();
    //duoshuo_create_functions();
    //Init Config
    if ($zbp->Config('changyan')->CountItem() == 0) {
        //$zbp->Config('changyan')->short_name = '';
        //$zbp->SaveConfig('changyan');
    }

    global $changyanPlugin;

    //See http://wordpress.stackexchange.com/questions/20327/plugin-action-links-filter-hook-deprecated
    //See also http://stackoverflow.com/questions/1580378/plugin-action-links-not-working-in-wordpress-2-8
    //add_filter('plugin_action_links_changyan/changyan.php', array($changyanPlugin, 'doPluginActionLinks', 10, 2));

    $script = $changyanPlugin->getOption('changyan_script');

    if (empty($script)) { //If not enabled, the changyan_appID is empty
        function changyan_config_notice()
        {
            global $zbp;
            //TODO the link is not available
            $zbp->SetHint('tips', '<strong>请完成相关<a href="' . $zbp->host . 'zb_users/plugin/changyan/main.php' . '">配置</a>，您就能享受畅言的服务了。</strong>');
        }

        //if the admin left menu item is not changyan currently, show links to the changyan item page
        changyan_config_notice();
    }

    //See http://wordpress.stackexchange.com/questions/14973/row-actions-for-custom-post-types
    changyan_base_init();
}

function UninstallPlugin_changyan()
{
}

function changyan_AddMenu(&$m)
{
    global $zbp;
    $b = false;
    $i = 0;
    $s = MakeLeftMenu("root", "畅言评论", $zbp->host . "zb_users/plugin/changyan/main.php", "nav_changyan", "aChangYan", $zbp->host . "zb_users/plugin/changyan/cy.png");
    foreach ($m as $key => $value) {
        if ($key === 'nav_comment') {
            $m[$key] = $s;
            $b = true;
        }
    }
    if (!$b) {
        reset($m);
        foreach ($m as $key => $value) {
            if (strpos($value, 'act=CommentMng') !== false) {
                $b = true;
                break;
            }
            $i = $i + 1;
        }
        if ($b) {
            array_splice($m, $i, 1, array('nav_changyan' => $s));
        }
    }
    if (!$b) {
        $m["nav_changyan"] = $s;
    }
}

function changyan_socialcomment(&$template)
{
    global $zbp,$changyanPlugin;
    $script = $changyanPlugin->getOption('changyan_script');
    if (!empty($script)) {
        $a = $template->GetTags('article');
        $s = 'sid="' . $a->ID . '"';
        $script = str_replace('id="SOHUCS"', 'id="SOHUCS" ' . $s, $script);
        $template->SetTags('socialcomment', $script);
    }
}

function changyan_init()
{
    global $changyanPlugin;

    changyan_base_init();
}

function changyan_base_init()
{
    global $zbp,$changyanPlugin;
    $script = $changyanPlugin->getOption('changyan_script');

    if (!empty($script)) {
        //add_filter('comments_template', array($changyanPlugin, 'getCommentsTemplate'));
    }
    //if(function_exists('ini_set'))ini_set('display_errors', '1');
    //schedule synchronization
    $isCron = $changyanPlugin->getOption('changyan_script');
    if ($isCron == true || $isCron == 'true') {
        //add_action('changyanCron', array($changyanPlugin, 'cronSync'));
        //if (!wp_next_scheduled('changyanCron')) {
        //    wp_schedule_event(time(), 'hourly', 'changyanCron');
        //}
    }
    $zbp->header .= '<script type="text/javascript" src="http://assets.changyan.sohu.com/upload/plugins/plugins.count.js"></script>' . "\r\n";
    $zbp->footer .= '<script id="cy_cmt_num" src="http://assets.changyan.sohu.com/upload/tools/cy_cmt_count.js?clientId=' . $changyanPlugin->getOption('changyan_appID') . '"></script>' . "\r\n";
}

function changyan_SubMenus()
{
    global $zbp;
    $id = 1;
    $url = GetRequestUri();
    if (strpos($url, 'analysis.php') !== false) {
        $id = 2;
    }
    if (strpos($url, 'settings.php') !== false) {
        $id = 3;
    }
    echo '<a href="main.php"><span class="m-left ' . ($id == 1 ? 'm-now' : '') . '">评论管理</span></a>';
    echo '<a href="analysis.php"><span class="m-left ' . ($id == 2 ? 'm-now' : '') . '">统计分析</span></a>';
    echo '<a href="settings.php"><span class="m-left ' . ($id == 3 ? 'm-now' : '') . '">设置与初始化</span></a>';
}

function changyan_deactivate()
{
    /*
     global $changyanPlugin;
    //unset($this->changyan_appID);
    //*****************Options List*********************
    //* changyan_script: is a string if the script of changyan is configured.
    //* changyan_lastSyncTime: is the time of last synchronization (including sync2WP and sync2CY).
    //* changyan_sync2WP: is the comment_ID in front of the ID synchronized to WordPress.
    //* changyan_sync2CY: is the comment_ID in front of the ID synchronized to Changyan.
    //* changyan_appKey: save appKey
    //**************************************************
    //Delete all options deserved when deactivited
    $changyanPlugin->delOption('changyan_script');
    $changyanPlugin->delOption('changyan_appID');
    $changyanPlugin->delOption('changyan_isBinded');
    $changyanPlugin->delOption('changyan_isSynchronized');
    */
}

function cy_profile_update($user_id, $older_user_data)
{
    echo 'User ' . $user_id . ',Older data is :<br/>';
    print_r($older_user_data);
}
