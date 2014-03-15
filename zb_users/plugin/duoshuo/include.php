<?php
define('DUOSHUO_DEBUG',TRUE);
define('DUOSHUO_PATH',dirname(__FILE__));

if(!class_exists('Network')){
	//ZBP1.3之前临时使用
	if($zbp->version <= 140220)
	{
		require DUOSHUO_PATH . '/class_network/network.php';
		require DUOSHUO_PATH . '/class_network/networkcurl.php';
		require DUOSHUO_PATH . '/class_network/networkfile_get_contents.php';
		require DUOSHUO_PATH . '/class_network/networkfsockopen.php';
	}
}

require DUOSHUO_PATH . '/jwt.php';
require DUOSHUO_PATH . '/duoshuo.class.php';
require DUOSHUO_PATH . '/duoshuo.api.php';

$table['plugin_duoshuo_comment'] = '%pre%plugin_duoshuo_comment';
$table['plugin_duoshuo_members'] = '%pre%plugin_duoshuo_members';

$datainfo['plugin_duoshuo_comment'] = array(
	'ID' => array('ds_ID','integer','',0),
	'key' => array('ds_key','string',128,''),
	'cmtid' => array('ds_cmtid','integer','',0)
);

$datainfo['plugin_duoshuo_members'] = array(
	'ID' => array('ds_ID','integer','',0),
	'key' => array('ds_key','string',128,''),
	'memid' => array('ds_memid','integer','',0),
	'accesstoken' => array('ds_accesstoken','string',128,''),
);

$duoshuo = new duoshuo_class();

RegisterPlugin("duoshuo","ActivePlugin_duoshuo");

function ActivePlugin_duoshuo()
{
	//拦截系统自带评论发布接口
	Add_Filter_Plugin("Filter_Plugin_Cmd_Begin","duoshuo_cmd_begin");
	//“评论管理”转向
	Add_Filter_Plugin('Filter_Plugin_Admin_Begin','duoshuo_admin_begin');
	//文章页插入多说
	Add_Filter_Plugin('Filter_Plugin_Template_GetTemplate','duoshuo_template_gettemplate');
	//重写系统自带评论
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Begin','duoshuo_view_post_begin');
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','duoshuo_view_post_template');
	Add_Filter_Plugin('Filter_Plugin_ViewList_Template','duoshuo_view_list_template');
	//同步到多说
	Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed',"duoshuo_post_article_succeed");


}

function InstallPlugin_duoshuo()
{
	global $zbp;
	@duoshuo_create_database();
	duoshuo_create_functions();
	//Init Config
	if(!isset($zbp->Config('duoshuo')->short_name))
	{
		$zbp->Config('duoshuo')->short_name = '';
		$zbp->Config('duoshuo')->secret = '';
		$zbp->Config('duoshuo')->api_hostname = 'api.duoshuo.com';
		$zbp->Config('duoshuo')->cron_sync_enabled = 'async';
		$zbp->Config('duoshuo')->cc_fix = '1';
		$zbp->Config('duoshuo')->comments_wrapper_intro = '';
		$zbp->Config('duoshuo')->comments_wrapper_outro	= '';
		$zbp->Config('duoshuo')->seo_enabled = 1;
		$zbp->Config('duoshuo')->lastpub = 0;
		$zbp->Config('duoshuo')->log_id = 0;
		$zbp->SaveConfig('duoshuo');
	}
}

function UninstallPlugin_duoshuo()
{
}

function duoshuo_cmd_begin()
{
	global $action;
	if($action == 'cmt') exit;
}

function duoshuo_create_functions()
{
	global $zbp;
	//global $duoshuo;
	if(!isset($zbp->modulesbyfilename['duoshuo_recentcomments']))
	{
		$t = new Module();
		$t->Name = "多说最新评论";
		$t->FileName = "Duoshuo_RecentComments";
		$t->Source = "plugin_duoshuo";
		$t->SidebarID = 0;
		$t->Content = "";
		$t->HtmlID = "Duoshuo_RecentComments";
 		$t->Type = "div";
		$t->Content = '<ul class="ds-recent-comments" data-num-items="10"></ul>';
		$t->Save();
	}
	if(!isset($zbp->modulesbyfilename['duoshuo_topthreads']))
	{
		$t = new Module();
		$t->Name = "多说最热文章";
		$t->FileName = "Duoshuo_TopThreads";
		$t->Source = "plugin_duoshuo";
		$t->SidebarID = 0;
		$t->Content = "";
		$t->HtmlID = "Duoshuo_TopThreads";
 		$t->Type = "div";
		$t->Content = '<ul class="ds-top-threads" data-range="weekly" data-num-items="10"></ul>';
		$t->Save();
	}
}

function duoshuo_create_database()
{
	global $zbp;
	if(!$zbp->db->ExistTable($GLOBALS['table']['plugin_duoshuo_comment']))
	{
		$s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_duoshuo_comment'],$GLOBALS['datainfo']['plugin_duoshuo_comment']);
		$zbp->db->QueryMulit($s);
	}
	if(!$zbp->db->ExistTable($GLOBALS['table']['plugin_duoshuo_members']))
	{
		$s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_duoshuo_members'],$GLOBALS['datainfo']['plugin_duoshuo_members']);
		$zbp->db->QueryMulit($s);
	}
}


function duoshuo_admin_begin()
{
	global $duoshuo;
	if(strtolower(GetVars("act","GET"))=="commentmng")
	{
		$duoshuo->init();
		header('Location: '. $duoshuo->duoshuo_path . 'main.php');
	}
}


function duoshuo_template_gettemplate(&$obj,$name)
{
	global $duoshuo;
	global $zbp;
	if($name == 'commentpost')
	{
		$duoshuo->init();
		$GLOBALS['Filter_Plugin_Template_GetTemplate']['duoshuo_template_gettemplate'] = PLUGIN_EXITSIGNAL_RETURN;
		return $zbp->path . 'zb_users/plugin/duoshuo/_commentpost.inc';
	}
	else if($name == 'comment')
	{
		$duoshuo->init();
	}
	$GLOBALS['Filter_Plugin_Template_GetTemplate']['duoshuo_template_gettemplate'] = PLUGIN_EXITSIGNAL_NONE;
}

function duoshuo_view_post_template(&$template)
{
	global $zbp;
	global $duoshuo;
	
	$template->SetTags('duoshuo_comments_wrapper_intro',$duoshuo->cfg->comments_wrapper_intro);
	$template->SetTags('duoshuo_comments_wrapper_outro',$duoshuo->cfg->comments_wrapper_outro);
	
	$spider = $duoshuo->check_spider();
	
	if(!$spider) 
	{
		$comment = &$template->GetTags('comments');
		$comment = array();
	}
	
	$zbp->option['ZC_COMMENT_TURNOFF'] = true;
	$post = &$template->GetTags('article');
	$post->IsLock = false;
	
	if($duoshuo->cfg->cc_fix)
	{
		$post->CommNums = '<span id="duoshuo_comment'.$post->ID.'" duoshuo_id="'.$post->ID.'"></span>';
		$duoshuo->cc_thread_key .= $post->ID.',';
	}
	
	$template->SetTags('footer',$duoshuo->get_footer_js());
	
}
function duoshuo_view_list_template(&$template)
{
	global $zbp;
	global $duoshuo;
	$duoshuo->init();
	$posts = &$template->GetTags('articles');
	foreach($posts as $post)
	{
		if($duoshuo->cfg->cc_fix)
		{
			$post->CommNums = '<span id="duoshuo_comment'.$post->ID.'" duoshuo_id="'.$post->ID.'"></span>';
			$duoshuo->cc_thread_key .= $post->ID.',';
		}
	}
	$template->SetTags('footer',$duoshuo->get_footer_js());
}

function duoshuo_view_post_begin($id,$alias)
{
	global $zbp;
	global $duoshuo;
	$duoshuo_spider = $duoshuo->check_spider();
	if(!$duoshuo_spider) $zbp->option['ZC_COMMENT_TURNOFF'] = true;
}

function duoshuo_post_article_succeed(&$article)
{
	global $duoshuo;
	$duoshuo->init();
	
	$odata = array();
	$odata[0] = 'threads[0][thread_key]=' . $article->ID;
	$odata[1] = 'threads[0][title]=' . urlencode($article->Title);
	$odata[2] = 'threads[0][url]=' . urlencode($article->Url);
	$odata[3] = 'threads[0][content]=' ;	
	$odata[4] = 'threads[0][author_key]=' . $article->AuthorID;
	$odata[5] = 'threads[0][excerpt]=' . urlencode($article->Intro);
	$odata[6] = 'threads[0][comment_status]=open';
	$odata[7] = 'threads[0][likes]=0';
	$odata[8] = 'threads[0][views]=' . $article->ViewNums;
	
	$ajax = Network::Create();
	if(!$ajax) throw new Exception('主机没有开启网络功能');
	
	$ajax->open('POST','http://' . $duoshuo->cfg->api_hostname . '/' . $duoshuo->url['threads']['import']);
	$ajax->send('short_name=' . urlencode($duoshuo->cfg->short_name) . "&secret=" . urlencode($duoshuo->cfg->secret) . '&' . implode('&',$odata));
	
	$ajax = null;
}



?>