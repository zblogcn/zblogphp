<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

foreach ($GLOBALS['Filter_Plugin_Search_Begin'] as $fpname => &$fpsignal) {$fpname();}

$zbp->Load();

$action='search';

if(!$zbp->CheckRights($action)){Redirect('./');}


$article = new Post;
$article->Title=$lang['msg']['search'] . '“' . GetVars('q','GET') . '”';
$article->IsLock=true;
$article->Type=ZC_POST_TYPE_PAGE;


$w=array();
$w[]=array('=','log_Type','0');
$s=trim(GetVars('q','GET'));
if($s){
	$w[]=array('search','log_Content','log_Intro','log_Title',$s);
}else{
	Redirect('./');
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


$zbp->template->SetTags('title',$article->Title);
$zbp->template->SetTags('article',$article);
$zbp->template->SetTags('type',$article->type=0?'article':'page');
$zbp->template->SetTemplate($article->Template);

$zbp->template->display();

RunTime();
?>