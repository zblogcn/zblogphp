<?php


#注册插件
RegisterPlugin("Howl","ActivePlugin_Howl");


function ActivePlugin_Howl() {

	Add_Filter_Plugin('Filter_Plugin_CheckRights_Begin','Howl_CheckRights');

}

function InstallPlugin_Howl(){
	global $zbp;
	$zbp->Config('Howl')->version='1.0';
	$zbp->SaveConfig('Howl');
}

function UninstallPlugin_Howl(){
	global $zbp;
	$zbp->DelConfig('Howl');
}


function Howl_CheckRights(&$action){
	global $zbp;

	#$GLOBALS['Filter_Plugin_CheckRights_Begin']['Howl_CheckRights']=PLUGIN_EXITSIGNAL_RETURN;

	#return true;
}



?>