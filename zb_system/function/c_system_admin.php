<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function zbp_addpagesubmenu(){
	echo '<a href="../cmd.php?act=PageEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_page'] . '</span></a>';
}

function zbp_addtagsubmenu(){
	echo '<a href="../cmd.php?act=TagEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_tag'] . '</span></a>';
}

function zbp_addcatesubmenu(){
	echo '<a href="../cmd.php?act=CategoryEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_category'] . '</span></a>';
}

function zbp_addmemsubmenu(){
	echo '<a href="../cmd.php?act=MemberNew"><span class="m-left">' . $GLOBALS['lang']['msg']['new_member'] . '</span></a>';
}

function zbp_addmodsubmenu(){
	echo '<a href="../cmd.php?act=ModuleEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_module'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=navbar"><span class="m-left">' . $GLOBALS['lang']['msg']['module_navbar'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=link"><span class="m-left">' . $GLOBALS['lang']['msg']['module_link'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=favorite"><span class="m-left">' . $GLOBALS['lang']['msg']['module_favorite'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=misc"><span class="m-left">' . $GLOBALS['lang']['msg']['module_misc'] . '</span></a>';
}

Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu','zbp_addpagesubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_TagMng_SubMenu','zbp_addtagsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu','zbp_addcatesubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_MemberMng_SubMenu','zbp_addmemsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu','zbp_addmodsubmenu');


$zbp->LoadTemplates();









################################################################################################################
$topmenus=array();

$leftmenus=array();


function ResponseAdmin_LeftMenu(){

	global $zbp;
	global $leftmenus;

	$leftmenus[]=MakeLeftMenu("ArticleEdt",$zbp->lang['msg']['new_article'],$zbp->host . "zb_system/cmd.php?act=ArticleEdt","nav_new","aArticleEdt","");
	$leftmenus[]=MakeLeftMenu("ArticleMng",$zbp->lang['msg']['article_manage'],$zbp->host . "zb_system/cmd.php?act=ArticleMng","nav_article","aArticleMng","");
	$leftmenus[]=MakeLeftMenu("PageMng",$zbp->lang['msg']['page_manage'],$zbp->host . "zb_system/cmd.php?act=PageMng","nav_page","aPageMng","");

	$leftmenus[]="<li class='split'><hr/></li>";


	$leftmenus[]=MakeLeftMenu("CategoryMng",$zbp->lang['msg']['category_manage'],$zbp->host . "zb_system/cmd.php?act=CategoryMng","nav_category","aCategoryMng","");
	$leftmenus[]=MakeLeftMenu("TagMng",$zbp->lang['msg']['tag_manage'],$zbp->host . "zb_system/cmd.php?act=TagMng","nav_tags","aTagMng","");
	$leftmenus[]=MakeLeftMenu("CommentMng",$zbp->lang['msg']['comment_manage'],$zbp->host . "zb_system/cmd.php?act=CommentMng","nav_comments","aCommentMng","");
	$leftmenus[]=MakeLeftMenu("UploadMng",$zbp->lang['msg']['upload_manage'],$zbp->host . "zb_system/cmd.php?act=UploadMng","nav_accessories","aUploadMng","");
	$leftmenus[]=MakeLeftMenu("MemberMng",$zbp->lang['msg']['member_manage'],$zbp->host . "zb_system/cmd.php?act=MemberMng","nav_user","aMemberMng","");

	$leftmenus[]="<li class='split'><hr/></li>";

	$leftmenus[]=MakeLeftMenu("ThemeMng",$zbp->lang['msg']['theme_manage'],$zbp->host . "zb_system/cmd.php?act=ThemeMng","nav_themes","aThemeMng","");
	$leftmenus[]=MakeLeftMenu("ModuleMng",$zbp->lang['msg']['module_manage'],$zbp->host . "zb_system/cmd.php?act=ModuleMng","nav_function","aModuleMng","");
	$leftmenus[]=MakeLeftMenu("PluginMng",$zbp->lang['msg']['plugin_manage'],$zbp->host . "zb_system/cmd.php?act=PluginMng","nav_plugin","aPluginMng","");

	foreach ($GLOBALS['Filter_Plugin_Admin_LeftMenu'] as $fpname => &$fpsignal) {
		$fpname($leftmenus);
	}

	foreach ($leftmenus as $m) {
		echo $m;
	}

}

function ResponseAdmin_TopMenu(){

	global $zbp;
	global $topmenus;

	$topmenus[]=MakeTopMenu("admin",$zbp->lang['msg']['dashboard'],$zbp->host . "zb_system/cmd.php?act=admin","","");
	$topmenus[]=MakeTopMenu("SettingMng",$zbp->lang['msg']['settings'],$zbp->host . "zb_system/cmd.php?act=SettingMng","","");

	foreach ($GLOBALS['Filter_Plugin_Admin_TopMenu'] as $fpname => &$fpsignal) {
		$fpname($topmenus);
	}

	$topmenus[]=MakeTopMenu("misc",$zbp->lang['msg']['official_website'],"http://www.rainbowsoft.org/","","_blank");

	foreach ($topmenus as $m) {
		echo $m;
	}

}


function MakeTopMenu($requireAction,$strName,$strUrl,$strLiId,$strTarget){
	global $zbp;

	static $AdminTopMenuCount=0;
	if ($zbp->CheckRights($requireAction)==false) {
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
	if ($zbp->CheckRights($requireAction)==false) {
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








################################################################################################################
function CreateOptoinsOfTemplate($default){
	global $zbp;

	$s=null;
	$s .= '<option value="" >' . $zbp->lang['msg']['none'] . '</option>';
	foreach ($zbp->templates as $key => $value) {
		if(substr($key,0,2)=='b_')continue;
		if(substr($key,0,2)=='c_')continue;
		if(substr($key,0,5)=='post-')continue;
		if(substr($key,0,6)=='module')continue;
		if(substr($key,0,6)=='header')continue;
		if(substr($key,0,6)=='footer')continue;	
		if(substr($key,0,7)=='comment')continue;
		if(substr($key,0,7)=='sidebar')continue;
		if(substr($key,0,7)=='pagebar')continue;
		if($default==$key){
			$s .= '<option value="' . $key . '" selected="selected">' . $key . ' ('.$zbp->lang['msg']['default_template'].')' . '</option>';
		}else{
			$s .= '<option value="' . $key . '" >' . $key . '</option>';
		}
	}

	return $s;
}



function CreateOptoinsOfMemberLevel($default){
	global $zbp;

	$s=null;
	if(!$zbp->CheckRights('MemberAll')){
		return '<option value="' . $default . '" selected="selected" >' . $zbp->lang['user_level_name'][$default] . '</option>';
	}
	if($zbp->user->ID==$default){
		return '<option value="' . $default . '" selected="selected" >' . $zbp->members[$default]->Name . '</option>';
	}
	for ($i=1; $i <7 ; $i++) {
		$s .= '<option value="' . $i . '" ' . ($default==$i?'selected="selected"':'') . ' >' . $zbp->lang['user_level_name'][$i] . '</option>';
	}
	return $s;
}



function CreateOptoinsOfMember($default){
	global $zbp;

	$s=null;
	if(!$zbp->CheckRights('ArticleAll')){
		return '<option value="' . $default . '" selected="selected" >' . $zbp->members[$default]->Name . '</option>';
	}
	foreach ($zbp->members as $key => $value) {
		$s .= '<option value="' . $key . '" ' . ($default==$key?'selected="selected"':'') . ' >' . $zbp->members[$key]->Name . '</option>';
	}
	return $s;
}


function CreateOptoinsOfPostStatus($default){
	global $zbp;

	$s=null;
	if(!$zbp->CheckRights('ArticlePub')&&$default==2){
		return '<option value="2" ' . ($default==2?'selected="selected"':'') . ' >' . $zbp->lang['post_status_name']['2'] . '</option>';
	}
	if(!$zbp->CheckRights('ArticleAll')&&$default==2){
		return '<option value="2" ' . ($default==2?'selected="selected"':'') . ' >' . $zbp->lang['post_status_name']['2'] . '</option>';
	}
	$s .= '<option value="0" ' . ($default==0?'selected="selected"':'') . ' >' . $zbp->lang['post_status_name']['0'] . '</option>';
	$s .= '<option value="1" ' . ($default==1?'selected="selected"':'') . ' >' . $zbp->lang['post_status_name']['1'] . '</option>';
	if($zbp->CheckRights('ArticleAll')){
		$s .= '<option value="2" ' . ($default==2?'selected="selected"':'') . ' >' . $zbp->lang['post_status_name']['2'] . '</option>';
	}
	return $s;
}



function CreateModuleDiv($m,$button=true){
	global $zbp;

	echo '<div class="widget widget_source_' . $m->SourceType . ' widget_id_' . $m->FileName . '">';
	echo '<div class="widget-title"><img class="more-action" width="16" src="../image/admin/brick.png" alt="" />' . ($m->SourceType!='theme'?$m->Name:$m->FileName) . '';

	if($button){
		if($m->SourceType!='theme'){
			echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;id=' . $m->ID . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="" title="" width="16" /></a>';
		}else{
			echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;source=theme&amp;filename=' . $m->FileName . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="" title="" width="16" /></a>';
		}
		if($m->SourceType!='system'&&$m->SourceType!='theme'){
			echo '&nbsp;<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ModuleDel&amp;id=' . $m->ID .'"><img src="../image/admin/delete.png" alt="" title="" width="16" /></a>';
		}
		echo '</span>';
	}

	echo '</div>';
	echo '<div class="funid" style="display:none">' . $m->FileName . '</div>';	
	echo '</div>';
}








################################################################################################################
function Admin_SiteInfo(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['info_intro'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_SiteInfo_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '<table class="tableFull tableBorder" id="tbStatistic"><tr><th colspan="4">&nbsp;' . $zbp->lang['msg']['site_analyze'] . '&nbsp;<a href="javascript:statistic(\'?act=misc&amp;type=statistic\');">[' . $zbp->lang['msg']['refresh_cache'] . ']</a> <img id="statloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';
	echo $zbp->GetCache('reload_statistic');
	echo '</table>';

	echo '<table class="tableFull tableBorder" id="tbUpdateInfo"><tr><th>&nbsp;' . $zbp->lang['msg']['latest_news'] . '&nbsp;<a href="javascript:updateinfo(\'?act=misc&amp;type=updateinfo\');">[' . $zbp->lang['msg']['refresh'] . ']</a> <img id="infoloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';
	echo $zbp->GetCache('reload_updateinfo');
	echo '</table>';

	echo '</div>';
	include $zbp->path . "zb_system/defend/thanks.html";
	echo '<script type="text/javascript">ActiveTopMenu("topmenu1");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/home_32.png' . '");</script>';	
}







################################################################################################################
function Admin_ArticleMng(){

	global $zbp;


	echo '<div class="divHeader">' . $zbp->lang['msg']['article_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ArticleMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<form class="search" id="search" method="post" action="#">';

	echo '<p>' . $zbp->lang['msg']['search'] . ':&nbsp;&nbsp;' . $zbp->lang['msg']['category'] . ' <select class="edit" size="1" name="category" style="width:150px;" ><option value="">' . $zbp->lang['msg']['any'] . '</option>';
	foreach ($zbp->categorysbyorder as $id => $cate) {
	  echo '<option value="'. $cate->ID .'">' . $cate->SymbolName . '</option>';
	}
	echo'</select>&nbsp;&nbsp;&nbsp;&nbsp;' . $zbp->lang['msg']['type'] . ' <select class="edit" size="1" name="status" style="width:80px;" ><option value="">' . $zbp->lang['msg']['any'] . '</option> <option value="0" >' . $zbp->lang['post_status_name']['0'] . '</option><option value="1" >' . $zbp->lang['post_status_name']['1'] . '</option><option value="2" >' . $zbp->lang['post_status_name']['2'] . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="checkbox" name="istop" value="True"/>&nbsp;' . $zbp->lang['msg']['top'] . '</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="search" style="width:250px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
	echo '</form>';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['category'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['title'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . $zbp->lang['msg']['comment'] . '</th>
	<th>' . $zbp->lang['msg']['status'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&page=%page%}{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}');
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$p->UrlRule->Rules['{%category%}']=GetVars('category');
$p->UrlRule->Rules['{%search%}']=urlencode(GetVars('search'));
$p->UrlRule->Rules['{%status%}']=GetVars('status');
$p->UrlRule->Rules['{%istop%}']=(boolean)GetVars('istop');

$w=array();
if(!$zbp->CheckRights('ArticleAll')){
	$w[]=array('=','log_AuthorID',$zbp->user->ID);
}
if(GetVars('search')){
	$w[]=array('search','log_Content','log_Intro','log_Title',GetVars('search'));
}
if(GetVars('istop')){
	$w[]=array('=','log_Istop','1');
}
if(GetVars('status')){
	$w[]=array('=','log_Status',GetVars('status'));
}
if(GetVars('category')){
	$w[]=array('=','log_CateID',GetVars('category'));
}

$array=$zbp->GetArticleList(
	'',
	$w,
	array('log_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $article) {
	echo '<tr>';
	echo '<td class="td5">' . $article->ID .  '</td>';
	echo '<td class="td10">' . $article->Category->Name . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td>' . $article->Title . '</td>';
	echo '<td class="td20">' .$article->Time() . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . $article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=ArticleEdt&amp;id='. $article->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ArticleDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';

foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}

	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aArticleMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/article_32.png' . '");</script>';	

}








################################################################################################################
function Admin_PageMng(){

	global $zbp;


	echo '<div class="divHeader">' . $zbp->lang['msg']['page_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_PageMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<!--<form class="search" id="search" method="post" action="#"></form>-->';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['title'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . $zbp->lang['msg']['comment'] . '</th>
	<th>' . $zbp->lang['msg']['status'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=PageMng{&page=%page%}');
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$array=$zbp->GetPageList(
	'',
	'',
	array('log_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $article) {
	echo '<tr>';
	echo '<td class="td5">' . $article->ID . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td>' . $article->Title . '</td>';
	echo '<td class="td20">' . $article->Time() . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . $article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=PageEdt&amp;id='. $article->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=PageDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aPageMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/page_32.png' . '");</script>';

}






################################################################################################################
function Admin_CategoryMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['category_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_CategoryMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>

	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['order'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['alias'] . '</th>
	<th>' . $zbp->lang['msg']['post_count'] . '</th>
	<th></th>
	</tr>';


foreach ($zbp->categorysbyorder as $category) {
	echo '<tr>';
	echo '<td class="td5">' . $category->ID . '</td>';
	echo '<td class="td5">' . $category->Order . '</td>';
	echo '<td class="td25">' . $category->Symbol . $category->Name . '</td>';
	echo '<td class="td20">' . $category->Alias . '</td>';
	echo '<td class="td10">' . $category->Count . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=CategoryEdt&amp;id='. $category->ID .'"><img src="../image/admin/folder_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=CategoryDel&amp;id=26"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/category_32.png' . '");</script>';
	
}







################################################################################################################
function Admin_CommentMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['comment_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_CommentMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aCommentMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/comments_32.png' . '");</script>';
}







################################################################################################################
function Admin_MemberMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['member_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_MemberMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<!--<form class="search" id="edit" method="post" action="#"></form>-->';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . '' . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['alias'] . '</th>
	<th>' . $zbp->lang['msg']['all_artiles'] . '</th>
	<th>' . $zbp->lang['msg']['all_comments'] . '</th>
	<th>' . $zbp->lang['msg']['all_uploads'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=MemberMng{&page=%page%}');
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;


$w=array();
if(!$zbp->CheckRights('MemberAll')){
	$w[]=array('=','mem_ID',$zbp->user->ID);
}
$array=$zbp->GetMemberList(
	'',
	$w,
	array('mem_ID'=>'ASC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $member) {
	echo '<tr>';
	echo '<td class="td5">' . $member->ID . '</td>';
	echo '<td class="td10">' . $member->LevelName . '</td>';
	echo '<td>' . $member->Name . '</td>';
	echo '<td class="td20">' . $member->Alias . '</td>';
	echo '<td class="td10">' . $member->Articles . '</td>';
	echo '<td class="td10">' . $member->Comments . '</td>';
	echo '<td class="td10">' . $member->Uploads . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=MemberEdt&amp;id='. $member->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=MemberDel&amp;id='. $member->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aMemberMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/user_32.png' . '");</script>';
}








################################################################################################################
function Admin_UploadMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['upload_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_UploadMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';


	echo '<form class="search" name="upload" id="upload" method="post" enctype="multipart/form-data" action="../cmd.php?act=UploadPst">';
	echo '<p>' . $zbp->lang['msg']['upload_file'] . ': </p>';
	echo '<p><input type="file" name="file" size="60" />&nbsp;&nbsp;';
	echo '<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '" onclick="" />&nbsp;&nbsp;';
	echo '<input class="button" type="reset" value="' . $zbp->lang['msg']['reset'] . '" /></p>';
	echo '</form>';

	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . $zbp->lang['msg']['size'] . '</th>
	<th>' . $zbp->lang['msg']['type'] . '</th>
	<th></th>
	</tr>';

$w=array();
if(!$zbp->CheckRights('UploadAll')){
	$w[]=array('=','ul_AuthorID',$zbp->user->ID);
}

$p=new Pagebar('{%host%}zb_system/cmd.php?act=UploadMng{&page=%page%}');
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$array=$zbp->GetUploadList(
	'',
	$w,
	array('ul_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $upload) {
	echo '<tr>';
	echo '<td class="td5">' . $upload->ID . '</td>';
	echo '<td class="td10">' . $upload->Author->Name . '</td>';
	echo '<td><a href="' . $upload->Url . '">' . $upload->Name . '</a></td>';
	echo '<td class="td15">' . $upload->Time() . '</td>';
	echo '<td class="td10">' . $upload->Size . '</td>';
	echo '<td class="td20">' . $upload->MimeType . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=UploadDel&amp;id='. $upload->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aUploadMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/accessories_32.png' . '");</script>';
}









################################################################################################################
function Admin_TagMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['tag_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_TagMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';


	echo '<div id="divMain2">';
	echo '<!--<form class="search" id="edit" method="post" action="#"></form>-->';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['alias'] . '</th>
	<th>' . $zbp->lang['msg']['post_count'] . '</th>	
	<th></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=TagMng{&page=%page%}');
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$array=$zbp->GetTagList(
	'',
	'',
	array('tag_Name'=>'ASC','tag_ID'=>'ASC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $tag) {
	echo '<tr>';
	echo '<td class="td5">' . $tag->ID . '</td>';
	echo '<td class="td25">' . $tag->Name . '</td>';
	echo '<td class="td20">' . $tag->Alias . '</td>';
	echo '<td class="td10">' . $tag->Count . '</td>';	
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=TagEdt&amp;id='. $tag->ID .'"><img src="../image/admin/tag_blue_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=TagDel&amp;id='. $tag->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] ." title=".$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';
foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}	
	echo '</p></div>';

	echo '<script type="text/javascript">ActiveLeftMenu("aTagMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/tag_32.png' . '");</script>';
}









################################################################################################################
function Admin_ThemeMng(){

	global $zbp;

	$zbp->LoadThemes();

	echo '<div class="divHeader">' . $zbp->lang['msg']['theme_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ThemeMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2"><form id="frmTheme" method="post" action="../cmd.php?act=ThemeSet">';
	echo '<input type="hidden" name="theme" id="theme" value="" />';
	echo '<input type="hidden" name="style" id="style" value="" />';

	foreach ($zbp->themes as $theme) {

echo '<div class="theme '.($theme->IsUsed()?'theme-now':'theme-other').'">';
echo '<div class="theme-name">';
echo '<img width="16" title="" alt="" src="../image/admin/layout.png"/>&nbsp;';
echo '<a target="_blank" href="'.$theme->url.'" title="">';
echo '<strong style="display:none;">default</strong><b>'.$theme->name.'</b></a></div>';
echo '<div><img src="'.$theme->GetScreenshot().'" title="'.$theme->name.'" alt="'.$theme->name.'" width="200" height="150" /></div>';
echo '<div class="theme-author">'.$zbp->lang['msg']['author'].': <a target="_blank" href="'.$theme->author_url.'">'.$theme->author_name.'</a></div>';
echo '<div class="theme-style">'.$zbp->lang['msg']['style'].': ';
echo '<select class="edit" size="1" style="width:110px;">';
foreach ($theme->GetCssFiles() as $key => $value) {
	echo '<option value="'.$key.'" '.($theme->IsUsed()?($key==$zbp->style?'selected="selected"':''):'').'>'.basename($value).'</option>';
}
echo '</select>';
echo '<input type="button" onclick="$(\'#style\').val($(this).prev().val());$(\'#theme\').val(\''.$theme->id.'\');$(\'#frmTheme\').submit();" class="theme-activate button" value="'.$zbp->lang['msg']['enable'].'">';
echo '</div>';
echo '</div>';

	}

	echo '</form></div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aThemeMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/themes_32.png' . '");</script>';
	
}









################################################################################################################
function Admin_ModuleMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['module_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_ModuleMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';

$sm=array();
$um=array();
$tm=array();
$pm=array();

foreach ($zbp->modules as $m) {
	if($m->Source=='system'){
		$sm[]=$m;
	}elseif($m->Source=='user'){
		$um[]=$m;
	}elseif($m->Source=='theme'){
		$tm[]=$m;
	}else{
		$pm[]=$m;
	}
}
	#widget-list begin
	echo '<div class="widget-left">';
	echo '<div class="widget-list">';

	echo '<script type="text/javascript">';
	echo 'var functions = {';
foreach ($zbp->modules as $key => $value) {
	echo "'" . $value->FileName . "':'" . $value->Source . "' ,";
}
	echo "'':''};";
	echo '</script>';
	echo "\r\n";
	echo '<div class="widget-list-header">'. $zbp->lang['msg']['system_module'] .'</div>';
	echo '<div class="widget-list-note">'. $zbp->lang['msg']['drag_module_to_sidebar'] .'</div>';
	echo "\r\n";
foreach ($sm as $m) {
	CreateModuleDiv($m);
}

	echo '<div class="widget-list-header">'. $zbp->lang['msg']['user_module'] .'</div>';
	echo "\r\n";
foreach ($um as $m) {
	CreateModuleDiv($m);
}

	echo '<div class="widget-list-header">'. $zbp->lang['msg']['theme_module'] .'</div>';
	echo "\r\n";
foreach ($tm as $m) {
	CreateModuleDiv($m);
}

	echo '<div class="widget-list-header">'. $zbp->lang['msg']['plugin_module'] .'</div>';
	echo "\r\n";
foreach ($pm as $m) {
	CreateModuleDiv($m);
}

	echo '<hr/>';
	echo "\r\n";
	echo '<form id="edit" method="post" action="../cmd.php?act=SidebarSet">';
	echo '<input type="hidden" id="strsidebar" name="edtSidebar" value="'. $zbp->option['ZC_SIDEBAR_ORDER'] .'"/>';
	echo '<input type="hidden" id="strsidebar2" name="edtSidebar2" value="'. $zbp->option['ZC_SIDEBAR2_ORDER'] .'"/>';
	echo '<input type="hidden" id="strsidebar3" name="edtSidebar3" value="'. $zbp->option['ZC_SIDEBAR3_ORDER'] .'"/>';
	echo '<input type="hidden" id="strsidebar4" name="edtSidebar4" value="'. $zbp->option['ZC_SIDEBAR4_ORDER'] .'"/>';
	echo '<input type="hidden" id="strsidebar5" name="edtSidebar5" value="'. $zbp->option['ZC_SIDEBAR5_ORDER'] .'"/>';
	echo '</form>';
	echo "\r\n";
	echo '<div class="clear"></div></div>';
	echo '</div>';
	#widget-list end
	echo "\r\n";
	#siderbar-list begin
	echo '<div class="siderbar-list">';
	echo '<div class="siderbar-drop" id="siderbar"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
	echo '<div class="siderbar-note" >' . str_replace('%s', Count($zbp->sidebar),$zbp->lang['msg']['sidebar_module_count']) . '</div>';
foreach ($zbp->sidebar as $m) {
	CreateModuleDiv($m,false);
}
	echo '</div></div>';
	echo "\r\n";

	echo '<div class="siderbar-drop" id="siderbar2"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar2'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
	echo '<div class="siderbar-note" >' . str_replace('%s', Count($zbp->sidebar2),$zbp->lang['msg']['sidebar_module_count']) . '</div>';
foreach ($zbp->sidebar2 as $m) {
	CreateModuleDiv($m,false);
}
	echo '</div></div>';
	echo "\r\n";

	echo '<div class="siderbar-drop" id="siderbar3"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar3'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
	echo '<div class="siderbar-note" >' . str_replace('%s', Count($zbp->sidebar3),$zbp->lang['msg']['sidebar_module_count']) . '</div>';
foreach ($zbp->sidebar3 as $m) {
	CreateModuleDiv($m,false);
}
	echo '</div></div>';
	echo "\r\n";

	echo '<div class="siderbar-drop" id="siderbar4"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar4'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
	echo '<div class="siderbar-note" >' . str_replace('%s', Count($zbp->sidebar4),$zbp->lang['msg']['sidebar_module_count']) . '</div>';
foreach ($zbp->sidebar4 as $m) {
	CreateModuleDiv($m,false);
}
	echo '</div></div>';
	echo "\r\n";
	
	echo '<div class="siderbar-drop" id="siderbar5"><div class="siderbar-header">' . $zbp->lang['msg']['sidebar5'] . '&nbsp;<img class="roll" src="../image/admin/loading.gif" width="16" alt="" /><span class="ui-icon ui-icon-triangle-1-s"></span></div><div  class="siderbar-sort-list" >';
	echo '<div class="siderbar-note" >' . str_replace('%s', Count($zbp->sidebar5),$zbp->lang['msg']['sidebar_module_count']) . '</div>';
foreach ($zbp->sidebar5 as $m) {
	CreateModuleDiv($m,false);
}
	echo '</div></div>';
	echo "\r\n";

	echo '<div class="clear"></div></div>';
	#siderbar-list end
	echo "\r\n";
	echo '<div class="clear"></div>';

	echo '</div>';
	echo "\r\n";

	echo '<script type="text/javascript">ActiveLeftMenu("aModuleMng");</script>';
?>
<script type="text/javascript">
	$(function() {

		function sortFunction(){
			var s1="";
			$("#siderbar").find("div.funid").each(function(i){
			   s1 += $(this).html() +"|";
			 });

			 var s2="";
			$("#siderbar2").find("div.funid").each(function(i){
			   s2 += $(this).html() +"|";
			 });

			 var s3="";
			$("#siderbar3").find("div.funid").each(function(i){
			   s3 += $(this).html() +"|";
			 });

			 var s4="";
			$("#siderbar4").find("div.funid").each(function(i){
			   s4 += $(this).html() +"|";
			 });

			 var s5="";
			$("#siderbar5").find("div.funid").each(function(i){
			   s5 += $(this).html() +"|";
			 });

			$("#strsidebar" ).val(s1);
			$("#strsidebar2").val(s2);
			$("#strsidebar3").val(s3);
			$("#strsidebar4").val(s4);
			$("#strsidebar5").val(s5);


			$.post($("#edit").attr("action"),
				{
				"sidebar": s1,
				"sidebar2": s2,
				"sidebar3": s3,
				"sidebar4": s4,
				"sidebar5": s5
				},
			   function(data){
				 //alert("Data Loaded: " + data);
			   });

		};

		var t;
		function hideWidget(item){
				item.find(".ui-icon").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-w");
				t=item.next();
				t.find(".widget").hide("fast").end().show();
				t.find(".siderbar-note>span").text(t.find(".widget").length);
		}
		function showWidget(item){
				item.find(".ui-icon").removeClass("ui-icon-triangle-1-w").addClass("ui-icon-triangle-1-s");
				t=item.next();
				t.find(".widget").show("fast");
				t.find(".siderbar-note>span").text(t.find(".widget").length);
		}

		$(".siderbar-header").toggle( function () {
				hideWidget($(this));
			  },
			  function () {
				showWidget($(this));
			  });

 		$( ".siderbar-sort-list" ).sortable({
 			items:'.widget',
			start:function(event, ui){
				showWidget(ui.item.parent().prev());
				 var c=ui.item.find(".funid").html();
				 if(ui.item.parent().find(".widget:contains("+c+")").length>1){
					ui.item.remove();
				 };
			} ,			
			stop:function(event, ui){$(this).parent().find(".roll").show("slow");sortFunction();$(this).parent().find(".roll").hide("slow");
				showWidget($(this).parent().prev());
			}
 		}).disableSelection(); 

		$( ".widget-list>.widget" ).draggable({
            connectToSortable: ".siderbar-sort-list",
            revert: "invalid", 
            containment: "document",
            helper: "clone",
            cursor: "move"
        }).disableSelection();

		$( ".widget-list" ).droppable({
			accept:".siderbar-sort-list>.widget",
            drop: function( event, ui ) {
            	ui.draggable.remove();
            }
        });

});

</script>
<?php
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/link_32.png' . '");</script>';
}





################################################################################################################
function Admin_PluginMng(){

	global $zbp;
	
	$zbp->LoadPlugins();

	echo '<div class="divHeader">' . $zbp->lang['msg']['plugin_manage'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_PluginMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain2">';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>

	<th></th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th></th>
	</tr>';
$plugins=array();

$app = new App;
if($app->LoadInfoByXml('theme',$zbp->theme)==true){
	if($app->HasPlugin()){
		array_unshift($plugins,$app);
	}
}

$pl=$zbp->option['ZC_USING_PLUGIN_LIST'];
$apl=explode('|',$pl);
foreach ($apl as $name) {
	foreach ($zbp->plugins as $plugin) {
		if($name==$plugin->id){
			$plugins[]=$plugin;
		}
	}
}
foreach ($zbp->plugins as $plugin) {
	if(!$plugin->IsUsed()){
		$plugins[]=$plugin;
	}
}


foreach ($plugins as $plugin) {
	echo '<tr>';
	echo '<td class="td5 tdCenter"><img ' . ($plugin->IsUsed()?'':'style="opacity:0.2"') . ' src="' . $plugin->GetLogo() . '" alt="" width="32" /></td>';
	echo '<td class="td25">' . $plugin->name . '</td>';
	echo '<td class="td20">' . $plugin->author_name . '</td>';
	echo '<td class="td20">' . $plugin->modified . '</td>';
	echo '<td class="td10 tdCenter">';

	if($plugin->type=='plugin'){
		if($plugin->IsUsed()){
			echo '<a href="../cmd.php?act=PluginDisable&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['disable'] . '"><img width="16" alt="' . $zbp->lang['msg']['disable'] . '" src="../image/admin/control-power.png"/></a>';
		}else{
			echo '<a href="../cmd.php?act=PluginEnable&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['enable'] . '"><img width="16" alt="' . $zbp->lang['msg']['enable'] . '" src="../image/admin/control-power-off.png"/></a>';
		}
	}
	if($plugin->IsUsed() && $plugin->CanManage()){
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a href="' . $plugin->GetManageUrl() . '" title="' . $zbp->lang['msg']['manage'] . '"><img width="16" alt="' . $zbp->lang['msg']['manage'] . '" src="../image/admin/setting_tools.png"/></a>';
	}	

	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '</div>';
	echo '<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/plugin_32.png' . '");</script>';
	
}







################################################################################################################
function Admin_SettingMng(){

	global $zbp;

	echo '<div class="divHeader">' . $zbp->lang['msg']['settings'] . '</div>';
	echo '<div class="SubMenu">';
	foreach ($GLOBALS['Filter_Plugin_Admin_SettingMng_SubMenu'] as $fpname => &$fpsignal) {
		$fpname();
	}	
	echo '</div>';
	echo '<div id="divMain">';

	echo '</div>';
	echo '<script type="text/javascript">ActiveTopMenu("topmenu2");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/setting_32.png' . '");</script>';
}

?>