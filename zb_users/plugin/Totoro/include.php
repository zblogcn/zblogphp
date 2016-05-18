<?php
/*  TODO:
 *  原有配置不考虑进行转移或升级
 *  1. 提取IP和网址
 */
RegisterPlugin("Totoro", "ActivePlugin_Totoro");
define('TOTORO_PATH', dirname(__FILE__));
define('TOTORO_INCPATH', TOTORO_PATH . '/inc/');

function Totoro_init() {
	require TOTORO_PATH . '/inc/totoro.php';
	global $Totoro;
	$Totoro = new Totoro_Class;
}

function ActivePlugin_Totoro() {
	Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu', 'Totoro_Admin_CommentMng_SubMenu');
	Add_Filter_Plugin('Filter_Plugin_PostComment_Core', 'Totoro_PostComment_Core');
	Add_Filter_Plugin('Filter_Plugin_Cmd_Begin', 'Totoro_Cmd_Begin');
	Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'Totoro_Html_Js_Add');	
}

function InstallPlugin_Totoro() {
}

function Totoro_Admin_CommentMng_SubMenu() {
	global $zbp;
	echo '<a href="' . $zbp->host . 'zb_users/plugin/Totoro/main.php"><span class="m-right">Totoro设置</span></a>';
	echo '<script src="' . $zbp->host . 'zb_users/plugin/Totoro/submenu.js"></script>';
}

function Totoro_Html_Js_Add() {
	global $zbp;
	echo '$(function () { if($("#inpId").size()==1){';
	echo 'jQuery.getScript("' . $zbp->host . 'zb_users/plugin/Totoro/add_token.php?id="+$("#inpId").val());';
	echo '}});';
}

function Totoro_PostComment_Core(&$comment) {
	global $zbp;
	
	//add Check Totoro_Token
	Totoro_CheckToken($comment->LogID,GetVars('totoro_token','GET'));
	
	Totoro_init();
	global $Totoro;
	$Totoro->check_comment($comment);
	if (!$comment->IsChecking && !$comment->IsThrow) {
		$Totoro->replace_comment($comment);
	}
}

function Totoro_Cmd_Begin() {
	global $zbp;

	if (GetVars('act', 'GET') == 'CommentChk') {
		if (!$zbp->ValidToken(GetVars('token', 'GET'))) {$zbp->ShowError(5, __FILE__, __LINE__);die();}
		$id = (int) GetVars('id', 'GET');
		$ischecking = (bool) GetVars('ischecking', 'GET');
		if ($ischecking) {
			Totoro_init();
			global $Totoro;
			$Totoro->add_black_list($id);
		}
	}
}

function Totoro_GetTokenbyID($id){
	global $zbp;
	$article = new Post();
	$article->LoadInfoByID($id);
	return md5($zbp->guid . date('Ymd') . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] . $article->ID . $article->CommNums);
}

function Totoro_CheckToken($id,$token){
	global $zbp;
	$article = new Post();
	$article->LoadInfoByID($id);
	$token_real=md5($zbp->guid . date('Ymd') . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] . $article->ID . $article->CommNums);
	if($token!==$token_real){
		$zbp->ShowError("token已更新，请重新刷新此页面！", __FILE__, __LINE__);die();
	}
}