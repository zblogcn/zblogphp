<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


$topmenus=array();

$leftmenus=array();




function ResponseAdminLeftMenu(){

	global $leftmenus;

	$leftmenus[]=MakeLeftMenu("ArticleEdt",$GLOBALS['lang']['ZC_MSG']['168'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleEdt","nav_new","aArticleEdt","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$GLOBALS['lang']['ZC_MSG']['067'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleMng","nav_article","aArticleMng","");
	$leftmenus[]=MakeLeftMenu("ArticleAll",$GLOBALS['lang']['ZC_MSG']['111'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleMng&amp;type=Page","nav_page","aPageMng","");

	$leftmenus[]="<li class='split'><hr/></li>";


	$leftmenus[]=MakeLeftMenu("CategoryMng",$GLOBALS['lang']['ZC_MSG']['066'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=CategoryMng","nav_category","aCategoryMng","");
	$leftmenus[]=MakeLeftMenu("TagMng",$GLOBALS['lang']['ZC_MSG']['141'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=TagMng","nav_tags","aTagMng","");
	$leftmenus[]=MakeLeftMenu("CommentMng",$GLOBALS['lang']['ZC_MSG']['068'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=CommentMng","nav_comments","aCommentMng","");
	$leftmenus[]=MakeLeftMenu("FileMng",$GLOBALS['lang']['ZC_MSG']['071'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=FileMng","nav_accessories","aFileMng","");
	$leftmenus[]=MakeLeftMenu("UserMng",$GLOBALS['lang']['ZC_MSG']['070'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=UserMng","nav_user","aUserMng","");

	$leftmenus[]="<li class='split'><hr/></li>";

	$leftmenus[]=MakeLeftMenu("ThemeMng",$GLOBALS['lang']['ZC_MSG']['223'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ThemeMng","nav_themes","aThemeMng","");
	$leftmenus[]=MakeLeftMenu("PlugInMng",$GLOBALS['lang']['ZC_MSG']['107'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=PlugInMng","nav_plugin","aPlugInMng","");
	$leftmenus[]=MakeLeftMenu("FunctionMng",$GLOBALS['lang']['ZC_MSG']['007'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=FunctionMng","nav_function","aFunctionMng","");


	foreach ($leftmenus as $m) {
		echo $m;
	}

}

function ResponseAdminTopMenu(){

	global $topmenus;

	$topmenus[]=MakeTopMenu("admin",$GLOBALS['lang']['ZC_MSG']['245'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=admin","","");
	$topmenus[]=MakeTopMenu("SettingMng",$GLOBALS['lang']['ZC_MSG']['247'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=SettingMng","","");


	$topmenus[]=MakeTopMenu("vrs",$GLOBALS['lang']['ZC_MSG']['006'],"http://www.rainbowsoft.org/","","_blank");

	foreach ($topmenus as $m) {
		echo $m;
	}

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


function MakeLeftMenu($requireAction,$strName,$strUrl,$strLiId,$strAId,$strImgUrl){

	static $AdminLeftMenuCount=0;
	if (CheckRights($requireAction)==false) {
		return null;
	}

	$AdminLeftMenuCount=$AdminLeftMenuCount+1;
	$tmp=null;
	if($strImgUrl==""){
		$tmp="<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\"><span style=\"background-image:url('" . $strImgUrl . "')\">" . $strName . "</span></a></li>";
	}else{
		$tmp="<li id=\"" . $strLiId . "\"><a id=\"" . $strAId . "\" href=\"" . $strUrl . "\"><span>" . $strName . "</span></a></li>";
	}
	return $tmp;
	
}

?>