<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

#接口模式复制自Z-Blog ASP版

define('PLUGIN_EXITSIGNAL_NONE', '');
define('PLUGIN_EXITSIGNAL_RETURN', 'return');
define('PLUGIN_EXITSIGNAL_BREAK', 'break');


#定义总插件激活函数列表
$plguins=array();



/*
'*********************************************************
' 目的： 注册插件函数，由每个插件主动调用
'*********************************************************
*/
function RegisterPlugin($strPluginName,$strPluginActiveFunction){

	$GLOBALS['plguins'][$strPluginName]=$strPluginActiveFunction;

}







/*
'*********************************************************
' 目的： 激活插件函数
'*********************************************************
*/
function ActivePlugin(){

	foreach ($GLOBALS['plguins'] as &$sPluginActiveFunctions) {
		$sPluginActiveFunctions();
	}

}




/*
'*********************************************************
' 目的： 安装插件函数，只运行一次
'*********************************************************
*/
function InstallPlugin($strPluginName){

	if(function_exists('InstallPlugin_' . $strPluginName)==true){
		$f='InstallPlugin_' . $strPluginName;
		$f();
	}

}




/*
'*********************************************************
' 目的： 删除插件函数，只运行一次
'*********************************************************
*/
function UninstallPlugin($strPluginName){

	if(function_exists('UninstallPlugin_' . $strPluginName)==true){
		$f='UninstallPlugin_' . $strPluginName;
		$f();
	}

}




/*
'*********************************************************
' 目的： 检测插件是否已激活
'*********************************************************
*/
function CheckPluginState($strPluginName){
	return false;
}





/*
'*********************************************************
' 目的：挂上Action接口
' 参数：'plugname:接口名称
		'actioncode:要执行的语句，要转义为Execute可执行语句
'*********************************************************
*/
function Add_Action_Plugin($plugname,$actioncode){
	$GLOBALS[$plugname][]=$actioncode;
}




/*
'*********************************************************
' 目的：挂上Filter接口
' 参数：'plugname:接口名称
		'functionname:要挂接的函数名
		'exitsignal:return,break,continue
'*********************************************************
*/
function Add_Filter_Plugin($plugname,$functionname,$exitsignal=''){
	$GLOBALS[$plugname][$functionname]=$exitsignal;
}




/*
'*********************************************************
' 目的：挂上Response接口
' 参数：'plugname:接口名称
		'parameter:要写入的内容
'*********************************************************
*/
Function Add_Response_Plugin($plugname,$functionname){
	$GLOBALS[$plugname][]=$functionname;
}







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Begin
'参数:
'说明:定义列表输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewList_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Begin
'参数:
'说明:定义index.php接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Index_Begin=array();

?>