<?php
/**
 *
 *****************************************************************************************************
 *    如果您看到了这个提示，那么我们很遗憾地通知您，您的空间不支持 PHP 。
 *    We regret to inform you that your web hosting not support PHP,
 *    and Z-BlogPHP CAN'T run on it if you see this prompt.
 *
 *    也就是说，您的空间可能是静态空间，或没有安装PHP，或没有为 Web 服务器打开 PHP 支持。
 *    It means that you may have a web hosting service supporting only static resources.
 *    Is PHP successfully installed on your server?
 *    Or, is HTTP Server configured correctly?
 *
 *    如您使用虚拟主机：
 *
 *        > 联系空间商，更换空间为支持 PHP 的空间。
 *        > Contact your service provider, and let them provice a new service which supports PHP.
 *
 *    如您使用 IIS，推荐您：
 *    Using IIS? Recommend:
 *
 *        > 下载并安装Z-Blog  > http://www.zblogcn.com/zblog/
 *        > Try Z-Blog > http://www.zblogcn.com/zblog/
 *
 *    如您使用其它 HTTP 服务器，推荐您：
 *    Using other HTTP Server? Recommend:
 *
 *        > 访问 PHP 官方网站获取安装帮助。
 *        > Visit PHP Official Website to get the documentation of installion and configuration.
 *        > http://php.net
 *
 ******************************************************************************************************
 */

/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version
 */
require 'zb_system/function/c_system_base.php';

$zbp->RedirectInstall();
$zbp->CheckGzip();
$zbp->Load();
$zbp->RedirectPermanentDomain();
$zbp->CheckSiteClosed();

foreach ($GLOBALS['hooks']['Filter_Plugin_Index_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}

ViewIndex();

foreach ($GLOBALS['hooks']['Filter_Plugin_Index_End'] as $fpname => &$fpsignal) {
    $fpname();
}

RunTime();
