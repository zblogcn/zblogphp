<?php
require 'jwt.php';
require 'duoshuo.class.php';

$table['plugin_duoshuo_comment']='%pre%plugin_duoshuo_comment';
$table['plugin_duoshuo_members']='%pre%plugin_duoshuo_members';

$datainfo['plugin_duoshuo_comment']=array(
	'ID'=>array('ds_ID','integer','',0),
	'key'=>array('ds_key','string',128,''),
	'cmtid'=>array('ds_cmtid','integer','',0)
);

$datainfo['plugin_duoshuo_members']=array(
	'ID'=>array('ds_ID','integer','',0),
	'key'=>array('ds_key','string',128,''),
	'memid'=>array('ds_memid','integer','',0),
	'accesstoken'=>array('ds_accesstoken','string',128,''),
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
}
function InstallPlugin_duoshuo()
{
	@duoshuo_create_database();
}
function UninstallPlugin_duoshuo()
{
}
function duoshuo_cmd_begin()
{
	global $action;
	if($action == 'cmt') exit;
}
function duoshuo_create_database()
{
	global $zbp;
	$s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_duoshuo_comment'],$GLOBALS['datainfo']['plugin_duoshuo_comment']);
	$zbp->db->QueryMulit($s);
	$s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_duoshuo_members'],$GLOBALS['datainfo']['plugin_duoshuo_members']);
	$zbp->db->QueryMulit($s);	
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
	
	if(!$duoshuo->check_spider()) 
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
	if(!$duoshuo->check_spider()) $zbp->option['ZC_COMMENT_TURNOFF'] = true;
}

?>