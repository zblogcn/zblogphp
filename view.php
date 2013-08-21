<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

foreach ($GLOBALS['Filter_Plugin_View_Begin'] as $fpname => &$fpsignal) {$fpname();}

$zbp->Load();

ViewPost(GetVars('id','GET'),GetVars('alias','GET'));

foreach ($GLOBALS['Filter_Plugin_View_End'] as $fpname => &$fpsignal) {$fpname();}

RunTime();
?>