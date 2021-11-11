<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-01
 */

// 标记为 API 运行模式
define('ZBP_IN_API', true);

require 'function/c_system_base.php';

$zbp->Load();

if (!$GLOBALS['option']['ZC_API_ENABLE']) {
    ApiResponse(null, null, 503, $GLOBALS['lang']['error']['95']);
}

foreach ($GLOBALS['hooks']['Filter_Plugin_API_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

ApiCheckAuth(false, 'api');

ApiCheckLimit();

$mods = array();
$mods_allow = array(); //格式为 array( array('模块名'=>'方法名') )
$mods_disallow = array(); //如果是 array( array('模块名'=>'') )方法名为空将匹配整个模块
$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

// 载入系统和应用的 mod
ApiLoadMods($mods);

//进行Api白名单和黑名单的检查
ApiCheckMods($mods_allow, $mods_disallow);

ApiLoadPostData();

ApiVerifyCSRF();

// 派发 API
ApiDispatch($mods, $mod, $act);
