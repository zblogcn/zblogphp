<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

#接口模式复制自Z-Blog ASP版


#定义总插件激活函数列表
$Plugins=array();




/*
'*********************************************************
' 目的： 注册插件函数，由每个插件主动调用
'*********************************************************
*/
function RegisterPlugin($strPluginName,$strPluginActiveFunction){

	$GLOBALS['Plugins'][$strPluginName]=$strPluginActiveFunction;

}







/*
'*********************************************************
' 目的： 激活插件函数
'*********************************************************
*/
function ActivePlugin(){

	foreach ($GLOBALS['Plugins'] as &$sPluginActiveFunctions) {
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
	array_push($GLOBALS[$plugname],$actioncode);
}




/*
'*********************************************************
' 目的：挂上Filter接口
' 参数：'plugname:接口名称
		'functionname:要挂接的函数名
'*********************************************************
*/
function Add_Filter_Plugin($plugname,$functionname){
	array_push($GLOBALS[$plugname],$functionname);
}




/*
'*********************************************************
' 目的：挂上Response接口
' 参数：'plugname:接口名称
		'parameter:要写入的内容
'*********************************************************
*/
Function Add_Response_Plugin($plugname,$parameter){
	/*
	On Error Resume Next
	Call Execute(plugname & "=" & plugname & "&""" & Replace(Replace(Replace(Replace(parameter,"""",""""""),vbCrlf,"""&vbCrlf&"""),vbLf,"""&vbLf&"""),vbCr,"""&vbCr&""") & """")
	Err.Clear
	*/
}







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ListExport_Begin
'参数:
'说明:定义列表输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_ListExport_Begin=array();







?>