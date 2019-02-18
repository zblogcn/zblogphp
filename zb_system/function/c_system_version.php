<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/*
 * 定义版本号

这是1.5取消的数组，被安排到应用中心客户端的include.php文件里。
#定义版本号列
$zbpvers=array();
$zbpvers['130707']='1.0 Beta Build 130707';
$zbpvers['131111']='1.0 Beta2 Build 131111';
$zbpvers['131221']='1.1 Taichi Build 131221';
$zbpvers['140220']='1.2 Hippo Build 140220';
$zbpvers['140614']='1.3 Wonce Build 140614';
$zbpvers['150101']='1.4 Deeplue Build 150101';
$zbpvers['151626']='1.5 Zero Build 151626';

 */
define('ZC_VERSION_MAJOR', '2');
define('ZC_VERSION_MINOR', '0');
define('ZC_VERSION_BUILD', '0');
define('ZC_VERSION_COMMIT', '1910');
define('ZC_VERSION_CODENAME', 'Beta');
define('ZC_VERSION', ZC_VERSION_MAJOR . '.' . ZC_VERSION_MINOR . '.' . ZC_VERSION_BUILD . '.' . ZC_VERSION_COMMIT);
define('ZC_VERSION_DISPLAY', ZC_VERSION_MAJOR . '.' . ZC_VERSION_MINOR . '.' . ZC_VERSION_BUILD . ' ' . ZC_VERSION_CODENAME);
define('ZC_VERSION_FULL', ZC_VERSION . ' (' . ZC_VERSION_CODENAME . ')');
define('ZC_BLOG_COMMIT', ZC_VERSION_COMMIT); // 为写入系统配置统一风格
$GLOBALS['blogversion'] = ZC_VERSION_MAJOR . ZC_VERSION_MINOR . ZC_VERSION_COMMIT;
define('ZC_BLOG_VERSION', ZC_VERSION_DISPLAY . ' Build ' . $GLOBALS['blogversion']);
