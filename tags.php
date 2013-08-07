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
	rsort($t);
	$i=$t[0];
	if($i>1000){$i=300;}
	elseif($i>700){$i=250;}
	elseif($i>500){$i=200;}
	elseif($i>300){$i=150;}
	elseif($i>200){$i=100;}
	elseif($i>100){$i=90;}
	elseif($i>80){$i=60;}
	elseif($i>50){$i=35;}
	elseif($i>20){$i=15;}
	elseif($i>10){$i=8;}	
	$t=array();	
	foreach ($array as $tag) {

		//$article->Content .='<a href="' .  $tag->Url . '">' . $tag->Name . '</a>&nbsp;&nbsp;';
		$t[]='<a href="' .  $tag->Url . '" style="font-size:' . $tag->Count/$i . 'em;margin:0.5em 0;">' . $tag->Name . '</a>&nbsp;&nbsp;';

	}
	shuffle($t);

	$article->Content ='<p>' . implode(' ',$t) . '</p>';

}


$zbp->template->SetTags('title',$article->Title);

$zbp->template->SetTags('article',$article);

$zbp->template->display($zbp->option['ZC_PAGE_DEFAULT_TEMPLATE']);

$zbp->Terminate();

RunTime();
?>