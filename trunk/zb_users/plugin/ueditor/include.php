<?php
#ZBP的第一个插件，ueditor插件


#注册插件
RegisterPlugin("ueditor","ActivePlugin_ueditor");


function ActivePlugin_ueditor() {

	Add_Filter_Plugin('Filter_Plugin_Edit_Begin','ueditor_addscript');

}





function ueditor_addscript(){
	global $zbp;
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/ueditor/ueditor.config.asp"></script>';
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/ueditor/ueditor.all.min.js"></script>';
}
?>