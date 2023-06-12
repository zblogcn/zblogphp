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

ApiCheckEnable();

HookFilterPlugin('Filter_Plugin_API_Begin');

ApiCheckAuth(false, 'api');

ApiCheckLimit();

$mods = &$GLOBALS['api_public_mods'];
$mods_allow = &$GLOBALS['api_allow_mods_rule']; //格式为 array( array('模块名'=>'方法名') )
$mods_disallow = &$GLOBALS['api_disallow_mods_rule']; //如果是 array( array('模块名'=>'') )方法名为空将匹配整个模块
$mod = strtolower(GetVars('mod', 'GET'));
$act = strtolower(GetVars('act', 'GET'));

// 载入系统和应用的 mod
ApiLoadMods();

//进行Api白名单和黑名单的设置并检查$mod和$act
ApiCheckMods();

ApiLoadPostData();

ApiVerifyCSRF();

// 派发 API
ApiDispatch($mods, $mod, $act);
