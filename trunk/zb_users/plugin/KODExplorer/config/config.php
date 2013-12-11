<?php

date_default_timezone_set('PRC');
error_reporting(0); 

function P($path){return str_replace('\\','/',$path);}
// 调整此参数，以适应linux link的方式，目前适用于windows，linux，虚拟机
define('WEB_ROOT',      str_replace(P($_SERVER['SCRIPT_NAME']),'',P(dirname(dirname(__FILE__))).'/index.php').'/');
define('HOST',          'http://'.$_SERVER['HTTP_HOST'].'/');
define('BASIC_PATH',    P(dirname(dirname(__FILE__))).'/');
define('APPHOST',       HOST.str_replace(WEB_ROOT,'',BASIC_PATH));//程序根目录
define('TEMPLATE',		BASIC_PATH .'template/');	//模版文件路径
define('LOG_PATH',		BASIC_PATH .'data/log/');	//日志目录
define('CONTROLLER_DIR',BASIC_PATH .'controller/'); //控制器目录
define('MODEL_DIR',		BASIC_PATH .'model/');		//模型目录
define('LIB_DIR',		BASIC_PATH .'lib/');		//库目录
define('FUNCTION_DIR',	LIB_DIR .'function/');		//函数库目录
define('CLASS_DIR',		LIB_DIR .'class/');			//内目录
define('CORER_DIR',		LIB_DIR .'core/');			//核心目录


define('HOME',WEB_ROOT);							//
define('USER',BASIC_PATH.'data/User/');				//
define('DESKTOP',BASIC_PATH.'data/User/desktop/');	//
define('STATIC_PATH','./static/');					//静态文件目录
//define('STATIC_PATH','http://static.kalcaddle.com/static/');
define('STATIC_LESS','css');


include(CORER_DIR.'Application.class.php');
include(CORER_DIR.'Controller.class.php');
include(CORER_DIR.'Model.class.php');

include(FUNCTION_DIR.'common.function.php');
include(FUNCTION_DIR.'web.function.php');
include(FUNCTION_DIR.'file.function.php');
include(BASIC_PATH.'config/user/setting.php');


//数据地址定义。
$config['seting_file']	= BASIC_PATH.'config/user/setting.php';//用户配置文件
$config['fav_path']		= BASIC_PATH.'config/user/fav.php';	// 收藏夹文件存放地址.
$config['pic_thumb']	= BASIC_PATH.'data/thumb/';		// 缩略图生成存放地址
$config['cache_dir']	= BASIC_PATH.'data/cache/';		// 缓存文件地址
$config['system_os']	= 'windows';		//windows,linux,mac
$config['system_charset']='gbk';			//系统编码
$config['app_charset']	 ='utf-8';			//该程序整体统一编码
$config['app_controller']='';				//controller 类名
$config['app_action']	 ='';				//action 方法名

//系统编码配置
if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
	$config['system_os']='windows';
	$config['system_charset']='gbk';
} else {
	$config['system_os']='linux';
	$config['system_charset']='utf-8';
}

$in = parse_incoming();//所有过滤处理。
$config['autorun'] = array(
	array('controller'=>'user','function'=>'loginCheck')
);
