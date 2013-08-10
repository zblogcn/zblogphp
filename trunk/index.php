<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

#if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install/');}

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {$fpname();}

$zbp->LoadData();

ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('tags','GET'),GetVars('date','GET'));

foreach ($GLOBALS['Filter_Plugin_Index_End'] as $fpname => &$fpsignal) {$fpname();}

RunTime();
?>