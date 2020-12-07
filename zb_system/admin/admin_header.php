<?php if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
if ($zbp->option['ZC_ADDITIONAL_SECURITY']) {
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Content-Security-Policy: ' . GetBackendCSPHeader());
    if ($zbp->isHttps) {
        header('Upgrade-Insecure-Requests: 1');
    }
}
?><!doctype html>
<html lang="<?php echo $lang['lang_bcp47']; ?>">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<meta name="generator" content="Z-BlogPHP <?php echo ZC_VERSION_DISPLAY; ?>" />
<meta name="robots" content="none" />
<meta name="renderer" content="webkit" />
<meta name="csrfToken" content="<?php echo $zbp->GetCSRFToken(); ?>" />
<meta name="csrfExpiration" content="<?php echo $zbp->csrfExpiration; ?>" />
<title><?php echo $blogname . ' - ' . $blogtitle; ?></title>
<link href="<?php echo $bloghost; ?>zb_system/css/admin2.css?<?php echo $blogversion; ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $bloghost; ?>zb_system/css/jquery-ui.custom.css?<?php echo $blogversion; ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo $bloghost; ?>zb_system/image/icon/icon.css?<?php echo $blogversion; ?>"/>
<script src="<?php echo $bloghost; ?>zb_system/script/jquery-2.2.4.min.js?<?php echo $blogversion; ?>"></script>
<script src="<?php echo $bloghost; ?>zb_system/script/zblogphp.js?<?php echo $blogversion; ?>"></script>
<script src="<?php echo $bloghost; ?>zb_system/script/c_admin_js_add.php?hash=<?php echo $zbp->admin_js_hash; ?>&<?php echo $blogversion; ?>"></script>
<script src="<?php echo $bloghost; ?>zb_system/script/jquery-ui.custom.min.js?<?php echo $blogversion; ?>"></script>
<script>if (!window.bloghost && window.confirm("<?php echo $lang['msg']['error_load_js']; ?>")) window.open('<?php echo str_replace('{%message%}', '', str_replace('{%id%}', 89, $lang['offical_urls']['more_help'])); ?>');</script>
<?php
if ($zbp->cache->success_updated_app !== '') {
    echo '<script src="' . $bloghost . 'zb_system/cmd.php?act=misc&type=updatedapp"></script>';
}

foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Header'] as $fpname => &$fpsignal) {
    $fpname();
}

