<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-05-18
 */
require 'zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->option['ZC_API_ENABLE']) {
    die;
}

$mod = GetVars('mod', 'GET');
$mod = str_replace(array('\\','/','.'), '', $mod);
$act = GetVars('act', 'GET');

if (file_exists($zbp->path . 'zb_system/api/' . $mod . '.php')) {
    include $zbp->path . 'zb_system/api/' . $mod . '.php';
    call_user_func('api_' . $mod . '_' . $act);
}

die;
