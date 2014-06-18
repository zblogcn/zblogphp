<?php
#注册插件
RegisterPlugin("AppCentre","ActivePlugin_AppCentre");

define('APPCENTRE_URL','http://app.zblogcn.com/client/');
define('APPCENTRE_SYSTEM_UPDATE','http://update.zblogcn.com/zblogphp/');

define('APPCENTRE_API_URL','http://app.zblogcn.com/api/index.php?api=');
define('APPCENTRE_API_APP_ISBUY','isbuy');
define('APPCENTRE_API_USER_INFO','userinfo');
define('APPCENTRE_API_ORDER_LIST','orderlist');
define('APPCENTRE_API_ORDER_DETAIL','orderdetail');


function ActivePlugin_AppCentre() {

	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','AppCentre_AddMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_ThemeMng_SubMenu','AppCentre_AddThemeMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_PluginMng_SubMenu','AppCentre_AddPluginMenu');
	Add_Filter_Plugin('Filter_Plugin_Admin_SiteInfo_SubMenu','AppCentre_AddSiteInfoMenu');
}

function InstallPlugin_AppCentre(){
	global $zbp;
	$zbp->Config('AppCentre')->enabledcheck=1;
	$zbp->Config('AppCentre')->checkbeta=0;
	$zbp->Config('AppCentre')->enabledevelop=0;
	$zbp->SaveConfig('AppCentre');
}


function AppCentre_AddMenu(&$m){
	global $zbp;
	$m['nav_AppCentre']=MakeLeftMenu("root","应用中心",$zbp->host . "zb_users/plugin/AppCentre/main.php","nav_AppCentre","aAppCentre",$zbp->host . "zb_users/plugin/AppCentre/images/Cube1.png");
}

function AppCentre_AddSiteInfoMenu(){
	global $zbp;
	if($zbp->Config('AppCentre')->enabledcheck){
		$last=(int)$zbp->Config('AppCentre')->lastchecktime;
		if( (time()-$last) > 11*60*60 ){
			echo "<script type='text/javascript'>$(document).ready(function(){  $.getScript('{$zbp->host}zb_users/plugin/AppCentre/main.php?method=checksilent&rnd='); });</script>";
			$zbp->Config('AppCentre')->lastchecktime=time();
			$zbp->SaveConfig('AppCentre');
		}
	}
}

function AppCentre_AddThemeMenu(){
	global $zbp;
	echo "<script type='text/javascript'>var app_enabledevelop=".(int)$zbp->Config('AppCentre')->enabledevelop.";</script>";
	echo "<script type='text/javascript'>var app_username='".$zbp->Config('AppCentre')->username."';</script>";
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/theme.js' type='text/javascript'></script>";
}

function AppCentre_AddPluginMenu(){
	global $zbp;
	echo "<script type='text/javascript'>var app_enabledevelop=".(int)$zbp->Config('AppCentre')->enabledevelop.";</script>";
	echo "<script type='text/javascript'>var app_username='".$zbp->Config('AppCentre')->username."';</script>";
	echo "<script src='{$zbp->host}zb_users/plugin/AppCentre/plugin.js' type='text/javascript'></script>";
}


//$appid是App在应用中心的发布后的文章ID数字号，非App的ID名称。
function AppCentre_App_Check_ISBUY($appid){
	global $zbp;
	$postdate = array(
		'email'=>$zbp->Config('AppCentre')->shop_username,
		'password'=>$zbp->Config('AppCentre')->shop_password,
		'appid'=>$appid,
	    );
	$http_post = Network::Create();
	$http_post->open('POST',APPCENTRE_API_URL.APPCENTRE_API_APP_ISBUY);
	$http_post->setRequestHeader('Referer',substr($zbp->host,0,-1) . $zbp->currenturl);

	$http_post->send($postdate);
	$result = json_decode($http_post->responseText,true);
	return $result;
}