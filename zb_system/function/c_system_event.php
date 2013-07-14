<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function ViewList($page,$cate,$auth,$date,$tags){

	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpk => &$fpv) {
		$fpr=$fpk($page,$cate,$auth,$date,$tags);
		if ($fpv==PLUGIN_EXITSIGNAL_RETURN) {return $fpr;}
	}

	$zbp=$GLOBALS['zbp'];

	echo $page;

	return null;

}

function ViewArticle(){


}

function ViewPage(){


}
?>