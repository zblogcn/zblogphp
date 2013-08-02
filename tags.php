<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {Redirect('./zb_install');}

$zbp->Initialize();

global $zbp;


$article = new Post;
$article->Title='标签';
$article->IsLock=true;


$array=$zbp->GetTagList(
	null,
	array('tag_Count'=>'DESC','tag_ID'=>'ASC'),
	array(100),
	null
);

foreach ($array as $tag) {

	$article->Content .='<a href="">' . $tag->Name . '</a>&nbsp;&nbsp;';

}





$zbp->template->SetTags('title',$blogname . ' -' . $article->Title);

$zbp->template->SetTags('article',$article);

$zbp->template->display($zbp->option['ZC_PAGE_DEFAULT_TEMPLATE']);

$zbp->Terminate();

RunTime();
?>