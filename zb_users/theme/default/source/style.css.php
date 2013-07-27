<?php
header('Content-type: text/css');

require_once '../../../../zb_system/function/c_system_base.php';

echo '@import url("' . $bloghost . 'zb_users/theme/' . $option['ZC_BLOG_THEME'] . '/style/' . $option['ZC_BLOG_CSS'] . '.css' . '")';

die();
?>