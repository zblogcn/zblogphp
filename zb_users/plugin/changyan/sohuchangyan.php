<?php
/*
Plugin Name: 畅言评论系统
Plugin URI: http://wordpress.org/plugins/changyan/
Description: 即装即用，永久免费的社会化评论系统。为各类网站提供新浪微博、QQ、人人、搜狐等账号登录评论功能，同时提供强大的内容管理后台和智能云过滤服务。
Version:  1.0
Author: 搜狐畅言
Author URI: http://changyan.sohu.com
*/
ini_set('max_execution_time', '0');
define('CHANGYAN_PLUGIN_PATH', dirname(__FILE__));

//check PHP version
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
    //is_admin：判断是否显示控制面板或管理栏,而不是判断是不是管理员身份
    if (is_admin()) {
        function changyan_php_version_notice()
        {
            echo '<div class="updated"><p><strong>您的PHP版本低于5.0，请升级到最新版本以享受畅言提供的服务。</strong></p></div>';
        }

        add_action('admin_notices', 'changyan_php_version_notice');
    }
}

//check WordPress version
if (version_compare($wp_version, '3.0', '<')) {
    if (is_admin()) {
        function changyan_wp_version_notice()
        {
            echo '<div class="updated"><p><strong>您的WordPress版本低于3.0，请升级到最新版本以享受畅言提供的服务。</strong></p></div>';
        }

        add_action('admin_notices', 'changyan_wp_version_notice');
    }
}

//get available transport
function changyan_get_transport()
{
    //check whether an extension named by a parameter is loaded
    //CURL is the default transport method for WP since 2.8.1
    if (extension_loaded('curl') && function_exists('curl_exec') && function_exists('curl_init')) {
        return 'curl';
    }
    return false;
}

$transport = changyan_get_transport();
if ($transport === false) {
    if (is_admin()) {
        function changyan_transport_notice()
        {
            echo '<div class="updated"><p><strong>没有可用的HTTP传输方法</strong>，请联系主机提供商安装或开启CURL</p></div>';
        }

        add_action('admin_notices', 'changyan_transport_notice');
    }
    return;
}

//in case of JSON not found
if (false === extension_loaded('json')) {
    include CHANGYAN_PLUGIN_PATH . '/Services_JSON.php';
}

require_once CHANGYAN_PLUGIN_PATH . '/Handler.php';

$changyanPlugin = Changyan_Handler::getInstance();

function changyan_admin_init()
{
    global $wp_version, $changyanPlugin, $plugin_page;

    //See http://wordpress.stackexchange.com/questions/20327/plugin-action-links-filter-hook-deprecated
    //See also http://stackoverflow.com/questions/1580378/plugin-action-links-not-working-in-wordpress-2-8
    add_filter('plugin_action_links_changyan/changyan.php', array($changyanPlugin, 'doPluginActionLinks', 10, 2));

    $script = $changyanPlugin->getOption('changyan_script');

    if (empty($script)) { //If not enabled, the changyan_appID is empty
        function changyan_config_notice()
        {
            //TODO the link is not available
            echo '<div class="updated"><p><strong>请完成相关<a href="' . admin_url('admin.php?page=changyan') . '">配置</a>，您就能享受畅言的服务了。</strong></p></div>';
        }

        //if the admin left menu item is not changyan currently, show links to the changyan item page
        if ($plugin_page !== 'changyan') {
            add_action('admin_notices', 'changyan_config_notice');
        }
    }

    //See http://wordpress.stackexchange.com/questions/14973/row-actions-for-custom-post-types
    //add_filter('post_row_actions', array($changyanPlugin, 'filterActions'));

    //level_10 is the admin level
    if (version_compare($wp_version, '3.0', '<') && current_user_can('level_10')) {
        function changyan_wp_version_warnning()
        {
            echo '<div class="updated"><p><strong>您的WordPress版本低于3.0，请升级到最新版本以享受畅言提供的服务。</strong></p></div>';
        }

        //check wp version when run into the changyan page
        add_action(get_plugin_page_hook('changyan', 'changyan'), 'changyan_wp_version_warnning');
    }

    add_action('admin_head-edit-comments.php', array($changyanPlugin, 'showCommentsNotice'));

    //use ajax on wordpress
    add_action('wp_ajax_changyan_sync2WordPress', array($changyanPlugin, 'sync2Wordpress'));
    add_action('wp_ajax_changyan_sync2Changyan', array($changyanPlugin, 'sync2Changyan'));
    add_action('wp_ajax_changyan_saveScript', array($changyanPlugin, 'saveScript'));
    add_action('wp_ajax_changyan_saveAppID', array($changyanPlugin, 'saveAppID'));
    add_action('wp_ajax_changyan_saveAppKey', array($changyanPlugin, 'saveAppKey'));
    add_action('wp_ajax_changyan_cron', array($changyanPlugin, 'setCron'));

    changyan_base_init();
}

function changyan_init()
{
    global $changyanPlugin;

    changyan_base_init();
}

function changyan_base_init()
{
    global $changyanPlugin;
    $script = $changyanPlugin->getOption('changyan_script');
    
    if (!empty($script)) {
        add_filter('comments_template', array($changyanPlugin, 'getCommentsTemplate'));
    }
    ini_set('display_errors', '1');
    //schedule synchronization
    $isCron = $changyanPlugin->getOption('changyan_isCron');
    if ($isCron == true || $isCron == 'true') {
        add_action('changyanCron', array($changyanPlugin, 'cronSync'));
        if (!wp_next_scheduled('changyanCron')) {
            wp_schedule_event(time(), 'hourly', 'changyanCron');
        }
    }
}

function changyan_add_menu_items()
{
    global $changyanPlugin;

    $changyan_appKey = $changyanPlugin->getOption('changyan_appKey');
    $changyan_script = $changyanPlugin->getOption('changyan_script');
    if (empty($changyan_appKey) || empty($changyan_script)) {
        add_object_page(
            '初始化',
            '畅言评论',
            'moderate_comments',
            'changyan',
            array($changyanPlugin, 'setup'),
            //icon set
            $changyanPlugin->PluginURL . 'logo.png'
        );
    } else {
        //installed and enabled
        if (current_user_can('moderate_comments')) {
            add_object_page(
                '畅言评论',
                '畅言评论',
                'moderate_comments',
                'changyan',
                array($changyanPlugin, 'configure'),
                //icon set
                $changyanPlugin->PluginURL . 'logo.png'
            );

            add_submenu_page(
                'changyan',
                '统计分析',
                '统计分析',
                'manage_options',
                'changyan_analysis',
                array($changyanPlugin, 'analysis')
            );

            add_submenu_page(
                'changyan',
                '高级选项',
                '高级选项',
                'manage_options',
                'changyan_settings',
                array($changyanPlugin, 'settings')
            );
        }
    }
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

//invoke the functions above
if (is_admin()) {
    //The function register_deactivation_hook (introduced in WordPress 2.0) registers a plugin function to be run when the plugin is deactivated.
    register_deactivation_hook(__FILE__, 'changyan_deactivate');
    //http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
    //TODO 0627
    add_action('admin_menu', 'changyan_add_menu_items');
    add_action('admin_init', 'changyan_admin_init');
} else {
    add_action('init', 'changyan_init');
}


//////////////////////
add_action('profile_update', 'cy_profile_update');

function cy_profile_update($user_id, $older_user_data)
{
    echo 'User ' . $user_id . ',Older data is :<br/>';
    print_r($older_user_data);
}
?>
