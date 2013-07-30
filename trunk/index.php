<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require_once './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install');}

$zbp->Initialize();

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {$fpname();}

ViewList(GetVars('page','GET'),GetVars('cate','GET'),GetVars('auth','GET'),GetVars('tags','GET'),GetVars('date','GET'));

$zbp->Terminate();

RunTime();
?>