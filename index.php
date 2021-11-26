<?php

/**
 *****************************************************************************************************
 *    如果您通过浏览器访问网站时看到了这个提示，那么我们很遗憾地通知您，您的空间不支持 PHP 。
 *    也就是说，您的空间可能是静态空间，或没有安装PHP，或没有为 Web 服务器打开 PHP 支持。
 *    Sorry, PHP is not installed on your web hosting if you see this prompt.
 *    Please check out the PHP configuration.
 *
 *    如您使用虚拟主机：
 *
 *        > 联系空间商，更换空间为支持 PHP 的空间。
 *        > Contact your service provider, and let them provice a new service which supports PHP.
 *
 *
 *    如您自行搭建服务器，推荐您：
 *    Configuring manually? Recommend:
 *
 *        > 访问 PHP 官方网站获取安装帮助。
 *        > Visit PHP Official Website to get the documentation of installion and configuration.
 *        > http://php.net
 *
 ******************************************************************************************************
 */

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 * @version
 */
require 'zb_system/function/c_system_base.php';

$zbp->RedirectInstall();
$zbp->Load();

HookFilterPlugin('Filter_Plugin_Index_Begin');

ViewIndex();

HookFilterPlugin('Filter_Plugin_Index_End');
