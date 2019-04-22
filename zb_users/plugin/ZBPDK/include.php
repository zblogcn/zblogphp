<?php

RegisterPlugin("ZBPDK", "ActivePlugin_ZBPDK");

function ActivePlugin_ZBPDK()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'ZBPDK_AddMenu');
}
function InstallPlugin_ZBPDK()
{
}
function UninstallPlugin_ZBPDK()
{
}

function ZBPDK_AddMenu(&$topmenus)
{
    global $zbp;
    $topmenus[] = MakeTopMenu('admin', '开发工具', $zbp->host . "zb_users/plugin/ZBPDK/main.php", "", "zbpdk");
}
