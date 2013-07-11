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
$c_option = require_once($blogpath.'zb_users/c_option.php');	
$c_lang = require_once($blogpath.'zb_users/language/'.$c_option['ZC_BLOG_LANGUAGEPACK'].'.php');

#date_default_timezone_set($c_option['ZC_TIME_ZONE_NAME']);

require_once $blogpath.'zb_system/function/c_system_debug.php';
require_once $blogpath.'zb_system/function/c_system_common.php';
require_once $blogpath.'zb_system/function/c_system_plugin.php';

$cookiespath = null;
$bloghost = GetCurrentHost($cookiespath);

require_once $blogpath.'zb_system/function/c_system_lib_zblogphp.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbfactory.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbmysql.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite.php';
require_once $blogpath.'zb_system/function/c_system_lib_dbsqlite3.php';

require_once $blogpath.'zb_system/function/c_system_lib_article.php';
require_once $blogpath.'zb_system/function/c_system_lib_category.php';
require_once $blogpath.'zb_system/function/c_system_lib_comment.php';
require_once $blogpath.'zb_system/function/c_system_lib_member.php';
require_once $blogpath.'zb_system/function/c_system_lib_meta.php';
require_once $blogpath.'zb_system/function/c_system_lib_module.php';
require_once $blogpath.'zb_system/function/c_system_lib_tag.php';
require_once $blogpath.'zb_system/function/c_system_lib_upload.php';

$zbp=new ZBlogPHP;

/*include plugin*/

?>