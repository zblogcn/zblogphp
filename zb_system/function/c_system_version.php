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
define('ZC_VERSION_MAJOR', '1');
define('ZC_VERSION_MINOR', '7');
define('ZC_VERSION_BUILD', '3');
define('ZC_VERSION_COMMIT', '3290');
define('ZC_VERSION_CODENAME', 'Finch');
define('ZC_VERSION', ZC_VERSION_MAJOR . '.' . ZC_VERSION_MINOR . '.' . ZC_VERSION_BUILD . '.' . ZC_VERSION_COMMIT);
if (strcasecmp(ZC_VERSION_CODENAME, 'Beta') == 0 || strcasecmp(ZC_VERSION_CODENAME, 'Alpha') == 0) {
    define('ZC_VERSION_DISPLAY', ZC_VERSION_MAJOR . '.' . ZC_VERSION_MINOR . '.' . ZC_VERSION_BUILD . '.' . ZC_VERSION_COMMIT . ' ' . ZC_VERSION_CODENAME);
} else {
    define('ZC_VERSION_DISPLAY', ZC_VERSION_MAJOR . '.' . ZC_VERSION_MINOR . '.' . ZC_VERSION_BUILD);
}
define('ZC_VERSION_FULL', ZC_VERSION . ' (' . ZC_VERSION_CODENAME . ')');
define('ZC_BLOG_COMMIT', ZC_VERSION_COMMIT); // 为写入系统配置统一风格
$GLOBALS['blogversion'] = ZC_VERSION_MAJOR . ZC_VERSION_MINOR . ZC_VERSION_COMMIT;
define('ZC_NOW_VERSION', $GLOBALS['blogversion']);
define('ZC_BLOG_VERSION', ZC_VERSION_DISPLAY . ' Build ' . $GLOBALS['blogversion']);
define('ZC_LAST_VERSION', '173000'); //数据库里写入的最新的数据库版本号（非系统版本号，只有更改数据库结构才会变动）
