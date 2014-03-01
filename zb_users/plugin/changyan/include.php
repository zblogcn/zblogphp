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
	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','changyan_AddMenu');
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','changyan_socialcomment');

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

function changyan_AddMenu(&$m){
	global $zbp;
	$b=false;
	$i=0;
	$s=MakeLeftMenu("root","畅言评论",$zbp->host . "zb_users/plugin/changyan/main.php","nav_changyan","aChangYan",$zbp->host . "zb_users/plugin/changyan/cy.png");
	foreach($m as $key=>$value){
		if($key==='nav_comment'){
			$m[$key]=$s;
			$b=true;
		}
	}
	if(!$b){
		reset($m);
		foreach($m as $key=>$value){
			if(strpos($value,'act=CommentMng')!==false){
				$b=true;
				break;
			}
			$i=$i+1;
		}
		if($b){
			array_splice($m,$i,1,array('nav_changyan'=>$s));
		}
	}
	if(!$b){$m["nav_changyan"]=$s;}
}

function changyan_socialcomment(&$template){
    global $zbp,$changyanPlugin;
    $script = $changyanPlugin->getOption('changyan_script');
    if (!empty($script)) {
		$a=$template->tags['article'];
		$s='sid="'.$a->ID.'"';
		$script=str_replace('id="SOHUCS"','id="SOHUCS" ' . $s,$script);
		$template->SetTags('socialcomment',$script);
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


function changyan_SubMenus()
{
	global $zbp;
	$id=1;
	$url = GetRequestUri();
	if(strpos($url,'analysis.php')!==false)$id=2;
	if(strpos($url,'settings.php')!==false)$id=3;
	echo '<a href="main.php"><span class="m-left '.($id==1?'m-now':'').'">评论管理</span></a>';
	echo '<a href="analysis.php"><span class="m-left '.($id==2?'m-now':'').'">统计分析</span></a>';
	echo '<a href="settings.php"><span class="m-left '.($id==3?'m-now':'').'">设置与初始化</span></a>';

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
