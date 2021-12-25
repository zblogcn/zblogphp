<?php

/*
 * 定义系统常、变量
 */
/*
 * 操作系统
 */
define('SYSTEM_UNKNOWN', 0);
define('SYSTEM_WINDOWS', 1);
define('SYSTEM_UNIX', 2);
define('SYSTEM_LINUX', 3);
define('SYSTEM_DARWIN', 4);
define('SYSTEM_CYGWIN', 5);
define('SYSTEM_BSD', 6);

/*
 * 网站服务器
 */
define('SERVER_UNKNOWN', 0);
define('SERVER_APACHE', 1);
define('SERVER_IIS', 2);
define('SERVER_NGINX', 3);
define('SERVER_LIGHTTPD', 4);
define('SERVER_KANGLE', 5);
define('SERVER_CADDY', 6);
define('SERVER_BUILTIN', 7);

/*
 * PHP引擎
 */
define('ENGINE_PHP', 1);
define('ENGINE_HHVM', 2);
define('PHP_SYSTEM', GetSystem());
define('PHP_SERVER', GetWebServer());
define('PHP_ENGINE', ENGINE_PHP);
define('IS_X64', (PHP_INT_SIZE === 8));

/*
 * 如果想获取准确的值，请zbp->Load后使用$zbp->isHttps
 * 此处仅为当前系统环境检测
 */
defined('HTTP_SCHEME') || define('HTTP_SCHEME', GetScheme($_SERVER));

/*
 * 兼容性策略
 */
define('IS_WINDOWS', PHP_SYSTEM === SYSTEM_WINDOWS);
define('IS_UNIX', PHP_SYSTEM === SYSTEM_UNIX);
define('IS_LINUX', PHP_SYSTEM === SYSTEM_LINUX);
define('IS_DARWIN', PHP_SYSTEM === SYSTEM_DARWIN);
define('IS_CYGWIN', PHP_SYSTEM === SYSTEM_CYGWIN);
define('IS_BSD', PHP_SYSTEM === SYSTEM_BSD);
define('IS_APACHE', PHP_SERVER === SERVER_APACHE);
define('IS_IIS', PHP_SERVER === SERVER_IIS);
define('IS_NGINX', PHP_SERVER === SERVER_NGINX);
define('IS_LIGHTTPD', PHP_SERVER === SERVER_LIGHTTPD);
define('IS_KANGLE', PHP_SERVER === SERVER_KANGLE);
define('IS_CADDY', PHP_SERVER === SERVER_CADDY);
define('IS_BUILTIN', PHP_SERVER === SERVER_BUILTIN);
define('IS_HHVM', PHP_ENGINE === ENGINE_HHVM);

define('IS_CLI', strtolower(php_sapi_name()) === 'cli');

define('IS_WORKERMAN', (IS_CLI && class_exists('Workerman\Worker')));
define('IS_SWOOLE', (IS_CLI && defined('SWOOLE_VERSION')));
define('IS_SCF', (getenv('SCF_RUNTIME') && getenv('SCF_FUNCTIONNAME')));

/*
 * 定义文章类型
 */
define('ZC_POST_TYPE_ARTICLE', 0); // 文章
define('ZC_POST_TYPE_PAGE', 1); // 页面
define('ZC_POST_TYPE_TWEET', 2); // 一句话
define('ZC_POST_TYPE_DISCUSSION', 3); // 讨论
define('ZC_POST_TYPE_LINK', 4); // 链接
define('ZC_POST_TYPE_MUSIC', 5); // 音乐
define('ZC_POST_TYPE_VIDEO', 6); // 视频
define('ZC_POST_TYPE_PHOTO', 7); // 照片
define('ZC_POST_TYPE_ALBUM', 8); // 相册

/*
 * 定义文章状态
 */
/*
 * 文章状态：公开发布
 */
define('ZC_POST_STATUS_PUBLIC', 0);
/*
 * 文章状态：草稿
 */
define('ZC_POST_STATUS_DRAFT', 1);
/*
 * 文章状态：审核
 */
define('ZC_POST_STATUS_AUDITING', 2);
/*
 * 用户状态：正常
 */
define('ZC_MEMBER_STATUS_NORMAL', 0);
/*
 * 用户状态：审核中
 */
define('ZC_MEMBER_STATUS_AUDITING', 1);
/*
 * 用户状态：已锁定
 */
define('ZC_MEMBER_STATUS_LOCKED', 2);
/*
 * 文章状态：私人
 */
define('ZC_POST_STATUS_PRIVATE', 4);
/*
 * 文章状态：加密
 */
define('ZC_POST_STATUS_PASSWORD', 8);

/*
 * 用户级别
 */
define('ZC_MEMBER_LEVER_HIGHEST', 1);
define('ZC_MEMBER_LEVER_LOWEST', 6);
define('ZC_MEMBER_LEVER_ADMINISTRATOR', 1);
define('ZC_MEMBER_LEVER_REDACTOR', 2);
define('ZC_MEMBER_LEVER_AUTHOR', 3);
define('ZC_MEMBER_LEVER_TEAMWORKER', 4);
define('ZC_MEMBER_LEVER_COMMENTATOR', 5);
define('ZC_MEMBER_LEVER_VISITOR', 6);
