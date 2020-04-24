<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 * @version
 */
require 'zb_system/function/c_system_base.php';

$zbp->Load();
$zbp->action = 'search';

foreach ($GLOBALS['hooks']['Filter_Plugin_Search_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

ViewIndex();

foreach ($GLOBALS['hooks']['Filter_Plugin_Search_End'] as $fpname => &$fpsignal) {
    $fpname();
}
