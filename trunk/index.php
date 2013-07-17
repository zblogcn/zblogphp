<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

require_once './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {header('Location: ./zb_install/');}

$zbp->Initialize();

foreach ($GLOBALS['Filter_Plugin_Index_Begin'] as $fpk => &$fpv) {
	$fpr=$fpk($page,$cate,$auth,$date,$tags);
}

ViewList(GetVars('page','GET'),GetVars('page','GET'),GetVars('page','GET'),GetVars('page','GET'),GetVars('page','GET'));

$zbp->Terminate();

#ZBlogException::Trace('看看写入debug信息成功吧？');

RunTime();
?>