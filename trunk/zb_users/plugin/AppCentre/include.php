<?php
#注册插件
RegisterPlugin("AppCentre","ActivePlugin_AppCentre");

define('APPCENTRE_URL','http://192.168.1.54/client/');

function ActivePlugin_AppCentre() {

	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','AppCentre_AddMenu');
	//Add_Filter_Plugin('Filter_Plugin_PostComment_Core','Totoro_Core');
}

function InstallPlugin_AppCentre(){
	global $zbp;

	$zbp->SaveConfig('AppCentre');
}


function AppCentre_AddMenu(&$m){
	global $zbp;
	//echo '<a href="'. $zbp->host .'zb_users/plugin/Totoro/main.php"><span class="m-left">Totoro设置</span></a>';

	$m[]=MakeLeftMenu("root","应用中心",$zbp->host . "zb_users/plugin/AppCentre/main.php","nav_AppCentre","aAppCentre",$zbp->host . "zb_users/plugin/AppCentre/images/Cube1.png");	
	
}





?>