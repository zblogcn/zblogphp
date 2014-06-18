<?php
/**
 * 系统初始化等相关操作
 * @package Z-BlogPHP
 * @subpackage System/Base 基础操作
 * @copyright (C) RainbowSoft Studio
 */

error_reporting(0);

ob_start();


#引入必备
$basepath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('ZBP_PATH',str_replace('\\','/',realpath($basepath . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)) . '/');
require $basepath . 'c_system_plugin.php';
require $basepath . 'c_system_debug.php';
require $basepath . 'c_system_common.php';
require $basepath . 'c_system_event.php';


#系统预处理
spl_autoload_register('AutoloadClass');

if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
	function _stripslashes(&$var) {
		if(is_array($var)) {
			foreach($var as $k=>&$v) {
				_stripslashes($v);
			}
		} else {
			$var = stripslashes($var);
		}
	}
	_stripslashes($_GET);
	_stripslashes($_POST);
	_stripslashes($_COOKIE);
	_stripslashes($_REQUEST);
}


#初始化统计信息
$_SERVER['_start_time'] = microtime(true); //RunTime
$_SERVER['_query_count'] = 0;
$_SERVER['_memory_usage'] = 0;
$_SERVER['_error_count'] = 0;
if(function_exists('memory_get_usage'))$_SERVER['_memory_usage'] = memory_get_usage(true);


#定义版本号列
$zbpvers=array();
$zbpvers['130707']='1.0 Beta Build 130707';
$zbpvers['131111']='1.0 Beta2 Build 131111';
$zbpvers['131221']='1.1 Taichi Build 131221';
$zbpvers['140220']='1.2 Hippo Build 140220';
$zbpvers['140614']='1.3 Wonce Build 140614';


#定义常量
/**
 *ZBLOGPHP版本号
 */
define('ZC_BLOG_VERSION', $zbpvers['140614']);

/**
 *文章类型：文章型
 */
define('ZC_POST_TYPE_ARTICLE', 0);
/**
 *文章类型：页面型
 */
define('ZC_POST_TYPE_PAGE', 1);

/**
 *文章状态：公开发布
 */
define('ZC_POST_STATUS_PUBLIC', 0);
/**
 *文章状态：草稿
 */
define('ZC_POST_STATUS_DRAFT', 1);
/**
 *文章状态：审核
 */
define('ZC_POST_STATUS_AUDITING', 2);
/**
 *用户状态：正常
 */
define('ZC_MEMBER_STATUS_NORMAL', 0);
/**
 *用户状态：审核
 */
define('ZC_MEMBER_STATUS_AUDITING', 1);
/**
 *用户状态：锁定
 */
define('ZC_MEMBER_STATUS_LOCKED', 2);


#定义全局变量
$zbp = null;
$action = '';
$currenturl = GetRequestUri();
$lang = array();

$blogpath = ZBP_PATH;
$usersdir = $blogpath . 'zb_users/';

$option_zbusers = null;
if(is_readable($filename = $usersdir . 'c_option.php')){
	$option_zbusers = require($filename);
}
if(!is_array($option_zbusers))$option_zbusers=array();
$option = require($blogpath . 'zb_system/defend/option.php');
foreach ($option_zbusers as $key => $value) {
	$option[$key] = $value;
}
unset($basepath,$key,$value,$option_zbusers);

$blogtitle = $option['ZC_BLOG_SUBNAME'];
$blogname = &$option['ZC_BLOG_NAME'];
$blogsubname = &$option['ZC_BLOG_SUBNAME'];
$blogtheme = &$option['ZC_BLOG_THEME'];
$blogstyle = &$option['ZC_BLOG_CSS'];
$blogversion = substr(ZC_BLOG_VERSION,-6,6);

$cookiespath = null;
$bloghost = GetCurrentHost($blogpath,$cookiespath);


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
	'ajax'=>6,

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


#定义数据表
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


#定义数据结构
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
	'Name'=>array('mem_Name','string',50,''),
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


#加载zbp 数据库类 基础对象
AutoloadClass('ZBlogPHP');
AutoloadClass('DbSql');
AutoloadClass('Base');


#实例化zbp
$zbp=ZBlogPHP::GetInstance();
$zbp->Initialize();


$activeapps=array();

#加载主题内置的插件
$activeapps[]=$blogtheme;
if (is_readable($filename = $usersdir . 'theme/' . $blogtheme . '/include.php')) {
	require $filename;
}


#加载插件
$ap=explode("|", $option['ZC_USING_PLUGIN_LIST']);
$ap=array_unique($ap);
foreach ($ap as $plugin) {
	if (is_readable($filename = $usersdir . 'plugin/' . $plugin . '/include.php')) {
		$activeapps[]=$plugin;
		require $filename;
	}elseif(is_readable($filename = $usersdir . 'plugin/' . $plugin . '/plugin.xml')){
		$activeapps[]=$plugin;
	}
}
unset($plugin,$ap,$filename);


#激活所有已加载的插件
ActivePlugin();
