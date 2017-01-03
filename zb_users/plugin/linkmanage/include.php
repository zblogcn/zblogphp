<?php

include_once $zbp->usersdir . 'plugin/linkmanage/function.php';
#注册插件
RegisterPlugin("linkmanage", "ActivePlugin_linkmanage");

$sysMenu = 'navbar|link|favorite|misc|Menu|Location|Nav|Version';

function ActivePlugin_linkmanage() {
	Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'linkmanage_TopMenu');
}

function linkmanage_TopMenu(&$m) {
	global $zbp;
	array_unshift($m, MakeTopMenu("root", '菜单链接管理', $zbp->host . "zb_users/plugin/linkmanage/main.php", "", "topmenu_linkmanage"));
}


function InstallPlugin_linkmanage() {
	global $zbp;
	if (!$zbp->Config('linkmanage')->HasKey('Version')) {
		$zbp->Config('linkmanage')->Version = '0.2';
		$zbp->Config('linkmanage')->Menus = '{"num":4,"data":{"navbar":{"id":"navbar","name":"导航栏","location":""},"link":{"id":"link","name":"友情链接","location":""},"favorite":{"id":"favorite","name":"网站收藏","location":""},"misc":{"id":"misc","name":"图标汇集","location":""}}}';
		//$zbp->Config('linkmanage')->Menu = '{}'; //菜单集{[{"id":"123456","title":"导航栏","url":"","newtable":"true","img":"","type":""}]}
		//$zbp->Config('linkmanage')->Location = '{}';

		//$zbp->Config('linkmanage')->navbar = '{}';
		//$zbp->Config('linkmanage')->link = '{}';
		//$zbp->Config('linkmanage')->favorite = '{}';
		//$zbp->Config('linkmanage')->misc = '{}';
		$zbp->SaveConfig('linkmanage');
	}
}

function UninstallPlugin_linkmanage() {}