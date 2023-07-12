<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 * @version 2.0 2013-06-14
 */

/**
 * 返回配置.
 *
 * @param
 *
 * @return array
 */
return array(

    // '---------------------------------关闭网站-----------------------------------
    'ZC_CLOSE_WHOLE_SITE' => false,
    'ZC_CLOSE_SITE'       => false,

    // '---------------------------------网站基本设置-----------------------------------
    'ZC_BLOG_HOST'         => 'http://localhost/',
    'ZC_BLOG_NAME'         => '我的网站',
    'ZC_BLOG_SUBNAME'      => 'Good Luck To You!',
    'ZC_BLOG_THEME'        => 'default',
    'ZC_BLOG_CSS'          => 'default',
    'ZC_BLOG_COPYRIGHT'    => 'Copyright Your WebSite.Some Rights Reserved.',
    'ZC_BLOG_LANGUAGE'     => 'zh-CN',
    'ZC_BLOG_LANGUAGEPACK' => 'zh-cn',

    // '----------------------------数据库配置---------------------------------------
    //mysql|mysqli|pdo_mysql|sqlite|sqlite3|pdo_sqlite
    'ZC_DATABASE_TYPE' => '',

    'ZC_SQLITE_NAME' => '',
    'ZC_SQLITE_PRE'  => 'zbp_',

    'ZC_MYSQL_SERVER'     => 'localhost',
    'ZC_MYSQL_USERNAME'   => 'root',
    'ZC_MYSQL_PASSWORD'   => '',
    'ZC_MYSQL_NAME'       => '',
    'ZC_MYSQL_CHARSET'    => 'utf8',
    'ZC_MYSQL_COLLATE'    => 'utf8_general_ci',
    'ZC_MYSQL_PRE'        => 'zbp_',
    'ZC_MYSQL_ENGINE'     => 'MyISAM',
    'ZC_MYSQL_PORT'       => '3306',
    'ZC_MYSQL_PERSISTENT' => false,

    'ZC_PGSQL_SERVER'     => 'localhost',
    'ZC_PGSQL_USERNAME'   => 'postgres',
    'ZC_PGSQL_PASSWORD'   => '',
    'ZC_PGSQL_NAME'       => '',
    'ZC_PGSQL_CHARSET'    => 'utf8',
    'ZC_PGSQL_PRE'        => 'zbp_',
    'ZC_PGSQL_PORT'       => '5432',
    'ZC_PGSQL_PERSISTENT' => false,

    // '---------------------------------插件----------------------------------------
    'ZC_USING_PLUGIN_LIST' => '',

    // '-------------------------------全局配置-----------------------------------
    'ZC_BLOG_CLSID'      => '',
    'ZC_TIME_ZONE_NAME'  => 'Asia/Shanghai',
    'ZC_UPDATE_INFO_URL' => 'https://update.zblogcn.com/info/',

    // '固定域名,默认为false
    'ZC_PERMANENT_DOMAIN_ENABLE'     => false,

    //
    'ZC_DEBUG_MODE'         => false,
    'ZC_DEBUG_MODE_STRICT'  => false,
    'ZC_DEBUG_MODE_WARNING' => true,
    'ZC_DEBUG_LOG_ERROR'    => false,

    // '当前 Z-Blog 版本

    'ZC_BLOG_PRODUCT'          => 'Z-BlogPHP',
    'ZC_BLOG_VERSION'          => '',
    'ZC_BLOG_COMMIT'           => '',
    'ZC_BLOG_PRODUCT_FULL'     => '',
    'ZC_BLOG_PRODUCT_HTML'     => '',
    'ZC_BLOG_PRODUCT_FULLHTML' => '',

    // '留言评论
    'ZC_COMMENT_TURNOFF'           => false,
    'ZC_COMMENT_VERIFY_ENABLE'     => false,
    'ZC_COMMENT_REVERSE_ORDER'     => false,
    'ZC_COMMENT_AUDIT'             => false,
    'ZC_COMMENT_VALIDCMTKEY_ENABLE' => false,

    // '验证码
    'ZC_VERIFYCODE_STRING' => 'ABCDEFGHKMNPRSTUVWXYZ23456789',
    'ZC_VERIFYCODE_WIDTH'  => 90,
    'ZC_VERIFYCODE_HEIGHT' => 30,
    'ZC_VERIFYCODE_FONT'   => 'zb_system/defend/arial.ttf',

    // '页面各项列数
    'ZC_DISPLAY_COUNT'          => 10,
    'ZC_DISPLAY_ORDER'          => 'log_PostTime',
    'ZC_PAGEBAR_COUNT'          => 10,
    'ZC_COMMENTS_DISPLAY_COUNT' => 100,

    'ZC_DISPLAY_SUBCATEGORYS' => true,

    // '杂项
    'ZC_RSS2_COUNT'       => 10,
    'ZC_RSS2_ORDER'       => 'log_PostTime',
    'ZC_RSS_EXPORT_WHOLE' => true,

    // '后台管理
    'ZC_MANAGE_COUNT' => 50,
    'ZC_MANAGE_ORDER' => 'log_PostTime',
    
    // 登录相关
    'ZC_LOGIN_CSRFCHECK_ENABLE' => true,
    'ZC_LOGIN_VERIFY_ENABLE'    => true,

    // '表情相关
    'ZC_EMOTICONS_FILENAME' => 'face',

    'ZC_EMOTICONS_FILETYPE' => 'png|gif|jpg',

    'ZC_EMOTICONS_FILESIZE' => '16',

    // '上传相关
    'ZC_UPLOAD_FILETYPE' => 'jpg|gif|png|jpeg|bmp|webp|psd|wmf|ico|rpm|deb|tar|gz|xz|sit|7z|bz2|zip|rar|xml|xsl|svg|svgz|rtf|doc|docx|ppt|pptx|xls|xlsx|wps|chm|txt|md|pdf|mp3|flac|ape|mp4|mkv|avi|mpg|rm|ra|rmvb|mov|wmv|wma|torrent|apk|json|zba|gzba',
    'ZC_UPLOAD_FILESIZE' => 2,
    'ZC_UPLOAD_DIR_YEARMONTHDAY' => false,

    // '用户名,密码,评论长度等限制
    'ZC_USERNAME_MIN' => 2,

    'ZC_USERNAME_MAX' => 50,

    'ZC_PASSWORD_MIN' => 8,

    'ZC_PASSWORD_MAX' => 20,

    'ZC_EMAIL_MAX' => 50,

    'ZC_HOMEPAGE_MAX' => 100,

    'ZC_CONTENT_MAX' => 1000,

    'ZC_ARTICLE_TITLE_MAX' => 100,
    'ZC_CATEGORY_NAME_MAX' => 50,
    'ZC_TAGS_NAME_MAX'     => 50,
    'ZC_MODULE_NAME_MAX'   => 50,

    // '自动摘要字数
    'ZC_ARTICLE_EXCERPT_MAX' => 250,
    'ZC_ARTICLE_INTRO_WITH_TEXT' => false,

    // '侧栏评论最大字数
    'ZC_COMMENT_EXCERPT_MAX' => 20,

    // '---------------------------------静态化配置-----------------------------------
    // '文章,页面类,列表页的静态模式ACTIVE or REWRITE
    'ZC_STATIC_MODE' => 'ACTIVE',

    'ZC_ARTICLE_REGEX' => '{%host%}?id={%id%}',

    'ZC_PAGE_REGEX' => '{%host%}?id={%id%}',

    'ZC_CATEGORY_REGEX' => '{%host%}?cate={%id%}&page={%page%}',

    'ZC_AUTHOR_REGEX' => '{%host%}?auth={%id%}&page={%page%}',

    'ZC_TAGS_REGEX' => '{%host%}?tags={%id%}&page={%page%}',

    'ZC_DATE_REGEX' => '{%host%}?date={%date%}&page={%page%}',

    'ZC_INDEX_REGEX' => '{%host%}?page={%page%}',

    'ZC_ALIAS_BACK_ATTR' => 'Name',

    'ZC_DATETIME_SEPARATOR' => '-',
    'ZC_DATETIME_RULE' => 'Y-n',
    'ZC_DATETIME_WITHDAY_RULE' => 'Y-n-j',

    'ZC_SEARCH_COUNT' => 20,
    'ZC_SEARCH_REGEX' => '{%host%}search.php?q={%q%}&page={%page%}',

    //列表页，POST页，搜索页的默认模板
    'ZC_INDEX_DEFAULT_TEMPLATE' => 'index',
    'ZC_POST_DEFAULT_TEMPLATE'  => 'single',
    'ZC_SEARCH_DEFAULT_TEMPLATE'  => 'search',

    'ZC_SIDEBAR_ORDER'  => 'calendar|controlpanel|catalog|searchpanel|comments|archives|favorite|link|misc',
    'ZC_SIDEBAR2_ORDER' => '',
    'ZC_SIDEBAR3_ORDER' => '',
    'ZC_SIDEBAR4_ORDER' => '',
    'ZC_SIDEBAR5_ORDER' => '',
    'ZC_SIDEBAR6_ORDER' => '',
    'ZC_SIDEBAR7_ORDER' => '',
    'ZC_SIDEBAR8_ORDER' => '',
    'ZC_SIDEBAR9_ORDER' => '',
    //'ZC_SIDEBARS_DEFAULT'  => '{"1":"calendar|controlpanel|catalog|searchpanel|comments|archives|favorite|link|misc","2":"","3":"","4":"","5":"","6":"","7":"","8":"","9":""}';

    // '--------------------------其它----------------------------------------
    // '代码高亮
    'ZC_SYNTAXHIGHLIGHTER_ENABLE' => true,
    // '源码编辑高亮
    'ZC_CODEMIRROR_ENABLE' => true,
    'ZC_ALLOW_AUDITTING_MEMBER_VISIT_MANAGE' => false,
    'ZC_OUTPUT_OPTION_MEMBER_MAX_LEVEL' => 0,
    'ZC_CATEGORY_MANAGE_LEGACY_DISPLAY' => true,
    'ZC_LOADMEMBERS_LEVEL'           => 1,
    //ZC_LAST_VERSION 本意是指数据库对应的系统版本号，非当前系统未更新版本号
    'ZC_LAST_VERSION'                => '',
    'ZC_MODULE_CATALOG_STYLE'        => 0,
    'ZC_MODULE_ARCHIVES_STYLE'       => 0,
    'ZC_VIEWNUMS_TURNOFF'            => false,
    'ZC_LISTONTOP_TURNOFF'           => false,
    'ZC_RELATEDLIST_COUNT'           => 10,
    'ZC_RUNINFO_DISPLAY'             => true,
    'ZC_POST_ALIAS_USE_ID_NOT_TITLE' => false,
    'ZC_COMPATIBLE_ASP_URL'          => true,
    'ZC_LARGE_DATA'                  => false,
    'ZC_VERSION_IN_HEADER'           => true,
    'ZC_ADDITIONAL_SECURITY'         => true,
    'ZC_XMLRPC_ENABLE'               => false,
    'ZC_XMLRPC_USE_WEBTOKEN'         => false,
    'ZC_USING_CDN_GUESTIP_TYPE'      => 'REMOTE_ADDR',
    'ZC_POST_BATCH_DELETE'           => false,
    'ZC_JS_304_ENABLE'               => true,
    'ZC_DELMEMBER_WITH_ALLDATA'      => false,
    'ZC_THUMB_DEFAULT_QUALITY'       => 90,
    'ZC_FIX_MODULE_MIXED_FILENAME'   => true,

    // API 相关
    'ZC_API_ENABLE'                     => false,
    'ZC_API_THROTTLE_ENABLE'            => false,
    'ZC_API_THROTTLE_MAX_REQS_PER_MIN'  => 60,
    'ZC_API_DISPLAY_COUNT'              => 10,
);
