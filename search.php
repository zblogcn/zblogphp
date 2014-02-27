<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

$zbp->CheckGzip();
$zbp->Load();

$action='search';

if(!$zbp->CheckRights($action)){Redirect('./');}

foreach ($GLOBALS['Filter_Plugin_Search_Begin'] as $fpname => &$fpsignal) {$fpname();}

ViewSearch();

RunTime();