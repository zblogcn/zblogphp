<?php
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
#$_ENV['ZBP_PRESET_HOST'] = 'https://localhost';
$_ENV['ZBP_PRESET_PLUGINS'] = 'ViewIndex';
require_once __DIR__ . '/zblog/zb_system/function/c_system_base.php';
$gl = 1;

function main_handler($event, $context) {
    global $gl;
    print "good";
    print " job ";
    print $gl;
    print "\n";
    $gl += 1;
    error_log( "Hello, errors!" );
    var_dump($event);
    var_dump($context);
    $zbp = \ZBlogPHP::GetInstance();
    var_dump($zbp->host);
    var_dump($zbp->path);
    var_dump(IS_SCF);
    return "hello world";
}
