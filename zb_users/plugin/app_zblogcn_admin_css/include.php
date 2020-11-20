<?php
#注册插件
RegisterPlugin("app_zblogcn_admin_css","ActivePlugin_app_zblogcn_admin_css");

function ActivePlugin_app_zblogcn_admin_css()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'app_zblogcn_admin_css');
    Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'app_zblogcn_admin_css_logo');
}

function InstallPlugin_app_zblogcn_admin_css()
{
}

function app_zblogcn_admin_css()
{
    global $zbp;

    if (stripos($zbp->currenturl, 'phpinfo') !== false) {
        return;
    }
    echo '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_users/plugin/app_zblogcn_admin_css/admin.css"/>' . "\r\n";
}

function app_zblogcn_admin_css_logo(&$m)
{
    global $zbp;
    array_unshift($m, '<div class="left-logo"><img src="'.$zbp->host.'zb_system/image/admin/logo_white.svg"/></div>');
}

function UninstallPlugin_app_zblogcn_admin_css()
{
}
