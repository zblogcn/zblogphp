<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

if (!$option['ZC_DATABASE_TYPE']){Redirect('./zb_install/');}

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {$fpname();}

$zbp->Load();

ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('date','GET'),GetVars('tags','GET'));

foreach ($GLOBALS['Filter_Plugin_Index_End'] as $fpname => &$fpsignal) {$fpname();}

RunTime();
?>