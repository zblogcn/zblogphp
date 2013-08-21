<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

foreach ($GLOBALS['Filter_Plugin_Tags_Begin'] as $fpname => &$fpsignal) {$fpname();}

$zbp->Load();

$article = new Post;
$article->Title=$zbp->lang['msg']['tags'];
$article->IsLock=true;
$article->Type=ZC_POST_TYPE_PAGE;

$array=$zbp->GetTagList(
	null,
	null,
	array('tag_Count'=>'DESC','tag_ID'=>'ASC'),
	array(100),
	null
);
if(count($array)>0){

	$t=array();
	foreach ($array as $tag) {
		$t[]=$tag->Count;
	}
	$j=$t[count($array)-1];
	$i=$t[0];
	$i=($i-$j)/count($array);
	$t=array();	
	$j=16;

	foreach ($array as $tag) {
		$j-=0.2;
		//$article->Content .='<a href="' .  $tag->Url . '">' . $tag->Name . '</a>&nbsp;&nbsp;';
		$t[]='<a href="' .  $tag->Url . '" style="font-size:' . ($j+$i) . 'pt;margin:0.5pt 0;">' . $tag->Name . '</a>&nbsp;&nbsp;';

	}
	shuffle($t);

	$article->Content ='<p>' . implode(' ',$t) . '</p>';

}


$zbp->template->SetTags('title',$article->Title);
$zbp->template->SetTags('article',$article);
$zbp->template->SetTags('type',$article->type=0?'article':'page');
$zbp->template->SetTags('header',$zbp->header);
$zbp->template->SetTags('footer',$zbp->footer);

$zbp->template->display($zbp->option['ZC_PAGE_DEFAULT_TEMPLATE']);

RunTime();
?>