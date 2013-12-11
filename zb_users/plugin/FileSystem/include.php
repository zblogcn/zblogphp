<?php


#注册插件
RegisterPlugin("FileSystem","ActivePlugin_FileSystem");


function ActivePlugin_FileSystem() {
Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','FileSystem_AddMenu');

}

function InstallPlugin_FileSystem(){}
function UninstallPlugin_FileSystem(){}

function FileSystem_AddMenu(&$m){
	global $zbp;
	$m[]=MakeLeftMenu("root","文件管理",$zbp->host . "zb_users/plugin/FileSystem/main.php","nav_FileSystem","aFileSystem",$zbp->host . "zb_system/image/common/file_1.png");	
}

?>