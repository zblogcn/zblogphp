<?php
header('Content-type: text/css');

require '../../../zb_system/function/c_system_base.php';

//Http304(__FILE__,$zbp->cache->refesh);

echo '@import url("' . $bloghost . 'zb_users/theme/' . $blogtheme . '/style/' . $blogstyle . '.css' . '")';

die();
?>