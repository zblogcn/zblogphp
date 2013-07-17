<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function ResponseAdminLeftMenu(){

}

function ResponseAdminTopMenu(){

echo MakeTopMenu("admin",$GLOBALS['lang']['ZC_MSG']['245'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=admin","","");
echo MakeTopMenu("SettingMng",$GLOBALS['lang']['ZC_MSG']['247'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=SettingMng","","");
echo MakeTopMenu("vrs",$GLOBALS['lang']['ZC_MSG']['006'],"http://www.rainbowsoft.org/","","_blank");

}


function MakeTopMenu($requireAction,$strName,$strUrl,$strLiId,$strTarget){

	static $AdminTopMenuCount=0;
	if (CheckRights($requireAction)==false) {
		return null;
	}

	$tmp=null;
	if($strTarget==""){$strTarget="_self";}
	$AdminTopMenuCount=$AdminTopMenuCount+1;
	if($strLiId==""){$strLiId="topmenu" . $AdminTopMenuCount;}
	$tmp="<li id=\"" . $strLiId . "\"><a href=\"" . $strUrl . "\" target=\"" . $strTarget . "\">" . $strName . "</a></li>";
	return $tmp;
}

?>