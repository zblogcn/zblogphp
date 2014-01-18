<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

$zbp->Load();
$zbp->CheckGzip();

$action='search';

if(!$zbp->CheckRights($action)){Redirect('./');}

foreach ($GLOBALS['Filter_Plugin_Search_Begin'] as $fpname => &$fpsignal) {$fpname();}

$q=trim(strip_tags(GetVars('q','GET')));

$article = new Post;
$article->ID=0;
$article->Title=$lang['msg']['search'] . '“' . $q . '”';
$article->IsLock=true;
$article->Type=ZC_POST_TYPE_PAGE;

if(isset($zbp->templates['search'])){
	$article->Template='search';
}

$w=array();
$w[]=array('=','log_Type','0');
if($q){
	$w[]=array('search','log_Content','log_Intro','log_Title',$q);
}else{
	Redirect('./');
}

if(!($zbp->CheckRights('ArticleAll')&&$zbp->CheckRights('PageAll'))){
	$w[]=array('=','log_Status',0);
}

$array=$zbp->GetArticleList(
	'',
	$w,
	array('log_PostTime'=>'DESC'),
	array($zbp->searchcount),
	null
);

foreach ($array as $a) {
	$article->Content .= '<p><br/>' . $a->Title . '<br/>';
	$article->Content .= '<a href="' . $a->Url . '">' . $a->Url . '</a></p>';
}

$zbp->header .= '<meta name="robots" content="none" />' . "\r\n";
$zbp->template->SetTags('title',$article->Title);
$zbp->template->SetTags('article',$article);
$zbp->template->SetTags('type',$article->type=0?'article':'page');
$zbp->template->SetTags('page',1);
$zbp->template->SetTags('pagebar',null);
$zbp->template->SetTags('comments',array());
$zbp->template->SetTemplate($article->Template);

foreach ($GLOBALS['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
	$fpreturn=$fpname($zbp->template);
}

$zbp->template->Display();

RunTime();