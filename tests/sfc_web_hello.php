<?php
define('ZBP_HOOKERROR', false);
define('ZBP_OBSTART', false);
#$_ENV['ZBP_PRESET_HOST'] = 'https://localhost';
$_ENV['ZBP_PRESET_PLUGINS'] = 'ViewIndex';
require_once __DIR__ . '/zblog/zb_system/function/c_system_base.php';
$gl = 1;

$zbp = \ZBlogPHP::GetInstance();
$zbp->Load();
$zbp->option['ZC_API_ENABLE'] = true;

ViewAuto();
