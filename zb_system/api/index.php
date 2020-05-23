<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-05-18
 */
require '../function/c_system_base.php';

$zbp->Load();

$input = file_get_contents('php://input');

foreach ($GLOBALS['hooks']['Filter_Plugin_API_Begin'] as $fpname => &$fpsignal) {
    $fpreturn = $fpname();
}
