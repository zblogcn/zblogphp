<?php

//注册插件
RegisterPlugin("STACentre", "ActivePlugin_STACentre");

function ActivePlugin_STACentre()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_SettingMng_SubMenu', 'STACentre_AddMenu');
}

function STACentre_AddMenu()
{
    global $zbp;
    echo '<a href="' . $zbp->host . 'zb_users/plugin/STACentre/main.php"><span class="m-left">静态化管理中心</span></a>';
}
