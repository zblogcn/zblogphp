<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function ListExport($page,$cate,$auth,$date,$tags){

	foreach ($GLOBALS['Filter_Plugin_ListExport_Begin'] as &$sFilter_Plugin_ListExport_Begin) {
		$sFilter_Plugin_ListExport_Begin($page,$cate,$auth,$date,$tags);
	}

	$zbp=$GLOBALS['zbp'];

	echo $page;

	return null;

}

function ArticleExport(){


}

function PageExport(){


}
?>