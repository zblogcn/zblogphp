<?php

//注册插件
define('TEMPLATE_/*TEMPLATE_NAME*/_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
RegisterPlugin('/*TEMPLATE_NAME*/', 'ActivePlugin_/*TEMPLATE_NAME*/');

function ActivePlugin_/*TEMPLATE_NAME*/()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', '/*TEMPLATE_NAME*/_AddMenu');
}

function /*TEMPLATE_NAME*/_AddMenu(&$menus)
{
    global $zbp;
    $menus[] = MakeTopMenu('root', '主题配置', $zbp->host . 'zb_users/theme//*TEMPLATE_NAME*//main.php', '', 'topmenu_/*TEMPLATE_NAME*/');
}

function InstallPlugin_/*TEMPLATE_NAME*/()
{
}

function UninstallPlugin_/*TEMPLATE_NAME*/()
{
}

function /*TEMPLATE_NAME*/_Require($config_name)
{
    global $blogpath;
    $file_name = explode('/', $config_name);
    include $blogpath . 'zb_users/theme//*TEMPLATE_NAME*//include/' . end($file_name);
}

function /*TEMPLATE_NAME*/_Url($config_name)
{
    global $bloghost;
    $file_array = explode('/', $config_name);

    return $bloghost . 'zb_users/theme//*TEMPLATE_NAME*//include/' . end($file_array); //safe
}
