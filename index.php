<?php
/////////////////////////////////////////////////////////////////////////////////
////              Z-Blog PHP 坑爹的开始
/////////////////////////////////////////////////////////////////////////////////

require_once 'zb_system/function/c_system_base.php';

$zbp=new zblogphp;
//echo $zbp->option['ZC_BLOG_TITLE'];
$zbp->run();

//echo $c_option['ZC_BLOG_TITLE'].'<br/>';
//echo $c_lang['ZC_MSG001'];
//echo getguid();
var_dump($zbp->option);

?>