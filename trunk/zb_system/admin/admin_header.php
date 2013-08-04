<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(strpos(GetVars('HTTP_USER_AGENT','SERVERS'),'MSIE')){?>
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<?php }?>
<meta name="generator" content="Z-BlogPHP <?php echo $option['ZC_BLOG_VERSION']?>" />
<meta name="robots" content="none" />
<title><?php echo $blogname . '-' . $blogtitle?></title>
<link href="<?php echo $bloghost?>zb_system/css/admin2.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $bloghost?>zb_system/script/common.js" type="text/javascript"></script>
<script src="<?php echo $bloghost?>zb_system/script/c_admin_js_add.php" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo $bloghost?>zb_system/css/jquery.bettertip.css" type="text/css" media="screen" />
<script src="<?php echo $bloghost?>zb_system/script/jquery.bettertip.pack.js" type="text/javascript"></script>
<script src="<?php echo $bloghost?>zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $bloghost?>zb_system/css/jquery-ui.custom.css"/>
<?php

foreach ($GLOBALS['Filter_Plugin_Admin_Header'] as $fpname => &$fpsignal) {$fpname();}

?>