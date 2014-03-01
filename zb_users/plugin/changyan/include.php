<?php
#注册插件
RegisterPlugin("changyan","ActivePlugin_changyan");

ini_set('max_execution_time', '0');
define('CHANGYAN_PLUGIN_PATH', dirname(__FILE__));
require CHANGYAN_PLUGIN_PATH . '/Handler.php';

if(!class_exists('Network')){
//ZBP1.3之前临时使用
require CHANGYAN_PLUGIN_PATH . '/network.php';
require CHANGYAN_PLUGIN_PATH . '/networkcurl.php';
require CHANGYAN_PLUGIN_PATH . '/networkfile_get_contents.php';
require CHANGYAN_PLUGIN_PATH . '/networkfsockopen.php';
}

$changyanPlugin = null;

#注册插件函数
function ActivePlugin_changyan() {

    global $changyanPlugin;
	$changyanPlugin = Changyan_Handler::getInstance();

	//add_action('init', 'changyan_init');
	Add_Filter_Plugin('Filter_Plugin_Zbp_Load','changyan_init');

}

function InstallPlugin_changyan(){
	global $zbp;
	//@duoshuo_create_database();
	//duoshuo_create_functions();
	//Init Config
	if($zbp->Config('changyan')->CountItem()==0)
	{
		//$zbp->Config('changyan')->short_name = '';
		//$zbp->SaveConfig('changyan');
	}


    global $wp_version, $changyanPlugin, $plugin_page;

    //See http://wordpress.stackexchange.com/questions/20327/plugin-action-links-filter-hook-deprecated
    //See also http://stackoverflow.com/questions/1580378/plugin-action-links-not-working-in-wordpress-2-8
    //add_filter('plugin_action_links_changyan/changyan.php', array($changyanPlugin, 'doPluginActionLinks', 10, 2));

    $script = $changyanPlugin->getOption('changyan_script');

    if (empty($script)) { //If not enabled, the changyan_appID is empty
        function changyan_config_notice()
        {	global $zbp;
            //TODO the link is not available
            $zbp->SetHint('tips','<strong>请完成相关<a href="' . $zbp->host . 'zb_users/plugin/changyan/main.php' . '">配置</a>，您就能享受畅言的服务了。</strong>');
        }

        //if the admin left menu item is not changyan currently, show links to the changyan item page
        if ($plugin_page !== 'changyan') {
            changyan_config_notice();
        }
    }

    //See http://wordpress.stackexchange.com/questions/14973/row-actions-for-custom-post-types
    //add_filter('post_row_actions', array($changyanPlugin, 'filterActions'));


    //add_action('admin_head-edit-comments.php', array($changyanPlugin, 'showCommentsNotice'));

    changyan_base_init();
	
}

function UninstallPlugin_changyan(){

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
    //ini_set('display_errors', '1');
    //schedule synchronization
    //$isCron = $zbp->Config('changyan')->changyan_isCron;
	$isCron = $changyanPlugin->getOption('changyan_script');
    if ($isCron == true || $isCron == 'true') {
        //add_action('changyanCron', array($changyanPlugin, 'cronSync'));
        //if (!wp_next_scheduled('changyanCron')) {
        //    wp_schedule_event(time(), 'hourly', 'changyanCron');
        //}
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


function cy_profile_update($user_id, $older_user_data)
{
    echo 'User ' . $user_id . ',Older data is :<br/>';
    print_r($older_user_data);
}


?>
