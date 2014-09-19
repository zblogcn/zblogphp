<?php

#注册插件
RegisterPlugin("KODExplorer","ActivePlugin_KODExplorer");

function ActivePlugin_KODExplorer() {
	Add_Filter_Plugin('Filter_Plugin_Admin_ThemeMng_SubMenu', 'KODExplorer_Admin_ThemeMng_SubMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','KODExplorer_Admin_LeftMenu');

}

function KODExplorer_Admin_LeftMenu(&$m){
	global $zbp;
	$m[] = MakeLeftMenu("root", "文件管理", $zbp->host . "zb_users/plugin/KODExplorer/main.php", "nav_KODExplorer", "aKODExplorer",$zbp->host . "zb_system/image/common/file_1.png");	
}

function KODExplorer_Admin_ThemeMng_SubMenu()
{
	global $zbp; global $usersdir; global $blogtheme;
	echo '<a href="'. $zbp->host .'zb_users/plugin/KODExplorer/main.php?explorer&path=';
	echo urlencode($usersdir . 'theme/' . $blogtheme . '/template/');
	echo '"><span class="m-left">在线编辑主题</span></a>';
}


function InstallPlugin_KODExplorer() {

}

function UninstallPlugin_KODExplorer() {

}

?>