<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
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

define('BINGO', '<span class="bingo"></span>');
define('ERROR', '<span class="error"></span>');

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

$a = ($zbp->option['ZC_DATABASE_TYPE'] !== '');
if ($a) {
    $b = (isset($zbp->option['ZC_INSTALL_AFTER_CONFIG']) == false);
    if ($b) {
        $zblogstep = 0;
    } else {
        $c = ($zbp->option['ZC_INSTALL_AFTER_CONFIG'] == false);
        if ($c) {
            $zblogstep = 0;
        } else {
            $d = ($zbp->db->ExistTable($GLOBALS['table']['Config']) == true);
            if ($d) {
                $zblogstep = 0;
            }
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $zbp->option['ZC_BLOG_LANGUAGE']; ?>">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
  <meta name="generator" content="Z-BlogPHP" />
  <meta name="robots" content="noindex,nofollow" />
  <script src="../zb_system/script/common.js?<?php echo $blogversion; ?>"></script>
  <script src="../zb_system/script/c_admin_js_add.php?<?php echo $blogversion; ?>"></script>
  <script src="../zb_system/script/md5.js?<?php echo $blogversion; ?>"></script>
  <script src="../zb_system/script/jquery-ui.custom.min.js?<?php echo $blogversion; ?>"></script>
  <link rel="stylesheet" href="../zb_system/css/jquery-ui.custom.css?<?php echo $blogversion; ?>" type="text/css" media="screen" />
  <link rel="stylesheet" href="../zb_system/css/admin3.css?<?php echo $blogversion; ?>" type="text/css" media="screen" />
  <link rel="stylesheet" href="../zb_system/image/icon/icon.css?<?php echo $blogversion; ?>" type="text/css" media="screen" />  
  <title>Z-BlogPHP <?php echo ZC_BLOG_VERSION . ' ' . $zbp->lang['zb_install']['install_program']; ?> </title>
  <?php Include_AddonAdminFont(); ?>
</head>

<body clas="install">
  <div class="setup">
    <?php
    $s = $_SERVER['QUERY_STRING'];
    $array = array();
    parse_str($s, $array);
    unset($array['step']);
    $s = '';
    foreach ($array as $key => $value) {
        $s .= '&amp;' . $key . '=' . urlencode($value);
    }
    if ($zblogstep >= 3) {
        $s = '';
    }
    ?>
    <form method="post" action="./index.php?step=<?php echo($zblogstep + 1) . $s; ?>">
      <input type="hidden" name="zbloglang" id="zbloglang" value="<?php echo $zbloglang; ?>" />
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
  <script>
    $("#language").change(function() {
      $("#zbloglang").val($(this).val());
      $("form").attr('action', './index.php');
      $("form").submit();
    });

    function Setup3() {

      if ($("input[name='fdbtype']:checked").val() == "mysql") {
        if ($("#dbmsyql_server").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbserver_need']; ?>");
          return false;
        };
        if ($("#dbmysql_name").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbname_need']; ?>");
          return false;
        };
        if ($("#dbmysql_username").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbusername_need']; ?>");
          return false;
        };
      }
      if ($("input[name='fdbtype']:checked").val() == "postgresql") {
        if ($("#dbpgsql_server").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbserver_need']; ?>");
          return false;
        };
        if ($("#dbpgsql_name").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbname_need']; ?>");
          return false;
        };
        if ($("#dbpgsql_username").val() == "") {
          alert("<?php echo $zbp->lang['zb_install']['dbusername_need']; ?>");
          return false;
        };
      }

      var username = $("#username").val();
      var password = $("#password").val();
      if ($("#blogtitle").val() == "") {
        alert("<?php echo $zbp->lang['zb_install']['sitetitle_need']; ?>");
        return false;
      };
      if (username == "") {
        alert("<?php echo $zbp->lang['zb_install']['adminusername_need']; ?>");
        return false;
      };
      if (username.length < <?php echo $zbp->option['ZC_USERNAME_MIN']; ?> || username.length > <?php echo $zbp->option['ZC_USERNAME_MAX']; ?> || !/^[\.\_A-Za-z0-9·@\u4e00-\u9fa5]+$/.test(username)) {
        alert("<?php echo $zbp->lang['error']['77']; ?>");
        return false;
      };
      if (password == "") {
        alert("<?php echo $zbp->lang['zb_install']['adminpassword_need']; ?>");
        return false;
      };
      var passwordRegExp = /^[A-Za-z0-9'"`~@#%&_/;!:=<>\[\]\(\)\{\}\+\|\.\-\*\^\$\?\\]+$/u;
      if (
        !(password.length >= <?php echo $zbp->option['ZC_PASSWORD_MIN']; ?> && password.length <= <?php echo $zbp->option['ZC_PASSWORD_MAX']; ?>) ||
        passwordRegExp.test(password) == false
      ) {
        alert("<?php echo $zbp->lang['error']['54']; ?>");
        return false;
      };
      if (password !== $("#repassword").val()) {
        alert("<?php echo $zbp->lang['error']['73']; ?>");
        return false;
      };

    }

    $(function() {
      $("#setup0").progressbar({
        value: 100
      });
      $("#setup1").progressbar({
        value: 0
      });
      $("#setup2").progressbar({
        value: 33
      });
      $("#setup3").progressbar({
        value: 66
      });
      $("#setup4").progressbar({
        value: 100
      });
    });
  </script>
</body>

</html>
<?php
function Setup0()
{
    global $zbp;
    ?>
  <dl>
    <dt></dt>
    <dd id="ddleft">
      <div id="headerimg"><img src="../zb_system/image/admin/logo.svg" alt="Z-BlogPHP" />
        <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
      <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
      <div id="setup0" class="left"></div>
      <p><?php echo $zbp->lang['zb_install']['install_license']; ?> » <?php echo $zbp->lang['zb_install']['environment_check']; ?> » <?php echo $zbp->lang['zb_install']['db_build_set']; ?> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
    </dd>
    <dd id="ddright">
      <p style="float:left;clear:both;width:100%;text-align:right;padding-bottom:0.5em;"><b><?php echo $zbp->lang['zb_install']['language']; ?></b>&nbsp;<select id="language" name="language" style="width:150px;">
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
    global $zbp;
    ?>
  <dl>
    <dt></dt>
    <dd id="ddleft">
      <div id="headerimg"><img src="../zb_system/image/admin/logo.svg" alt="Z-BlogPHP" />
        <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
      <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
      <div id="setup1" class="left"></div>
      <p><b><?php echo $zbp->lang['zb_install']['install_license']; ?></b> » <?php echo $zbp->lang['zb_install']['environment_check']; ?> » <?php echo $zbp->lang['zb_install']['db_build_set']; ?> » <?php echo $zbp->lang['zb_install']['install_result']; ?></p>
    </dd>
    <dd id="ddright">
      <p style="float:left;clear:both;width:100%;text-align:right;padding-bottom:0.5em;"><b><?php echo $zbp->lang['zb_install']['language']; ?></b>&nbsp;<select id="language" name="language" style="width:150px;">
          <?php echo CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK']); ?>
        </select></p>
      <div id="title">Z-BlogPHP <?php echo ZC_BLOG_VERSION . ' ' . $zbp->lang['zb_install']['install_license']; ?></div>
      <div id="content">
        <textarea readonly>
    <?php echo $zbp->lang['zb_install']['license_title'] . "\r\n"; ?>

感谢您选择Z-BlogPHP。 Z-BlogPHP 基于 PHP 技术开发，采用 MySQL 或 SQLite 或 PostgreSQL 作为数据库，全部源码开放。她既是博客程序，也是CMS建站系统。已走过十余年风雨的她们，有着强大的可定制性、丰富的主题和插件，致力于给国内用户提供优秀的博客写作体验。期待能成为您写博客的第一选择。

Z-BlogPHP官方网址：https://www.zblogcn.com/

为了使您正确并合法的使用本软件，请您在使用前务必阅读清楚下面的协议条款：

一、本授权协议适用且仅适用于 Z-BlogPHP。

二、协议许可的权利
1. 本程序基于 MIT 协议开源，您可以在 MIT 协议允许的范围内对源代码进行使用，包括修改源代码或界面风格以适应您的网站要求。
2. 您拥有使用本软件构建的网站及其全部内容的所有权，并独立承担与这些内容的相关法律义务。
3. 如您需要使用Z-Blog应用中心，您需要在应用中心内同意《应用中心额外条款及最终用户许可协议》。

三、协议规定的约束和限制
1. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用 Z-BlogPHP 程序本身，未经书面许可，必须保留页面底部的版权（Powered by Z-BlogPHP），不得删除；但可以以任何访客可见的形式对其进行修改和美化。
2. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。

四、有限担保和免责声明
1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，包括系统受损、资料丢失以及其它任何风险。在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3. 电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装 Z-BlogPHP ，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。

版权所有 ©2005 - 2020，保留所有权利。

协议发布时间：2013 年 8 月 1 日
版本最新更新：2019 年 12 月 16 日

    Z-BlogPHP免责申明

(一)、ZBLOGCN.COM发布免费、开源的建站程序Z-Blog和Z-BlogPHP（以下简称“本建站程序”），不提供网站运行相关的主机空间、建站、内容、维护等服务。

(二)、用户可自由选择使用或不用本建站程序，ZBLOGCN.COM与用户及使用本建站程序所建立的网站无任何关联，ZBLOGCN.COM对用户及其网站不承担任何责任。

(三)、用户下载、安装、使用本建站程序，即表明用户信任ZBLOGCN.COM，ZBLOGCN.COM对任何原因在使用本建站程序时可能对用户自己或他人造成的任何形式的损失和伤害不承担责任。

(四)、用户不得利用本建站程序以任何方式从事违反法律法规及社会公序良俗的行为，用户须对其不当使用行为可能带来的后果负全部责任。

(五)、任何单位或个人认为使用本建站程序所建立的网站可能涉嫌侵犯其合法权益的，应该及时向该网站的所有权人反映、交涉、或诉诸法律手段。

(六)、本声明未涉及的问题参见国家有关法律法规，当本声明与国家法律法规冲突时，以国家法律法规为准。

  </textarea>
      </div>
      <div id="bottom">
        <label>
          <input type="checkbox" />
          <?php echo $zbp->lang['zb_install']['i_agree']; ?></label>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" name="next" id="netx" value="<?php echo $zbp->lang['zb_install']['next']; ?>" disabled="disabled" />
        <script>
          $("input[type=checkbox]").change(function() {
            if ($(this).prop("checked")) {
              $("#netx").prop("disabled", false);
            } else {
              $("#netx").prop("disabled", true);
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
    CheckServer();
    ?>
  <dl>
    <dt></dt>
    <dd id="ddleft">
      <div id="headerimg"><img src="../zb_system/image/admin/logo.svg" alt="Z-BlogPHP" />
        <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
      <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
      <div id="setup2" class="left"></div>
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
          <?php if ($GLOBALS['CheckResult']['mysql'][0]) { ?>
            <tr>
              <td scope="row">mysql</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['mysqli'][0]) { ?>
            <tr>
              <td scope="row">mysqli</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysqli'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysqli'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['sqlite3'][0]) { ?>
            <tr>
              <td scope="row">sqlite3</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['sqlite'][0]) { ?>
            <tr>
              <td scope="row">sqlite2</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['pgsql'][0]) { ?>
            <tr>
              <td scope="row">pgsql</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pgsql'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pgsql'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['pdo_mysql'][0]) { ?>
            <tr>
              <td scope="row">pdo_mysql</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['pdo_sqlite'][0]) { ?>
            <tr>
              <td scope="row">pdo_sqlite</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_sqlite'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_sqlite'][1]; ?></td>
            </tr>
          <?php } ?>
          <?php if ($GLOBALS['CheckResult']['pdo_pgsql'][0]) { ?>
            <tr>
              <td scope="row">pdo_pgsql</td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_pgsql'][0]; ?></td>
              <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_pgsql'][1]; ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td scope="row">OpenSSL</td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['openssl'][0]; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['openssl'][1]; ?></td>
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
            <td scope="row"><?php echo $zbp->lang['zb_install']['environment_network']; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['network'][0]; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['network'][1]; ?></td>
          </tr>
          <tr>
            <td scope="row"><?php echo $zbp->lang['zb_install']['environment_xml']; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['simplexml_load_string'][0]; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['simplexml_load_string'][1]; ?></td>
          </tr>
          <tr>
            <td scope="row"><?php echo $zbp->lang['zb_install']['environment_json']; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['json_decode'][0]; ?></td>
            <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['json_decode'][1]; ?></td>
          </tr>
          <tr>
            <td scope="row"><?php echo $zbp->lang['zb_install']['environment_iconv']; ?></td>
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

    $hasPostgresql = false;

    $hasMysql = (bool) ((bool) ($CheckResult['mysql'][0]) || (bool) ($CheckResult['mysqli'][0]) || (bool) ($CheckResult['pdo_mysql'][0]));

    $hasSqlite = (bool) ((bool) ($CheckResult['sqlite3'][0]) || (bool) ($CheckResult['sqlite'][0]) || (bool) ($CheckResult['pdo_sqlite'][0]));

    $hasPostgresql = (bool) ((bool) ($CheckResult['pgsql'][0]) || (bool) ($CheckResult['pdo_pgsql'][0]));

    $option2 = array();
    $option2['blogtitle'] = GetVars('blogtitle', 'GET');
    $option2['username'] = GetVars('username', 'GET');
    $option2['password'] = GetVars('password', 'GET');
    $option2['repassword'] = GetVars('repassword', 'GET');

    if (GetVars('dbmysql_server', 'GET') != '') {
        $option['ZC_MYSQL_SERVER'] = GetVars('dbmysql_server', 'GET');
    }
    if (GetVars('dbmysql_name', 'GET') != '') {
        $option['ZC_MYSQL_NAME'] = GetVars('dbmysql_name', 'GET');
    }
    if (GetVars('dbmysql_username', 'GET') != '') {
        $option['ZC_MYSQL_USERNAME'] = GetVars('dbmysql_username', 'GET');
    }
    if (GetVars('dbmysql_password', 'GET') != '') {
        $option['ZC_MYSQL_PASSWORD'] = GetVars('dbmysql_password', 'GET');
    }
    if (GetVars('dbmysql_pre', 'GET') != '') {
        $option['ZC_MYSQL_PRE'] = GetVars('dbmysql_pre', 'GET');
    }
    if (GetVars('dbengine', 'GET') != '') {
        $option['ZC_MYSQL_ENGINE'] = GetVars('dbengine', 'GET');
    }
    if (GetVars('dbcharset', 'GET') != '') {
        $option['ZC_MYSQL_CHARSET'] = GetVars('dbcharset', 'GET');
    }
    if (GetVars('dbcollate', 'GET') != '') {
        $option['ZC_MYSQL_COLLATE'] = GetVars('dbcollate', 'GET');
    }
    if (GetVars('dbpgsql_server', 'GET') != '') {
        $option['ZC_PGSQL_SERVER'] = GetVars('dbpgsql_server', 'GET');
    }
    if (GetVars('dbpgsql_name', 'GET') != '') {
        $option['ZC_PGSQL_NAME'] = GetVars('dbpgsql_name', 'GET');
    }
    if (GetVars('dbpgsql_username', 'GET') != '') {
        $option['ZC_PGSQL_USERNAME'] = GetVars('dbpgsql_username', 'GET');
    }
    if (GetVars('dbpgsql_password', 'GET') != '') {
        $option['ZC_PGSQL_PASSWORD'] = GetVars('dbpgsql_password', 'GET');
    }
    if (GetVars('dbpgsql_pre', 'GET') != '') {
        $option['ZC_PGSQL_PRE'] = GetVars('dbpgsql_pre', 'GET');
    }
    ?>
  <style type="text/css">
    .themelist label {
      margin-right: 40px;
      position: relative;
    }

    .themelist input[type=radio] {
      margin-right: 5px;
    }

    .themelist em {
      font-size: 16px;
      font-style: normal;
    }

    .themelist span {
      width: 210px;
      margin-left: -100px;
      padding: 10px;
      border: 1px solid #ccc;
      background: #fff;
      position: absolute;
      left: 50%;
      bottom: 32px;
      display: none;
    }

    .themelist span img {
      width: 188px;
    }

    .themelist span:before {
      content: "";
      width: 0;
      height: 0;
      margin-left: -10px;
      border-style: solid;
      border-width: 10px 10px 0 10px;
      border-color: #fff transparent transparent transparent;
      position: absolute;
      bottom: -9px;
      left: 50%;
      z-index: 1;
    }

    .themelist span:after {
      content: "";
      width: 0;
      height: 0;
      margin-left: -10px;
      border-style: solid;
      border-width: 10px 10px 0 10px;
      border-color: #ccc transparent transparent transparent;
      position: absolute;
      bottom: -10px;
      left: 50%;
    }
  </style>
  <dl>
    <dt></dt>
    <dd id="ddleft">
      <div id="headerimg"><img src="../zb_system/image/admin/logo.svg" alt="Z-BlogPHP" />
        <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
      <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
      <div id="setup3" class="left"></div>
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
                <input type="radio" name="fdbtype" value="mysql" /> MySQL</label>
                <?php
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            if ($hasSqlite) {
                ?>
              <label class="dbselect" id="sqlite_radio">
                <input type="radio" name="fdbtype" value="sqlite" /> SQLite</label>
                <?php
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            if ($hasPostgresql) {
                ?>
              <label class="dbselect" id="postgresql_radio">
                <input type="radio" name="fdbtype" value="postgresql" /> PostgreSQL</label>
                <?php
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            ?>
          </p>
        </div>
        <?php
        if ($hasMysql) {
            ?>
          <div class="dbdetail" id="mysql">
            <p><b><?php echo $zbp->lang['zb_install']['db_server']; ?></b>
              <input type="text" name="dbmysql_server" id="dbmysql_server" value="<?php echo $option['ZC_MYSQL_SERVER']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_name']; ?></b>
              <input type="text" name="dbmysql_name" id="dbmysql_name" value="<?php echo $option['ZC_MYSQL_NAME']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_username']; ?></b>
              <input type="text" name="dbmysql_username" id="dbmysql_username" value="<?php echo $option['ZC_MYSQL_USERNAME']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_password']; ?></b>
              <input type="password" name="dbmysql_password" id="dbmysql_password" value="<?php echo $option['ZC_MYSQL_PASSWORD']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_pre']; ?></b>
              <input type="text" name="dbmysql_pre" id="dbmysql_pre" value="<?php echo $option['ZC_MYSQL_PRE']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_engine']; ?></b>
              <select id="dbengine" name="dbengine" style="width:350px;padding:0.3em 0;">
                <option value="MyISAM" selected>MyISAM</option>
                <option value="InnoDB">InnoDB</option>
              </select>
            </p>
            <!--<p><b><?php echo $zbp->lang['zb_install']['db_charset']; ?></b>
              <select id="dbcharset" name="dbcharset" style="width:350px;padding:0.3em 0;">
                <option value="utf8" selected>utf8(<?php echo $zbp->lang['msg']['default']; ?>)</option>
                <option value="utf8mb4">utf8mb4</option>
              </select>
              <script>
                $("#dbengine").change(function(){
                  if($("#dbengine").val()=='InnoDB')
                    $("#dbcharset").val("utf8mb4");
                });
              </script>
              <script>
                $("#dbcharset").val("<?php echo $option['ZC_MYSQL_CHARSET']; ?>");
              </script>
              <?php echo '<input type="hidden" name="dbcollate" id="dbcollate" value="' . $option['ZC_MYSQL_COLLATE'] . '" />'; ?>
              -->
              <script>
                $("#dbengine").val("<?php echo $option['ZC_MYSQL_ENGINE']; ?>");
              </script>
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_drive']; ?></b>
            <?php
            if ($CheckResult['mysqli'][0]) {
                ?>
                <label>
                  <input value="mysqli" type="radio" name="dbtype" /> mysqli</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
            }
            if ($CheckResult['pdo_mysql'][0]) {
                ?>
                <label>
                  <input value="pdo_mysql" type="radio" name="dbtype" /> pdo_mysql</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
            }
            if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                if ($CheckResult['mysql'][0] && !$CheckResult['mysqli'][0] && !$CheckResult['pdo_mysql'][0]) { // 强制淘汰mysql
                    ?>
                  <label>
                    <input value="mysql" type="radio" name="dbtype" /> mysql</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                }
            }
            ?>
              <br /><small><?php echo $zbp->lang['zb_install']['db_set_port']; ?></small>
            </p>
          </div>
            <?php
        }
        if ($hasSqlite) {
            ?>
          <div class="dbdetail" id="sqlite">
            <p><b><?php echo $zbp->lang['zb_install']['db_name']; ?></b>
              <input type="text" name="dbsqlite_name" id="dbsqlite_name" value="<?php echo GetDbName(); ?>" readonly style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_pre']; ?></b>
              <input type="text" name="dbsqlite_pre" id="dbsqlite_pre" value="zbp_" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_drive']; ?></b>
            <?php
            if ($CheckResult['pdo_sqlite'][0]) {
                ?>
                <label>
                  <input value="pdo_sqlite" type="radio" name="dbtype" /> pdo_sqlite</label>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            if ($CheckResult['sqlite3'][0]) {
                ?>
                <label>
                  <input value="sqlite3" type="radio" name="dbtype" /> sqlite3</label>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $not_use_sqlite2 = false;
            if (extension_loaded('pdo_sqlite')) {
                $a = PDO::getAvailableDrivers();
                if (in_array('sqlite', $a)) {
                    $not_use_sqlite2 = true;
                }
            }
            if (class_exists('SQLite3', false)) {
                $not_use_sqlite2 = true;
            }
            if ($CheckResult['sqlite'][0] && $not_use_sqlite2 == false) {
                ?>
                <label>
                  <input value="sqlite" type="radio" name="dbtype" /> sqlite</label>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            ?>
            </p>
          </div>
            <?php
        }
        if ($hasPostgresql) {
            ?>
          <div class="dbdetail" id="postgresql">
            <p><b><?php echo $zbp->lang['zb_install']['db_server']; ?></b>
              <input type="text" name="dbpgsql_server" id="dbpgsql_server" value="<?php echo $option['ZC_PGSQL_SERVER']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_name']; ?></b>
              <input type="text" name="dbpgsql_name" id="dbpgsql_name" value="<?php echo $option['ZC_PGSQL_NAME']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_username']; ?></b>
              <input type="text" name="dbpgsql_username" id="dbpgsql_username" value="<?php echo $option['ZC_PGSQL_USERNAME']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_password']; ?></b>
              <input type="password" name="dbpgsql_password" id="dbpgsql_password" value="<?php echo $option['ZC_PGSQL_PASSWORD']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_pre']; ?></b>
              <input type="text" name="dbpgsql_pre" id="dbpgsql_pre" value="<?php echo $option['ZC_PGSQL_PRE']; ?>" style="width:350px;" />
            </p>
            <p><b><?php echo $zbp->lang['zb_install']['db_drive']; ?></b>
                <?php
                if ($CheckResult['pgsql'][0]) {
                    ?>
                <label>
                  <input value="postgresql" type="radio" name="dbtype" /> postgresql</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                }
                if ($CheckResult['pdo_pgsql'][0]) {
                    ?>
                <label>
                  <input value="pdo_postgresql" type="radio" name="dbtype" /> pdo_postgresql</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                }
                ?>
              <br /><small><?php echo str_ireplace('3306', '5432', $zbp->lang['zb_install']['db_set_port']); ?></small>
            </p>
          </div>
            <?php
        }
        ?>
        <p class="title"><?php echo $zbp->lang['zb_install']['website_setting']; ?></p>
        <p><b><?php echo $zbp->lang['zb_install']['blog_name']; ?></b>
          <input type="text" name="blogtitle" id="blogtitle" value="<?php echo $option2['blogtitle']; ?>" style="width:350px;" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['admin_username']; ?></b>
          <input type="text" name="username" id="username" value="<?php echo $option2['username']; ?>" style="width:200px;" />
          &nbsp;<small><?php echo $zbp->lang['zb_install']['username_intro']; ?></small></p>
        <p><b><?php echo $zbp->lang['zb_install']['admin_password']; ?></b>
          <input type="password" name="password" id="password" value="<?php echo $option2['password']; ?>" style="width:200px;" autocomplete="off" />
          &nbsp;<small><?php echo $zbp->lang['zb_install']['password_intro']; ?></small></p>
        <p><b><?php echo $zbp->lang['zb_install']['re_password']; ?></b>
          <input type="password" name="repassword" id="repassword" value="<?php echo $option2['repassword']; ?>" style="width:200px;" autocomplete="off" />
        </p>
        <p><b><?php echo $zbp->lang['zb_install']['theme']; ?></b><span class="themelist">
            <?php
            //add four themes
            $themes2 = array(
                'tpure'     => 'style',
                'Zit'       => 'style',
                'default'   => 'default',
                'WhitePage' => 'default',
            );
            $themes = array();
            foreach ($themes2 as $key => $value) {
                $app = $zbp->LoadApp('theme', $key);
                if ($app->isloaded == true) {
                    $themes[$key] = $value;
                }
            }
            foreach ($themes as $key => $value) {
                echo '<label><input value="' . $key . '|' . $value . '" type="radio" name="blogtheme"/> ' . $key . '<span><img src="../zb_users/theme/' . $key . '/screenshot.png" alt=""></span></label>';
            }
            ?>
        </p>
      </div>
      <div id="bottom">
        <input type="submit" name="next" id="netx" onClick="return Setup3()" value="<?php echo $zbp->lang['zb_install']['next']; ?>" />
      </div>
    </dd>
  </dl>
  <script>
    $(".dbselect").click(function() {
      $(".dbdetail").hide();
      $("#" + $(this).attr("id").split("_radio")[0]).show();
      $("input[name='dbtype']:visible").get(0).click();
    });
    $(".dbdetail").hide();
    $("#" + $(".dbselect").attr("id").split("_radio")[0]).show();
    $("input[name='dbtype']:visible").get(0).click();
    $("input[name='fdbtype']:visible").get(0).click();
    $("input[name='blogtheme']:visible").get(Math.round(Math.random() * <?php echo count($themes); ?> - 1)).click();

    $(".themelist label").hover(function() {
      $(this).find("span").show();
    }, function() {
      $(this).find("span").hide();
    });
    <?php
    if (GetVars('dbselect', 'GET') != '') {
        echo '$("input:radio[name=fdbtype][value=' . GetVars('dbselect', 'GET') . ']").click();';
    }
    if (GetVars('dbtype', 'GET') != '') {
        echo '$("input[name=\'dbtype\'][value=' . GetVars('dbtype', 'GET') . ']:visible").get(0).click();';
    }
    ?>
  </script>
    <?php
}

//###############################################################################################################
//4
function Setup4()
{
    global $zbp;
    ?>
  <dl>
    <dt></dt>
    <dd id="ddleft">
      <div id="headerimg"><img src="../zb_system/image/admin/logo.svg" alt="Z-BlogPHP" />
        <strong><?php echo $zbp->lang['zb_install']['install_program']; ?></strong></div>
      <div class="left"><?php echo $zbp->lang['zb_install']['install_progress']; ?>&nbsp;</div>
      <div id="setup4" class="left"></div>
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
                //$zbp->option['ZC_MYSQL_CHARSET'] = GetVars('dbcharset', 'POST');
                $zbp->option['ZC_MYSQL_USERNAME'] = trim(GetVars('dbmysql_username', 'POST'));
                $zbp->option['ZC_MYSQL_PASSWORD'] = trim(GetVars('dbmysql_password', 'POST'));
                $zbp->option['ZC_MYSQL_NAME'] = trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbmysql_name', 'POST')));
                $zbp->option['ZC_MYSQL_PRE'] = trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbmysql_pre', 'POST')));
                if ($zbp->option['ZC_MYSQL_PRE'] == '') {
                    $zbp->option['ZC_MYSQL_PRE'] = 'zbp_';
                }

                $zbp->option['ZC_MYSQL_ENGINE'] = GetVars('dbengine', 'POST');
                $cts = str_replace('MyISAM', $zbp->option['ZC_MYSQL_ENGINE'], $cts);

                if (!(isset($zbp->option['ZC_INSTALL_AFTER_CONFIG']) && $zbp->option['ZC_INSTALL_AFTER_CONFIG'] == true)) {
                    $newdb = ZBlogPHP::InitializeDB($zbp->option['ZC_DATABASE_TYPE']);
                    if ($newdb->CreateDB($zbp->option['ZC_MYSQL_SERVER'], $zbp->option['ZC_MYSQL_PORT'], $zbp->option['ZC_MYSQL_USERNAME'], $zbp->option['ZC_MYSQL_PASSWORD'], $zbp->option['ZC_MYSQL_NAME']) == true) {
                        echo $zbp->lang['zb_install']['create_db'] . $zbp->option['ZC_MYSQL_NAME'] . "<br/>";
                    }
                    $newdb->dbpre = $zbp->option['ZC_MYSQL_PRE'];
                    $newdb->Close();
                    $newdb = null;
                }
                break;
            case 'sqlite':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/sqlite.sql');
                $cts = str_replace(' autoincrement', '', $cts);
                $zbp->option['ZC_SQLITE_NAME'] = trim(GetVars('dbsqlite_name', 'POST'));
                $zbp->option['ZC_SQLITE_PRE'] = trim(GetVars('dbsqlite_pre', 'POST'));
                if ($zbp->option['ZC_SQLITE_PRE'] == '') {
                    $zbp->option['ZC_SQLITE_PRE'] = 'zbp_';
                }
                break;
            case 'sqlite3':
            case 'pdo_sqlite':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/sqlite.sql');
                $zbp->option['ZC_SQLITE_NAME'] = trim(GetVars('dbsqlite_name', 'POST'));
                $zbp->option['ZC_SQLITE_PRE'] = trim(GetVars('dbsqlite_pre', 'POST'));
                if ($zbp->option['ZC_SQLITE_PRE'] == '') {
                    $zbp->option['ZC_SQLITE_PRE'] = 'zbp_';
                }
                break;
            case 'postgresql':
            case 'pdo_postgresql':
                $cts = file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/createtable/pgsql.sql');
                $zbp->option['ZC_PGSQL_SERVER'] = GetVars('dbpgsql_server', 'POST');
                if (strpos($zbp->option['ZC_PGSQL_SERVER'], ':') !== false) {
                    $servers = explode(':', $zbp->option['ZC_PGSQL_SERVER']);
                    $zbp->option['ZC_PGSQL_SERVER'] = trim($servers[0]);
                    $zbp->option['ZC_PGSQL_PORT'] = (int) $servers[1];
                    if ($zbp->option['ZC_PGSQL_PORT'] == 0) {
                        $zbp->option['ZC_PGSQL_PORT'] = 5432;
                    }

                    unset($servers);
                }
                $zbp->option['ZC_PGSQL_CHARSET'] = 'utf8';
                $zbp->option['ZC_PGSQL_USERNAME'] = trim(GetVars('dbpgsql_username', 'POST'));
                $zbp->option['ZC_PGSQL_PASSWORD'] = trim(GetVars('dbpgsql_password', 'POST'));
                $zbp->option['ZC_PGSQL_NAME'] = strtolower(trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbpgsql_name', 'POST'))));
                $zbp->option['ZC_PGSQL_PRE'] = trim(str_replace(array('\'', '"'), array('', ''), GetVars('dbpgsql_pre', 'POST')));
                if ($zbp->option['ZC_PGSQL_PRE'] == '') {
                    $zbp->option['ZC_PGSQL_PRE'] == 'zbp_';
                }

                if (!(isset($zbp->option['ZC_INSTALL_AFTER_CONFIG']) && $zbp->option['ZC_INSTALL_AFTER_CONFIG'] == true)) {
                    $newdb = ZBlogPHP::InitializeDB($zbp->option['ZC_DATABASE_TYPE']);
                    if ($newdb->CreateDB($zbp->option['ZC_PGSQL_SERVER'], $zbp->option['ZC_PGSQL_PORT'], $zbp->option['ZC_PGSQL_USERNAME'], $zbp->option['ZC_PGSQL_PASSWORD'], $zbp->option['ZC_PGSQL_NAME']) == true) {
                        echo $zbp->lang['zb_install']['create_db'] . $zbp->option['ZC_PGSQL_NAME'] . "<br/>";
                    }
                    $newdb->dbpre = $zbp->option['ZC_PGSQL_PRE'];
                    $newdb->Close();
                    $newdb = null;
                }
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
                        $i = 0;
                    } else {
                        //$hasError = true;
                        $i = 0;
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
        }
        ?>

      </div>
      <div id="bottom">
        <?php
        if ($hasError == true) {
            echo '<p><input type="button" name="next" onClick="javascript:history.go(-1)" id="netx" value="' . $zbp->lang['zb_install']['clicktoback'] . '"/></p>';
        } else {
            ?>
          <input type="button" name="next" onClick="window.location.href='../'" id="netx" value="<?php echo $zbp->lang['zb_install']['ok']; ?>" />
            <?php
        }
        ?>
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
        'server'  => array(GetVars('SERVER_SOFTWARE', 'SERVER'), BINGO),
        'phpver'  => array(PHP_VERSION, ''),
        'zbppath' => array($zbp->path, BINGO),
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
        'openssl'    => array('', ''),
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
        $CheckResult['phpver'][1] = BINGO;
    } else {
        $CheckResult['phpver'][1] = ERROR;
    }

    //针对PCRE老版本报错
    $pv = explode(' ', PCRE_VERSION);
    $CheckResult['pcre'][0] = PCRE_VERSION;
    if (version_compare($pv[0], '6.6') <= 0) {
        $CheckResult['pcre'][1] = ERROR;
    } else {
        $CheckResult['pcre'][1] = BINGO;
    }

    if (function_exists("gd_info")) {
        $info = gd_info();
        $CheckResult['gd2'][0] = $info['GD Version'];
        $CheckResult['gd2'][1] = $CheckResult['gd2'][0] ? BINGO : ERROR;
    }
    if (function_exists("mysql_get_client_info")) {
        $CheckResult['mysql'][0] = strtok(mysql_get_client_info(), '$');
        $CheckResult['mysql'][1] = $CheckResult['mysql'][0] ? BINGO : ERROR;
    }
    if (function_exists("mysqli_get_client_info")) {
        $CheckResult['mysqli'][0] = strtok(mysqli_get_client_info(), '$');
        $CheckResult['mysqli'][1] = $CheckResult['mysqli'][0] ? BINGO : ERROR;
    }
    if (function_exists("mb_language")) {
        $CheckResult['mbstring'][0] = mb_language();
        $CheckResult['mbstring'][1] = $CheckResult['mbstring'][0] ? BINGO : ERROR;
    }
    if (class_exists("PDO", false)) {
        if (extension_loaded('pdo_mysql')) {
            //$pdo = new PDO( 'mysql:');
            $v = ' '; //strtok($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),'$');
            $pdo = null;
            $CheckResult['pdo_mysql'][0] = $v;
            $CheckResult['pdo_mysql'][1] = $CheckResult['pdo_mysql'][0] ? BINGO : ERROR;
        }

        if (extension_loaded('pdo_sqlite')) {
            //$pdo = new PDO('sqlite::memory:');
            $a = PDO::getAvailableDrivers();
            $v = '';
            if (in_array('sqlite', $a)) {
                $v .= 'sqlite3';
            }
            if (in_array('sqlite2', $a)) {
                $v .= ' sqlite2';
            }
            $pdo = null;
            $CheckResult['pdo_sqlite'][0] = $v;
            $CheckResult['pdo_sqlite'][1] = $CheckResult['pdo_sqlite'][0] ? BINGO : ERROR;
        }

        if (extension_loaded('pdo_pgsql')) {
            $v = ' ';
            $pdo = null;
            $CheckResult['pdo_pgsql'][0] = $v;
            $CheckResult['pdo_pgsql'][1] = $CheckResult['pdo_pgsql'][0] ? BINGO : ERROR;
        }
    }
    if (defined("PGSQL_STATUS_STRING")) {
        $CheckResult['pgsql'][0] = PGSQL_STATUS_STRING;
        $CheckResult['pgsql'][1] = $CheckResult['pgsql'][0] ? BINGO : ERROR;
    }
    if (function_exists("sqlite_libversion")) {
        $CheckResult['sqlite'][0] = sqlite_libversion();
        $CheckResult['sqlite'][1] = $CheckResult['sqlite'][0] ? BINGO : ERROR;
    }
    if (class_exists('SQLite3', false)) {
        $info = SQLite3::version();
        $CheckResult['sqlite3'][0] = $info['versionString'];
        $CheckResult['sqlite3'][1] = $CheckResult['sqlite3'][0] ? BINGO : ERROR;
    }
    if (defined('OPENSSL_VERSION_TEXT')) {
        $info = OPENSSL_VERSION_TEXT;
        $CheckResult['openssl'][0] = $info;
        $CheckResult['openssl'][1] = $CheckResult['openssl'][0] ? BINGO : ERROR;
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
    $CheckResult['network'][1] = ($networkTest == false) ? ERROR : BINGO;
    $CheckResult['simplexml_load_string'][1] = function_exists('simplexml_load_string') ? BINGO : ERROR;
    $CheckResult['json_decode'][1] = function_exists('json_decode') ? BINGO : ERROR;
    $CheckResult['iconv'][1] = function_exists('iconv') ? BINGO : ERROR;
    $CheckResult['mb_strlen'][1] = function_exists('mb_strlen') ? BINGO : ERROR;
}

function getRightsAndExport($folderparent, $folder)
{
    global $zbp;
    $s = GetFilePerms($zbp->path . $folderparent . $folder);
    $o = GetFilePermsOct($zbp->path . $folderparent . $folder);
    $GLOBALS['CheckResult'][$folder][0] = $s . ' | ' . $o;

    if (substr($s, 0, 1) == '-') {
        $GLOBALS['CheckResult'][$folder][1] = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r') ? BINGO : ERROR;
    } else {
        $GLOBALS['CheckResult'][$folder][1] = (substr($s, 1, 1) == 'r' && substr($s, 2, 1) == 'w' && substr($s, 3, 1) == 'x' && substr($s, 4, 1) == 'r' && substr($s, 7, 1) == 'r' && substr($s, 6, 1) == 'x' && substr($s, 9, 1) == 'x') ? BINGO : ERROR;
    }
}

function CreateTable($sql)
{
    global $zbp;

    if (stripos($zbp->option['ZC_DATABASE_TYPE'], 'mysql') !== false) {
        $zbp->option['ZC_MYSQL_CHARSET'] = $zbp->db->charset;
        $zbp->option['ZC_MYSQL_COLLATE'] = $zbp->db->collate;
        $sql = str_ireplace('CHARSET=utf8', 'CHARSET=' . $zbp->option['ZC_MYSQL_CHARSET'], $sql);
        $sql = str_ireplace('COLLATE=utf8_general_ci', 'COLLATE=' . $zbp->option['ZC_MYSQL_COLLATE'], $sql);
    }

    if ($zbp->db->ExistTable($GLOBALS['table']['Config']) == true) {
        echo $zbp->lang['zb_install']['exist_table_in_db'];

        return false;
    }

    $sql = $zbp->db->sql->ReplacePre($sql);

    $zbp->db->QueryMulit($sql);

    if ($zbp->db->ExistTable($GLOBALS['table']['Config']) == false) {
        echo $zbp->lang['zb_install']['not_create_table'];
        if (!empty($zbp->db->error)) {
            echo '<pre style="height:30em;overflow:scroll ">';
            call_user_func('var_dump', $zbp->db->error);
            echo '</pre>';
        }

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
    $t->Build();
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
    $t->Content = '<form name="search" method="post" action="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=search"><label><span style="position:absolute;color:transparent;z-index:-9999;">Search</span><input type="text" name="q" size="11" /></label> <input type="submit" value="' . $zbp->lang['msg']['search'] . '" /></form>';
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
    $t->Content = '<li><a href="https://app.zblogcn.com/" target="_blank">Z-Blog应用中心</a></li><li><a href="https://bbs.zblogcn.com/" target="_blank">ZBlogger社区</a></li><li><a href="https://z5encrypt.com/" target="_blank" title="全新的PHP加密方案，致力于PHP源码的保护">Z5 PHP加密</a></li>';
    $t->HtmlID = "divFavorites";
    $t->Type = "ul";
    $t->Save();

    $t = new Module();
    $t->Name = $zbp->lang['msg']['module_link'];
    $t->FileName = "link";
    $t->Source = "system";
    $t->SidebarID = 1;
    $t->Content = '<li><a href="https://github.com/zblogcn" target="_blank" title="Z-Blog on Github">Z-Blog on Github</a></li>';
    $t->HtmlID = "divLinkage";
    $t->Type = "ul";
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
    $zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre|UEditor|Totoro|LinksManage';

    $zbp->option['ZC_BLOG_THEME'] = SplitAndGet(GetVars('blogtheme', 'POST'), '|', 0);
    $zbp->option['ZC_BLOG_CSS'] = SplitAndGet(GetVars('blogtheme', 'POST'), '|', 1);
    $zbp->option['ZC_DEBUG_MODE'] = false;
    $zbp->option['ZC_LAST_VERSION'] = ZC_LAST_VERSION;
    $zbp->option['ZC_NOW_VERSION'] = $zbp->version;

    $zbp->LoadCache();
    $app = $zbp->LoadApp('theme', 'default');
    if ($app->isloaded == true) {
        $app->SaveSideBars();
    }

    $app = $zbp->LoadApp('theme', 'Zit');
    if ($app->isloaded == true) {
        $app->LoadSideBars();
        $app->SaveSideBars();
    }

    $app = $zbp->LoadApp('theme', 'tpure');
    if ($app->isloaded == true) {
        $app->LoadSideBars();
        $app->SaveSideBars();
    }

    $app = $zbp->LoadApp('theme', 'WhitePage');
    if ($app->isloaded == true) {
        $app->LoadSideBars();
        $app->SaveSideBars();
    }

    $app = $zbp->LoadApp('theme', $zbp->option['ZC_BLOG_THEME']);
    $app->LoadSideBars();

    $zbp->SaveOption();

    if (file_exists($zbp->path . 'zb_users/c_option.php') == false) {
        echo $zbp->lang['zb_install']['not_create_option_file'] . "<br/>";

        $s = "<pre>&lt;" . "?" . "php\r\n";
        $s .= "return ";
        $option = array();
        $reserve_keys = explode('|', ZBlogPHP::OPTION_RESERVE_KEYS);
        foreach ($zbp->option as $key => $value) {
            if (in_array($key, $reserve_keys)) {
                $option[$key] = $value;
            }
        }
        $s .= var_export($option, true);
        $s .= ";\r\n</pre>";

        echo $s;
    }

    $zbp->Config('cache')->templates_md5 = '';
    $zbp->SaveCache();

    $zbp->Config('AppCentre')->enabledcheck = 1;
    $zbp->Config('AppCentre')->checkbeta = 0;
    $zbp->Config('AppCentre')->enabledevelop = 0;
    $zbp->Config('AppCentre')->enablegzipapp = 0;
    $zbp->SaveConfig('AppCentre');

    if (is_readable($file_base = $GLOBALS['usersdir'] . 'theme/' . $zbp->option['ZC_BLOG_THEME'] . '/include.php')) {
        if (CheckIncludedFiles($file_base) == false) {
            include $file_base;
        }
    }
    if (function_exists($fn = 'InstallPlugin_' . $zbp->option['ZC_BLOG_THEME'])) {
        $fn();
    }

    $zbp->template = $zbp->PrepareTemplate();
    $zbp->BuildTemplate();

    if (file_exists($zbp->path . 'zb_users/cache/compiled/' . $zbp->option['ZC_BLOG_THEME'] . '/index.php') == false) {
        echo $zbp->lang['zb_install']['not_create_template_file'] . "<br/>";
    }

    $zbp->LoadCategories();
    $zbp->LoadModules();
    $zbp->RegBuildModules();
    $zbp->modulesbyfilename['calendar']->Build();
    $zbp->modulesbyfilename['calendar']->Save();
    $zbp->modulesbyfilename['catalog']->Build();
    $zbp->modulesbyfilename['catalog']->Save();

    echo $zbp->lang['zb_install']['save_option'] . "<br/>";

    return true;
}

RunTime();
