<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

zbp_index_redirect_install();

$zbp->Load();

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {$fpname();}

if(isset($_SERVER['REQUEST_URI'])){
	$url=$_SERVER['REQUEST_URI'];
}else{
	$url=$_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING']? '?'.$_SERVER['QUERY_STRING'] : '');
}

if($url==$cookiespath||$url==$cookiespath . 'index.php'){
	ViewList(null,null,null,null,null);
}elseif(isset($_GET['id'])||isset($_GET['alias'])){
	ViewPost(GetVars('id','GET'),GetVars('alias','GET'));
}elseif(isset($_GET['page'])||isset($_GET['cate'])||isset($_GET['auth'])||isset($_GET['date'])||isset($_GET['tags'])){
	ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('date','GET'),GetVars('tags','GET'));
}else{
	ViewAuto($url);
}

foreach ($GLOBALS['Filter_Plugin_Index_End'] as $fpname => &$fpsignal) {$fpname();}

RunTime();
?>