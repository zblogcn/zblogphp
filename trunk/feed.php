<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require './zb_system/function/c_system_base.php';

$zbp->Load();

$action='feed';

if(!$zbp->CheckRights($action)){Http404();}

foreach ($GLOBALS['Filter_Plugin_Feed_Begin'] as $fpname => &$fpsignal) {$fpname();}

$rss2 = new Rss2($zbp->name,$zbp->host,$zbp->subname);

$articles=$zbp->GetArticleList(
	array('*'),
	array(array('=','log_Status',0)),
	array('log_PostTime'=>'DESC'),
	array(10),
	null
);

foreach ($articles as $article) {
	$rss2->addItem($article->Title,$article->Url,$article->Content,$article->PostTime);
}

header("Content-type:text/xml; Charset=utf-8");

echo $rss2->saveXML();

RunTime();