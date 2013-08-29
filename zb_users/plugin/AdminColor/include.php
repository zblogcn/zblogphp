<?php
#注册插件
RegisterPlugin("AdminColor","ActivePlugin_AdminColor");

function ActivePlugin_AdminColor() {

	Add_Filter_Plugin('Filter_Plugin_Admin_SiteInfo_SubMenu','AdminColor_ColorButton');
	Add_Filter_Plugin('Filter_Plugin_Admin_Header','AdminColor_Css');
}

function AdminColor_Css(){
	global $zbp;
	echo '<link rel="stylesheet" type="text/css" href="'. $zbp->host .'zb_users/plugin/AdminColor/css.php"/>';
}

function AdminColor_ColorButton(){
	global $zbp;
	
	$s='';

	for ($i=0; $i < 9; $i++) { 
		$s.="&nbsp;&nbsp;<a href='". $zbp->host ."zb_users/plugin/AdminColor/css.php?setcolor=".$i."'><span style='height:16px;width:16px;background:".$GLOBALS['NormalColor'][$i]."'><img src='". $zbp->host ."zb_system/image/admin/none.gif' width='16' height='16' alt='' /></span></a>&nbsp;&nbsp;";
	}

	$s.="&nbsp;&nbsp;<a href='". $zbp->host ."zb_users/plugin/AdminColor/css.php?setcolor=10' title='文艺范'><span style='height:16px;width:16px;background:#eee;'><img src='". $zbp->host ."zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

	echo "<script type='text/javascript'>$('.divHeader').append(\"<div id='admin_color'>". $s ."</div>\");</script>";
}



$BlodColor[0]='#1d4c7d';
$NormalColor[0]='#3a6ea5';
$LightColor[0]='#b0cdee';
$HighColor[0]='#3399cc';
$AntiColor[0]='#d60000';


$BlodColor[1]='#143c1f';
$NormalColor[1]='#5b992e';
$LightColor[1]='#bee3a3';
$HighColor[1]='#6ac726';
$AntiColor[1]='#d60000';


$BlodColor[2]='#06282b';
$NormalColor[2]='#2db1bd';
$LightColor[2]='#87e6ef';
$HighColor[2]='#119ba7';
$AntiColor[2]='#d60000';


$BlodColor[3]='#3e1165';
$NormalColor[3]='#5c2c84';
$LightColor[3]='#a777d0';
$HighColor[3]='#8627d7';
$AntiColor[3]='#08a200';


$BlodColor[4]='#3f280d';
$NormalColor[4]='#b26e1e';
$LightColor[4]='#e3b987';
$HighColor[4]='#d88625';
$AntiColor[4]='#d60000';


$BlodColor[5]='#0a4f3e';
$NormalColor[5]='#267662';
$LightColor[5]='#68cdb4';
$HighColor[5]='#25bb96';
$AntiColor[5]='#d60000';


$BlodColor[6]='#3a0b19';
$NormalColor[6]='#7c243f';
$LightColor[6]='#d57c98';
$HighColor[6]='#d31b54';
$AntiColor[6]='#2039b7';


$BlodColor[7]='#2d2606';
$NormalColor[7]='#d9b611';
$LightColor[7]='#ebd87d';
$HighColor[7]='#c4a927';
$AntiColor[7]='#d60000';

$BlodColor[8]='#3f0100';
$NormalColor[8]='#e5535f';
$LightColor[8]='#ffb3a7';
$HighColor[8]='#da4b4a';
$AntiColor[8]='#ff000c';


$BlodColor[9]='#333333';
$NormalColor[9]='#555555';
$LightColor[9]='#ababab';
$HighColor[9]='#8b8b8b';
$AntiColor[9]='#d60000';

$BlodColor[10]='#4a380b';
$NormalColor[10]='#896a1c';
$LightColor[10]='#caa855';
$HighColor[10]='#b08313';
$AntiColor[10]='#2227e0';

?>