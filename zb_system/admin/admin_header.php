<!DOCTYPE html>
<html lang="<?php echo $lang['lang_bcp47'] ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<meta name="generator" content="Z-BlogPHP <?php echo ZC_VERSION_DISPLAY ?>" />
<meta name="robots" content="none" />
<meta name="renderer" content="webkit" />
<title><?php echo $blogname . '-' . $blogtitle ?></title>
<link href="<?php echo $bloghost ?>zb_system/css/admin2.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $bloghost ?>zb_system/css/jquery-ui.custom.css"/>
<script src="<?php echo $bloghost ?>zb_system/script/jquery-2.2.4.min.js" type="text/javascript"></script>
<script src="<?php echo $bloghost ?>zb_system/script/zblogphp.js" type="text/javascript"></script>
<script src="<?php echo $bloghost ?>zb_system/script/c_admin_js_add.php" type="text/javascript"></script>
<script src="<?php echo $bloghost ?>zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
<script>if (!window.bloghost && window.confirm("<?php echo $lang['msg']['error_load_js']; ?>")) window.open('<?php echo str_replace('{%message%}', '', str_replace('{%id%}', 89, $lang['offical_urls']['more_help']))?>');</script>
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Header'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
