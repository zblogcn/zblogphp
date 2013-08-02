<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install');}

$zbp->Initialize();

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {$fpname();}

ViewPost(GetVars('id','GET'),GetVars('alias','GET'));

$zbp->Terminate();
throw new Exception("Error Processing Request", 1);

RunTime();
?>