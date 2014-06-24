<?php
#注册插件
RegisterPlugin("AdminColor","ActivePlugin_AdminColor");

$Filter_Plugin_AdminColor_CSS_Pre=array();

function ActivePlugin_AdminColor() {
	Add_Filter_Plugin('Filter_Plugin_Admin_SiteInfo_SubMenu','AdminColor_ColorButton');
	Add_Filter_Plugin('Filter_Plugin_Admin_Header','AdminColor_Css');
	Add_Filter_Plugin('Filter_Plugin_Login_Header','AdminColor_Css');
	Add_Filter_Plugin('Filter_Plugin_Other_Header','AdminColor_Css');
}

function AdminColor_Css(){
	global $zbp;
	echo '<link rel="stylesheet" type="text/css" href="'. $zbp->host .'zb_users/plugin/AdminColor/css.php"/>' . "\r\n";
}

function AdminColor_ColorButton(){
	global $zbp;
	
	$s='';

	for ($i=0; $i < 9; $i++) { 
		$s.="&nbsp;&nbsp;<a href='". $zbp->host ."zb_users/plugin/AdminColor/css.php?setcolor=".$i."'><span style='height:16px;width:16px;background:".$GLOBALS['AdminColor_NormalColor'][$i]."'><img src='". $zbp->host ."zb_system/image/admin/none.gif' width='16' height='16' alt='' /></span></a>&nbsp;&nbsp;";
	}

	$s.="&nbsp;&nbsp;<a href='". $zbp->host ."zb_users/plugin/AdminColor/css.php?setcolor=10' title='文艺范'><span style='height:16px;width:16px;background:#eee;'><img src='". $zbp->host ."zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

	echo "<script type='text/javascript'>$('.divHeader').append(\"<div id='admin_color'>". $s ."</div>\");</script>";
}



$AdminColor_BlodColor[0]='#1d4c7d';
$AdminColor_NormalColor[0]='#3a6ea5';
$AdminColor_LightColor[0]='#b0cdee';
$AdminColor_HighColor[0]='#3399cc';
$AdminColor_AntiColor[0]='#d60000';


$AdminColor_BlodColor[1]='#143c1f';
$AdminColor_NormalColor[1]='#5b992e';
$AdminColor_LightColor[1]='#bee3a3';
$AdminColor_HighColor[1]='#6ac726';
$AdminColor_AntiColor[1]='#d60000';


$AdminColor_BlodColor[2]='#06282b';
$AdminColor_NormalColor[2]='#2db1bd';
$AdminColor_LightColor[2]='#87e6ef';
$AdminColor_HighColor[2]='#119ba7';
$AdminColor_AntiColor[2]='#d60000';


$AdminColor_BlodColor[3]='#3e1165';
$AdminColor_NormalColor[3]='#5c2c84';
$AdminColor_LightColor[3]='#a777d0';
$AdminColor_HighColor[3]='#8627d7';
$AdminColor_AntiColor[3]='#08a200';


$AdminColor_BlodColor[4]='#3f280d';
$AdminColor_NormalColor[4]='#b26e1e';
$AdminColor_LightColor[4]='#e3b987';
$AdminColor_HighColor[4]='#d88625';
$AdminColor_AntiColor[4]='#d60000';


$AdminColor_BlodColor[5]='#0a4f3e';
$AdminColor_NormalColor[5]='#267662';
$AdminColor_LightColor[5]='#68cdb4';
$AdminColor_HighColor[5]='#25bb96';
$AdminColor_AntiColor[5]='#d60000';


$AdminColor_BlodColor[6]='#3a0b19';
$AdminColor_NormalColor[6]='#7c243f';
$AdminColor_LightColor[6]='#d57c98';
$AdminColor_HighColor[6]='#d31b54';
$AdminColor_AntiColor[6]='#2039b7';


$AdminColor_BlodColor[7]='#2d2606';
$AdminColor_NormalColor[7]='#d9b611';
$AdminColor_LightColor[7]='#ebd87d';
$AdminColor_HighColor[7]='#c4a927';
$AdminColor_AntiColor[7]='#d60000';

$AdminColor_BlodColor[8]='#3f0100';
$AdminColor_NormalColor[8]='#e5535f';
$AdminColor_LightColor[8]='#ffb3a7';
$AdminColor_HighColor[8]='#da4b4a';
$AdminColor_AntiColor[8]='#ff000c';


$AdminColor_BlodColor[9]='#333333';
$AdminColor_NormalColor[9]='#555555';
$AdminColor_LightColor[9]='#ababab';
$AdminColor_HighColor[9]='#8b8b8b';
$AdminColor_AntiColor[9]='#d60000';

$AdminColor_BlodColor[10]='#4a380b';
$AdminColor_NormalColor[10]='#896a1c';
$AdminColor_LightColor[10]='#caa855';
$AdminColor_HighColor[10]='#b08313';
$AdminColor_AntiColor[10]='#2227e0';

?>