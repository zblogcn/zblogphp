<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version
 */
require 'zb_system/function/c_system_base.php';

$zbp->CheckGzip();
$zbp->Load();
$zbp->CheckSiteClosed();

$action = 'feed';

foreach ($GLOBALS['hooks']['Filter_Plugin_Feed_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

ViewIndex();

foreach ($GLOBALS['hooks']['Filter_Plugin_Feed_End'] as $fpname => &$fpsignal) {
    $fpname();
}

RunTime();
