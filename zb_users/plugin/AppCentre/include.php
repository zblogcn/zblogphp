<?php
#注册插件
RegisterPlugin("AppCentre","ActivePlugin_AppCentre");

define('APPCENTRE_URL','http://192.168.1.54/client/');

define('APPCENTRE_SYSTEM_UPDATE','http://update.rainbowsoft.org/zblogphp/');

function ActivePlugin_AppCentre() {

	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','AppCentre_AddMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_ThemeMng_SubMenu','AppCentre_AddThemeMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_PluginMng_SubMenu','AppCentre_AddPluginMenu');
}

function InstallPlugin_AppCentre(){
	global $zbp;

	$zbp->SaveConfig('AppCentre');
}


function AppCentre_AddMenu(&$m){
	global $zbp;
	$m[]=MakeLeftMenu("root","应用中心",$zbp->host . "zb_users/plugin/AppCentre/main.php","nav_AppCentre","aAppCentre",$zbp->host . "zb_users/plugin/AppCentre/images/Cube1.png");	
}

function AppCentre_AddThemeMenu(){
	global $zbp;
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/theme.js' type='text/javascript'></script>";
}

function AppCentre_AddPluginMenu(){
	global $zbp;
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/plugin.js' type='text/javascript'></script>";
}
?>