<?php

/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

#error_reporting(0);
ini_set('display_errors',1);
error_reporting(E_ALL);

@ini_set('magic_quotes_runtime',0);
@ini_set('magic_quotes_gpc',0);

ob_start();

$action=null;

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';
$cookiespath = null;
$bloghost = null;

$option = require($blogpath . 'zb_system/defend/c_option.php');
if(file_exists($blogpath . 'zb_users/c_option.php')){
	$option = require($blogpath . 'zb_users/c_option.php');
}

$option['ZC_BLOG_PRODUCT_FULL']=$option['ZC_BLOG_PRODUCT'] . ' ' . $option['ZC_BLOG_VERSION'];
$option['ZC_BLOG_PRODUCT_FULLHTML']='<a href="http://www.rainbowsoft.org/" title="RainbowSoft Z-BlogPHP">' . $option['ZC_BLOG_PRODUCT_FULL'] . '</a>';

date_default_timezone_set($option['ZC_TIME_ZONE_NAME']);
header('Product:' . $option['ZC_BLOG_PRODUCT_FULL']);

$lang = require($blogpath . 'zb_users/language/' . $option['ZC_BLOG_LANGUAGEPACK'] . '.php');

$blogtitle = $option['ZC_BLOG_NAME'] . '-' . $option['ZC_BLOG_SUBNAME'];
$blogname = $option['ZC_BLOG_NAME'];
$blogsubname = $option['ZC_BLOG_SUBNAME'];

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
$lib_array = array('base','metas','post','category','comment','counter','member','module','tag','template','upload','pagebar');
foreach ($lib_array as $f) {
	require $blogpath.'zb_system/function/lib/' . $f . '.php';
}


#定义常量
define('ZC_LOG_TYPE_ARTICLE', 0);
define('ZC_LOG_TYPE_PAGE', 1);
define('ZC_LOG_STATUS_PUBLIC', 0);
define('ZC_LOG_STATUS_PRIVATE', 1);
define('ZC_LOG_STATUS_DRAFT', 2);

#定义命令
$actions=array(
	'root'=>1,
	'login'=>5,
	'logout'=>5,
	'verify'=>5,
	'admin'=>4,
	'search'=>5,
	'misc'=>5,

	'ArticleEdt'=>3,
	'ArticleDel'=>3,

	'CategoryEdt'=>1,
	'CategoryPst'=>1,

	'MemberEdt'=>0,

	'ArticleMng'=>3,
	'CategoryMng'=>2,
	'SettingMng'=>1,
	'TagMng'=>2,
	'CommentMng'=>4,
	'UploadMng'=>2,
	'MemberMng'=>4,
	'ThemeMng'=>1,
	'PluginMng'=>1,
	'ModuleMng'=>1,

	'ArticleAll'=>2,
	'MemberAll'=>1,

);




$zbp=ZBlogPHP::GetInstance();
#创建User类
$zbp->user=new Member();	



/*include plugin*/

#加载主题插件
if (file_exists($filename=$blogpath.'zb_users/theme/'.$option['ZC_BLOG_THEME'].'/plugin/include.php')) {
	require $filename;
}

#加载激活插件
foreach (explode("|", $option['ZC_USING_PLUGIN_LIST']) as $plugin) {
	if ($filename&&file_exists($filename=$blogpath.'zb_users/plugin/'.$plugin.'/include.php')) {
		require $filename;
	}
}




function __autoload($classname) {
     require $GLOBALS['blogpath'] . 'zb_system/function/lib/' . strtolower($classname) .'.php';
}


?>