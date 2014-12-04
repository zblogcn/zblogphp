<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php  echo $language;  ?>" lang="<?php  echo $language;  ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="Content-Language" content="<?php  echo $language;  ?>" />
	<title><?php  echo $name;  ?>-<?php  echo $title;  ?></title>
	<meta name="generator" content="<?php  echo $zblogphp;  ?>" />
	<link rel="stylesheet" rev="stylesheet" href="<?php  echo $host;  ?>zb_users/theme/<?php  echo $theme;  ?>/style.css.php" type="text/css" media="all" />
	<script src="<?php  echo $host;  ?>zb_system/script/common.js" type="text/javascript"></script>
	<script src="<?php  echo $host;  ?>zb_system/script/c_html_js_add.php" type="text/javascript"></script>
<?php  echo $header;  ?>
<?php if ($type=='index'&&$page=='1') { ?>
	<link rel="alternate" type="application/rss+xml" href="<?php  echo $feedurl;  ?>" title="<?php  echo $name;  ?>" />
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php  echo $host;  ?>zb_system/xml-rpc/?rsd" />
	<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="<?php  echo $host;  ?>zb_system/xml-rpc/wlwmanifest.xml" /> 
<?php } ?>
	<!--[if lt IE 7]>
	<style type="text/css" media="all"> @import "<?php  echo $host;  ?>zb_users/themes/<?php  echo $theme;  ?>/style/<?php  echo $style;  ?>/fix-ie.css";</style>
	<![endif]-->
	<link rel="shortcut icon" href="<?php  echo $host;  ?>zb_users/theme/<?php  echo $theme;  ?>/style/<?php  echo $style;  ?>/favicon.ico" >
