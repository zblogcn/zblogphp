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

require '../zb_system/function/c_system_base.php';

define('bingo','<span class="bingo"></span>');
define('error','<span class="error"></span>');


$zblogstep=(int)GetVars('step');
if($zblogstep=="")$zblogstep=1;

if($zbp->option['ZC_DATABASE_TYPE']&&(!$zbp->option['ZC_YUN_SITE'])){
	$zblogstep=0;
}elseif($zbp->option['ZC_DATABASE_TYPE']&&($zbp->option['ZC_YUN_SITE'])){
	$zbp->Initialize();
	if(count($zbp->members)>0)$zblogstep=0;
}
?>
<!DOCTYPE HTML>
<html>
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
<title>Z-BlogPHP <?php echo ZC_BLOG_VERSION?>安装程序</title>
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
      <input type="button" name="next" onclick="window.location.href='../'" id="netx" value="退出" />
    </div>
  </dd>
</dl>
<?php
}

function Setup1(){
  global $zbp;
?>
<dl>
  <dt></dt>
  <dd id="ddleft"><img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
    <div class="left">安装进度：</div>
    <div id="setup1"  class="left"></div>
    <p><b>安装协议</b>» 环境检查 » 数据库建立与设置 » 安装结果</p>
  </dd>
  <dd id="ddright">
    <div id="title">Z-BlogPHP <?php echo ZC_BLOG_VERSION?>安装协议</div>
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
          <td scope="row">PDO_MySQL</td>
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
<?php if(file_exists('../zb_users/c_option.php')){?>
        <tr>
          <td scope="row">zb_users/c_option.php</td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option.php'][0];?></td>
          <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option.php'][1];?></td>
        </tr>  
<?php }?>
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

  global $CheckResult,$option;
  CheckServer();

  $hasMysql=false;

  $hasSqlite=false;

  $hasMysql=(boolean)((boolean)($CheckResult['mysql'][0]) or (boolean)($CheckResult['pdo_mysql'][0]));

  $hasSqlite=(boolean)((boolean)($CheckResult['sqlite3'][0]) or (boolean)($CheckResult['sqlite'][0]));

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
      <div>
        <p><b>数据库：</b>
        <?php
        if($hasMysql){
        ?>
          <label class="dbselect" id="mysql_radio">
          <input type="radio" name="fdbtype"/>MySQL数据库</label>
        <?php
          echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        ?>
        <?php
        if($hasSqlite){
        ?>
          <label class="dbselect" id="sqlite_radio">
          <input type="radio" name="fdbtype"/>SQLite数据库</label>
        <?php
        }
        ?>
        </p>
      </div>
      <?php if($hasMysql){?>
      <div class="dbdetail" id="mysql">
        <p><b>数据库主机:</b>
          <input type="text" name="dbmysql_server" id="dbmysql_server" value="<?php echo $option['ZC_MYSQL_SERVER'];?>" style="width:350px;" />
        </p>
        <p><b>用户名称:</b>
          <input type="text" name="dbmysql_username" id="dbmysql_username" value="<?php echo $option['ZC_MYSQL_USERNAME'];?>" style="width:350px;" />
        </p>
        <p><b>用户密码:</b>
          <input type="password" name="dbmysql_password" id="dbmysql_password" value="<?php echo $option['ZC_MYSQL_PASSWORD'];?>" style="width:350px;" />
        </p>
        <p><b>数据库名称:</b>
          <input type="text" name="dbmysql_name" id="dbmysql_name" value="<?php echo $option['ZC_MYSQL_NAME'];?>" style="width:350px;" />
        </p>
        <p><b>表&nbsp;前&nbsp;缀:</b>
          <input type="text" name="dbmysql_pre" id="dbmysql_pre" value="<?php echo $option['ZC_MYSQL_PRE'];?>" style="width:350px;" />
        </p>
      <p><b>连接选择:</b> 
        <?php if($CheckResult['mysql'][0]){?>
        <label>
          <input value="mysql" type="radio" name="dbtype"/>MySQL原生连接</label>
        <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php if($CheckResult['pdo_mysql'][0]){?>
        <label>
          <input value="pdo_mysql" type="radio" name="dbtype"/>PDO_MySQL连接</label>
        <?php } ?>
      </p>
      </div>
      <?php } ?>
      <?php if($hasSqlite){?>
      <div class="dbdetail" id="sqlite">
        <p><b>数据库:</b>
          <input type="text" name="dbsqlite_name" id="dbsqlite_name" value="<?php echo GetDbName()?>" readonly style="width:350px;" />
        </p>
        <p><b>表前缀:</b>
          <input type="text" name="dbsqlite_pre" id="dbsqlite_pre" value="zbp_" style="width:350px;" />
        </p>
      <p><b>版本选择:</b>
        <?php if($CheckResult['sqlite'][0]){?>
        <label>
          <input value="sqlite" type="radio" name="dbtype" />SQLite</label>
        <?php 
          echo '&nbsp;&nbsp;&nbsp;&nbsp;';
          }
        ?>
        <?php if($CheckResult['sqlite3'][0]){?>
        <label>
          <input value="sqlite3" type="radio" name="dbtype" />SQLite3</label>
        <?php } ?>
      </p>
      </div>
      <?php } ?>
      <p class="title">网站设置</p>
      <p><b>网站名称:</b>
        <input type="text" name="blogtitle" id="blogtitle" value="" style="width:250px;" />
      </p>
      <p><b>用&nbsp;户&nbsp;名:</b>
        <input type="text" name="username" id="username" value="" style="width:250px;" />
        &nbsp;(英文,数字,汉字或._的组合)</p>
      <p><b>密&nbsp;&nbsp;&nbsp;&nbsp;码:</b>
        <input type="password" name="password" id="password" value="" style="width:250px;" />
        &nbsp;(8位或更长的数字或字母组合)</p>
      <p><b>确认密码:</b>
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
  $("input[name='dbtype']:visible").get(0).click();
});
$(".dbdetail").hide();
$("#"+$(".dbselect").attr("id").split("_radio")[0]).show();
$("input[name='dbtype']:visible").get(0).click();
$("input[name='fdbtype']:visible").get(0).click();
</script>
<?php
}



################################################################################################################
#4
function Setup4(){

  global $zbp;

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
if(!$zbp->option['ZC_YUN_SITE'])FileWriteTest();

$zbp->option['ZC_DATABASE_TYPE']=GetVars('dbtype','POST');

$cts='';

switch ($zbp->option['ZC_DATABASE_TYPE']) {
case 'mysql':
case 'pdo_mysql': 
  $cts=file_get_contents($GLOBALS['blogpath'].'zb_system/defend/createtable/mysql.sql');
  $zbp->option['ZC_MYSQL_SERVER']=GetVars('dbmysql_server','POST');
  $zbp->option['ZC_MYSQL_USERNAME']=GetVars('dbmysql_username','POST');
  $zbp->option['ZC_MYSQL_PASSWORD']=GetVars('dbmysql_password','POST');
  $zbp->option['ZC_MYSQL_NAME']=GetVars('dbmysql_name','POST');
  $zbp->option['ZC_MYSQL_PRE']=GetVars('dbmysql_pre','POST');
  $zbp->InitializeDB($zbp->option['ZC_DATABASE_TYPE']);
  $zbp->db->CreateDB($zbp->option['ZC_MYSQL_SERVER'],$zbp->option['ZC_MYSQL_PORT'],$zbp->option['ZC_MYSQL_USERNAME'],$zbp->option['ZC_MYSQL_PASSWORD'],$zbp->option['ZC_MYSQL_NAME']);
  $zbp->db->dbpre=$zbp->option['ZC_MYSQL_PRE'];
  break;
case 'sqlite':
  $cts=file_get_contents($GLOBALS['blogpath'].'zb_system/defend/createtable/sqlite.sql');
  $zbp->option['ZC_SQLITE_NAME']=GetVars('dbsqlite_name','POST');
  $zbp->option['ZC_SQLITE_PRE']=GetVars('dbsqlite_pre','POST');
  break;
case 'sqlite3':
  $cts=file_get_contents($GLOBALS['blogpath'].'zb_system/defend/createtable/sqlite3.sql');
  $zbp->option['ZC_SQLITE_NAME']=GetVars('dbsqlite_name','POST');
  $zbp->option['ZC_SQLITE_PRE']=GetVars('dbsqlite_pre','POST');
  break;
}

$zbp->OpenConnect();
$zbp->db->QueryMulit($cts);

InsertInfo();

SaveConfig();

$zbp->CloseConnect();

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
  global $zbp;
  header('Location: '.$zbp->host);

}


$CheckResult=null;

function CheckServer(){
global $zbp;
global $CheckResult;

$CheckResult=array(
 //服务器 
  'server' => array(GetVars('SERVER_SOFTWARE','SERVER'),''), 
  'phpver' => array(phpversion(),''), 
  'zbppath' => array($zbp->path,''), 
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
  'c_option.php'=>array('',''), 
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

  getRightsAndExport('','zb_users');
  getRightsAndExport('zb_users/','cache');
  getRightsAndExport('zb_users/','data');
  getRightsAndExport('zb_users/','theme');
  getRightsAndExport('zb_users/','plugin');
  getRightsAndExport('zb_users/','upload');
  getRightsAndExport('zb_users/','c_option.php');

  $CheckResult['file_get_contents'][1]=function_exists('file_get_contents')?bingo:error;
  $CheckResult['gethostbyname'][1]=function_exists('gethostbyname')?bingo:error;
  $CheckResult['xml_parser_create'][1]=function_exists('xml_parser_create')?bingo:error;
  $CheckResult['fsockopen'][1]=function_exists('fsockopen')?bingo:error;

}

function getRightsAndExport($folderparent,$folder){
  global $zbp;
  $s=GetFilePerms($zbp->path.$folderparent.$folder);
  $o=GetFilePermsOct($zbp->path.$folderparent.$folder);
  $GLOBALS['CheckResult'][$folder][0]=$s . ' | ' . $o;
  if(substr($s,0,1)=='-'){
    $GLOBALS['CheckResult'][$folder][1]=substr($s,1,2)=='rw'&&substr($s,-3,2)=='rw'?bingo:error;  
  }else{
    $GLOBALS['CheckResult'][$folder][1]=substr($s,1,3)=='rwx'&&substr($s,-3,2)=='rw'?bingo:error;  
  }
}

function InsertInfo(){
  global $zbp;
	
  $mem = new Member();
  $guid=GetGuid();

  $mem->Guid=$guid;
  $mem->Level=1;
  $mem->Name=GetVars('username','POST');
  $mem->Password=Member::GetPassWordByGuid(GetVars('password','POST'),$guid);
  $mem->IP=GetGuestIP();
  $mem->PostTime=time();

  $mem->Save();


  $cate = new Category();
  $cate->Name='未分类';
  $cate->Save();
  
  $t=new Module();
  $t->Name="导航栏";
  $t->FileName="navbar";
  $t->Source="system";
  $t->SidebarID=0;
  $t->Content='<li><a id="nvabar-item-index" href="{#ZC_BLOG_HOST#}">首页</a></li><li id="navbar-page-2"><a href="{#ZC_BLOG_HOST#}?id=2">留言本</a></li>';
  $t->HtmlID="divNavBar";
  $t->Type="ul";
  $t->Save();


  $t=new Module();
  $t->Name="日历";
  $t->FileName="calendar";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content="";
  $t->HtmlID="divCalendar";
  $t->Type="div";
  $t->IsHideTitle=true;
  $t->Save();




  $t=new Module();
  $t->Name="控制面板";
  $t->FileName="controlpanel";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content='<span class="cp-hello">您好,欢迎到访网站!</span><br/><span class="cp-login"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=login">[用户登录]</a></span>&nbsp;&nbsp;<span class="cp-vrs"><a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=misc&amp;type=vrs">[查看权限]</a></span>';
  $t->HtmlID="divContorPanel";
  $t->Type="div";
  $t->Save();




  $t=new Module();
  $t->Name="网站分类";
  $t->FileName="catalog";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content="";
  $t->HtmlID="divCatalog";
  $t->Type="ul";
  $t->Save();


  $t=new Module();
  $t->Name="搜索";
  $t->FileName="searchpanel";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content='<form name="search" method="post" action="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=search"><input type="text" name="q" size="11" /> <input type="submit" value="搜索" /></form>';
  $t->HtmlID="divSearchPanel";
  $t->Type="div";
  $t->Save();


  $t=new Module();
  $t->Name="最新留言";
  $t->FileName="comments";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content="";
  $t->HtmlID="divComments";
  $t->Type="ul";
  $t->Save();




  $t=new Module();
  $t->Name="文章归档";
  $t->FileName="archives";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content="";
  $t->HtmlID="divArchives";
  $t->Type="ul";
  $t->Save();



  $t=new Module();
  $t->Name="站点信息";
  $t->FileName="statistics";
  $t->Source="system";
  $t->SidebarID=0;
  $t->Content="";
  $t->HtmlID="divStatistics";
  $t->Type="div";
  $t->Save();




  $t=new Module();
  $t->Name="网站收藏";
  $t->FileName="favorite";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content='<li><a href="http://bbs.rainbowsoft.org/" target="_blank">ZBlogger社区</a></li><li><a href="http://app.rainbowsoft.org/" target="_blank">Z-Blog应用中心</a></li><li><a href="http://t.qq.com/zblogcn" target="_blank">Z-Blog微博</a></li>';
  $t->HtmlID="divFavorites";
  $t->Type="ul";
  $t->Save();




  $t=new Module();
  $t->Name="友情链接";
  $t->FileName="link";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content='<li><a href="http://www.dbshost.cn/" target="_blank" title="独立博客服务 Z-Blog官方主机">DBS主机</a></li>';
  $t->HtmlID="divLinkage";
  $t->Type="ul";
  $t->Save();



  $t=new Module();
  $t->Name="图标汇集";
  $t->FileName="misc";
  $t->Source="system";
  $t->SidebarID=1;
  $t->Content='<li><a href="http://www.rainbowsoft.org/" target="_blank"><img src="{#ZC_BLOG_HOST#}zb_system/image/logo/zblog.gif" height="31" width="88" alt="RainbowSoft Studio Z-Blog" /></a></li><li><a href="{#ZC_BLOG_HOST#}feed.php" target="_blank"><img src="{#ZC_BLOG_HOST#}zb_system/image/logo/rss.png" height="31" width="88" alt="订阅本站的 RSS 2.0 新闻聚合" /></a></li>';
  $t->HtmlID="divMisc";
  $t->Type="ul";
  $t->IsHideTitle=true;
  $t->Save();




  $t=new Module();
  $t->Name="作者列表";
  $t->FileName="authors";
  $t->Source="system";
  $t->SidebarID=0;
  $t->Content="";
  $t->HtmlID="divAuthors";
  $t->Type="ul";
  $t->Save();




  $t=new Module();
  $t->Name="最近发表";
  $t->FileName="previous";
  $t->Source="system";
  $t->SidebarID=0;
  $t->Content="";
  $t->HtmlID="divPrevious";
  $t->Type="ul";
  $t->Save();



  $t=new Module();
  $t->Name="Tags列表";
  $t->FileName="tags";
  $t->Source="system";
  $t->SidebarID=0;
  $t->Content="";
  $t->HtmlID="divTags";
  $t->Type="ul";
  $t->Save();



  $a=new Post();
  $a->CateID=1;
  $a->AuthorID=1;
  $a->Tag='';
  $a->Status=ZC_POST_STATUS_PUBLIC;
  $a->Type=ZC_POST_TYPE_ARTICLE;
  $a->Alias='';
  $a->IsTop=false;
  $a->IsLock=false;
  $a->Title='欢迎使用Z-BlogPHP！';
  $a->Intro='<p>欢迎使用Z-Blog,这是程序自动生成的文章.您可以删除或是编辑它:)</p><p>系统总共生成了一个&quot;留言本&quot;页面,和一个&quot;欢迎使用Z-BlogPHP!&quot;文章,祝您使用愉快!</p>';
  $a->Content='<p>欢迎使用Z-Blog,这是程序自动生成的文章.您可以删除或是编辑它:)</p><p>系统总共生成了一个&quot;留言本&quot;页面,和一个&quot;欢迎使用Z-BlogPHP!&quot;文章,祝您使用愉快!</p>';
  $a->IP=GetGuestIP();
  $a->PostTime=time();
  $a->CommNums=0;
  $a->ViewNums=0;
  $a->Template='';
  $a->Meta='';
  $a->Save();


  $a=new Post();
  $a->CateID=0;
  $a->AuthorID=1;
  $a->Tag='';
  $a->Status=ZC_POST_STATUS_PUBLIC;
  $a->Type=ZC_POST_TYPE_PAGE;
  $a->Alias='';
  $a->IsTop=false;
  $a->IsLock=false;
  $a->Title='留言本';
  $a->Intro='';
  $a->Content='这是一个留言本,是由程序自动生成,您可以编辑修改.';
  $a->IP=GetGuestIP();
  $a->PostTime=time();
  $a->CommNums=0;
  $a->ViewNums=0;
  $a->Template='';
  $a->Meta='';
  $a->Save();  

  echo "创建并插入数据成功!<br/>";
  
}


function SaveConfig(){
	global $zbp;

  $zbp->option['ZC_BLOG_VERSION']=ZC_BLOG_VERSION;
  $zbp->option['ZC_USING_PLUGIN_LIST']='AppCentre|UEditor|Totoro';  
  $zbp->option['ZC_SIDEBAR_ORDER'] ='calendar|controlpanel|catalog|searchpanel|comments|archives|favorite|link|misc';
  $zbp->option['ZC_SIDEBAR2_ORDER']='';
  $zbp->option['ZC_SIDEBAR3_ORDER']='';
  $zbp->option['ZC_SIDEBAR4_ORDER']='';
  $zbp->option['ZC_SIDEBAR5_ORDER']='';

  $zbp->SaveOption();
  //$zbp->BuildTemplate();
  
  echo "保存设置,编译模板成功!<br/>";

}


function FileWriteTest(){
	global $zbp;
	
$f=$zbp->path . 'zb_users/c_option.php';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/c_option.php'成功!<br/>";}

//$f=$zbp->path . 'zb_users/avatar/0.png';
//if(file_exists($f)){file_put_contents($f,file_get_contents($f));
//echo "读写'zb_users/avatar/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/cache/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/cache/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/data/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/data/'目录成功!<br/>";}

//$f=$zbp->path . 'zb_users/emotion/index.html';
//if(file_exists($f)){file_put_contents($f,file_get_contents($f));
//echo "读写'zb_users/emotion/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/language/SimpChinese.php';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/language/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/logs/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/logs/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/plugin/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/plugin/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/theme/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/theme/'目录成功!<br/>";}

$f=$zbp->path . 'zb_users/upload/index.html';
if(file_exists($f)){file_put_contents($f,file_get_contents($f));
echo "读写'zb_users/upload/'目录成功!<br/>";}

}

RunTime();
?>
