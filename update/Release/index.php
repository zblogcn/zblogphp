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

if(isset($_GET['id'])||isset($_GET['alias'])){
	ViewPost(GetVars('id','GET'),GetVars('alias','GET'));
}else{
	ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('date','GET'),GetVars('tags','GET'));
}

foreach ($GLOBALS['Filter_Plugin_Index_End'] as $fpname => &$fpsignal) {$fpname();}

RunTime();
?>