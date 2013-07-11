<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

require_once './zb_system/function/c_system_base.php';

if (!$zbp->option['ZC_DATABASE_TYPE']) {header('Location: ./zb_install/');}
#echo GetGuid();
//echo method_exists('SQLite3','version');
//echo $zbp->option['ZC_BLOG_TITLE'];

$zbp->Initialize();

$zbp->Run();

$zbp->Terminate();

//echo $c_option['ZC_BLOG_TITLE'].'<br/>';
//echo $c_lang['ZC_MSG001'];
//echo getguid();
//var_dump($zbp->option);
echo RunTime();
?>