<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function ListExport($page,$cate,$auth,$date,$tags){

	foreach ($GLOBALS['Action_Plugin_ListExport_Begin'] as &$sAction_Plugin_ListExport_Begin) {
		eval($sAction_Plugin_ListExport_Begin);
	}

	$zbp=$GLOBALS['zbp'];

	return null;

}

function ArticleExport(){


}

function PageExport(){


}
?>