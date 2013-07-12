<?php
#ZBP的第一个插件，主题插件


#注册插件
RegisterPlugin("Default","ActivePlugin_Default");


function ActivePlugin_Default() {

	Add_Filter_Plugin('Filter_Plugin_ListExport_Begin','Default_ListExport_Begin');

}





function Default_ListExport_Begin(&$page,&$cate,&$auth,&$date,&$tags){
	$page='4444';
}

?>