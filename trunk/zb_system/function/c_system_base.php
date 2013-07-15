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

ob_start();

$blogpath = str_replace('\\','/',realpath(dirname(__FILE__).'/../../')) . '/';
$cookiespath = null;
$bloghost = null;
$option = require_once($blogpath . 'zb_users/c_option.php');	
$lang = require_once($blogpath . 'zb_users/language/' . $option['ZC_BLOG_LANGUAGEPACK'] . '.php');
$lib_array = array('base', 'article','category','comment','member','meta','module','tag','upload');

date_default_timezone_set($option['ZC_TIME_ZONE_NAME']);

require_once $blogpath.'zb_system/function/c_system_debug.php';
require_once $blogpath.'zb_system/function/c_system_common.php';
require_once $blogpath.'zb_system/function/c_system_plugin.php';
require_once $blogpath.'zb_system/function/c_system_event.php';

$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

@ini_set('magic_quotes_runtime',0);
@ini_set('magic_quotes_gpc',0);
if(get_magic_quotes_gpc()){
	_stripslashes($_GET);
	_stripslashes($_POST);
	_stripslashes($_COOKIE);
}


require_once $blogpath.'zb_system/function/c_system_lib_zblogphp.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbfactory.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbmysql.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite3.php';

for($i = 0; $i <= 7; $i++){
	require $blogpath.'zb_system/function/c_system_lib_' .$lib_array[$i]. '.php';
}

$zbp=ZBlogPHP::GetInstance();
/*include plugin*/
#加载主题插件
if (file_exists($filename=$blogpath.'zb_users/theme/'.$option['ZC_BLOG_THEME'].'/plugin/include.php')) {
	require_once $filename;
}
#加载激活插件
foreach (explode("|", $option['ZC_USING_PLUGIN_LIST']) as $plugin) {
	if ($filename&&file_exists($filename=$blogpath.'zb_users/plugin/'.$plugin.'/include.php')) {
		require_once $filename;
	}
}
?>