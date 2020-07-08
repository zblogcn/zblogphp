<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-02
 */

/**
 * 获取系统信息接口.
 *
 * @return array
 */
function api_system_get_info()
{
    global $zbp;

    ApiCheckAuth(true, 'misc');
    ApiCheckAuth(true, 'admin');
    
    $info = array(
        'environment' => $zbp->cache->system_environment,
        'full_version' => ZC_VERSION_FULL,
        'articles' => (int) $zbp->cache->all_article_nums,
        'categories' => (int) $zbp->cache->all_category_nums,
        'pages' => (int) $zbp->cache->all_page_nums,
        'comments' => (int) $zbp->cache->all_comment_nums,
        'views' => (int) $zbp->cache->all_view_nums,
        'members' => (int) $zbp->cache->all_member_nums,
        'theme' => $zbp->theme,
        'xml_rpc' => $zbp->xmlrpcurl,
    );

    return array(
        'data' => array('info' => $info,),
    );
}

/**
 * 清空缓存并重新编译模板接口.
 *
 * @return array
 */
function api_system_statistic()
{
    global $zbp;

    ApiCheckAuth(true, 'misc');
    ApiCheckAuth(true, 'root');

    include ZBP_PATH . 'zb_system/function/c_system_misc.php';
    ob_clean();

    misc_statistic();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}
