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
$a=array();
$a[1]=array();
$a[2]=array();
$a[3]=array();
$a[4]=array();
$a[5]=array();
$a[6]=array();

if($zbp->Config('Howl')->HasKey('Group1')){$a[1]=$zbp->Config('Howl')->Group1;}
if($zbp->Config('Howl')->HasKey('Group2')){$a[2]=$zbp->Config('Howl')->Group2;}
if($zbp->Config('Howl')->HasKey('Group3')){$a[3]=$zbp->Config('Howl')->Group3;}	
if($zbp->Config('Howl')->HasKey('Group4')){$a[4]=$zbp->Config('Howl')->Group4;}
if($zbp->Config('Howl')->HasKey('Group5')){$a[5]=$zbp->Config('Howl')->Group5;}
if($zbp->Config('Howl')->HasKey('Group6')){$a[6]=$zbp->Config('Howl')->Group6;}


$g=$zbp->user->Level;
if(array_key_exists($action, $a[$g])){
	$GLOBALS['Filter_Plugin_CheckRights_Begin']['Howl_CheckRights']=PLUGIN_EXITSIGNAL_RETURN;
	return (boolean)$a[$g][$action];
}

	#$GLOBALS['Filter_Plugin_CheckRights_Begin']['Howl_CheckRights']=PLUGIN_EXITSIGNAL_RETURN;

	#return true;
}



?>