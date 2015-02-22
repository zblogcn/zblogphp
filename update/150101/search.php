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

foreach ($GLOBALS['Filter_Plugin_Search_Begin'] as $fpname => &$fpsignal) $fpname();

ViewIndex();

RunTime();