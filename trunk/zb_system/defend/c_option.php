<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
 
/**
 * 返回配置
 * @param 
 * @return array
 */
return array(
	// '---------------------------------网站基本设置-----------------------------------
	'ZC_BLOG_HOST' => 'http://localhost/',
	'ZC_BLOG_TITLE' => '我的网站',
	'ZC_BLOG_SUBTITLE' => 'Good Luck To You!',
	'ZC_BLOG_THEME' => 'default',
	'ZC_BLOG_CSS' => 'default',
	'ZC_BLOG_COPYRIGHT' => 'Copyright Your WebSite.Some Rights Reserved.',
	'ZC_BLOG_LANGUAGE' => 'zh-CN',
	'ZC_BLOG_LANGUAGEPACK' => 'SimpChinese',


	// '----------------------------数据库配置---------------------------------------
	//mysql|sqlite|sqlite3|pdo_mysql
	'ZC_DATABASE_TYPE'=> '',
	'ZC_SQLITE_ENABLE' => False,

		'ZC_SQLITE_NAME' => '',
		'ZC_SQLITE_PRE' => 'zbp_',

	'ZC_SQLITE3_ENABLE' => False,

		'ZC_SQLITE3_NAME' => '',
		'ZC_SQLITE3_PRE' => 'zbp_',
	
	'ZC_MYSQL_ENABLE' => False,
			
		'ZC_MYSQL_SERVER' => 'localhost',								
		'ZC_MYSQL_USERNAME' => 'root',				
		'ZC_MYSQL_PASSWORD' => '',				
		'ZC_MYSQL_NAME' => 'zblog',				
		'ZC_MYSQL_CHARSET' => 'utf8',				
		'ZC_MYSQL_PRE' => 'zbp_',							
		'ZC_MYSQL_ENGINE'=>'MyISAM',

	// '---------------------------------插件----------------------------------------
	'ZC_USING_PLUGIN_LIST' => '',

	// '-------------------------------全局配置-----------------------------------
	'ZC_BLOG_CLSID' => '',
	'ZC_TIME_ZONE' => '+0800',
	'ZC_TIME_ZONE_NAME' => 'Asia/Shanghai',
	'ZC_UPDATE_INFO_URL' => 'http://update.rainbowsoft.org/info/',
	// '固定域名,默认为False,如启用则'ZC_BLOG_HOST生效而'ZC_MULTI_DOMAIN_SUPPORT无效
	'ZC_PERMANENT_DOMAIN_ENABLE' => False,
	'ZC_MULTI_DOMAIN_SUPPORT' => False,

	// '当前 Z-Blog 版本
	
	'ZC_BLOG_PRODUCT' => 'Z-BlogPHP',
	'ZC_BLOG_VERSION' => '1.0 Beta Build 130707',
	'ZC_BLOG_PRODUCT_FULL' => '',
	'ZC_BLOG_PRODUCT_FULLHTML' => '',


	// '留言评论
	'ZC_COMMENT_TURNOFF' => False,
	'ZC_COMMENT_VERIFY_ENABLE' => True,
	'ZC_COMMENT_REVERSE_ORDER_EXPORT' => False,
	'ZC_COMMNET_MAXFLOOR' => 8,

	// '验证码
	'ZC_VERIFYCODE_STRING' => '0123456789',
	'ZC_VERIFYCODE_WIDTH' => 60,
	'ZC_VERIFYCODE_HEIGHT' => 20,

	// '页面各项列数
	'ZC_DISPLAY_COUNT' => 5,
	'ZC_RSS2_COUNT' => 10,
	'ZC_SEARCH_COUNT' => 25,
	'ZC_PAGEBAR_COUNT' => 15,
	'ZC_MUTUALITY_COUNT' => 10,
	'ZC_COMMENTS_DISPLAY_COUNT' => 3,

	// '杂项
	'ZC_USE_NAVIGATE_ARTICLE' => True,
	'ZC_RSS_EXPORT_WHOLE' => True,
	'ZC_TEMPLATE_DIRECTORY' => 'template',

	// '后台管理
	'ZC_MANAGE_COUNT' => 50,
	'ZC_REBUILD_FILE_COUNT' => 50,
	'ZC_REBUILD_FILE_INTERVAL' => 1,

	// 'UBB转换
	'ZC_UBB_ENABLE' => True,
	'ZC_UBB_LINK_ENABLE' => False,
	'ZC_UBB_FONT_ENABLE' => True,
	'ZC_UBB_CODE_ENABLE' => True,
	'ZC_UBB_FACE_ENABLE' => True,
	'ZC_UBB_IMAGE_ENABLE' => True,
	'ZC_UBB_MEDIA_ENABLE' => True,
	'ZC_UBB_FLASH_ENABLE' => True,
	'ZC_UBB_TYPESET_ENABLE' => True,
	'ZC_UBB_AUTOLINK_ENABLE' => False,
	'ZC_UBB_AUTOKEY_ENABLE' => False,


	// '表情相关
	'ZC_EMOTICONS_FILENAME' => 'face',

	'ZC_EMOTICONS_FILETYPE' => 'png|gif|jpg',

	'ZC_EMOTICONS_FILESIZE' => '16',


	// '上传相关
	'ZC_UPLOAD_FILETYPE' => 'jpg|gif|png|jpeg|bmp|psd|wmf|ico|rpm|deb|tar|gz|sit|7z|bz2|zip|rar|xml|xsl|svg|svgz|doc|xls|wps|chm|txt|pdf|mp3|avi|mpg|rm|ra|rmvb|mov|wmv|wma|swf|fla|torrent|zpi|zti|zba',

	'ZC_UPLOAD_FILESIZE' => 10485760,

	'ZC_UPLOAD_DIRBYMONTH' => True,

	'ZC_UPLOAD_DIRECTORY' => 'zb_users/upload',


	// '用户名,密码,评论长度等限制
	'ZC_USERNAME_MIN' => 4,

	'ZC_USERNAME_MAX' => 14,

	'ZC_PASSWORD_MIN' => 8,

	'ZC_PASSWORD_MAX' => 14,

	'ZC_EMAIL_MAX' => 30,

	'ZC_HOMEPAGE_MAX' => 100,

	'ZC_CONTENT_MAX' => 1000,


	// '---------------------------------静态化配置-----------------------------------
	// '静态文件名{asp html shtml}
	'ZC_STATIC_TYPE' => 'html',

	'ZC_STATIC_DIRECTORY' => 'post',

	// '文章,页面类的静态模式ACTIVE or STATIC or REWRITE
	'ZC_POST_STATIC_MODE' => 'ACTIVE',

	// '列表页的静态模式ACTIVE or MIX or REWRITE
	'ZC_STATIC_MODE' => 'ACTIVE',

	'ZC_ARTICLE_REGEX' => '{%host%}/view.php?id={%id%}',

	'ZC_PAGE_REGEX' => '{%host%}/view.php?id={%id%}',

	'ZC_CATEGORY_REGEX' => '{%host%}/catalog.php?cate={%id%}',

	'ZC_USER_REGEX' => '{%host%}/catalog.php?auth={%id%}',

	'ZC_TAGS_REGEX' => '{%host%}/catalog.php?tags={%alias%}',

	'ZC_DATE_REGEX' => '{%host%}/catalog.php?date={%date%}',

	'ZC_DEFAULT_REGEX' => '{%host%}/catalog.php',

	// '--------------------------其它----------------------------------------
	// '代码高亮
	'ZC_SYNTAXHIGHLIGHTER_ENABLE' => True,

	// '源码编辑高亮
	'ZC_CODEMIRROR_ENABLE' => True,

	// '自动摘要字数
	'ZC_ARTICLE_EXCERPT_MAX' => 250,

	// '侧栏评论最大字数
	'ZC_COMMENT_EXCERPT_MAX' => 20,


	'ZC_HTTP_LASTMODIFIED' => False,

	'ZC_DEFAULT_PAGES_TEMPLATE' => '',

	'ZC_SIDEBAR_ORDER' => 'calendar:comments:controlpanel:searchpanel:archives:favorite:catalog:statistics:previous:tags:link:misc',

	'ZC_SIDEBAR_ORDER2' => '',

	'ZC_SIDEBAR_ORDER3' => '',

	'ZC_SIDEBAR_ORDER4' => '',

	'ZC_SIDEBAR_ORDER5' => '',

	//The default language of uEditor
	//The language files will be loaded from ue-lang\ZC_EDITORLANG by editor.
	'ZC_EDITORLANG'=>'zh-cn',
	'ZC_DEBUGMODE' => true
);
?>