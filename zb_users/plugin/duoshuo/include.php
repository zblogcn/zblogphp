<?php


#注册插件
RegisterPlugin("duoshuo","ActivePlugin_duoshuo");


function ActivePlugin_duoshuo() {

Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','duoshuo_addjs');

}

function InstallPlugin_duoshuo(){

}

function UninstallPlugin_duoshuo(){

}

function duoshuo_addjs(&$template){
	global $zbp;

	$s=$zbp->Config('duoshuo')->commoncode;

	$template->SetTags('socialcomment',$s);

}


?>