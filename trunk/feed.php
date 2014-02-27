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

$action='feed';

if(!$zbp->CheckRights($action)){Http404();die;}

foreach ($GLOBALS['Filter_Plugin_Feed_Begin'] as $fpname => &$fpsignal) {$fpname();}

ViewFeed();

RunTime();