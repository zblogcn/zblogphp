<?php

$_SERVER["QUERY_STRING"] = "";
$_SERVER["HTTP_HOST"] = "http://localhost";
$_SERVER['SERVER_SOFTWARE'] = "IIS";
define('ZBP_HOOKERROR', false);

function commandmock_loadzbp()
{
    include dirname(__FILE__) . '/../zb_system/function/c_system_base.php';
    $GLOBALS['zbp']->Load();

    set_error_handler(create_function('', 'return false;'));
    set_exception_handler(create_function('', 'return false;'));
    register_shutdown_function(create_function('', 'return false;'));
}
