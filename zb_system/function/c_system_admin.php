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

	$leftmenus[]=MakeLeftMenu("ArticleEdt",$GLOBALS['lang']['msg']['new_article'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleEdt","nav_new","aArticleEdt","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$GLOBALS['lang']['msg']['article_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleMng","nav_article","aArticleMng","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$GLOBALS['lang']['msg']['page_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ArticleMng&amp;type=Page","nav_page","aPageMng","");

	$leftmenus[]="<li class='split'><hr/></li>";


	$leftmenus[]=MakeLeftMenu("CategoryMng",$GLOBALS['lang']['msg']['category_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=CategoryMng","nav_category","aCategoryMng","");
	$leftmenus[]=MakeLeftMenu("TagMng",$GLOBALS['lang']['msg']['tags_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=TagMng","nav_tags","aTagMng","");
	$leftmenus[]=MakeLeftMenu("CommentMng",$GLOBALS['lang']['msg']['comment_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=CommentMng","nav_comments","aCommentMng","");
	$leftmenus[]=MakeLeftMenu("FileMng",$GLOBALS['lang']['msg']['upload_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=FileMng","nav_accessories","aFileMng","");
	$leftmenus[]=MakeLeftMenu("UserMng",$GLOBALS['lang']['msg']['member_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=UserMng","nav_user","aUserMng","");

	$leftmenus[]="<li class='split'><hr/></li>";

	$leftmenus[]=MakeLeftMenu("ThemeMng",$GLOBALS['lang']['msg']['theme_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=ThemeMng","nav_themes","aThemeMng","");
	$leftmenus[]=MakeLeftMenu("PlugInMng",$GLOBALS['lang']['msg']['plugin_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=PlugInMng","nav_plugin","aPlugInMng","");
	$leftmenus[]=MakeLeftMenu("FunctionMng",$GLOBALS['lang']['msg']['module_manage'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=FunctionMng","nav_function","aFunctionMng","");


	foreach ($leftmenus as $m) {
		echo $m;
	}

}

function ResponseAdminTopMenu(){

	global $topmenus;

	$topmenus[]=MakeTopMenu("admin",$GLOBALS['lang']['msg']['dashboard'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=admin","","");
	$topmenus[]=MakeTopMenu("SettingMng",$GLOBALS['lang']['msg']['settings'],$GLOBALS['bloghost'] . "zb_system/cmd.php?act=SettingMng","","");


	$topmenus[]=MakeTopMenu("vrs",$GLOBALS['lang']['msg']['official_website'],"http://www.rainbowsoft.org/","","_blank");

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


function ExportSiteInfo(){

	echo "<div class=\"divHeader\">" . $GLOBALS['lang']['msg']['info_intro'] . "</div>";
	echo "<div class=\"SubMenu\">" . '@$Response_Plugin_SiteInfo_SubMenu' . "</div>";
	echo "<div id=\"divMain2\">";



	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"100%\" class=\"tableBorder\" id=\"tbStatistic\"><tr><th height=\"32\" colspan=\"4\"  align=\"center\">&nbsp;" . $GLOBALS['lang']['msg']['site_analyze'] . "&nbsp;<a href=\"javascript:statistic('?reload');\">[" . $GLOBALS['lang']['msg']['refresh_cache'] . "]</a> <img id=\"statloading\" style=\"display:none\" src=\"../image/admin/loading.gif\"></th></tr><tr><td></td></tr></table>";
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"100%\" class=\"tableBorder\"><tr><th height=\"32\" colspan=\"4\" align=\"center\">&nbsp;" . $GLOBALS['lang']['msg']['latest_news'] . "&nbsp;<a href=\"javascript:updateinfo('?reload');\">[" . $GLOBALS['lang']['msg']['refresh'] . "]</a> <img id=\"infoloading\" style=\"display:none\" src=\"../image/admin/loading.gif\"></th></tr><tr><td height=\"25\" colspan=\"4\" id=\"tdUpdateInfo\"></td></tr></table>";


	include_once $GLOBALS['blogpath'] . "zb_system/defend/thanks.html";

}

?>