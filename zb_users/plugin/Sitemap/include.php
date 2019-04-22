<?php

//注册插件
RegisterPlugin("Sitemap", "ActivePlugin_Sitemap");

function ActivePlugin_Sitemap()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_SettingMng_SubMenu', 'Sitemap_AddMenu');
}

function Sitemap_AddMenu()
{
    global $zbp;
    echo '<a href="' . $zbp->host . 'zb_users/plugin/Sitemap/main.php"><span class="m-left">SiteMap生成器</span></a>';
}
