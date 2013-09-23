<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function zbp_admin_addpagesubmenu(){
	echo '<a href="../cmd.php?act=PageEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_page'] . '</span></a>';
}

function zbp_admin_addtagsubmenu(){
	echo '<a href="../cmd.php?act=TagEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_tag'] . '</span></a>';
}

function zbp_admin_addcatesubmenu(){
	echo '<a href="../cmd.php?act=CategoryEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_category'] . '</span></a>';
}

function zbp_admin_addmemsubmenu(){
	global $zbp;
	if($zbp->CheckRights('MemberNew')){
		echo '<a href="../cmd.php?act=MemberNew"><span class="m-left">' . $GLOBALS['lang']['msg']['new_member'] . '</span></a>';
	}
}

function zbp_admin_addmodsubmenu(){
	echo '<a href="../cmd.php?act=ModuleEdt"><span class="m-left">' . $GLOBALS['lang']['msg']['new_module'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=navbar"><span class="m-left">' . $GLOBALS['lang']['msg']['module_navbar'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=link"><span class="m-left">' . $GLOBALS['lang']['msg']['module_link'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=favorite"><span class="m-left">' . $GLOBALS['lang']['msg']['module_favorite'] . '</span></a>';
	echo '<a href="../cmd.php?act=ModuleEdt&amp;filename=misc"><span class="m-left">' . $GLOBALS['lang']['msg']['module_misc'] . '</span></a>';
}
function zbp_admin_addcmtsubmenu(){
	global $zbp;
	if($zbp->CheckRights('CommentAll')){
		$n=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(comm_ID) AS num FROM ' . $GLOBALS['table']['Comment'] . ' WHERE comm_Ischecking=1'),'num');
		if($n!=0){$n=' ('.$n.')';}else{$n='';}
		echo '<a href="../cmd.php?act=CommentMng&amp;ischecking=1"><span class="m-left '.(GetVars('ischecking')?'m-now':'').'">' . $GLOBALS['lang']['msg']['check_comment']  . $n . '</span></a>';
	}
}


Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu','zbp_admin_addpagesubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_TagMng_SubMenu','zbp_admin_addtagsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu','zbp_admin_addcatesubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_MemberMng_SubMenu','zbp_admin_addmemsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu','zbp_admin_addmodsubmenu');
Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu','zbp_admin_addcmtsubmenu');

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

	$topmenus[]=MakeTopMenu("misc",$zbp->lang['msg']['official_website'],"http://www.rainbowsoft.org/","_blank","");

	foreach ($topmenus as $m) {
		echo $m;
	}

}


function MakeTopMenu($requireAction,$strName,$strUrl,$strTarget,$strLiId){
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
	if($strImgUrl!=""){
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
	for ($i=1; $i <7 ; $i++) {
		$s .= '<option value="' . $i . '" ' . ($default==$i?'selected="selected"':'') . ' >' . $zbp->lang['user_level_name'][$i] . '</option>';
	}
	return $s;
}



function CreateOptoinsOfMember($default){
	global $zbp;

	$s=null;
	if(!$zbp->CheckRights('ArticleAll')){
		if(!isset($zbp->members[$default]))return '<option value="0" selected="selected" ></option>';
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
			echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;id=' . $m->ID . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="'.$zbp->lang['msg']['edit'].'" title="'.$zbp->lang['msg']['edit'].'" width="16" /></a>';
		}else{
			echo '<span class="widget-action"><a href="../cmd.php?act=ModuleEdt&amp;source=theme&amp;filename=' . $m->FileName . '"><img class="edit-action" src="../image/admin/brick_edit.png" alt="'.$zbp->lang['msg']['edit'].'" title="'.$zbp->lang['msg']['edit'].'" width="16" /></a>';
		}
		if($m->SourceType!='system'&&$m->SourceType!='theme'){
			echo '&nbsp;<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ModuleDel&amp;id=' . $m->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
		}
		echo '</span>';
	}

	echo '</div>';
	echo '<div class="funid" style="display:none">' . $m->FileName . '</div>';	
	echo '</div>';
}



function CreateOptionsOfTimeZone($default){
	global $zbp;
	$s='';
$tz=array
     (
		'Kwajalein' => '-12:00',
		'Pacific/Midway' => '-11:00',
		'Pacific/Honolulu' => '-10:00',
		'America/Anchorage' => '-09:00',
		'America/Los_Angeles' => '-08:00',
		'America/Denver' => '-07:00',
		'America/Tegucigalpa' => '-06:00',
		'America/New_York' => '-05:00',
		'America/Halifax' => '-04:00',
		'America/Argentina/Buenos_Aires' => '-03:00',
		'Atlantic/South_Georgia' => '-02:00',
		'Atlantic/Azores' => '-01:00',
		'Europe/Dublin' => '00:00',
		'Europe/Belgrade' => '+01:00',
		'Europe/Minsk' => '+02:00',
		'Asia/Kuwait' => '+03:00',
		'Asia/Muscat' => '+04:00',
		'Asia/Yekaterinburg' => '+05:00',
		'Asia/Dhaka' => '+06:00',
		'Asia/Krasnoyarsk' => '+07:00',
		'Asia/Shanghai' => '+08:00',
		'Asia/Seoul' => '+09:00',
		'Australia/Canberra' => '+10:00',
		'Asia/Magadan' => '+11:00',
		'Pacific/Fiji' => '+12:00',
		'Pacific/Tongatapu' => '+13:00'
     );

	foreach ($tz as $key => $value) {
		$s .= '<option value="' . $key . '" ' . ($default==$key?'selected="selected"':'') . ' >' . $key . ' ' . $value . '</option>';
	}

	return $s;
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

	echo '<table class="tableFull tableBorder" id="tbStatistic"><tr><th colspan="4"  scope="col">&nbsp;' . $zbp->lang['msg']['site_analyze'] . '&nbsp;<a href="javascript:statistic(\'?act=misc&amp;type=statistic\');" id="statistic">[' . $zbp->lang['msg']['refresh_cache'] . ']</a> <img id="statloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';

	if((time()-(int)$zbp->cache->reload_statistic_time) > (23*60*60) && $zbp->CheckRights('root')){
		echo '<script type="text/javascript">$(document).ready(function(){ statistic(\'?act=misc&type=statistic\'); });</script>';
	}else{
		$r=$zbp->cache->reload_statistic;
		$r=str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
		echo $r;
	}

	echo '</table>';

	echo '<table class="tableFull tableBorder" id="tbUpdateInfo"><tr><th>&nbsp;' . $zbp->lang['msg']['latest_news'] . '&nbsp;<a href="javascript:updateinfo(\'?act=misc&amp;type=updateinfo\');">[' . $zbp->lang['msg']['refresh'] . ']</a> <img id="infoloading" style="display:none" src="../image/admin/loading.gif" alt=""/></th></tr>';
	
	if((time()-(int)$zbp->cache->reload_updateinfo_time) > (23*60*60) && $zbp->CheckRights('root')){
		echo '<script type="text/javascript">$(document).ready(function(){ updateinfo(\'?act=misc&type=updateinfo\'); });</script>';
	}else{
		echo $zbp->cache->reload_updateinfo;
	}

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

$p=new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&page=%page%}{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}',false);
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
	echo '<td><a href="'.$article->Url.'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $article->Title . '</td>';
	echo '<td class="td20">' .$article->Time() . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . ($article->IsTop?$zbp->lang['msg']['top'].'|':'').$article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=ArticleEdt&amp;id='. $article->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=ArticleDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
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

$p=new Pagebar('{%host%}zb_system/cmd.php?act=PageMng{&page=%page%}',false);
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$w=array();
if(!$zbp->CheckRights('PageAll')){
	$w[]=array('=','log_AuthorID',$zbp->user->ID);
}

$array=$zbp->GetPageList(
	'',
	$w,
	array('log_PostTime'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $article) {
	echo '<tr>';
	echo '<td class="td5">' . $article->ID . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td><a href="'.$article->Url.'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $article->Title . '</td>';
	echo '<td class="td20">' . $article->Time() . '</td>';
	echo '<td class="td5">' . $article->CommNums . '</td>';
	echo '<td class="td5">' . $article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=PageEdt&amp;id='. $article->ID .'"><img src="../image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=PageDel&amp;id='. $article->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
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
	echo '<td class="td25"><a href="'.$category->Url .'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $category->Symbol . $category->Name . '</td>';
	echo '<td class="td20">' . $category->Alias . '</td>';
	echo '<td class="td10">' . $category->Count . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=CategoryEdt&amp;id='. $category->ID .'"><img src="../image/admin/folder_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=CategoryDel&amp;id='. $category->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
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



	echo '<form class="search" id="search" method="post" action="#">';
	echo '<p>' . $zbp->lang['msg']['search'] . '&nbsp;&nbsp;&nbsp;&nbsp;<input name="search" style="width:450px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';
	echo '</form>';
	echo '<form method="post" action="'.$zbp->host.'zb_system/cmd.php?act=CommentBat">';
	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['parend_id'] . '</th>
	<th>' . $zbp->lang['msg']['name'] . '</th>
	<th>' . $zbp->lang['msg']['content'] . '</th>
	<th>' . $zbp->lang['msg']['article'] . '</th>	
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>' . ''. '</th>
	<th><a href="" onclick="BatchSelectAll();return false;">' . $zbp->lang['msg']['select_all'] . '</a></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=CommentMng{&page=%page%}{&ischecking=%ischecking%}{&search=%search%}',false);
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$p->UrlRule->Rules['{%search%}']=urlencode(GetVars('search'));
$p->UrlRule->Rules['{%ischecking%}']=(boolean)GetVars('ischecking');

$w=array();
if(!$zbp->CheckRights('CommentAll')){
	$w[]=array('=','comm_AuthorID',$zbp->user->ID);
}
if(GetVars('search')){
	$w[]=array('search','comm_Content','comm_Name',GetVars('search'));
}

$w[]=array('=','comm_Ischecking',(int)GetVars('ischecking'));



$array=$zbp->GetCommentList(
	'',
	$w,
	array('comm_ID'=>'DESC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $cmt) {
	echo '<tr>';
	echo '<td class="td5">' . $cmt->ID .  '</td>';
	echo '<td class="td5">' . $cmt->ParentID . '</td>';
	echo '<td class="td10">' . $cmt->Author->Name . '</td>';
	echo '<td><div style="overflow:hidden;max-width:500px;">' . $cmt->Content . '<div></td>';
	echo '<td class="td5">' . $cmt->LogID .  '</td>';	
	echo '<td class="td15">' .$cmt->Time() . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=CommentDel&amp;id='. $cmt->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
if(!GetVars('ischecking','GET')){
	echo '<a href="../cmd.php?act=CommentChk&amp;id='. $cmt->ID .'&amp;ischecking='.(int)!GetVars('ischecking','GET').'"><img src="../image/admin/minus-shield.png" alt="'.$zbp->lang['msg']['audit'] .'" title="'.$zbp->lang['msg']['audit'] .'" width="16" /></a>';
}else{
	echo '<a href="../cmd.php?act=CommentChk&amp;id='. $cmt->ID .'&amp;ischecking='.(int)!GetVars('ischecking','GET').'"><img src="../image/admin/ok.png" alt="'.$zbp->lang['msg']['pass'] .'" title="'.$zbp->lang['msg']['pass'] .'" width="16" /></a>';
}
	echo '</td>';
	echo '<td class="td5 tdCenter">' . '<input type="checkbox" id="id'.$cmt->ID.'" name="id[]" value="'.$cmt->ID.'"/>' . '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';

foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;' ;
}

	echo '</p><p>';

	//echo '<input type="submit" onclick="" value="删除所选评论"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	//echo '<input type="submit" onclick="" value="审核所选评论"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	echo '</p><hr/></form>';



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
	<th>' . $zbp->lang['msg']['all_pages'] . '</th>
	<th>' . $zbp->lang['msg']['all_comments'] . '</th>
	<th>' . $zbp->lang['msg']['all_uploads'] . '</th>
	<th></th>
	</tr>';

$p=new Pagebar('{%host%}zb_system/cmd.php?act=MemberMng{&page=%page%}',false);
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
	echo '<td><a href="'.$member->Url.'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $member->Name . '</td>';
	echo '<td class="td15">' . $member->Alias . '</td>';
	echo '<td class="td10">' . $member->Articles . '</td>';
	echo '<td class="td10">' . $member->Pages . '</td>';
	echo '<td class="td10">' . $member->Comments . '</td>';
	echo '<td class="td10">' . $member->Uploads . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=MemberEdt&amp;id='. $member->ID .'"><img src="../image/admin/user_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
if($zbp->CheckRights('MemberDel')){
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=MemberDel&amp;id='. $member->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
}
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

$p=new Pagebar('{%host%}zb_system/cmd.php?act=UploadMng{&page=%page%}',false);
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
	echo '<td><a href="'.$upload->Url.'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $upload->Name . '</td>';
	echo '<td class="td15">' . $upload->Time() . '</td>';
	echo '<td class="td10">' . $upload->Size . '</td>';
	echo '<td class="td20">' . $upload->MimeType . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=UploadDel&amp;id='. $upload->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
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

$p=new Pagebar('{%host%}zb_system/cmd.php?act=TagMng&page={%page%}',false);
$p->PageCount=$zbp->managecount;
$p->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
$p->PageBarCount=$zbp->pagebarcount;

$array=$zbp->GetTagList(
	'',
	'',
	array('tag_ID'=>'ASC'),
	array(($p->PageNow-1) * $p->PageCount,$p->PageCount),
	array('pagebar'=>$p)
);

foreach ($array as $tag) {
	echo '<tr>';
	echo '<td class="td5">' . $tag->ID . '</td>';
	echo '<td class="td25"><a href="'.$tag->Url.'" target="_blank"><img src="../image/admin/link.png" alt="" title="" width="16" /></a> ' . $tag->Name . '</td>';
	echo '<td class="td20">' . $tag->Alias . '</td>';
	echo '<td class="td10">' . $tag->Count . '</td>';	
	echo '<td class="td10 tdCenter">';
	echo '<a href="../cmd.php?act=TagEdt&amp;id='. $tag->ID .'"><img src="../image/admin/tag_blue_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="../cmd.php?act=TagDel&amp;id='. $tag->ID .'"><img src="../image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
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

if($theme->IsUsed() && $theme->path){
echo '<a href="'.$theme->GetManageUrl().'" title="管理" class="button"><img width="16" title="" alt="" src="../image/admin/setting_tools.png"/></a>&nbsp;&nbsp;';
}else{
echo '<img width="16" title="" alt="" src="../image/admin/layout.png"/>&nbsp;&nbsp;';
}
echo '<a target="_blank" href="'.$theme->url.'" title=""><strong style="display:none;">'.$theme->id.'</strong>';
echo '<b>'.$theme->name.'</b></a></div>';
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
	echo '<td class="td5 tdCenter'.($plugin->type=='plugin'?' plugin':'').($plugin->IsUsed()?' plugin-on':'').'"><strong style="display:none;">'.$plugin->id.'</strong><img ' . ($plugin->IsUsed()?'':'style="opacity:0.2"') . ' src="' . $plugin->GetLogo() . '" alt="" width="32" height="32" /></td>';
	echo '<td class="td25">' . $plugin->name .' '. $plugin->version . '</td>';
	echo '<td class="td20">' . $plugin->author_name . '</td>';
	echo '<td class="td20">' . $plugin->modified . '</td>';
	echo '<td class="td10 tdCenter">';

	if($plugin->type=='plugin'){
		if($plugin->IsUsed()){
			echo '<a href="../cmd.php?act=PluginDis&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['disable'] . '"><img width="16" alt="' . $zbp->lang['msg']['disable'] . '" src="../image/admin/control-power.png"/></a>';
		}else{
			echo '<a href="../cmd.php?act=PluginEnb&amp;name=' . htmlspecialchars($plugin->id) . '" title="' . $zbp->lang['msg']['enable'] . '"><img width="16" alt="' . $zbp->lang['msg']['enable'] . '" src="../image/admin/control-power-off.png"/></a>';
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

?>

          <form method="post" action="../cmd.php?act=SettingSav">
            <div id="divMain2">
              <div class="content-box"><!-- Start Content Box -->
                
                <div class="content-box-header">
                  <ul class="content-box-tabs">
                    <li><a href="#tab1" class="default-tab"><span><?php echo $zbp->lang['msg']['basic_setting']?></span></a></li>
                    <li><a href="#tab2"><span><?php echo $zbp->lang['msg']['global_setting']?></span></a></li>
                    <li><a href="#tab3"><span><?php echo $zbp->lang['msg']['page_setting']?></span></a></li>
                    <li><a href="#tab4"><span><?php echo $zbp->lang['msg']['comment_setting']?></span></a></li>
                  </ul>
                  <div class="clear"></div>
                </div>
                <!-- End .content-box-header -->
                
                <div class="content-box-content">
<?php

	echo '<div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">';
	echo '<table style="padding:0px;margin:0px;width:100%;">';
	echo '<tr><td class="td25"><p><b>'.$zbp->lang['msg']['blog_host'].'</b><br/><span class="note">&nbsp;'.$zbp->lang['msg']['blog_host_add'].'</span></p></td><td><p><input id="ZC_BLOG_HOST" name="ZC_BLOG_HOST" style="width:600px;" type="text" value="'.$zbp->option['ZC_BLOG_HOST'].'" '.($zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']?'':'readonly="readonly"').' />';
	echo '<p><label onclick="$(\'#ZC_BLOG_HOST\').prop(\'readonly\', $(\'#ZC_PERMANENT_DOMAIN_ENABLE\').val()==0?true:false);"><input type="text" id="ZC_PERMANENT_DOMAIN_ENABLE" name="ZC_PERMANENT_DOMAIN_ENABLE" class="checkbox" value="'.$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'].'"/></label>&nbsp;&nbsp;'.$zbp->lang['msg']['permanent_domain'].'</p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['blog_name'].'</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="'.$zbp->option['ZC_BLOG_NAME'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['blog_subname'].'</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="'.$zbp->option['ZC_BLOG_SUBNAME'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['copyright'].'</b><br/><span class="note">&nbsp;'.$zbp->lang['msg']['copyright_add'].'</span></p></td><td><p><textarea cols="3" rows="6" id="ZC_BLOG_COPYRIGHT" name="ZC_BLOG_COPYRIGHT" style="width:600px;">'.htmlspecialchars($zbp->option['ZC_BLOG_COPYRIGHT']).'</textarea></p></td></tr>';

	echo '</table>';
	echo '</div>';



	echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">';
	echo '<table style="padding:0px;margin:0px;width:100%;">';

	echo '<tr><td class="td25"><p><b>'.$zbp->lang['msg']['blog_timezone'].'</b></p></td><td><p><select id="ZC_TIME_ZONE_NAME" name="ZC_TIME_ZONE_NAME" style="width:600px;" >';
	echo CreateOptionsOfTimeZone($zbp->option['ZC_TIME_ZONE_NAME']);
	echo '</select></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['blog_language'].'</b></p></td><td><p><input id="ZC_BLOG_LANGUAGE" name="ZC_BLOG_LANGUAGE" style="width:600px;" type="text" value="'.$zbp->option['ZC_BLOG_LANGUAGE'].'" /></p></td></tr>';

	echo '<tr><td><p><b>'.$zbp->lang['msg']['allow_upload_type'].'</b></p></td><td><p><input id="ZC_UPLOAD_FILETYPE" name="ZC_UPLOAD_FILETYPE" style="width:600px;" type="text" value="'.$zbp->option['ZC_UPLOAD_FILETYPE'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['allow_upload_size'].'</b></p></td><td><p><input id="ZC_UPLOAD_FILESIZE" name="ZC_UPLOAD_FILESIZE" style="width:600px;" type="text" value="'.$zbp->option['ZC_UPLOAD_FILESIZE'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['debug_mode'].'</b></p></td><td><p><input id="ZC_DEBUG_MODE" name="ZC_DEBUG_MODE" type="text" value="'.$zbp->option['ZC_DEBUG_MODE'].'" class="checkbox"/></p></td></tr>';

	echo '</table>';
	echo '</div>';
	echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">';
	echo '<table style="padding:0px;margin:0px;width:100%;">';

	echo '<tr><td><p><b>'.$zbp->lang['msg']['display_count'].'</b></p></td><td><p><input id="ZC_DISPLAY_COUNT" name="ZC_DISPLAY_COUNT" style="width:600px;" type="text" value="'.$zbp->option['ZC_DISPLAY_COUNT'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['display_subcategorys'].'</b></p></td><td><p><input id="ZC_DISPLAY_SUBCATEGORYS" name="ZC_DISPLAY_SUBCATEGORYS" type="text" value="'.$zbp->option['ZC_DISPLAY_SUBCATEGORYS'].'" class="checkbox"/></p></td></tr>';	
	echo '<tr><td><p><b>'.$zbp->lang['msg']['pagebar_count'].'</b></p></td><td><p><input id="ZC_PAGEBAR_COUNT" name="ZC_PAGEBAR_COUNT" style="width:600px;" type="text" value="'.$zbp->option['ZC_PAGEBAR_COUNT'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['search_count'].'</b></p></td><td><p><input id="ZC_SEARCH_COUNT" name="ZC_SEARCH_COUNT" style="width:600px;" type="text" value="'.$zbp->option['ZC_SEARCH_COUNT'].'" /></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['manage_count'].'</b></p></td><td><p><input id="ZC_MANAGE_COUNT" name="ZC_MANAGE_COUNT" style="width:600px;" type="text" value="'.$zbp->option['ZC_MANAGE_COUNT'].'" /></p></td></tr>';
	echo '</table>';
	echo '</div>';
	echo '<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">';
	echo '<table style="padding:0px;margin:0px;width:100%;">';


	echo '<tr><td class="td25"><p><b>'.$zbp->lang['msg']['comment_turnoff'].'</b></p></td><td><p><input id="ZC_COMMENT_TURNOFF" name="ZC_COMMENT_TURNOFF" type="text" value="'.$zbp->option['ZC_COMMENT_TURNOFF'].'" class="checkbox"/></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['comment_reverse_order'].'</b></p></td><td><p><input id="ZC_COMMENT_REVERSE_ORDER" name="ZC_COMMENT_REVERSE_ORDER" type="text" value="'.$zbp->option['ZC_COMMENT_REVERSE_ORDER'].'" class="checkbox"/></p></td></tr>';
	echo '<tr><td><p><b>'.$zbp->lang['msg']['comments_display_count'].'</b></p></td><td><p><input id="ZC_COMMENTS_DISPLAY_COUNT" name="ZC_COMMENTS_DISPLAY_COUNT" type="text" value="'.$zbp->option['ZC_COMMENTS_DISPLAY_COUNT'].'"  style="width:600px;" /></p></td></tr>';




	echo '</table>';
	echo '</div>';
?>
                </div>
                <!-- End .content-box-content --> 
                
              </div>
              <hr/>
			  <p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p>
            </div>
          </form>
<?php

	echo '<script type="text/javascript">ActiveTopMenu("topmenu2");</script>';
	echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/setting_32.png' . '");</script>';
}

?>