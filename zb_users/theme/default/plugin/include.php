<?php
#ZBP的第一个插件，主题插件


#注册插件
RegisterPlugin("Default","ActivePlugin_Default");


function ActivePlugin_Default() {

	Add_Filter_Plugin('Filter_Plugin_ViewList_Begin','Default_ViewList_Begin');

}





function Default_ViewList_Begin(&$page,&$cate,&$auth,&$date,&$tags){
	$GLOBALS['Filter_Plugin_ViewList_Begin']['Default_ViewList_Begin']=PLUGIN_EXITSIGNAL_RETURN;
	global $zbp;

	$zbp->title='首页';
	$html=null;

	$html=$zbp->templatetags['TEMPLATE_DEFAULT'];

foreach ($zbp->templatetags as $key => $value) {
	$html=str_ireplace('<#' . $key . '#>', $value, $html);
}

	echo $html;

}

?>