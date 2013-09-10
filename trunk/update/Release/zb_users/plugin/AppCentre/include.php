<?php
#注册插件
RegisterPlugin("AppCentre","ActivePlugin_AppCentre");

define('APPCENTRE_URL','http://app.rainbowsoft.org/client/');

define('APPCENTRE_SYSTEM_UPDATE','http://update.rainbowsoft.org/zblogphp/');

function ActivePlugin_AppCentre() {

	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','AppCentre_AddMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_ThemeMng_SubMenu','AppCentre_AddThemeMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_PluginMng_SubMenu','AppCentre_AddPluginMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_SiteInfo_SubMenu','AppCentre_AddSiteInfoMenu');
}

function InstallPlugin_AppCentre(){
	global $zbp;
	$zbp->Config('AppCentre')->enabledcheck=1;
	$zbp->Config('AppCentre')->checkbeta=0;
	$zbp->Config('AppCentre')->enabledevelop=0;
	$zbp->SaveConfig('AppCentre');
}


function AppCentre_AddMenu(&$m){
	global $zbp;
	$m[]=MakeLeftMenu("root","应用中心",$zbp->host . "zb_users/plugin/AppCentre/main.php","nav_AppCentre","aAppCentre",$zbp->host . "zb_users/plugin/AppCentre/images/Cube1.png");	
}

function AppCentre_AddSiteInfoMenu(){
	global $zbp;
	if($zbp->Config('AppCentre')->enabledcheck){
		echo "<script type='text/javascript'>$(document).ready(function(){  $.getScript('{$zbp->host}zb_users/plugin/AppCentre/main.php?method=checksilent&rnd='); });</script>";
	}
}

function AppCentre_AddThemeMenu(){
	global $zbp;
	echo "<script type='text/javascript'>var app_enabledevelop=".(int)$zbp->Config('AppCentre')->enabledevelop.";</script>";
	echo "<script type='text/javascript'>var app_username='".$zbp->Config('AppCentre')->username."';</script>";
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/theme.js' type='text/javascript'></script>";
}

function AppCentre_AddPluginMenu(){
	global $zbp;
	echo "<script type='text/javascript'>var app_enabledevelop=".(int)$zbp->Config('AppCentre')->enabledevelop.";</script>";
	echo "<script type='text/javascript'>var app_username='".$zbp->Config('AppCentre')->username."';</script>";
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/plugin.js' type='text/javascript'></script>";
}
?>