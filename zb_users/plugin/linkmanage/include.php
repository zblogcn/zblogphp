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
	array_unshift($m, MakeTopMenu("root", '链接管理', $zbp->host . "zb_users/plugin/linkmanage/main.php", "", "topmenu_linkmanage"));
}

function linkmanage_RegLocation($themeid, $local) {
	global $zbp;
	$location = json_decode($zbp->Config('linkmanage')->Location, true);
	foreach ($local as $key => $value) {
		$location[$themeid . '_' . $key] = array($themeid, $themeid . '_' . $key, $value);
	}
	$zbp->Config('linkmanage')->Location = json_encode($location);
	$zbp->SaveConfig('linkmanage');
}

function linkmanage_SetMenuLocation($id, $location) {
	global $zbp;
	$n = json_decode($zbp->Config('linkmanage')->Nav, true);
	foreach ($n['data'] as $key => $value) {
		if ($value['id'] == $id) {
			$n['data'][$key]['location'] = $location;
		} else {
			continue;
		}
	}
	$zbp->Config('linkmanage')->Nav = json_encode($n);

	$zbp->SaveConfig('linkmanage');
	return $Navs['data'];
}

function InstallPlugin_linkmanage() {
	global $zbp;
	if (!$zbp->Config('linkmanage')->HasKey('Version')) {
		$zbp->Config('linkmanage')->Version = '0.2';
		$zbp->Config('linkmanage')->Nav = '{"num":4,"data":{"navbar":{"id":"navbar","name":"导航栏","location":""},"link":{"id":"link","name":"友情链接","location":""},"favorite":{"id":"favorite","name":"网站收藏","location":""},"misc":{"id":"misc","name":"图标汇集","location":""}}}';
		$zbp->Config('linkmanage')->Menu = '{}'; //菜单集{[{"id":"123456","title":"导航栏","url":"","newtable":"true","img":"","type":""}]}
		$zbp->Config('linkmanage')->Location = '{}';

		$zbp->Config('linkmanage')->navbar = '{}';
		$zbp->Config('linkmanage')->link = '{}';
		$zbp->Config('linkmanage')->favorite = '{}';
		$zbp->Config('linkmanage')->misc = '{}';
		$zbp->SaveConfig('linkmanage');
	}
	$zbp->SaveConfig('linkmanage');
}
function UninstallPlugin_linkmanage() {}