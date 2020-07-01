<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-05-18
 */
define('ZBP_IN_API', true);
require 'zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->option['ZC_API_ENABLE']) {
    ApiResponse(array(
        'message' => 'API is not available!'
    ));
}

$mods = array();
foreach (GetFilesInDir($zbp->path . 'zb_system/api/', 'php') as $sortname => $fullname) {
    $mods[$sortname] = $fullname;
}

foreach ($GLOBALS['hooks']['Filter_Plugin_API_Mod'] as $fpname => &$fpsignal) {
    $fpname($mods);
}

$mod = GetVars('mod', 'GET');
$mod = str_replace(array('\\','/','.'), '', $mod);
$act = GetVars('act', 'GET');

if (isset($mods[$mod]) && file_exists($mod_file = $mods[$mod])) {
    include $mod_file;
    ApiResponse(call_user_func('api_' . $mod . '_' . $act));
}

die;
