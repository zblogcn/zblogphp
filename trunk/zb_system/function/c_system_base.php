<?php

/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

error_reporting(0);
@ini_set("display_errors",0);

ob_start();

$action=null;

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';
$cookiespath = null;
$bloghost = null;

$usersdir = 'zb_users/';

$option_zbusers=null;
if(file_exists($blogpath . 'zb_users/c_option.php')){
	$option_zbusers = require($blogpath . 'zb_users/c_option.php');
}
if(is_array($option_zbusers)){
    $option = $option_zbusers;
}else{
    $option = require($blogpath . 'zb_system/defend/c_option.php');
}

if($option['ZC_DEBUG_MODE']==true){
    error_reporting(-1);
    @ini_set("display_errors",1);
}

$option['ZC_BLOG_PRODUCT_FULL']=$option['ZC_BLOG_PRODUCT'] . ' ' . $option['ZC_BLOG_VERSION'];
$option['ZC_BLOG_PRODUCT_FULLHTML']='<a href="http://www.rainbowsoft.org/" title="RainbowSoft Z-BlogPHP">' . $option['ZC_BLOG_PRODUCT_FULL'] . '</a>';

date_default_timezone_set($option['ZC_TIME_ZONE_NAME']);
header('Product:' . $option['ZC_BLOG_PRODUCT_FULL']);

$lang = require($blogpath . 'zb_users/language/' . $option['ZC_BLOG_LANGUAGEPACK'] . '.php');

$blogtitle = $option['ZC_BLOG_SUBNAME'];
$blogname = $option['ZC_BLOG_NAME'];
$blogsubname = $option['ZC_BLOG_SUBNAME'];
$blogtheme = $option['ZC_BLOG_THEME'];
$blogstyle = $option['ZC_BLOG_CSS'];

require $blogpath.'zb_system/function/c_system_debug.php';
require $blogpath.'zb_system/function/c_system_common.php';
require $blogpath.'zb_system/function/c_system_plugin.php';
require $blogpath.'zb_system/function/c_system_event.php';

$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

#加载zbp
require $blogpath.'zb_system/function/lib/zblogphp.php';

#加载数据库类
require $blogpath.'zb_system/function/lib/dbfactory.php';
if($option['ZC_DATABASE_TYPE']){
	require $blogpath.'zb_system/function/lib/db' . $option['ZC_DATABASE_TYPE'] . '.php';
}

#加载对象
$lib_array = array('base','metas','post','category','comment','counter','member','module','tag','template','upload','pagebar','urlrule','app','rss2');
foreach ($lib_array as $f) {
	require $blogpath.'zb_system/function/lib/' . $f . '.php';
}


#定义常量
define('ZC_POST_TYPE_ARTICLE', 0);
define('ZC_POST_TYPE_PAGE', 1);
define('ZC_POST_STATUS_PUBLIC', 0);
define('ZC_POST_STATUS_DRAFT', 1);
define('ZC_POST_STATUS_AUDITING', 2);

#定义命令
$actions=array(
	'root'=>1,
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
	'CommentPst'=>5,
	'CommentDel'=>5,

	'MemberEdt'=>5,
	'MemberPst'=>5,
	'MemberDel'=>1,
	'MemberNew'=>1,	
	
	'TagEdt'=>2,
	'TagPst'=>2,
	'TagDel'=>2,
	'TagNew'=>2,

	'PluginEnable'=>1,
	'PluginDisable'=>1,

	'UploadPst'=>3,
	'UploadDel'=>3,

	'ThemeSet'=>3,	

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
	'CategoryAll'=>2,
	'CommentAll'=>2,
	'MemberAll'=>1,
	'TagAll'=>2,
	'UploadAll'=>2,
);




$zbp=ZBlogPHP::GetInstance();

#创建User类
$zbp->user=new Member();	


/*include plugin*/

#加载主题插件
if (file_exists($filename=$blogpath.'zb_users/theme/'.$blogtheme.'/include.php')) {
	require $filename;
}

#加载激活插件
foreach (explode("|", $option['ZC_USING_PLUGIN_LIST']) as $plugin) {
	if ($filename&&file_exists($filename=$blogpath.'zb_users/plugin/'.$plugin.'/include.php')) {
		require $filename;
	}
}


/*system plugin*/
function zbp_default_cache_read(){
	global $zbp;
	$zbp->LoadCache();
	if($zbp->HasCache('default_html')){
		if((integer)$zbp->GetCacheTime('default_html') < (integer)$zbp->GetCache('refesh_time'))return;
		echo $zbp->GetCache('default_html');
		RunTime();
		die();
	}
}

function zbp_default_cache_write(){
	global $zbp;
	$s=ob_get_clean();
	echo $s;
	$zbp->SetCache('default_html',$s);
	$zbp->SetCache('refesh',time());
	$zbp->SaveCache(true);
}

#Add_Filter_Plugin('Filter_Plugin_Index_PreInitialize','zbp_default_cache_read');
#Add_Filter_Plugin('Filter_Plugin_Index_End','zbp_default_cache_write');


/*autoload*/
function __autoload($classname) {
     require $GLOBALS['blogpath'] . 'zb_system/function/lib/' . strtolower($classname) .'.php';
}
?>