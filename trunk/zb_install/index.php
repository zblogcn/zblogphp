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

$zblogstep=isset($_GET['step']) ? intval($_GET['step']) : 0;
if($zblogstep=="") { $zblogstep=1;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$zbp->option['ZC_BLOG_LANGUAGE']?>" lang="<?=$zbp->option['ZC_BLOG_LANGUAGE']?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<?=$zbp->option['ZC_BLOG_LANGUAGE']?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
	<meta name="generator" content="Z-Blog <?=$zbp->option['ZC_BLOG_VERSION']?>" />
	<meta name="robots" content="nofollow" />
	<script src="../zb_system/script/common.js" type="text/javascript"></script>
  <script src="../zb_system/function/c_admin_js_add.php" type="text/javascript"></script>
	<script src="../zb_system/script/md5.js" type="text/javascript"></script>
  <script src="../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/jquery-ui.custom.css"  type="text/css" media="screen" />
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/admin3.css" type="text/css" media="screen" />
	<title>Z-BlogPHP <?=$zbp->option['ZC_BLOG_VERSION']?> 安装程序</title>
    
</head>
<body>
	<div class="setup"><form method="post" action="?step=<?php echo $zblogstep+1;?>">
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
  </form></div>

<script language="JavaScript" type="text/javascript">
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
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
<div class="left">安装进度： </div><div id="setup0"  class="left"></div>
<p>安装协议 » 环境检查 » 数据库建立与设置 » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">安装提示</div>
<div id="content">
通过配置文件的检验,您已经安装并配置好Z-BlogPHP了,不能再重复使用安装程序.
</div>
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
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
<div class="left">安装进度： </div><div id="setup1"  class="left"></div>
<p><b>安装协议</b> » 环境检查 » 数据库建立与设置 » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">Z-BlogPHP <?=$GLOBALS['zbp']->option['ZC_BLOG_VERSION']?> 安装协议</div>
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
 <label><input type="checkbox"/>我已阅读并同意此协议.</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="next" id="netx" value="下一步" disabled="disabled" />
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
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
<div class="left">安装进度： </div><div id="setup2"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » 数据库建立与设置 » 安装结果</p>
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
    <td scope="row">zb_users/c_option.asp</td>
    <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option'][0];?></td>
    <td style="text-align:center"><?php echo $GLOBALS['CheckResult']['c_option'][1];?></td>
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
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-BlogPHP 在线安装" />
<div class="left">安装进度： </div><div id="setup3"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » <b>数据库建立与设置</b> » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">数据库建立与设置</div>
<div id="content">
<input type="hidden" name="dbtype" id="dbtype" value="mysql" />
<p><b>类型选择</b>:
  &nbsp;&nbsp;<label onClick="$('#sqlite').hide();$('#sqlite3').hide();$('#mysql').show();$('#dbtype').val('mysql');"><input type="radio" name="db" checked="checked" />MySQL</label>
  &nbsp;&nbsp;<label onClick="$('#mysql').hide();$('#sqlite3').hide();$('#sqlite').show();$('#dbtype').val('sqlite');"<?php if(!$CheckResult['sqlite'][0]){ echo 'style=\'display:none;\''; }?>><input type="radio" name="db" />SQLite</label>
  &nbsp;&nbsp;<label onClick="$('#mysql').hide();$('#sqlite').hide();$('#sqlite3').show();$('#dbtype').val('sqlite3');"<?php if(!$CheckResult['sqlite3'][0]){ echo 'style=\'display:none;\''; }?>><input type="radio" name="db" />SQLite3</label>  
</p>
<div id="sqlite" style="display:none;">
<p><b>数据库:</b>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dbsqlite_name" id="dbsqlite_name" value="<?php echo CreateDbName()?>.db" readonly style="width:350px;" /></p>
<p><b>表前缀:</b>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dbsqlite_pre" id="dbsqlite_pre" value="zbp_" style="width:350px;" /></p>
</div>
<div id="sqlite3" style="display:none;">
<p><b>数据库:</b>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dbsqlite3_name" id="dbsqlite3_name" value="<?php echo CreateDbName()?>.db" readonly style="width:350px;" /></p>
<p><b>表前缀:</b>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="dbsqlite3_pre" id="dbsqlite3_pre" value="zbp_" style="width:350px;" /></p>
</div>
<div id="mysql">
<p><b>数据库主机:</b><input type="text" name="dbmysql_server" id="dbmysql_server" value="localhost" style="width:350px;" /></p>
<p><b>用户名称:</b>&nbsp;&nbsp;<input type="text" name="dbmysql_username" id="dbmysql_username" value="" style="width:350px;" /></p>
<p><b>用户密码:</b>&nbsp;&nbsp;<input type="text" name="dbmysql_password" id="dbmysql_password" value="" style="width:350px;" /></p>
<p><b>数据库名称:</b><input type="text" name="dbmysql_name" id="dbmysql_name" value="" style="width:350px;" /></p>
<p><b>表&nbsp;前&nbsp;缀:</b>&nbsp;&nbsp;<input type="text" name="dbmysql_pre" id="dbmysql_pre" value="zbp_" style="width:350px;" /></p>
</div>
<p class="title">网站设置</p>
<p><b>网站名称:</b>&nbsp;&nbsp;<input type="text" name="blogtitle" id="blogtitle" value="" style="width:350px;" /></p>
<p><b>用&nbsp;户&nbsp;名:</b>&nbsp;&nbsp;<input type="text" name="username" id="username" value="" style="width:250px;" />&nbsp;(英文,数字,汉字和._的组合)</p>
<p><b>密&nbsp;&nbsp;&nbsp;&nbsp;码:</b>&nbsp;&nbsp;<input type="password" name="password" id="password" value="" style="width:250px;" />&nbsp;(8位或更长的数字和字母,字符组合)</p>
<p><b>确认密码:</b>&nbsp;&nbsp;<input type="password" name="repassword" id="repassword" value="" style="width:250px;" /></p>
</div>
<div id="bottom">
<input type="submit" name="next" id="netx" onClick="return Setup3()" value="下一步" />
</div>
</dd>
</dl>
<?php
}

function Setup4(){

?>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup4"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » <b>数据库建立与设置</b> » <b>安装结果</b></p>
</dd>
<dd id="ddright">

<div id="title">安装结果</div>
<div id="content">

<?php

$dbtype=isset($_POST['dbtype']) ? $_POST['dbtype'] : '';
#echo $dbtype;

switch ($dbtype) {
  case 'mysql':

    $dbf=DbFactory::Create('mysql');
    if($dbf->Open(array($_POST['dbmysql_server'],$_POST['dbmysql_username'],$_POST['dbmysql_password'],$_POST['dbmysql_name'],$_POST['dbmysql_pre']))==true){
      $dbf->CreateTable();
      $dbf->Close();
    } else {
      echo 'MySQL服务器连接失败，或数据库不存在。';
    }

    break;
  case 'sqlite':

    $dbf=DbFactory::Create('sqlite');
    if($dbf->Open(array($GLOBALS["zbp"]->path . $_POST['dbsqlite_name'],$_POST['dbsqlite_pre']))==true){
      $dbf->CreateTable();
      $dbf->Close();
   } else {
      echo 'SQLite数据库创建失败。';
    }

    break;
  case 'sqlite3':

    $dbf=DbFactory::Create('sqlite3');
    if($dbf->Open(array($GLOBALS["zbp"]->path . $_POST['dbsqlite3_name'],$_POST['dbsqlite3_pre']))==true){
      $dbf->CreateTable();
      $dbf->Close();
   } else {
      echo 'SQLite数据库创建失败。';
    }

    break;
}

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
  'server' => array($_SERVER['SERVER_SOFTWARE'],''), 
  'phpver' => array(phpversion(),''), 
  'zbppath' => array($GLOBALS['zbp']->path,''), 
 //组件
  'mysql' => array('',''), 
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
  'c_option'=>array('',''), 
  //函数
  'file_get_contents'=>array(function_exists('file_get_contents'),''),
  'gethostbyname'=>array(function_exists('gethostbyname'),''),
  'xml_parser_create'=>array(function_exists('xml_parser_create'),''),
  'fsockopen'=>array(function_exists('fsockopen'),'')

);
  if( function_exists("gd_info") ){
    $info = gd_info();
    $CheckResult['gd2'][0]=$info['GD Version'];
  }
  if( function_exists("mysql_get_client_info") ){
    $CheckResult['mysql'][0]=mysql_get_client_info();
  }
  if( function_exists("sqlite_libversion") ){
    $CheckResult['sqlite'][0]=sqlite_libversion();
  }
  if( method_exists('SQLite3','version') ){
    $info = SQLite3::version();
    $CheckResult['sqlite3'][0]=$info['versionString'];
  }

  $CheckResult['zb_users'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users')), -4);
  $CheckResult['cache'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/cache')), -4);
  $CheckResult['data'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/data')), -4);
  $CheckResult['include'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/include')), -4);
  $CheckResult['theme'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/theme')), -4);
  $CheckResult['plugin'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/plugin')), -4);
  $CheckResult['upload'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/upload')), -4);
  $CheckResult['c_option'][0]=substr(sprintf('%o', fileperms($GLOBALS['zbp']->path.'zb_users/c_option.php')), -4);

}

echo RunTime();
?>