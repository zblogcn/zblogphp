<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 */
require '../function/c_system_base.php';
$zbp->Load();
ob_clean();

$zbp->ShowValidCode(GetVars('id', 'GET'));
