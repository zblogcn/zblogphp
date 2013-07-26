<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */
 
/**
 * 安装程序
 * @param 
 * @return array
 */

require_once '../zb_system/function/c_system_base.php';

#加载默认的c_option.php


define('bingo','<span class="bingo"></span>');
define('error','<span class="error"></span>');
$option = require_once($blogpath . 'zb_system/defend/c_option.php');

$zblogstep=GetVars('step')<>'' ? intval(GetVars('step')) : 0;
if($zblogstep=="") { $zblogstep=1;}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<meta name="generator" content="Z-Blog <?php echo $zbp->option['ZC_BLOG_VERSION']?>" />
<meta name="robots" content="noindex,nofollow"/>
<script src="../zb_system/script/common.js" type="text/javascript"></script>
<script src="../zb_system/script/c_admin_js_add.php" type="text/javascript"></script>
<script src="../zb_system/script/md5.js" type="text/javascript"></script>
<script src="../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../zb_system/css/jquery-ui.custom.css"  type="text/css" media="screen" />
<link rel="stylesheet" href="../zb_system/css/admin3.css" type="text/css" media="screen" />
<title>Z-BlogPHP <?php echo $zbp->option['ZC_BLOG_VERSION']?>安装程序</title>
</head>
<body>
<div class="setup">
  <form method="post" action="?step=<?php echo $zblogstep+1;?>">
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
function Setup3(){
	if($("#dbtype").val()=="mssql"){
		if($("#dbserver").val()==""){alert("数据库服务器需要填写");return false;};
		if($("#dbname").val()==""){alert("数据库名称需要填写");return false;};
		if($("#dbusername").val()==""){alert("数据库用户名需要填写");return false;};
	}



if($("#blogtitle").val()==""){alert("网站标题需要填写");return false;};
if($("#username").val()==""){alert("管理员名称需要填写");return false;};
if($("#password").val()==""){alert("管理员密码需要填写");return false;};
if($("#password").val().toString().search("^[A-Za-z0-9`~!@#\$%\^&\*\-_]{8,}$")==-1){alert("管理员密码必须是8位或更长的数字和字母,字符组合");return false;};
if($("#password").val()!==$("#repassword").val()){alert("必须确认密码");return false;};

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
function Setup0(){
?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup0"  class="left"></div>
    <p>安装协议 » 环境检查 » 数据库建立与设置 » 安装结果</p>
  </dd>
  <dd id="ddright">
    <div id="title">安装提示</div>
    <div id="content">通过配置文件的检验,您已经安装并配置好Z-BlogPHP了,不能再重复使用安装程序.</div>
    <div id="bottom">
      <input type="button" name="next" onClick="window.location.href='<%=BlogHost%>'" id="netx" value="退出" />
    </div>
  </dd>
</dl>
<?php
}

function Setup1(){
?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup1"  class="left"></div>
    <p><b>安装协议</b>» 环境检查 » 数据库建立与设置 » 安装结果</p>
  </dd>
  <dd id="ddright">
    <div id="title">Z-BlogPHP <?php echo $GLOBALS['zbp']->option['ZC_BLOG_VERSION']?>安装协议</div>
    <div id="content">
      <textarea readonly>
Z-BlogPHP  最终用户授权协议 

感谢您选择Z-BlogPHP。 Z-BlogPHP基于 PHP 的技术开发，采用MySQL 和 SQLite 作为数据库，全部源码开放。希望我们的努力能为您提供一个高效快速、强大的站点解决方案。

Z-BlogPHP官方网址：http://www.rainbowsoft.org/

为了使您正确并合法的使用本软件，请您在使用前务必阅读清楚下面的协议条款： 

一、本授权协议适用且仅适用于 Z-BlogPHP 版本，Rainbow Studio官方对本授权协议拥有最终解释权。

二、协议许可的权利

1.本程序完全开源，您可以将其用于任何用途。
2.您可以在协议规定的约束和限制范围内修改 Z-BlogPHP 源代码或界面风格以适应您的网站要求。
3.您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。
4.您可以任意分发Z-BlogPHP任何派生版本、修改版本或第三方版本。
5.您可以从Z-BlogPHP提供的应用中心服务中下载适合您网站的应用程序，但应向应用程序开发者/所有者支付相应的费用。

三、协议规定的约束和限制

1. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用Z-BlogPHP 的整体或任何部分，未经书面许可，页面页脚处的版权标识（Powered by Z-BlogPHP） 和Z-BlogPHP官方网站（http://www.rainbowsoft.org）的链接都必须保留，而不能清除或修改。
2.您从应用中心下载的应用程序，未经应用程序开发者/所有者的书面许可，不得对其进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等。
3.如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。

四、有限担保和免责声明

1.本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
2.用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3.电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装Z-BlogPHP，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
4.如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。

版权所有 ©2005-2013，rainbowsoft.org 保留所有权利。 
协议发布时间：2013年8月1 日 版本最新更新：2013年8月1日 By rainbowsoft.org


  </textarea>
    </div>
    <div id="bottom">
      <label>
        <input type="checkbox"/>
        我已阅读并同意此协议.</label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <input type="submit" name="next" id="netx" value="下一步" disabled="disabled" />
      <script type="text/javascript">
$( "input[type=checkbox]" ).click(function() {
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

function Setup2(){

CheckServer();

?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup2"  class="left"></div>
    <p><b>安装协议</b>»<b>环境检查</b>» 数据库建立与设置 » 安装结果</p>
  </dd>
  <dd id="ddright">
    <div id="title">环境检查</div>
    <div id="content">
      <table border="0" style="width:100%;">
        <tr>
          <th colspan="3" scope="row">服务器环境检查</th>
        </tr>
        <tr>
          <td scope="row">HTTP 服务器</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['server'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['server'][1];?></td>
        </tr>
        <tr>
          <td scope="row">PHP 版本支持</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['phpver'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['phpver'][1];?></td>
        </tr>
        <tr>
          <td scope="row">Z-BlogPHP 路径</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zbppath'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zbppath'][1];?></td>
        </tr>
        <tr>
          <th colspan="3" scope="col">组件支持检查</th>
        </tr>
        <tr>
          <td scope="row" style="width:200px">GD2</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gd2'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gd2'][1];?></td>
        </tr>
        <tr>
          <td scope="row">MySQL</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['mysql'][1];?></td>
        </tr>
        <tr>
          <td scope="row">pdo_mysql</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['pdo_mysql'][1];?></td>
        </tr>
        <tr>
          <td scope="row">SQLite</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite'][1];?></td>
        </tr>
        <tr>
          <td scope="row">SQLite3</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['sqlite3'][1];?></td>
        </tr>
        <tr>
          <th colspan="3" scope="row">权限检查</th>
        </tr>
        <tr>
          <td scope="row">zb_users</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zb_users'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['zb_users'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/cache</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['cache'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['cache'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/data</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['data'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['data'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/include</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['include'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['include'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/theme</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['theme'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['theme'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/plugin</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['plugin'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['plugin'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/upload</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['upload'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['upload'][1];?></td>
        </tr>
        <tr>
          <td scope="row">zb_users/c_option.php</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option_php'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option_php'][1];?></td>
        </tr>
        <tr>
          <th colspan="3" scope="row">函数检查</th>
        </tr>
        <tr>
          <td scope="row">file_get_contents</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['file_get_contents'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['file_get_contents'][1];?></td>
        </tr>
        <tr>
          <td scope="row">gethostbyname</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gethostbyname'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['gethostbyname'][1];?></td>
        </tr>
        <tr>
          <td scope="row">xml_parser_create</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['xml_parser_create'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['xml_parser_create'][1];?></td>
        </tr>
        <tr>
          <td scope="row">fsockopen</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['fsockopen'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['fsockopen'][1];?></td>
        </tr>
      </table>
    </div>
    <div id="bottom">
      <script type="text/javascript">bmx2table();</script>
      <?php ?>
      <input type="submit" name="next" id="netx" value="下一步" />
      <?php ?>
    </div>
  </dd>
</dl>
<?php
}

function Setup3(){

  global $CheckResult;
  CheckServer();
?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup3"  class="left"></div>
    <p><b>安装协议</b>»<b>环境检查</b>»<b>数据库建立与设置</b>» 安装结果</p>
  </dd>
  <dd id="ddright">
    <div id="title">数据库建立与设置</div>
    <div id="content">
      <p><b>类型选择</b>:
        &nbsp;&nbsp;
        <label class="dbselect" id="mysql_radio">
          <input value="mysql" type="radio" name="dbtype" checked="checked"/>
          MySQL</label>
        &nbsp;&nbsp;
        <label class="dbselect" id="pdo_mysql_radio"<?php if(!$CheckResult['pdo_mysql'][0]){ echo 'style=\'display:none;\''; }?>>
          <input value="pdo_mysql" type="radio" name="dbtype" checked="checked" />
          pdo_mysql</label>
        &nbsp;&nbsp;
        <label class="dbselect" id="sqlite_radio"<?php if(!$CheckResult['sqlite'][0]){ echo 'style=\'display:none;\''; }?>>
          <input value="sqlite" type="radio" name="dbtype" />
          SQLite</label>
        &nbsp;&nbsp;
        <label class="dbselect" id="sqlite3_radio"<?php if(!$CheckResult['sqlite3'][0]){ echo 'style=\'display:none;\''; }?>>
          <input value="sqlite3" type="radio" name="dbtype" />
          SQLite3</label>
      </p>
      <div class="dbdetail" id="sqlite" style="display:none;">
        <p><b>数据库:</b>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="text" name="dbsqlite_name" id="dbsqlite_name" value="<?php echo GetDbName()?>" readonly style="width:350px;" />
        </p>
        <p><b>表前缀:</b>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="text" name="dbsqlite_pre" id="dbsqlite_pre" value="zbp_" style="width:350px;" />
        </p>
      </div>
      <div class="dbdetail" id="sqlite3" style="display:none;">
        <p><b>数据库:</b>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="text" name="dbsqlite3_name" id="dbsqlite3_name" value="<?php echo GetDbName()?>" readonly style="width:350px;" />
        </p>
        <p><b>表前缀:</b>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type="text" name="dbsqlite3_pre" id="dbsqlite3_pre" value="zbp_" style="width:350px;" />
        </p>
      </div>
      <div class="dbdetail" id="pdo_mysql" style="display:none">
        <p><b>数据库主机:</b>
          <input type="text" name="dbpdo_mysql_server" id="dbpdo_mysql_server" value="localhost" style="width:350px;" />
        </p>
        <p><b>用户名称:</b>&nbsp;&nbsp;
          <input type="text" name="dbpdo_mysql_username" id="dbpdo_mysql_username" value="" style="width:350px;" />
        </p>
        <p><b>用户密码:</b>&nbsp;&nbsp;
          <input type="text" name="dbpdo_mysql_password" id="dbpdo_mysql_password" value="" style="width:350px;" />
        </p>
        <p><b>数据库名称:</b>
          <input type="text" name="dbpdo_mysql_name" id="dbpdo_mysql_name" value="" style="width:350px;" />
        </p>
        <p><b>表&nbsp;前&nbsp;缀:</b>&nbsp;&nbsp;
          <input type="text" name="dbpdo_mysql_pre" id="dbpdo_mysql_pre" value="zbp_" style="width:350px;" />
        </p>
      </div>
      <div class="dbdetail" id="mysql">
        <p><b>数据库主机:</b>
          <input type="text" name="dbmysql_server" id="dbmysql_server" value="localhost" style="width:350px;" />
        </p>
        <p><b>用户名称:</b>&nbsp;&nbsp;
          <input type="text" name="dbmysql_username" id="dbmysql_username" value="" style="width:350px;" />
        </p>
        <p><b>用户密码:</b>&nbsp;&nbsp;
          <input type="text" name="dbmysql_password" id="dbmysql_password" value="" style="width:350px;" />
        </p>
        <p><b>数据库名称:</b>
          <input type="text" name="dbmysql_name" id="dbmysql_name" value="" style="width:350px;" />
        </p>
        <p><b>表&nbsp;前&nbsp;缀:</b>&nbsp;&nbsp;
          <input type="text" name="dbmysql_pre" id="dbmysql_pre" value="zbp_" style="width:350px;" />
        </p>
      </div>
      <p class="title">网站设置</p>
      <p><b>网站名称:</b>&nbsp;&nbsp;
        <input type="text" name="blogtitle" id="blogtitle" value="" style="width:250px;" />
      </p>
      <p><b>用&nbsp;户&nbsp;名:</b>&nbsp;&nbsp;
        <input type="text" name="username" id="username" value="" style="width:250px;" />
        &nbsp;(英文,数字,汉字和._的组合)</p>
      <p><b>密&nbsp;&nbsp;&nbsp;&nbsp;码:</b>&nbsp;&nbsp;
        <input type="password" name="password" id="password" value="" style="width:250px;" />
        &nbsp;(8位或更长的数字和字母,字符组合)</p>
      <p><b>确认密码:</b>&nbsp;&nbsp;
        <input type="password" name="repassword" id="repassword" value="" style="width:250px;" />
      </p>
    </div>
    <div id="bottom">
      <input type="submit" name="next" id="netx" onClick="return Setup3()" value="下一步" />
    </div>
  </dd>
</dl>
<script type="text/javascript">
$(".dbselect").click(function(){
  $(".dbdetail").hide();
  $("#"+$(this).attr("id").split("_radio")[0]).show();
})
</script>
<?php
}

function Setup4(){

?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup4"  class="left"></div>
    <p><b>安装协议</b>»<b>环境检查</b>»<b>数据库建立与设置</b>»<b>安装结果</b></p>
  </dd>
  <dd id="ddright">
    <div id="title">安装结果</div>
    <div id="content">
      <?php

$dbtype=GetVars('dbtype','POST');
#echo $dbtype;
$db=DbFactory::Create($dbtype);
$GLOBALS['zbp']->db=&$db;
switch ($dbtype) {
  case 'mysql':
    $array=array(GetVars('dbmysql_server','POST'),GetVars('dbmysql_username','POST'),GetVars('dbmysql_password','POST'),GetVars('dbmysql_name','POST'),GetVars('dbmysql_pre','POST'));
    if($db->Open($array)){
    } else {
      echo '<p>MySQL服务器连接失败，或数据库不存在。</p>
            <p>请确认：</p>
            <ul>
            <li> 您的MySQL帐号密码是否正确？ </li>
            <li> 是否创建了'.GetVars('dbmysql_name','POST').'数据库？</li>
            </ul>
      ';
    }
    break; 
  case 'pdo_mysql':
    $array=array(GetVars('dbpdo_mysql_server','POST'),GetVars('dbpdo_mysql_username','POST'),GetVars('dbpdo_mysql_password','POST'),GetVars('dbpdo_mysql_name','POST'),GetVars('dbpdo_mysql_pre','POST'));
    if($db->Open($array)){
    } else {
      echo '<p>MySQL服务器连接失败，或数据库不存在。</p>
            <p>请确认：</p>
            <ul>
            <li> 您的MySQL帐号密码是否正确？ </li>
            <li> 是否创建了'.GetVars('dbpdo_mysql_name','POST').'数据库？</li>
            </ul>
      ';
    }
    break;
  case 'sqlite':
    $array=array($GLOBALS["zbp"]->path . GetVars('dbsqlite_name','POST'),GetVars('dbsqlite_pre','POST'));
    if($db->Open($array)){
      } else {
      echo 'SQLite数据库创建失败。';
    }
    break;
  case 'sqlite3':
    $array=array($GLOBALS["zbp"]->path . GetVars('dbsqlite3_name','POST'),GetVars('dbsqlite3_pre','POST'));
    if($db->Open($array)){
    } else {
        echo 'SQLite数据库创建失败。';
    }
    break;
}
$db->CreateTable($GLOBALS["zbp"]->path);
InsertInfo();
SaveConfig();
$db->Close();

?>
      
      <!--<p>Z-Blog 2.0安装成功了,现在您可以点击"完成"进入网站首页.</p>-->
      
    </div>
    <div id="bottom">
      <input type="button" name="next" onClick="window.location.href='../'" id="netx" value="完成" />
    </div>
  </dd>
</dl>
<?php
}

function Setup5(){

  header('Location: '.$GLOBALS['zbp']->host);

}


$CheckResult=null;

function CheckServer(){

global $CheckResult;
$CheckResult=array(
 //服务器 
  'server' => array(GetVars('SERVER_SOFTWARE','SERVER'),''), 
  'phpver' => array(phpversion(),''), 
  'zbppath' => array($GLOBALS['zbp']->path,''), 
 //组件
  'mysql' => array('',''), 
  'pdo_mysql' => array('',''),
  'sqlite' => array('',''),
  'sqlite3' => array('',''),
  'gd2' => array('',''), 
 //权限  
  'zb_users'=>array('',''), 
  'cache'=>array('',''), 
  'data'=>array('',''), 
  'include'=>array('',''), 
  'theme'=>array('',''), 
  'plugin'=>array('',''), 
  'upload'=>array('',''), 
  'c_option_php'=>array('',''), 
  //函数
  'file_get_contents'=>array('用于连接应用中心',''),
  'gethostbyname'=>array('用于解析DNS',''),
  'xml_parser_create'=>array('用于处理XML',''),
  'fsockopen'=>array('用于打开文件','')

);

  if((float)(substr(phpversion(),0,3))>=5.2){
    $CheckResult['phpver'][1]=bingo;
  }
  else{
    $CheckResult['phpver'][1]=error;
  }


  if( function_exists("gd_info") ){
    $info = gd_info();
    $CheckResult['gd2'][0]=$info['GD Version'];
  }
  if( function_exists("mysql_get_client_info") ){
    $CheckResult['mysql'][0]=mysql_get_client_info();
  }
  if( class_exists("PDO",false) ){
    $CheckResult['pdo_mysql'][0]=PDO::ATTR_DRIVER_NAME;
  }
  if( function_exists("sqlite_libversion") ){
    $CheckResult['sqlite'][0]=sqlite_libversion();
  }
  if( class_exists('SQLite3',false) ){
    $info = SQLite3::version();
    $CheckResult['sqlite3'][0]=$info['versionString'];
  }

  getRightsAndExport('','zb_users','0777');
  getRightsAndExport('zb_users/','cache','0777');
  getRightsAndExport('zb_users/','data','0777');
  getRightsAndExport('zb_users/','include','0777');
  getRightsAndExport('zb_users/','theme','0777');
  getRightsAndExport('zb_users/','plugin','0777');
  getRightsAndExport('zb_users/','upload','0777');
  getRightsAndExport('zb_users/','c_option.php','0666');


  $CheckResult['file_get_contents'][1]=function_exists('file_get_contents')?bingo:error;
  $CheckResult['gethostbyname'][1]=function_exists('gethostbyname')?bingo:error;
  $CheckResult['xml_parser_create'][1]=function_exists('xml_parser_create')?bingo:error;
  $CheckResult['fsockopen'][1]=function_exists('fsockopen')?bingo:error;

}

function getRightsAndExport($folderparent,$folder,$right){
  $sGlobal=str_replace('.','_',$folder);
  $GLOBALS['CheckResult'][$sGlobal][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.$folderparent.$folder)), -4);
  $GLOBALS['CheckResult'][$sGlobal][1]=$GLOBALS['CheckResult'][$sGlobal][0]==$right?bingo:error;
}

function InsertInfo(){

  $mem = new Member();
  $guid=GetGuid();

  $mem->LoadInfobyArray(array(
      0,
      $guid,
      1,
      0,
      GetVars('username','POST'),
      GetPassWordByGuid(GetVars('password','POST'),$guid),
      '',
      '',
      GetGuestIP(),
      time(),
      '',
      '',
      0,
      0,
      0,
      0,
      '',
      ''
    ));
  $mem->Post();


  $cate = new Category();
  $cate->LoadInfobyArray(array(
    0,
    '未分类',
    0,
    0,
    '',
    '',
    0,
    0,
    '',
    '',
    '',
  ));
  $cate->Post();
  
  $t=new Module();
  $t->Name="导航栏";
  $t->FileName="navbar";
  $t->IsHidden=0;
  $t->Source="system";
  $t->SidebarID=0;
  $t->Order=1;
  $t->Content='<li><a href="<#ZC_BLOG_HOST#>">首页</a></li><li><a href="<#ZC_BLOG_HOST#>tags.php">标签</a></li><li id="menu-page-2"><a href="<#ZC_BLOG_HOST#>guestbook.html">留言本</a></li>';
  $t->HtmlID="divNavBar";
  $t->Ftype="ul";
  $t->Post();


  $t=new Module();
  $t->Name="日历";
  $t->FileName="calendar";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=2;
  $t->Content="";
  $t->HtmlID="divCalendar";
  $t->Ftype="div";
  $t->IsHideTitle=true;
  $t->Post();




  $t=new Module();
  $t->Name="控制面板";
  $t->FileName="controlpanel";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=3;
  $t->Content='<span class="cp-hello">您好,欢迎到访网站!</span><br/><span class="cp-login"><a href="<#ZC_BLOG_HOST#>zb_system/cmd.php?act=login">[<#msg009#>]</a></span>&nbsp;&nbsp;<span class="cp-vrs"><a href="<#ZC_BLOG_HOST#>zb_system/cmd.php?act=vrs">[<#msg021#>]</a></span>';
  $t->HtmlID="divContorPanel";
  $t->Ftype="div";
  $t->Post();




  $t=new Module();
  $t->Name="网站分类";
  $t->FileName="catalog";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=4;
  $t->Content="";
  $t->HtmlID="divCatalog";
  $t->Ftype="ul";
  $t->Post();


  $t=new Module();
  $t->Name="搜索";
  $t->FileName="searchpanel";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=5;
  $t->Content='<form method="post" action="<#ZC_BLOG_HOST#>zb_system/cmd.php?act=Search"><input type="text" name="edtSearch" id="edtSearch" size="12" /> <input type="submit" value="<#msg087#>" name="btnPost" id="btnPost" /></form>';
  $t->HtmlID="divSearchPanel";
  $t->Ftype="div";
  $t->Post();


  $t=new Module();
  $t->Name="最新留言";
  $t->FileName="comments";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=6;
  $t->Content="";
  $t->HtmlID="divComments";
  $t->Ftype="ul";
  $t->Post();




  $t=new Module();
  $t->Name="文章归档";
  $t->FileName="archives";
  $t->IsHidden=true;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=7;
  $t->Content="";
  $t->HtmlID="divArchives";
  $t->Ftype="ul";
  $t->Post();



  $t=new Module();
  $t->Name="站点统计";
  $t->FileName="statistics";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=0;
  $t->Order=8;
  $t->Content="";
  $t->HtmlID="divStatistics";
  $t->Ftype="ul";
  $t->Post();




  $t=new Module();
  $t->Name="网站收藏";
  $t->FileName="favorite";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=9;
  $t->Content='<li><a href="http://bbs.rainbowsof$t->org/" target="_blank">ZBlogger社区</a></li><li><a href="http://download.rainbowsof$t->org/" target="_blank">菠萝的海</a></li><li><a href="http://$t->qq.com/zblogcn" target="_blank">Z-Blog微博</a></li>';
  $t->HtmlID="divFavorites";
  $t->Ftype="ul";
  $t->Post();




  $t=new Module();
  $t->Name="友情链接";
  $t->FileName="link";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=10;
  $t->Content='<li><a href="http://www.dbshos$t->cn/" target="_blank" title="独立博客服务 Z-Blog官方主机">DBS主机</a></li><li><a href="http://www.dutory.com/blog/" target="_blank">Dutory官方博客</a></li>';
  $t->HtmlID="divLinkage";
  $t->Ftype="ul";
  $t->Post();



  $t=new Module();
  $t->Name="图标汇集";
  $t->FileName="misc";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=1;
  $t->Order=11;
  $t->Content='<li><a href="http://www.rainbowsoft.org/" target="_blank"><img src="<#ZC_BLOG_HOST#>zb_system/image/logo/zblog.gif" height="31" width="88" alt="RainbowSoft Studio Z-Blog" /></a></li><li><a href="<#ZC_BLOG_HOST#>feed.php" target="_blank"><img src="<#ZC_BLOG_HOST#>zb_system/image/logo/rss.png" height="31" width="88" alt="订阅本站的 RSS 2.0 新闻聚合" /></a></li>';
  $t->HtmlID="divMisc";
  $t->Ftype="ul";
  $t->Post();




  $t=new Module();
  $t->Name="作者列表";
  $t->FileName="authors";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=0;
  $t->Order=12;
  $t->Content="";
  $t->HtmlID="divAuthors";
  $t->Ftype="ul";
  $t->Post();




  $t=new Module();
  $t->Name="最近发表";
  $t->FileName="previous";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=0;
  $t->Order=13;
  $t->Content="";
  $t->HtmlID="divPrevious";
  $t->Ftype="ul";
  $t->Post();



  $t=new Module();
  $t->Name="Tags列表";
  $t->FileName="tags";
  $t->IsHidden=false;
  $t->Source="system";
  $t->SidebarID=0;
  $t->Order=14;
  $t->Content="";
  $t->HtmlID="divTags";
  $t->Ftype="ul";
  $t->Post();



  $a=new Log();
  $a->CateID=1;
  $a->AuthorID=1;
  $a->Tag='';
  $a->Status=ZC_LOG_STATUS_PUBLIC;
  $a->Type=ZC_LOG_TYPE_ARTICLE;
  $a->Alias='';
  $a->IsTop=false;
  $a->IsLock=false;
  $a->Title='欢迎使用Z-BlogPHP';
  $a->Intro='欢迎使用Z-BlogPHP';
  $a->Content='欢迎使用Z-BlogPHP';
  $a->IP=GetGuestIP();
  $a->PostTime=time();
  $a->CommNums=0;
  $a->ViewNums=0;
  $a->Template='';
  $a->Meta='';
  $a->Post();


  $a=new Log();
  $a->CateID=0;
  $a->AuthorID=1;
  $a->Tag='';
  $a->Status=ZC_LOG_STATUS_PUBLIC;
  $a->Type=ZC_LOG_TYPE_PAGE;
  $a->Alias='';
  $a->IsTop=false;
  $a->IsLock=false;
  $a->Title='留言本';
  $a->Intro='';
  $a->Content='这是一个留言本.';
  $a->IP=GetGuestIP();
  $a->PostTime=time();
  $a->CommNums=0;
  $a->ViewNums=0;
  $a->Template='';
  $a->Meta='';
  $a->Post();  

}


function SaveConfig(){


  $GLOBALS['zbp']->option['ZC_DATABASE_TYPE']=GetVars('dbtype','POST');

  switch ($GLOBALS['zbp']->option['ZC_DATABASE_TYPE']) {
    case 'mysql':
      $GLOBALS['zbp']->option['ZC_MYSQL_SERVER']=GetVars('dbmysql_server','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_USERNAME']=GetVars('dbmysql_username','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_PASSWORD']=GetVars('dbmysql_password','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_NAME']=GetVars('dbmysql_name','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_PRE']=GetVars('dbmysql_pre','POST');	
    case 'pdo_mysql':	
      $GLOBALS['zbp']->option['ZC_MYSQL_SERVER']=GetVars('dbpdo_mysql_server','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_USERNAME']=GetVars('dbpdo_mysql_username','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_PASSWORD']=GetVars('dbpdo_mysql_password','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_NAME']=GetVars('dbpdo_mysql_name','POST');
      $GLOBALS['zbp']->option['ZC_MYSQL_PRE']=GetVars('dbpdo_mysql_pre','POST');
      break;
    case 'sqlite':
      $GLOBALS['zbp']->option['ZC_SQLITE_NAME']=GetVars('dbsqlite_name','POST');
      $GLOBALS['zbp']->option['ZC_SQLITE_PRE']=GetVars('dbsqlite_pre','POST');
      break;
    case 'sqlite3':
      $GLOBALS['zbp']->option['ZC_SQLITE3_NAME']=GetVars('dbsqlite3_name','POST');
      $GLOBALS['zbp']->option['ZC_SQLITE3_PRE']=GetVars('dbsqlite3_pre','POST');
      break;  
  }


      $GLOBALS['zbp']->option['ZC_BLOG_VERSION']='1.0 Alpha Build 130707';

  $GLOBALS['zbp']->SaveOption();

}

RunTime();
?>
