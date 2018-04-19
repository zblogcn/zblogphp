<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version 2.0 2013-07-05
 */
/**
 *****************************************************************************************************
 *    如果您看到了这个提示，那么我们很遗憾地通知您，您的空间不支持 PHP 。
 *    也就是说，您的空间可能是静态空间，或没有安装PHP，或没有为 Web 服务器打开 PHP 支持。
 *    Sorry, PHP is not installed on your web hosting if you see this prompt.
 *    Please check out the PHP configuration.
 *
 *    如您使用虚拟主机：
 *
 *        > 联系空间商，更换空间为支持 PHP 的空间。
 *        > Contact your service provider, and let them provice a new service which supports PHP.
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
 * 安装程序.
 *
 * @param
 *
 * @return array
 */
date_default_timezone_set('UTC');

require '../zb_system/function/c_system_base.php';
require '../zb_system/function/c_system_admin.php';

header('Content-type: text/html; charset=utf-8');

define('bingo', '<span class="bingo"></span>');
define('error', '<span class="error"></span>');

$zbloglang = &$zbp->option['ZC_BLOG_LANGUAGEPACK'];
if (isset($_POST['zbloglang'])) {
    $zbloglang = FilterCorrectName($_POST['zbloglang']);
}

$zbp->LoadLanguage('system', '', $zbloglang);
$zbp->LoadLanguage('zb_install', 'zb_install', $zbloglang);
$zbp->option['ZC_BLOG_LANGUAGE'] = $zbp->lang['lang'];
$zblogstep = (int) GetVars('step');
if ($zblogstep == 0) {
    $zblogstep = 1;
}

if (($zbp->option['ZC_DATABASE_TYPE'] !== '') && ($zbp->option['ZC_YUN_SITE'] == '')) {
    $zblogstep = 0;
} elseif (($zbp->option['ZC_DATABASE_TYPE']) && ($zbp->option['ZC_YUN_SITE'])) {
    if ($zbp->Config('system')->CountItem() > 0) {
        $zblogstep = 0;
    }
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $zbp->option['ZC_BLOG_LANGUAGE']; ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<meta name="generator" content="Z-BlogPHP" />
<meta name="robots" content="noindex,nofollow"/>
<script src="../zb_system/script/common.js" type="text/javascript"></script>
<script src="../zb_system/script/c_admin_js_add.php" type="text/javascript"></script>
<script src="../zb_system/script/md5.js" type="text/javascript"></script>
<script src="../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../zb_system/css/jquery-ui.custom.css"  type="text/css" media="screen" />
<link rel="stylesheet" href="../zb_system/css/admin3.css" type="text/css" media="screen" />
<title>Z-BlogPHP <?php echo ZC_BLOG_VERSION . ' ' . $zbp->lang['zb_install']['install_program']?> </title>
<?php
Include_AddonAdminFont();
?>
</head>
<body>
<div class="setup">
  <form method="post" action="./index.php?step=<?php echo $zblogstep + 1; ?>">
    <input type="hidden" name="zbloglang" id="zbloglang" value="<?php echo $zbloglang; ?>"/>
    <?php

    switch ($zblogstep) {
        case 0:
            Setup0();
            break;
        case 1:
            Setup1();
            break;
        case 2:
            Setup2();
            break;
        case 3:
            Setup3();
            break;
        case 4:
            Setup4();
            break;
        case 5:
            Setup5();
            break;
    }

?>
  </form>
</div>
<script type="text/javascript">

$( "#language" ).change(function() {
    $("#zbloglang").val($(this).val());
    $("form").attr('action','./index.php');
    $("form").submit();
});

function Setup3(){

  if($("input[name='fdbtype']:checked").val()=="mysql"){
    if($("#dbmsyql_server").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbserver_need']; ?>");return false;};
    if($("#dbmysql_name").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbname_need']; ?>");return false;};
    if($("#dbmysql_username").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbusername_need']; ?>");return false;};
  }
  if($("input[name='fdbtype']:checked").val()=="pgsql"){
    if($("#dbpgsql_server").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbserver_need']; ?>");return false;};
    if($("#dbpgsql_name").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbname_need']; ?>");return false;};
    if($("#dbpgsql_username").val()==""){alert("<?php echo $zbp->lang['zb_install']['dbusername_need']; ?>");return false;};
  }

  if($("#blogtitle").val()==""){alert("<?php echo $zbp->lang['zb_install']['sitetitle_need']; ?>");return false;};
  if($("#username").val()==""){alert("<?php echo $zbp->lang['zb_install']['adminusername_need']; ?>");return false;};
  if($("#password").val()==""){alert("<?php echo $zbp->lang['zb_install']['adminpassword_need']; ?>");return false;};
  if($("#password").val().toString().search("^[A-Za-z0-9`~!@#\$%\^&\*\-_]{8,}$")==-1){alert("<?php echo $zbp->lang['error']['54']; ?>");return false;};
  if($("#password").val()!==$("#repassword").val()){alert("<?php echo $zbp->lang['error']['73']; ?>");return false;};

}

$(function() {
  $( "#setup0" ).progressbar({value: 100});
  $( "#setup1" ).progressbar({value: 0});
  $( "#setup2" ).progressbar({value: 33});
  $( "#setup3" ).progressbar({value: 66});
  $( "#setup4" ).progressbar({value: 100});
 });

</script>
</body>
</html>
<?php
function Setup0()
{
    global $zbp; ?>
<dl>
  <dt></dt>
  <dd id="ddleft"><div id="headerimg"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP" />
  <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
    <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
    <div id="setup0"  class="left"></div>
    <p><?php echo $zbp->lang['zb_install']['install_license']; ?> » <?php echo $zbp->lang['zb_install']['environment_check']; ?> » <?php echo $zbp->lang['zb_install']['db_build_set']; ?> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
  </dd>
  <dd id="ddright">
      <p style="float:left;clear:both;width:100%;text-align:right;padding-bottom:0.5em;"><b><?php echo $zbp->lang['zb_install']['language']; ?></b>&nbsp;<select id="language" name="language" style="width:150px;" >
<?php echo CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK']); ?>
      </select></p>
    <div id="title"><?php echo $zbp->lang['zb_install']['install_tips']; ?></div>
    <div id="content"><?php echo $zbp->lang['zb_install']['install_disable']; ?></div>
    <div id="bottom">
      <input type="button" name="next" onclick="window.location.href='../'" id="netx" value="<?php echo $zbp->lang['zb_install']['exit']; ?>" />
    </div>
  </dd>
</dl>
<?php
}

function Setup1()
{
    global $zbp; ?>
<dl>
  <dt></dt>
  <dd id="ddleft"><div id="headerimg"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP" />
  <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
    <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
    <div id="setup1"  class="left"></div>
    <p><b><?php echo $zbp->lang['zb_install']['install_license']; ?></b> » <?php echo $zbp->lang['zb_install']['environment_check']; ?> » <?php echo $zbp->lang['zb_install']['db_build_set']; ?> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
  </dd>
  <dd id="ddright">
      <p style="float:left;clear:both;width:100%;text-align:right;padding-bottom:0.5em;"><b><?php echo $zbp->lang['zb_install']['language']; ?></b>&nbsp;<select id="language" name="language" style="width:150px;" >
<?php echo CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK']); ?>
      </select></p>
    <div id="title">Z-BlogPHP <?php echo ZC_BLOG_VERSION . ' ' . $zbp->lang['zb_install']['install_license']?></div>
    <div id="content">
      <textarea readonly>
<?php echo $zbp->lang['zb_install']['license_title'] . "\r\n"; ?>

感谢您选择Z-BlogPHP。 Z-BlogPHP 基于 PHP 的技术开发，采用 MySQL 或 SQLite 或 PostgreSQL 作为数据库，全部源码开放。希望我们的努力能为您提供一个高效快速、强大的站点解决方案。

Z-BlogPHP官方网址：http://www.zblogcn.com/

为了使您正确并合法的使用本软件，请您在使用前务必阅读清楚下面的协议条款：

一、本授权协议适用且仅适用于 Z-BlogPHP ，Rainbow Studio官方对本授权协议拥有最终解释权。

二、协议许可的权利

1. 本程序基于 MIT 协议开源，您可以在 MIT 协议允许的范围内对源代码进行使用，包括源代码或界面风格以适应您的网站要求。
2. 您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。
3. 您可以从 Z-BlogPHP 提供的应用中心服务中下载适合您网站的应用程序，但应向应用程序开发者/所有者支付相应的费用。
4. 本程序在云主机（新浪SAE、百度BAE、阿里云等）使用的相关授权费用由 RainbowSoft 另行规定。

三、协议规定的约束和限制

1. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用 Z-BlogPHP 程序本身，未经书面许可，必须保留页面底部的版权（Powered by Z-BlogPHP），不得删除；但可以以任何访客可见的形式对其进行修改和美化。
2. 您从应用中心下载的应用程序，未经应用程序开发者的书面许可，不得对其进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等。
3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。

四、有限担保和免责声明

1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3. 电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装 Z-BlogPHP ，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
4. 如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。

版权所有 ©2005 - 2015，RainbowSoft Studio 保留所有权利。

协议发布时间：2013 年 8 月 1 日
版本最新更新：2015 年 7 月 31 日 By RainbowSoft Studio


  </textarea>
    </div>
    <div id="bottom">
      <label>
        <input type="checkbox"/>
        <?php echo $zbp->lang['zb_install']['i_agree']; ?></label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="next" id="netx" value="<?php echo $zbp->lang['zb_install']['next']; ?>" disabled="disabled" />
      <script type="text/javascript">
$( "input[type=checkbox]" ).change(function() {
  if ( $( this ).prop( "checked" ) ) {
    $("#netx").prop("disabled",false);
  }
  else{
    $("#netx").prop("disabled",true);
  }
});
</script>
    </div>
  </dd>
</dl>
<?php
}

function Setup2()
{
    global $zbp;
    CheckServer(); ?>
<dl>
  <dt></dt>
  <dd id="ddleft"><div id="headerimg"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP" />
  <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
    <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
    <div id="setup2"  class="left"></div>
    <p><b><?php echo $zbp->lang['zb_install']['install_license']; ?></b> » <b><?php echo $zbp->lang['zb_install']['environment_check']; ?></b> » <?php echo $zbp->lang['zb_install']['db_build_set']; ?> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
  </dd>
  <dd id="ddright">
    <div id="title"><?php echo $zbp->lang['zb_install']['']; ?><?php echo $zbp->lang['zb_install']['environment_check']; ?></div>
    <div id="content">
      <table border="0" style="width:100%;" class="table_striped table_hover">
        <tr>
          <th colspan="3" scope="row"><?php echo $zbp->lang['zb_install']['server_check']; ?></th>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['http_server']; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['server'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['server'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['php_version']; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['phpver'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['phpver'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">Z-BlogPHP <?php echo $zbp->lang['zb_install']['path']; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zbppath'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zbppath'][1]; ?></td>
        </tr>
        <tr>
          <th colspan="3" scope="col"><?php echo $zbp->lang['zb_install']['lib_check']; ?></th>
        </tr>
        <tr>
          <td scope="row" style="width:200px">PCRE</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pcre'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pcre'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row" style="width:200px">gd2</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gd2'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gd2'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row" style="width:200px">mbstring</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mbstring'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mbstring'][1]; ?></td>
        </tr>        
<?php if (version_compare(PHP_VERSION, '5.5.0', '<')) {
        ?>
        <tr>
          <td scope="row">mysql</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][1]; ?></td>
        </tr>
<?php
    } ?>
        <tr>
          <td scope="row">mysqli</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysqli'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysqli'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">sqlite3</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">sqlite</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">pgsql</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pgsql'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pgsql'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">pdo_mysql</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">pdo_sqlite</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_sqlite'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_sqlite'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">pdo_pgsql</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_pgsql'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_pgsql'][1]; ?></td>
        </tr>
        <tr>
          <th colspan="3" scope="row"><?php echo $zbp->lang['zb_install']['permission_check']; ?></th>
        </tr>
        <tr>
          <td scope="row">zb_users</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zb_users'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zb_users'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/cache</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['cache'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['cache'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/data</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['data'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['data'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/theme</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['theme'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['theme'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/plugin</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['plugin'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['plugin'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/upload</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['upload'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['upload'][1]; ?></td>
        </tr>
        <tr>
          <th colspan="3" scope="row"><?php echo $zbp->lang['zb_install']['function_check']; ?></th>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['environment_network']?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['network'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['network'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['environment_xml']?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['simplexml_load_string'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['simplexml_load_string'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['environment_json']?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['json_decode'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['json_decode'][1]; ?></td>
        </tr>
        <tr>
          <td scope="row"><?php echo $zbp->lang['zb_install']['environment_iconv']?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['iconv'][0]; ?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['iconv'][1]; ?></td>
        </tr>
      </table>
    </div>
    <div id="bottom">
      <input type="submit" name="next" id="netx" value="<?php echo $zbp->lang['zb_install']['next']; ?>" />
    </div>
  </dd>
</dl>
<?php
}

function Setup3()
{
    global $zbp;
    global $CheckResult, $option;
    CheckServer();

    $hasMysql = false;

    $hasSqlite = false;

    $hasPgsql = false;

    $hasMysql = (bool) ((bool) ($CheckResult['mysql'][0]) or (bool) ($CheckResult['mysqli'][0]) or (bool) ($CheckResult['pdo_mysql'][0]));

    $hasSqlite = (bool) ((bool) ($CheckResult['sqlite3'][0]) or (bool) ($CheckResult['sqlite'][0]) or (bool) ($CheckResult['pdo_sqlite'][0]));

    $hasPgsql = (bool) ((bool) ($CheckResult['pgsql'][0]) or (bool) ($CheckResult['pdo_pgsql'][0])); ?>
<dl>
  <dt></dt>
  <dd id="ddleft"><div id="headerimg"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP" />
  <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
    <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
    <div id="setup3"  class="left"></div>
    <p><b><?php echo $zbp->lang['zb_install']['install_license']; ?></b> » <b><?php echo $zbp->lang['zb_install']['environment_check']; ?></b> » <b><?php echo $zbp->lang['zb_install']['db_build_set']; ?></b> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
  </dd>
  <dd id="ddright">
    <div id="title"><?php echo $zbp->lang['zb_install']['db_build_set']; ?></div>
    <div id="content">
      <div>
        <p><b><?php echo $zbp->lang['zb_install']['db_database']; ?></b>
        <?php
        if ($hasMysql) {
            ?>
                  <label class="dbselect" id="mysql_radio">
                  <input type="radio" name="fdbtype" value="mysql"/> MySQL</label>
                <?php
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        } ?>
        <?php
        if ($hasSqlite) {
            ?>
                  <label class="dbselect" id="sqlite_radio">
                  <input type="radio" name="fdbtype" value="sqlite"/> SQLite</label>
                <?php
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        } ?>
        </p>
      </div>
        <?php if ($hasMysql) {
            ?>
      <div class="dbdetail" id="mysql">
        <p><b><?php echo $zbp->lang['zb_install']['db_server']; ?></b>
          <input type="text" name="dbmysql_server" id="dbmysql_server" value="<?php echo $option['ZC_MYSQL_SERVER']; ?>" style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['db_username']; ?></b>
          <input type="text" name="dbmysql_username" id="dbmysql_username" value="<?php echo $option['ZC_MYSQL_USERNAME']; ?>" style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['db_password']; ?></b>
          <input type="password" name="dbmysql_password" id="dbmysql_password" value="<?php echo $option['ZC_MYSQL_PASSWORD']; ?>" style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['db_name']; ?></b>
          <input type="text" name="dbmysql_name" id="dbmysql_name" value="<?php echo $option['ZC_MYSQL_NAME']; ?>" style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['db_pre']; ?></b>
          <input type="text" name="dbmysql_pre" id="dbmysql_pre" value="<?php echo $option['ZC_MYSQL_PRE']; ?>" style="width:350px;" />
        </p>
<?php if ($zbp->option['ZC_YUN_SITE'] == '') {
                ?>
        <p><b><?php echo $zbp->lang['zb_install']['db_engine']; ?></b>
          <select id="dbengine" name="dbengine" style="width:360px;" >
          <option value="MyISAM" selected>MyISAM(<?php echo $zbp->lang['msg']['default']; ?>)</option>
          <option value="InnoDB" >InnoDB</option>
        </select>
        </p>
<?php
            } ?>
      <p><b><?php echo $zbp->lang['zb_install']['db_drive']; ?></b>
        <?php if ($CheckResult['mysqli'][0]) {
                ?>
        <label>
          <input value="mysqli" type="radio" name="dbtype"/> mysqli</label>
        <?php
            } ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php if ($CheckResult['pdo_mysql'][0]) {
                ?>
        <label>
          <input value="pdo_mysql" type="radio" name="dbtype"/> pdo_mysql</label>
        <?php
            } ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                ?>
        <?php if ($CheckResult['mysql'][0] && !$CheckResult['mysqli'][0] && !$CheckResult['pdo_mysql'][0]) { // 强制淘汰mysql?>
        <label>
          <input value="mysql" type="radio" name="dbtype"/> mysql</label>
        <?php
                } ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
            } ?>
        <br/><small><?php echo $zbp->lang['zb_install']['db_set_port']; ?></small>
      </p>
      </div>
        <?php
        } ?>

        <?php if ($hasSqlite) {
            ?>
      <div class="dbdetail" id="sqlite">
        <p><b><?php echo $zbp->lang['zb_install']['db_name']; ?></b>
          <input type="text" name="dbsqlite_name" id="dbsqlite_name" value="<?php echo GetDbName()?>" readonly style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['db_pre']; ?></b>
          <input type="text" name="dbsqlite_pre" id="dbsqlite_pre" value="zbp_" style="width:350px;" />
        </p>
      <p><b><?php echo $zbp->lang['zb_install']['db_drive']; ?></b>
        <?php if ($CheckResult['sqlite3'][0]) {
                ?>
        <label>
          <input value="sqlite3" type="radio" name="dbtype" /> sqlite3</label>
        <?php
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            } ?>
        <?php if ($CheckResult['pdo_sqlite'][0]) {
                ?>
        <label>
          <input value="pdo_sqlite" type="radio" name="dbtype" /> pdo_sqlite</label>
        <?php
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            } ?>
        <?php if ($CheckResult['sqlite'][0]) {
                ?>
        <label>
          <input value="sqlite" type="radio" name="dbtype" /> sqlite</label>
        <?php
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            } ?>
      </p>
      </div>
        <?php
        } ?>

      <p class="title"><?php echo $zbp->lang['zb_install']['website_setting']; ?></p>
      <p><b><?php echo $zbp->lang['zb_install']['blog_name']; ?></b>
        <input type="text" name="blogtitle" id="blogtitle" value="" style="width:350px;" />
      </p>
      <p><b><?php echo $zbp->lang['zb_install']['admin_username']; ?></b>
        <input type="text" name="username" id="username" value="" style="width:200px;" />
        &nbsp;<small><?php echo $zbp->lang['zb_install']['username_intro']; ?></small></p>
      <p><b><?php echo $zbp->lang['zb_install']['admin_password']; ?></b>
        <input type="password" name="password" id="password" value="" style="width:200px;" />
        &nbsp;<small><?php echo $zbp->lang['zb_install']['password_intro']; ?></small></p>
      <p><b><?php echo $zbp->lang['zb_install']['re_password']; ?></b>
        <input type="password" name="repassword" id="repassword" value="" style="width:200px;" />
      </p>
    </div>
    <div id="bottom">
      <input type="submit" name="next" id="netx" onClick="return Setup3()" value="<?php echo $zbp->lang['zb_install']['next']; ?>" />
    </div>
  </dd>
</dl>
<script type="text/javascript">
$(".dbselect").click(function(){
  $(".dbdetail").hide();
  $("#"+$(this).attr("id").split("_radio")[0]).show();
  $("input[name='dbtype']:visible").get(0).click();
});
$(".dbdetail").hide();
$("#"+$(".dbselect").attr("id").split("_radio")[0]).show();
$("input[name='dbtype']:visible").get(0).click();
$("input[name='fdbtype']:visible").get(0).click();
</script>
<?php
}

//###############################################################################################################
//4
function Setup4()
{
    global $zbp; ?>
<dl>
  <dt></dt>
  <dd id="ddleft"><div id="headerimg"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP" />
  <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
    <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
    <div id="setup4"  class="left"></div>
    <p><b><?php echo $zbp->lang['zb_install']['install_license']; ?></b> » <b><?php echo $zbp->lang['zb_install']['environment_check']; ?></b> » <b><?php echo $zbp->lang['zb_install']['db_build_set']; ?></b> » <b><?php echo $zbp->lang['zb_install']['install_result']; ?></b></p>
  </dd>
  <dd id="ddright">
    <div id="title"><?php echo $zbp->lang['zb_install']['install_result']; ?></div>
    <div id="content">
        <?php
        $isInstallFlag = true;
    $zbp->option['ZC_DATABASE_TYPE'] = GetVars('dbtype', 'POST');

    $cts = '';

    switch ($zbp->option['ZC_DATABASE_TYPE']) {
            case 'mysql':
            case 'mysqli':
            case 'pdo_mysql':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/mysql.sql');

                if ($zbp->option['ZC_YUN_SITE'] != '') {
                    break;
                }

                $zbp->option['ZC_MYSQL_SERVER'] = GetVars('dbmysql_server', 'POST');
                if (strpos($zbp->option['ZC_MYSQL_SERVER'], ':') !== false) {
                    $servers = explode(':', $zbp->option['ZC_MYSQL_SERVER']);
                    $zbp->option['ZC_MYSQL_SERVER'] = trim($servers[0]);
                    $zbp->option['ZC_MYSQL_PORT'] = (int) $servers[1];
                    if ($zbp->option['ZC_MYSQL_PORT'] == 0) {
                        $zbp->option['ZC_MYSQL_PORT'] = 3306;
                    }

                    unset($servers);
                }
                $zbp->option['ZC_MYSQL_USERNAME'] = trim(GetVars('dbmysql_username', 'POST'));
                $zbp->option['ZC_MYSQL_PASSWORD'] = trim(GetVars('dbmysql_password', 'POST'));
                $zbp->option['ZC_MYSQL_NAME'] = trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbmysql_name', 'POST')));
                $zbp->option['ZC_MYSQL_PRE'] = trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbmysql_pre', 'POST')));
                if ($zbp->option['ZC_MYSQL_PRE'] == '') {
                    $zbp->option['ZC_MYSQL_PRE'] == 'zbp_';
                }

                $zbp->option['ZC_MYSQL_ENGINE'] = GetVars('dbengine', 'POST');
                $cts = str_replace('MyISAM', $zbp->option['ZC_MYSQL_ENGINE'], $cts);

                $zbp->db = ZBlogPHP::InitializeDB($zbp->option['ZC_DATABASE_TYPE']);
                if ($zbp->db->CreateDB($zbp->option['ZC_MYSQL_SERVER'], $zbp->option['ZC_MYSQL_PORT'], $zbp->option['ZC_MYSQL_USERNAME'], $zbp->option['ZC_MYSQL_PASSWORD'], $zbp->option['ZC_MYSQL_NAME']) == true) {
                    echo $zbp->lang['zb_install']['create_db'] . $zbp->option['ZC_MYSQL_NAME'] . "<br/>";
                }
                $zbp->db->dbpre = $zbp->option['ZC_MYSQL_PRE'];
                $zbp->db->Close();

                break;
            case 'sqlite':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/sqlite.sql');
                $cts = str_replace(' autoincrement', '', $cts);
                $zbp->option['ZC_SQLITE_NAME'] = trim(GetVars('dbsqlite_name', 'POST'));
                $zbp->option['ZC_SQLITE_PRE'] = trim(GetVars('dbsqlite_pre', 'POST'));
                break;
            case 'sqlite3':
            case 'pdo_sqlite':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/sqlite.sql');
                $zbp->option['ZC_SQLITE_NAME'] = trim(GetVars('dbsqlite_name', 'POST'));
                $zbp->option['ZC_SQLITE_PRE'] = trim(GetVars('dbsqlite_pre', 'POST'));
                break;
            default:
                $isInstallFlag = false;
                break;
        }
    $hasError = false;
    if ($isInstallFlag) {
        $zbp->OpenConnect();
        $zbp->ConvertTableAndDatainfo();
        if (CreateTable($cts)) {
            if (InsertInfo()) {
                if (SaveConfig()) {
                    //ok
                } else {
                    //$hasError = true;
                }
            } else {
                $hasError = true;
            }
        } else {
            $hasError = true;
        }

        $zbp->CloseConnect();
    } else {
        $hasError = true;
    } ?>

    </div>
    <div id="bottom">
        <?php
        if ($hasError == true) {
            echo '<p><a href="javascript:history.go(-1)">' . $zbp->lang['zb_install']['clicktoback'] . '</a></p>';
        } else {
            ?>
        <input type="button" name="next" onClick="window.location.href='../'" id="netx" value="<?php echo $zbp->lang['zb_install']['ok']; ?>" />
        <?php
        } ?>
    </div>
  </dd>
</dl>
<?php
}

function Setup5()
{
    global $zbp;
    header('Location: ' . $zbp->host);
}

$CheckResult = null;

function CheckServer()
{
    global $zbp;
    global $CheckResult;

    $CheckResult = array(
        //服务器
        'server'  => array(GetVars('SERVER_SOFTWARE', 'SERVER'), bingo),
        'phpver'  => array(PHP_VERSION, ''),
        'zbppath' => array($zbp->path, bingo),
        //组件
        'pcre'       => array('', ''),
        'gd2'        => array('', ''),
        'mysql'      => array('', ''),
        'mysqli'     => array('', ''),
        'pdo_mysql'  => array('', ''),
        'sqlite'     => array('', ''),
        'sqlite3'    => array('', ''),
        'pdo_sqlite' => array('', ''),
        'pgsql'      => array('', ''),
        'pdo_pgsql'  => array('', ''),
        'mbstring'   => array('', ''),
        //权限
        'zb_users'     => array('', ''),
        'cache'        => array('', ''),
        'data'         => array('', ''),
        'include'      => array('', ''),
        'theme'        => array('', ''),
        'plugin'       => array('', ''),
        'upload'       => array('', ''),
        'c_option.php' => array('', ''),
        //函数
        'curl'            => array($zbp->lang['zb_install']['connect_appcenter'], ''),
        'allow_url_fopen' => array($zbp->lang['zb_install']['connect_appcenter'], ''),
        'gethostbyname'   => array($zbp->lang['zb_install']['whois_dns'], ''),

    );

    if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
        $CheckResult['phpver'][1] = bingo;
    } else {
        $CheckResult['phpver'][1] = error;
    }

    //针对PCRE老版本报错
    $pv = explode(' ', PCRE_VERSION);
    $CheckResult['pcre'][0] = PCRE_VERSION;
    if (version_compare($pv[0], '6.6') <= 0) {
        $CheckResult['pcre'][1] = error;
    } else {
        $CheckResult['pcre'][1] = bingo;
    }

    if (function_exists("gd_info")) {
        $info = gd_info();
        $CheckResult['gd2'][0] = $info['GD Version'];
        $CheckResult['gd2'][1] = $CheckResult['gd2'][0] ? bingo : error;
    }
    if (function_exists("mysql_get_client_info")) {
        $CheckResult['mysql'][0] = strtok(mysql_get_client_info(), '$');
        $CheckResult['mysql'][1] = $CheckResult['mysql'][0] ? bingo : error;
    }
    if (function_exists("mysqli_get_client_info")) {
        $CheckResult['mysqli'][0] = strtok(mysqli_get_client_info(), '$');
        $CheckResult['mysqli'][1] = $CheckResult['mysqli'][0] ? bingo : error;
    }
    if (function_exists("mb_language")) {
        $CheckResult['mbstring'][0] = mb_language();
        $CheckResult['mbstring'][1] = $CheckResult['mbstring'][0] ? bingo : error;
    }
    if (class_exists("PDO", false)) {
        if (extension_loaded('pdo_mysql')) {
            //$pdo = new PDO( 'mysql:');
            $v = ' '; //strtok($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),'$');
            $pdo = null;
            $CheckResult['pdo_mysql'][0] = $v;
            $CheckResult['pdo_mysql'][1] = $CheckResult['pdo_mysql'][0] ? bingo : error;
        }

        if (extension_loaded('pdo_sqlite')) {
            //$pdo = new PDO('sqlite::memory:');
            $v = ' '; //$pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
            $pdo = null;
            $CheckResult['pdo_sqlite'][0] = $v;
            $CheckResult['pdo_sqlite'][1] = $CheckResult['pdo_sqlite'][0] ? bingo : error;
        }

        if (extension_loaded('pdo_pgsql')) {
            $v = ' ';
            $pdo = null;
            $CheckResult['pdo_pgsql'][0] = $v;
            $CheckResult['pdo_pgsql'][1] = $CheckResult['pdo_pgsql'][0] ? bingo : error;
        }
    }
    if (defined("PGSQL_STATUS_STRING")) {
        $CheckResult['pgsql'][0] = PGSQL_STATUS_STRING;
        $CheckResult['pgsql'][1] = $CheckResult['pgsql'][0] ? bingo : error;
    }
    if (function_exists("sqlite_libversion")) {
        $CheckResult['sqlite'][0] = sqlite_libversion();
        $CheckResult['sqlite'][1] = $CheckResult['sqlite'][0] ? bingo : error;
    }
    if (class_exists('SQLite3', false)) {
        $info = SQLite3::version();
        $CheckResult['sqlite3'][0] = $info['versionString'];
        $CheckResult['sqlite3'][1] = $CheckResult['sqlite3'][0] ? bingo : error;
    }

    getRightsAndExport('', 'zb_users');
    getRightsAndExport('zb_users/', 'cache');
    getRightsAndExport('zb_users/', 'data');
    getRightsAndExport('zb_users/', 'theme');
    getRightsAndExport('zb_users/', 'plugin');
    getRightsAndExport('zb_users/', 'upload');
    //getRightsAndExport('zb_users/','c_option.php');

    $CheckResult['network'][0] = $zbp->lang['zb_install']['environment_network_description'];
    $CheckResult['simplexml_load_string'][0] = $zbp->lang['zb_install']['environment_xml_description'];
    $CheckResult['json_decode'][0] = $zbp->lang['zb_install']['environment_json_description'];
    $CheckResult['iconv'][0] = $zbp->lang['zb_install']['environment_iconv_description'];
    $CheckResult['mb_strlen'][0] = $zbp->lang['zb_install']['environment_mb_description'];

    $networkTest = Network::create();
    $CheckResult['network'][1] = ($networkTest == false) ? error : bingo;
    $CheckResult['simplexml_load_string'][1] = function_exists('simplexml_load_string') ? bingo : error;
    $CheckResult['json_decode'][1] = function_exists('json_decode') ? bingo : error;
    $CheckResult['iconv'][1] = function_exists('iconv') ? bingo : error;
    $CheckResult['mb_strlen'][1] = function_exists('mb_strlen') ? bingo : error;
}

function getRightsAndExport($folderparent, $folder)
{
    global $zbp;
    $s = GetFilePerms($zbp->path . $folderparent . $folder);
    $o = GetFilePermsOct($zbp->path . $folderparent . $folder);
    $GLOBALS['CheckResult'][$folder][0] = $s . ' | ' . $o;

    if (substr($s, 0, 1) == '-') {
        $GLOBALS['CheckResult'][$folder][1] = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r') ? bingo : error;
    } else {
        $GLOBALS['CheckResult'][$folder][1] = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 3, 1) == 'x' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r' && substr($s, 6, 1) == 'x' && substr($s, 9, 1) == 'x') ? bingo : error;
    }
}

function CreateTable($sql)
{
    global $zbp;

    if ($zbp->db->ExistTable($GLOBALS['table']['Config']) == true) {
        echo $zbp->lang['zb_install']['exist_table_in_db'];

        return false;
    }

    $sql = $zbp->db->sql->ReplacePre($sql);
    $zbp->db->QueryMulit($sql);

    if ($zbp->db->ExistTable($GLOBALS['table']['Config']) == false) {
        echo $zbp->lang['zb_install']['not_create_table'];

        return false;
    }

    echo $zbp->lang['zb_install']['create_table'] . "<br/>";

    return true;
}

function InsertInfo()
{
    global $zbp;

    $zbp->guid = GetGuid();

    $mem = new Member();
    $guid = GetGuid();

    $mem->Guid = $guid;
    $mem->Level = 1;
    $mem->Name = GetVars('username', 'POST');
    $mem->Password = Member::GetPassWordByGuid(GetVars('password', 'POST'), $guid);
    $mem->IP = GetGuestIP();
    $mem->PostTime = time();

    FilterMember($mem);
    $mem->Save();

    $cate = new Category();
    $cate->Name = $zbp->lang['msg']['uncategory'];
    $cate->Alias = 'uncategorized';
    $cate->Count = 1;
    $cate->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_navbar'];
    $t->FileName = "navbar";
    $t->Source = "system";
    $t->SidebarID = 0;
    $t->Content = '<li id="nvabar-item-index"><a href="{#ZC_BLOG_HOST#}">' . $zbp->lang['zb_install']['index'] . '</a></li><li id="navbar-page-2"><a href="{#ZC_BLOG_HOST#}?id=2">' . $zbp->lang['zb_install']['guestbook'] . '</a></li>';
    $t->HtmlID = "divNavBar";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['calendar'];
    $t->FileName = "calendar";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = "";
    $t->HtmlID = "divCalendar";
    $t->Type = "div";
    $t->IsHideTitle = true;
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['control_panel'];
    $t->FileName = "controlpanel";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<span class="cp-hello">' . $zbp->lang['zb_install']['wellcome'] . '</span><br/><span class="cp-login"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=login">' . $zbp->lang['msg']['admin_login'] . '</a></span>&nbsp;&nbsp;<span class="cp-vrs"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=misc&amp;type=vrs">' . $zbp->lang['msg']['view_rights'] . '</a></span>';
    $t->HtmlID = "divContorPanel";
    $t->Type = "div";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_catalog'];
    $t->FileName = "catalog";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = "";
    $t->HtmlID = "divCatalog";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['search'];
    $t->FileName = "searchpanel";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<form name="search" method="post" action="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=search"><input type="text" name="q" size="11" /> <input type="submit" value="' . $zbp->lang['msg']['search'] . '" /></form>';
    $t->HtmlID = "divSearchPanel";
    $t->Type = "div";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_comments'];
    $t->FileName = "comments";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = "";
    $t->HtmlID = "divComments";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_archives'];
    $t->FileName = "archives";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = "";
    $t->HtmlID = "divArchives";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_statistics'];
    $t->FileName = "statistics";
    $t->Source = "system";
    $t->SidebarID = 0;
    $t->Content = "";
    $t->HtmlID = "divStatistics";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_favorite'];
    $t->FileName = "favorite";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<li><a href="http://app.zblogcn.com/" target="_blank">Z-Blog应用中心</a></li><li><a href="http://weibo.com/zblogcn" target="_blank">Z-Blog官方微博</a></li><li><a href="http://bbs.zblogcn.com/" target="_blank">ZBlogger社区</a></li>';
    $t->HtmlID = "divFavorites";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_link'];
    $t->FileName = "link";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<li><a href="https://github.com/zblogcn" target="_blank" title="Z-Blog on Github">Z-Blog on Github</a></li><li><a href="https://zbloghost.cn/" target="_blank" title="Z-Blog官方主机">Z-Blog主机</a></li>';
    $t->HtmlID = "divLinkage";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_misc'];
    $t->FileName = "misc";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<li><a href="http://www.zblogcn.com/" target="_blank"><img src="{#ZC_BLOG_HOST#}zb_system/image/logo/zblog.gif" height="31" width="88" alt="RainbowSoft Studio Z-Blog" /></a></li><li><a href="{#ZC_BLOG_HOST#}feed.php" target="_blank"><img src="{#ZC_BLOG_HOST#}zb_system/image/logo/rss.png" height="31" width="88" alt="订阅本站的 RSS 2.0 新闻聚合" /></a></li>';
    $t->HtmlID = "divMisc";
    $t->Type = "ul";
    $t->IsHideTitle = true;
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_authors'];
    $t->FileName = "authors";
    $t->Source = "system";
    $t->SidebarID = 0;
    $t->Content = "";
    $t->HtmlID = "divAuthors";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_previous'];
    $t->FileName = "previous";
    $t->Source = "system";
    $t->SidebarID = 0;
    $t->Content = "";
    $t->HtmlID = "divPrevious";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_tags'];
    $t->FileName = "tags";
    $t->Source = "system";
    $t->SidebarID = 0;
    $t->Content = "";
    $t->HtmlID = "divTags";
    $t->Type = "ul";
    $t->Save();

    $a = new Post();
    $a->CateID = 1;
    $a->AuthorID = 1;
    $a->Tag = '';
    $a->Status = ZC_POST_STATUS_PUBLIC;
    $a->Type = ZC_POST_TYPE_ARTICLE;
    $a->Alias = '';
    $a->IsTop = false;
    $a->IsLock = false;
    $a->Title = $zbp->lang['zb_install']['hello_zblog'];
    $a->Intro = $zbp->lang['zb_install']['hello_zblog_content'];
    $a->Content = $zbp->lang['zb_install']['hello_zblog_content'];
    $a->IP = GetGuestIP();
    $a->PostTime = time();
    $a->CommNums = 0;
    $a->ViewNums = 0;
    $a->Template = '';
    $a->Meta = '';
    $a->Save();

    $a = new Post();
    $a->CateID = 0;
    $a->AuthorID = 1;
    $a->Tag = '';
    $a->Status = ZC_POST_STATUS_PUBLIC;
    $a->Type = ZC_POST_TYPE_PAGE;
    $a->Alias = '';
    $a->IsTop = false;
    $a->IsLock = false;
    $a->Title = $zbp->lang['zb_install']['guestbook'];
    $a->Intro = '';
    $a->Content = $zbp->lang['zb_install']['guestbook_content'];
    $a->IP = GetGuestIP();
    $a->PostTime = time();
    $a->CommNums = 0;
    $a->ViewNums = 0;
    $a->Template = '';
    $a->Meta = '';
    $a->Save();

    $zbp->LoadMembers(0);
    if (count($zbp->members) == 0) {
        echo $zbp->lang['zb_install']['not_insert_data'] . "<br/>";

        return false;
    } else {
        echo $zbp->lang['zb_install']['create_datainfo'] . "<br/>";

        return true;
    }
}

function SaveConfig()
{
    global $zbp;

    $zbp->option['ZC_BLOG_VERSION'] = ZC_BLOG_VERSION;
    $zbp->option['ZC_BLOG_NAME'] = GetVars('blogtitle', 'POST');
    $zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre|UEditor|Totoro';
    $zbp->option['ZC_SIDEBAR_ORDER'] = 'calendar|controlpanel|catalog|searchpanel|comments|archives|favorite|link|misc';
    $zbp->option['ZC_SIDEBAR2_ORDER'] = '';
    $zbp->option['ZC_SIDEBAR3_ORDER'] = '';
    $zbp->option['ZC_SIDEBAR4_ORDER'] = '';
    $zbp->option['ZC_SIDEBAR5_ORDER'] = '';
    $zbp->option['ZC_BLOG_THEME'] = 'default';
    $zbp->option['ZC_DEBUG_MODE'] = false;
    $zbp->option['ZC_LAST_VERSION'] = $zbp->version;
    $zbp->option['ZC_NOW_VERSION'] = $zbp->version;
    $zbp->SaveOption();

    if (file_exists($zbp->path . 'zb_users/c_option.php') == false) {
        echo $zbp->lang['zb_install']['not_create_option_file'] . "<br/>";

        $s = "<pre>&lt;" . "?" . "php\r\n";
        $s .= "return ";
        $option = array();
        foreach ($zbp->option as $key => $value) {
            if (($key == 'ZC_YUN_SITE') ||
                ($key == 'ZC_DATABASE_TYPE') ||
                ($key == 'ZC_SQLITE_NAME') ||
                ($key == 'ZC_SQLITE_PRE') ||
                ($key == 'ZC_MYSQL_SERVER') ||
                ($key == 'ZC_MYSQL_USERNAME') ||
                ($key == 'ZC_MYSQL_PASSWORD') ||
                ($key == 'ZC_MYSQL_NAME') ||
                ($key == 'ZC_MYSQL_CHARSET') ||
                ($key == 'ZC_MYSQL_PRE') ||
                ($key == 'ZC_MYSQL_ENGINE') ||
                ($key == 'ZC_MYSQL_PORT') ||
                ($key == 'ZC_MYSQL_PERSISTENT') ||
                ($key == 'ZC_PGSQL_SERVER') ||
                ($key == 'ZC_PGSQL_USERNAME') ||
                ($key == 'ZC_PGSQL_PASSWORD') ||
                ($key == 'ZC_PGSQL_NAME') ||
                ($key == 'ZC_PGSQL_CHARSET') ||
                ($key == 'ZC_PGSQL_PRE') ||
                ($key == 'ZC_PGSQL_PORT') ||
                ($key == 'ZC_PGSQL_PERSISTENT') ||
                ($key == 'ZC_CLOSE_WHOLE_SITE')
            ) {
                $option[$key] = $value;
            }
        }
        $s .= var_export($option, true);
        $s .= ";\r\n</pre>";

        echo $s;

        return false;
    }

    $zbp->template = $zbp->PrepareTemplate();
    $zbp->BuildTemplate();

    if (file_exists($zbp->path . 'zb_users/cache/compiled/default/index.php') == false) {
        echo $zbp->lang['zb_install']['not_create_template_file'] . "<br/>";
    }

    $zbp->Config('cache')->templates_md5 = '';
    $zbp->SaveCache();

    $zbp->Config('AppCentre')->enabledcheck = 1;
    $zbp->Config('AppCentre')->checkbeta = 0;
    $zbp->Config('AppCentre')->enabledevelop = 0;
    $zbp->Config('AppCentre')->enablegzipapp = 0;
    $zbp->SaveConfig('AppCentre');

    $zbp->Config('WhitePage')->custom_pagetype = '5';
    $zbp->Config('WhitePage')->custom_pagewidth = '1200';
    $zbp->Config('WhitePage')->custom_headtitle = 'center';
    $zbp->Config('WhitePage')->custom_bgcolor = '6699ff';
    $zbp->Config('WhitePage')->text_indent = '0';
    $zbp->SaveConfig('WhitePage');

    echo $zbp->lang['zb_install']['save_option'] . "<br/>";

    return true;
}

RunTime();
