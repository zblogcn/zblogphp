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

	global $zbp;
	global $leftmenus;

	$leftmenus[]=MakeLeftMenu("ArticleEdt",$zbp->lang['msg']['new_article'],$zbp->host . "zb_system/cmd.php?act=ArticleEdt","nav_new","aArticleEdt","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$zbp->lang['msg']['article_manage'],$zbp->host . "zb_system/cmd.php?act=ArticleMng","nav_article","aArticleMng","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$zbp->lang['msg']['page_manage'],$zbp->host . "zb_system/cmd.php?act=ArticleMng&amp;type=Page","nav_page","aPageMng","");

	$leftmenus[]="<li class='split'><hr/></li>";


	$leftmenus[]=MakeLeftMenu("CategoryMng",$zbp->lang['msg']['category_manage'],$zbp->host . "zb_system/cmd.php?act=CategoryMng","nav_category","aCategoryMng","");
	$leftmenus[]=MakeLeftMenu("TagMng",$zbp->lang['msg']['tags_manage'],$zbp->host . "zb_system/cmd.php?act=TagMng","nav_tags","aTagMng","");
	$leftmenus[]=MakeLeftMenu("CommentMng",$zbp->lang['msg']['comment_manage'],$zbp->host . "zb_system/cmd.php?act=CommentMng","nav_comments","aCommentMng","");
	$leftmenus[]=MakeLeftMenu("FileMng",$zbp->lang['msg']['upload_manage'],$zbp->host . "zb_system/cmd.php?act=FileMng","nav_accessories","aFileMng","");
	$leftmenus[]=MakeLeftMenu("UserMng",$zbp->lang['msg']['member_manage'],$zbp->host . "zb_system/cmd.php?act=UserMng","nav_user","aUserMng","");

	$leftmenus[]="<li class='split'><hr/></li>";

	$leftmenus[]=MakeLeftMenu("ThemeMng",$zbp->lang['msg']['theme_manage'],$zbp->host . "zb_system/cmd.php?act=ThemeMng","nav_themes","aThemeMng","");
	$leftmenus[]=MakeLeftMenu("PlugInMng",$zbp->lang['msg']['plugin_manage'],$zbp->host . "zb_system/cmd.php?act=PlugInMng","nav_plugin","aPlugInMng","");
	$leftmenus[]=MakeLeftMenu("FunctionMng",$zbp->lang['msg']['module_manage'],$zbp->host . "zb_system/cmd.php?act=FunctionMng","nav_function","aFunctionMng","");


	foreach ($leftmenus as $m) {
		echo $m;
	}

}

function ResponseAdminTopMenu(){

	global $zbp;
	global $topmenus;

	$topmenus[]=MakeTopMenu("admin",$zbp->lang['msg']['dashboard'],$zbp->host . "zb_system/cmd.php?act=admin","","");
	$topmenus[]=MakeTopMenu("SettingMng",$zbp->lang['msg']['settings'],$zbp->host . "zb_system/cmd.php?act=SettingMng","","");


	$topmenus[]=MakeTopMenu("vrs",$zbp->lang['msg']['official_website'],"http://www.rainbowsoft.org/","","_blank");

	foreach ($topmenus as $m) {
		echo $m;
	}

}


function MakeTopMenu($requireAction,$strName,$strUrl,$strLiId,$strTarget){
	global $zbp;

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
	global $zbp;

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
	global $zbp;

	echo "<div class=\"divHeader\">" . $zbp->lang['msg']['info_intro'] . "</div>";
	echo "<div class=\"SubMenu\">" . '@$Response_Plugin_SiteInfo_SubMenu' . "</div>";
	echo "<div id=\"divMain2\">";



	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"100%\" class=\"tableBorder\" id=\"tbStatistic\"><tr><th height=\"32\" colspan=\"4\"  align=\"center\">&nbsp;" . $zbp->lang['msg']['site_analyze'] . "&nbsp;<a href=\"javascript:statistic('?act=reload&amp;statistic');\">[" . $zbp->lang['msg']['refresh_cache'] . "]</a> <img id=\"statloading\" style=\"display:none\" src=\"../image/admin/loading.gif\"></th></tr>";
	echo $zbp->GetCacheValue('reload_statistic');
	echo "</table>";

	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"100%\" class=\"tableBorder\" id=\"tbUpdateInfo\"><tr><th height=\"32\" colspan=\"4\" align=\"center\">&nbsp;" . $zbp->lang['msg']['latest_news'] . "&nbsp;<a href=\"javascript:updateinfo('?act=reload&amp;updateinfo');\">[" . $zbp->lang['msg']['refresh'] . "]</a> <img id=\"infoloading\" style=\"display:none\" src=\"../image/admin/loading.gif\"></th></tr>";
	echo $zbp->GetCacheValue('reload_updateinfo');
	echo "</table>";


	include_once $zbp->path . "zb_system/defend/thanks.html";

}

?>