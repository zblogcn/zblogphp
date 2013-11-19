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
	'ZC_BLOG_NAME' => '我的网站',
	'ZC_BLOG_SUBNAME' => 'Good Luck To You!',
	'ZC_BLOG_THEME' => 'default',
	'ZC_BLOG_CSS' => 'default',
	'ZC_BLOG_COPYRIGHT' => 'Copyright Your WebSite.Some Rights Reserved.',
	'ZC_BLOG_LANGUAGE' => 'zh-CN',
	'ZC_BLOG_LANGUAGEPACK' => 'SimpChinese',


	// '----------------------------数据库配置---------------------------------------
	//mysql|sqlite|sqlite3|pdo_mysql
	'ZC_DATABASE_TYPE'=> '',

	'ZC_SQLITE_NAME' => '',
	'ZC_SQLITE_PRE' => 'zbp_',
		
	'ZC_MYSQL_SERVER' => 'localhost',
	'ZC_MYSQL_USERNAME' => 'root',
	'ZC_MYSQL_PASSWORD' => '',
	'ZC_MYSQL_NAME' => '',
	'ZC_MYSQL_CHARSET' => 'utf8',
	'ZC_MYSQL_PRE' => 'zbp_',
	'ZC_MYSQL_ENGINE'=>'MyISAM',
    'ZC_MYSQL_PORT' => '3306', 
	// '---------------------------------插件----------------------------------------
	'ZC_USING_PLUGIN_LIST' => '',

	// '-------------------------------全局配置-----------------------------------
	'ZC_YUN_SITE'=>'',
	'ZC_DEBUG_MODE' => false,
	'ZC_BLOG_CLSID' => '',
	'ZC_TIME_ZONE_NAME' => 'Asia/Shanghai',
	'ZC_UPDATE_INFO_URL' => 'http://update.rainbowsoft.org/info/',
	// '固定域名,默认为false,如启用则'ZC_BLOG_HOST生效而'ZC_MULTI_DOMAIN_SUPPORT无效
	'ZC_PERMANENT_DOMAIN_ENABLE' => false,
	'ZC_MULTI_DOMAIN_SUPPORT' => false,

	// '当前 Z-Blog 版本
	
	'ZC_BLOG_PRODUCT' => 'Z-BlogPHP',
	'ZC_BLOG_VERSION' => '',
	'ZC_BLOG_PRODUCT_FULL' => '',
	'ZC_BLOG_PRODUCT_FULLHTML' => '',


	// '留言评论
	'ZC_COMMENT_TURNOFF' => false,
	'ZC_COMMENT_VERIFY_ENABLE' => false,
	'ZC_COMMENT_REVERSE_ORDER' => false,


	// '验证码
	'ZC_VERIFYCODE_STRING' => '0123456789',
	'ZC_VERIFYCODE_WIDTH' => 60,
	'ZC_VERIFYCODE_HEIGHT' => 20,

	// '页面各项列数
	'ZC_DISPLAY_COUNT' => 10,
	'ZC_SEARCH_COUNT' => 25,
	'ZC_PAGEBAR_COUNT' => 10,
	'ZC_COMMENTS_DISPLAY_COUNT' => 100,
	
	'ZC_DISPLAY_SUBCATEGORYS' => false,

	// '杂项
	'ZC_RSS2_COUNT' => 10,
	'ZC_RSS_EXPORT_WHOLE' => true,

	// '后台管理
	'ZC_MANAGE_COUNT' => 50,

	// '表情相关
	'ZC_EMOTICONS_FILENAME' => 'face',

	'ZC_EMOTICONS_FILETYPE' => 'png|gif|jpg',

	'ZC_EMOTICONS_FILESIZE' => '16',


	// '上传相关
	'ZC_UPLOAD_FILETYPE' => 'jpg|gif|png|jpeg|bmp|psd|wmf|ico|rpm|deb|tar|gz|sit|7z|bz2|zip|rar|xml|xsl|svg|svgz|doc|docx|ppt|pptx|xls|xlsx|wps|chm|txt|pdf|mp3|avi|mpg|rm|ra|rmvb|mov|wmv|wma|swf|fla|torrent|apk|zba',

	'ZC_UPLOAD_FILESIZE' => 2,

	// '用户名,密码,评论长度等限制
	'ZC_USERNAME_MIN' => 3,

	'ZC_USERNAME_MAX' => 20,

	'ZC_PASSWORD_MIN' => 8,

	'ZC_PASSWORD_MAX' => 20,

	'ZC_EMAIL_MAX' => 30,

	'ZC_HOMEPAGE_MAX' => 100,

	'ZC_CONTENT_MAX' => 1000,

	// '自动摘要字数
	'ZC_ARTICLE_EXCERPT_MAX' => 250,

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

	#首页，分类页，文章页，页面页的默认模板
	'ZC_INDEX_DEFAULT_TEMPLATE' => 'index',
	'ZC_POST_DEFAULT_TEMPLATE' => 'single',
	
	'ZC_SIDEBAR_ORDER' => 'calendar|controlpanel|catalog|searchpanel|comments|archives|favorite|link|misc',

	'ZC_SIDEBAR2_ORDER' => '',

	'ZC_SIDEBAR3_ORDER' => '',

	'ZC_SIDEBAR4_ORDER' => '',

	'ZC_SIDEBAR5_ORDER' => '',
	// '--------------------------其它----------------------------------------
	// '代码高亮
	'ZC_SYNTAXHIGHLIGHTER_ENABLE' => true,

	// '源码编辑高亮
	'ZC_CODEMIRROR_ENABLE' => true,


	'ZC_HTTP_LASTMODIFIED' => false,
)
?>