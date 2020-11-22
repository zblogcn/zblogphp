<?php
#注册插件
RegisterPlugin("app_zblogcn_admin_css","ActivePlugin_app_zblogcn_admin_css");

function ActivePlugin_app_zblogcn_admin_css()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'app_zblogcn_admin_css');
    Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'app_zblogcn_admin_css_left_menu');
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

function app_zblogcn_admin_css_left_menu(&$m)
{
    global $zbp;

    // logo
    array_unshift($m, '<div class="left-logo"><img src="'.$zbp->host.'zb_system/image/admin/logo_white.svg"/></div>');

    // change icons for app.zblogcn.com
    if ($zbp->theme == 'appcentre_server') {
        $m['nav_new'] = str_replace('icon-pencil-square-fill', 'icon-cloud-upload-fill', $m['nav_new']);
        $m['nav_article'] = str_replace('icon-stickies', 'icon-collection-fill', $m['nav_article']);
    }
}

function UninstallPlugin_app_zblogcn_admin_css()
{
}
