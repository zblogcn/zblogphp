<?php
#ZBP的第一个插件，主题插件


#注册插件
RegisterPlugin("Default","ActivePlugin_Default");


function ActivePlugin_Default() {

	Add_Filter_Plugin('Filter_Plugin_ViewList_Begin','Default_ViewList_Begin');

}





function Default_ViewList_Begin(&$page,&$cate,&$auth,&$date,&$tags){
	$page='4444';
	echo 'xxxxx3423';
	$GLOBALS['Filter_Plugin_ViewList_Begin']['Default_ViewList_Begin']=PLUGIN_EXITSIGNAL_NONE;
	return null;
}

?>