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

$setting_keys = array(
    'ZC_BLOG_HOST',
    'ZC_PERMANENT_DOMAIN_ENABLE',
    'ZC_BLOG_NAME',
    'ZC_BLOG_SUBNAME',
    'ZC_BLOG_COPYRIGHT',
    'ZC_TIME_ZONE_NAME',
    'ZC_BLOG_LANGUAGEPACK',
    'ZC_API_ENABLE',
    'ZC_DEBUG_MODE',
    'ZC_DEBUG_MODE_WARNING',
    'ZC_ADDITIONAL_SECURITY',
    'ZC_USING_CDN_GUESTIP_TYPE',
    'ZC_CLOSE_SITE',
    'ZC_DISPLAY_COUNT',
    'ZC_DISPLAY_SUBCATEGORYS',
    'ZC_PAGEBAR_COUNT',
    'ZC_SEARCH_COUNT',
    'ZC_SYNTAXHIGHLIGHTER_ENABLE',
    'ZC_COMMENT_TURNOFF',
    'ZC_COMMENT_AUDIT',
    'ZC_COMMENT_REVERSE_ORDER',
    'ZC_COMMENTS_DISPLAY_COUNT',
    'ZC_COMMENT_VERIFY_ENABLE',
    'ZC_UPLOAD_FILETYPE',
    'ZC_UPLOAD_FILESIZE',
    'ZC_ARTICLE_INTRO_WITH_TEXT',
    'ZC_MANAGE_COUNT',
    'ZC_POST_BATCH_DELETE',
);

/**
 * 设置获取接口.
 *
 * @return array
 */
function api_setting_get()
{
    global $zbp, $setting_keys;

    ApiCheckAuth(true, 'SettingMng');

    $settingList = array();
    foreach ($setting_keys as $key) {
        $settingList[$key] = $zbp->option[$key];
    }

    return array(
        'data' => array('list' => $settingList),
    );
}

/**
 * 设置更新接口.
 *
 * @return array
 */
function api_setting_update()
{
    global $zbp, $setting_keys;

    ApiCheckAuth(true, 'SettingSav');

    foreach ($_POST as $key => $value) {
        if (! in_array($key, $setting_keys, true)) {
            continue;
        }

        $zbp->option[strtoupper($key)] = $value;
    }
    $zbp->SaveOption();

    $settingList = array();
    foreach ($setting_keys as $key) {
        $settingList[$key] = $zbp->option[$key];
    }
    
    return array(
        'data' => array('list' => $settingList,),
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}
