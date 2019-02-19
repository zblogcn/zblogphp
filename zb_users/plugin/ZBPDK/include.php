<?php

require 'zbpdk_include.php';
$zbpdk = new zbpdk_t();
$zbpdk->scan_extensions();

RegisterPlugin("ZBPDK", "ActivePlugin_ZBPDK");

function ActivePlugin_ZBPDK()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'ZBPDK_AddMenu');
    ExtFunc_ZBPDK('ActivePlugin_');
}

function InstallPlugin_ZBPDK()
{
    ExtFunc_ZBPDK('InstallPlugin_');
}

function UninstallPlugin_ZBPDK()
{
    ExtFunc_ZBPDK('UninstallPlugin_');
}

function ZBPDK_AddMenu(&$topmenus)
{
    global $zbp;
    $topmenus[] = MakeTopMenu('admin', '开发工具', $zbp->host . "zb_users/plugin/ZBPDK/main.php", "", "zbpdk");
}

/**
 * 执行子扩展函数.
 *
 * @param string $preifx 函数前缀
 */
function ExtFunc_ZBPDK($prefix)
{
    global $zbpdk;
    foreach ($zbpdk->objects as $ext) {
        $func = $prefix . $ext->id;
        if (function_exists($func)) {
            $func();
        }
    }
}
