<?php

/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

error_reporting(0);
//@ini_set("display_errors",0);
//@ini_set('magic_quotes_runtime',0);
//@ini_set('magic_quotes_gpc',0);

$zbpvers=array();
$zbpvers['130707']='1.0 Beta Build 130707';

#定义常量
define('ZC_BLOG_VERSION', '1.0 Beta Build 130707');

define('ZC_POST_TYPE_ARTICLE', 0);
define('ZC_POST_TYPE_PAGE', 1);

define('ZC_POST_STATUS_PUBLIC', 0);
define('ZC_POST_STATUS_DRAFT', 1);
define('ZC_POST_STATUS_AUDITING', 2);

define('ZC_MEMBER_STATUS_NORMAL', 0);
define('ZC_MEMBER_STATUS_AUDITING', 1);
define('ZC_MEMBER_STATUS_LOCKED', 2);

function _stripslashes(&$val) {
	if(!is_array($val)) return stripslashes($val);
	foreach($val as $k => &$v) $val[$k] = _stripslashes($v);
	return $val;
}

if(get_magic_quotes_gpc()){
	_stripslashes($_GET);
	_stripslashes($_POST);
	_stripslashes($_COOKIE);
}

ob_start();

$action=null;

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';
$usersdir = $blogpath . 'zb_users/';


$option_zbusers=null;
if(file_exists($usersdir . 'c_option.php')){
	$option_zbusers = require($usersdir . 'c_option.php');
}
if(!is_array($option_zbusers))$option_zbusers=array();
$option = require($blogpath . 'zb_system/defend/option.php');
foreach ($option_zbusers as $key => $value) {
	$option[$key]=$value;
}

date_default_timezone_set($option['ZC_TIME_ZONE_NAME']);

$lang = require($blogpath . 'zb_users/language/' . $option['ZC_BLOG_LANGUAGEPACK'] . '.php');

$blogtitle = $option['ZC_BLOG_SUBNAME'];
$blogname = &$option['ZC_BLOG_NAME'];
$blogsubname = &$option['ZC_BLOG_SUBNAME'];
$blogtheme = &$option['ZC_BLOG_THEME'];
$blogstyle = &$option['ZC_BLOG_CSS'];
$blogversion = substr(ZC_BLOG_VERSION,-6,6);

require $blogpath.'zb_system/function/c_system_common.php';

$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

require $blogpath.'zb_system/function/c_system_debug.php';
require $blogpath.'zb_system/function/c_system_plugin.php';
require $blogpath.'zb_system/function/c_system_event.php';


/*autoload*/
function __autoload($classname) {
	foreach ($GLOBALS['Filter_Plugin_Autoload'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($classname);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}
	require $GLOBALS['blogpath'] . 'zb_system/function/lib/' . strtolower($classname) .'.php';
}


#加载zbp 数据库类 对象
$lib_array = array('zblogphp','dbsql','base','metas','post','category','comment','counter','member','module','tag','template','upload','pagebar','urlrule','app','rss2');
foreach ($lib_array as $f) {
	require $blogpath.'zb_system/function/lib/' . $f . '.php';
}


#定义命令
$actions=array(
	'login'=>6,
	'logout'=>6,
	'verify'=>6,
	'admin'=>5,
	'search'=>6,
	'misc'=>6,
	'feed'=>6,
	'cmt'=>6,
	'getcmt'=>6,

	'ArticleEdt'=>4,
	'ArticlePst'=>4,
	'ArticleDel'=>4,
	'ArticlePub'=>3,	

	'PageEdt'=>2,
	'PagePst'=>2,
	'PageDel'=>2,

	'CategoryEdt'=>2,
	'CategoryPst'=>2,
	'CategoryDel'=>2,

	'CommentEdt'=>5,
	'CommentSav'=>5,
	'CommentDel'=>5,
	'CommentChk'=>5,
	'CommentBat'=>5,

	'MemberEdt'=>5,
	'MemberPst'=>5,
	'MemberDel'=>1,
	'MemberNew'=>1,	
	
	'TagEdt'=>2,
	'TagPst'=>2,
	'TagDel'=>2,
	'TagNew'=>2,

	'PluginEnb'=>1,
	'PluginDis'=>1,

	'UploadPst'=>3,
	'UploadDel'=>3,

	'ModuleEdt'=>3,
	'ModulePst'=>3,
	'ModuleDel'=>3,

	'ThemeSet'=>1,
	'SidebarSet'=>1,
	
	'SettingSav'=>1,	

	'ArticleMng'=>4,
	'PageMng'=>2,
	'CategoryMng'=>2,
	'SettingMng'=>1,
	'TagMng'=>2,
	'CommentMng'=>5,
	'UploadMng'=>3,
	'MemberMng'=>5,
	'ThemeMng'=>1,
	'PluginMng'=>1,
	'ModuleMng'=>1,

	'ArticleAll'=>2,
	'PageAll'=>2,
	'CategoryAll'=>2,
	'CommentAll'=>2,
	'MemberAll'=>1,
	'TagAll'=>2,
	'UploadAll'=>2,

	'root'=>1,
);



$table=array(

'Post'=> '%pre%post',
'Category'=> '%pre%category',
'Comment'=> '%pre%comment',
'Tag'=> '%pre%tag',
'Upload'=> '%pre%upload',
'Counter'=> '%pre%counter',
'Module'=> '%pre%module',
'Member'=> '%pre%member',
'Config'=>'%pre%config',

);


$datainfo=array(
'Config'=>array(
	'Name'=>array('conf_Name','string',250,''),
	'Value'=>array('conf_Value','string','',''),
),
'Post'=> array(
	'ID'=>array('log_ID','integer','',0),
	'CateID'=>array('log_CateID','integer','',0),
	'AuthorID'=>array('log_AuthorID','integer','',0),
	'Tag'=>array('log_Tag','string',250,''),
	'Status'=>array('log_Status','integer','',0),
	'Type'=>array('log_Type','integer','',0),
	'Alias'=>array('log_Alias','string',250,''),
	'IsTop'=>array('log_IsTop','boolean','',false),
	'IsLock'=>array('log_IsLock','boolean','',false),
	'Title'=>array('log_Title','string',250,''),
	'Intro'=>array('log_Intro','string','',''),
	'Content'=>array('log_Content','string','',''),
	'PostTime'=>array('log_PostTime','integer','',0),
	'CommNums'=>array('log_CommNums','integer','',0),
	'ViewNums'=>array('log_ViewNums','integer','',0),
	'Template'=>array('log_Template','string',50,''),
	'Meta'=>array('log_Meta','string','',''),
),
'Category'=>array(
	'ID'=>array('cate_ID','integer','',0),
	'Name'=>array('cate_Name','string',50,''),
	'Order'=>array('cate_Order','integer','',0),
	'Count'=>array('cate_Count','integer','',0),
	'Alias'=>array('cate_Alias','string',50,''),
	'Intro'=>array('cate_Intro','string','',''),
	'RootID'=>array('cate_RootID','integer','',0),
	'ParentID'=>array('cate_ParentID','integer','',0),
	'Template'=>array('cate_Template','string',50,''),
	'LogTemplate'=>array('cate_LogTemplate','string',50,''),
	'Meta'=>array('cate_Meta','string','',''),
),
'Comment'=> array(
	'ID'=>array('comm_ID','integer','',0),
	'LogID'=>array('comm_LogID','integer','',0),
	'IsChecking'=>array('comm_IsChecking','boolean','',false),
	'RootID'=>array('comm_RootID','integer','',0),
	'ParentID'=>array('comm_ParentID','integer','',0),
	'AuthorID'=>array('comm_AuthorID','integer','',0),
	'Name'=>array('comm_Name','string',20,''),
	'Content'=>array('comm_Content','string','',''),
	'Email'=>array('comm_Email','string',50,''),
	'HomePage'=>array('comm_HomePage','string',250,''),
	'PostTime'=>array('comm_PostTime','integer','',0),
	'IP'=>array('comm_IP','string',15,''),
	'Agent'=>array('comm_Agent','string','',''),
	'Meta'=>array('comm_Meta','string','',''),
),
'Counter'=> array(
	'ID'=>array('coun_ID','integer','',0),
	'MemID'=>array('coun_MemID','integer','',0),
	'IP'=>array('coun_IP','string',15,''),
	'Agent'=>array('coun_Agent','string','',''),
	'Refer'=>array('coun_Refer','string',250,''),
	'Title'=>array('coun_Title','string',250,''),
	'PostTime'=>array('coun_PostTime','integer','',0),
	'Description'=>array('coun_Description','string','',''),
	'PostData'=>array('coun_PostData','string','',''),
	'AllRequestHeader'=>array('coun_AllRequestHeader','string','',''),
),
'Module'=> array(
	'ID'=>array('mod_ID','integer','',0),
	'Name'=>array('mod_Name','string',100,''),
	'FileName'=>array('mod_FileName','string',50,''),
	'Content'=>array('mod_Content','string','',''),
	'HtmlID'=>array('mod_HtmlID','string',50,''),
	'Type'=>array('mod_Type','string',5,'div'),
	'MaxLi'=>array('mod_MaxLi','integer','',0),
	'Source'=>array('mod_Source','string',50,'user'),
	'IsHideTitle'=>array('mod_IsHideTitle','boolean','',false),
	'Meta'=>array('mod_Meta','string','',''),
),
'Member'=> array(
	'ID'=>array('mem_ID','integer','',0),
	'Guid'=>array('mem_Guid','string',36,''),
	'Level'=>array('mem_Level','integer','',6),	
	'Status'=>array('mem_Status','integer','',0),
	'Name'=>array('mem_Name','string',20,''),
	'Password'=>array('mem_Password','string',32,''),
	'Email'=>array('mem_Email','string',50,''),
	'HomePage'=>array('mem_HomePage','string',250,''),
	'IP'=>array('mem_IP','string',15,''),
	'PostTime'=>array('mem_PostTime','integer','',0),
	'Alias'=>array('mem_Alias','string',250,''),
	'Intro'=>array('mem_Intro','string','',''),
	'Articles'=>array('mem_Articles','integer','',0),
	'Pages'=>array('mem_Pages','integer','',0),
	'Comments'=>array('mem_Comments','integer','',0),
	'Uploads'=>array('mem_Uploads','integer','',0),
	'Template'=>array('mem_Template','string',50,''),
	'Meta'=>array('mem_Meta','string','',''),
),
'Tag'=> array(
	'ID'=>array('tag_ID','integer','',0),
	'Name'=>array('tag_Name','string',250,''),
	'Order'=>array('tag_Order','integer','',0),
	'Count'=>array('tag_Count','integer','',0),
	'Alias'=>array('tag_Alias','string',250,''),	
	'Intro'=>array('tag_Intro','string','',''),
	'Template'=>array('tag_Template','string',50,''),
	'Meta'=>array('tag_Meta','string','',''),
),
'Upload'=> array(
	'ID'=>array('ul_ID','integer','',0),
	'AuthorID'=>array('ul_AuthorID','integer','',0),
	'Size'=>array('ul_Size','integer','',0),
	'Name'=>array('ul_Name','string',250,''),
	'SourceName'=>array('ul_SourceName','string',250,''),
	'MimeType'=>array('ul_MimeType','string',50,''),
	'PostTime'=>array('ul_PostTime','integer','',0),
	'DownNums'=>array('ul_DownNums','integer','',0),
	'LogID'=>array('ul_LogID','integer','',0),	
	'Intro'=>array('ul_Intro','string','',''),
	'Meta'=>array('ul_Meta','string','',''),
),
);



$zbp=ZBlogPHP::GetInstance();
$zbp->Initialize();


/*include plugin*/
#加载主题插件
if (file_exists($filename = $usersdir . 'theme/'.$blogtheme.'/include.php')) {
	require $filename;
}


#加载激活插件
$ap=explode("|", $option['ZC_USING_PLUGIN_LIST']);
$ap=array_unique($ap);
foreach ($ap as $plugin) {
	if (file_exists($filename = $usersdir . 'plugin/' . $plugin . '/include.php')) {
		require $filename;
	}
}


ActivePlugin();	



/*system plugin*/
function zbp_index_cache_read(){
	global $zbp;
	if(count($_GET)==0){
		if($zbp->cache->HasKey('default_html')){
			if((integer)$zbp->cache->default_html_time < (integer)$zbp->cache->refesh )return;
			echo $zbp->cache->default_html;
			RunTime();
			die();
		}
	}
}

function zbp_index_cache_write(){
	global $zbp;
	if(count($_GET)==0){
		$s=ob_get_clean();
		echo $s;
		$zbp->cache->default_html=$s;
		$zbp->cache->default_html_time=time();
		$zbp->SaveCache();
	}
}

function  zbp_index_redirect_install(){
	global $zbp;
	if (!$zbp->option['ZC_DATABASE_TYPE']){Redirect('./zb_install/');}
}

#Add_Filter_Plugin('Filter_Plugin_Index_Pre','zbp_index_redirect_install');
#Add_Filter_Plugin('Filter_Plugin_Index_Pre','zbp_default_cache_read');
#Add_Filter_Plugin('Filter_Plugin_Index_End','zbp_default_cache_write');



?>