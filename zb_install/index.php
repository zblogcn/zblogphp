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


$zblogstep=$_GET['step'];
if($zblogstep=="") { $zblogstep=1;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<%=ZC_BLOG_LANGUAGE%>" lang="<%=ZC_BLOG_LANGUAGE%>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<%=ZC_BLOG_LANGUAGE%>" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="generator" content="Z-Blog <%=ZC_BLOG_VERSION%>" />
	<meta name="robots" content="nofollow" />
	<script language="JavaScript" src="../zb_system/script/common.js" type="text/javascript"></script>
	<script language="JavaScript" src="../zb_system/script/md5.js" type="text/javascript"></script>
    <script language="JavaScript" src="../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/jquery-ui.custom.css"  type="text/css" media="screen" />
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/admin3.css" type="text/css" media="screen" />
	<title>Z-Blog <%=ZC_BLOG_VERSION%> 安装程序</title>
    
</head>
<body>
	<div class="setup"><form method="post" action="index.php?step=<?php echo $zblogstep+1;?>">
<?php

switch ($zblogstep) {
    case 0:
        Setup0();
    case 1:
        Setup1();
    case 2:
        Setup2();
    case 3:
        Setup3();
    case 4:
        Setup4();
    case 5:
        Setup5();
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
<img src="../zb_system/image/admin/install.png" alt="Z-Blog With PHP 2.0在线安装" />
<div class="left">安装进度： </div><div id="setup0"  class="left"></div>
<p>安装协议 » 环境检查 » 数据库建立与设置 » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">安装提示</div>
<div id="content">
通过配置文件的检验,您已经安装并配置好Z-Blog了,不能再重复使用安装程序.
</div>
<div id="bottom">
<input type="button" name="next" onClick="window.location.href='<%=BlogHost%>'" id="netx" value="退出" />
</div>
</dd>
</dl>
<?php
}
?>

<?php
function Setup1(){
?>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup1"  class="left"></div>
<p><b>安装协议</b> » 环境检查 » 数据库建立与设置 » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">Z-Blog With PHP <?php echo 131313?> 安装协议</div>
<div id="content">
  <textarea readonly>
Z-Blog  最终用户授权协议 

感谢您选择Z-Blog。 Z-Blog基于 ASP 的技术开发，采用Microsoft Access 和 Microsoft SQL Server作为数据库，全部源码开放。希望我们的努力能为您提供一个高效快速、强大的站点解决方案。

Z-Blog官方网址：http://www.rainbowsoft.org

为了使您正确并合法的使用本软件，请您在使用前务必阅读清楚下面的协议条款： 

一、本授权协议适用且仅适用于 Z-Blog 2.2 版本，Rainbow Studio官方对本授权协议拥有最终解释权。

二、协议许可的权利

1.本程序完全开源，您可以将其用于任何用途。
2.您可以在协议规定的约束和限制范围内修改 Z-Blog 源代码或界面风格以适应您的网站要求。
3.您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。
4.您可以任意分发Z-Blog任何派生版本、修改版本或第三方版本。
5.您可以从Z-Blog提供的应用中心服务中下载适合您网站的应用程序，但应向应用程序开发者/所有者支付相应的费用。

三、协议规定的约束和限制

1. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用Z-Blog 的整体或任何部分，未经书面许可，页面页脚处的版权标识（Powered by Z-Blog） 和Z-Blog官方网站（http://www.rainbowsoft.org）的链接都必须保留，而不能清除或修改。
2.您从应用中心下载的应用程序，未经应用程序开发者/所有者的书面许可，不得对其进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等。
3.如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。

四、有限担保和免责声明

1.本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
2.用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3.电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装Z-Blog，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
4.如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。

版权所有 ©2005-2012，rainbowsoft.org 保留所有权利。 
协议发布时间：2012年10月1 日 版本最新更新：2012年10月1日 By rainbowsoft.org


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
?>

<?php
function Setup2(){
?>

<%CheckServer%>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup2"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » 数据库建立与设置 » 安装结果</p>
<p>错误信息：<ul>
<%=IIf(strErrorMsg="","<li>恭喜，全部测试通过</li>",strErrorMsg)%>
</ul></p>
</dd>
<dd id="ddright">
<div id="title">环境检查</div>
<div id="content">

<table border="0" style="width:100%;">
  <tr>
    <th colspan="3" scope="row">服务器环境检查</th>
  </tr>
  <tr>
    <td scope="row">HTTP服务器</td>
    <td style="text-align:center"><%=Checked123(0,0,0)%></td>
    <td style="text-align:center"><%=Checked123(0,0,1)%></td>
  </tr>
  <tr>
    <td scope="row">ASP Script支持</td>
    <td style="text-align:center"><%=Checked123(0,1,0)%></td>
    <td style="text-align:center"><%=Checked123(0,1,1)%></td>
  </tr>
  <tr>
    <td scope="row">Z-Blog 路径</td>
    <td style="text-align:center"><%=Checked123(0,2,0)%></td>
    <td style="text-align:center"><%=Checked123(0,2,1)%></td>
  </tr>
  <tr>
    <th colspan="3" scope="col">组件支持检查</th>
  </tr>
  <tr>
    <td scope="row" style="width:200px">ADODB.Stream</td>
    <td style="text-align:center"><%=Checked123(1,0,0)%></td>
    <td style="text-align:center"><%=Checked123(1,0,1)%></td>
  </tr>
  <tr>
    <td scope="row">ADODB.Connection</td>
    <td style="text-align:center"><%=Checked123(1,1,0)%></td>
    <td style="text-align:center"><%=Checked123(1,1,1)%></td>
  </tr>
  <tr>
    <td scope="row">ADODB.RecordSet</td>
    <td style="text-align:center"><%=Checked123(1,2,0)%></td>
    <td style="text-align:center"><%=Checked123(1,2,1)%></td>
  </tr>
  <tr>
    <td scope="row">Scripting.FileSystemObject</td>
    <td style="text-align:center"><%=Checked123(1,3,0)%></td>
    <td style="text-align:center"><%=Checked123(1,3,1)%></td>
  </tr>
  <tr>
    <td scope="row">Scripting.Dictionary</td>
    <td style="text-align:center"><%=Checked123(1,4,0)%></td>
    <td style="text-align:center"><%=Checked123(1,4,1)%></td>
  </tr>
  <tr>
    <td scope="row">MSXML2.ServerXMLHTTP</td>
    <td style="text-align:center"><%=Checked123(1,5,0)%></td>
    <td style="text-align:center"><%=Checked123(1,5,1)%></td>
  </tr>
  <tr>
    <td scope="row">Microsoft.XMLDOM</td>
    <td style="text-align:center"><%=Checked123(1,6,0)%></td>
    <td style="text-align:center"><%=Checked123(1,6,1)%></td>
  </tr>
  <tr>
    <th colspan="3" scope="row">权限检查</th>
  </tr>
  <tr>
    <td scope="row">zb_users</td>
    <td style="text-align:center"><%=Checked123(2,0,0)%></td>
    <td style="text-align:center"><%=Checked123(2,0,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\cache</td>
    <td style="text-align:center"><%=Checked123(2,1,0)%></td>
    <td style="text-align:center"><%=Checked123(2,1,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\data</td>
    <td style="text-align:center"><%=Checked123(2,2,0)%></td>
    <td style="text-align:center"><%=Checked123(2,2,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\include</td>
    <td style="text-align:center"><%=Checked123(2,3,0)%></td>
    <td style="text-align:center"><%=Checked123(2,3,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\theme</td>
    <td style="text-align:center"><%=Checked123(2,4,0)%></td>
    <td style="text-align:center"><%=Checked123(2,4,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\plugin</td>
    <td style="text-align:center"><%=Checked123(2,5,0)%></td>
    <td style="text-align:center"><%=Checked123(2,5,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\upload</td>
    <td style="text-align:center"><%=Checked123(2,6,0)%></td>
    <td style="text-align:center"><%=Checked123(2,6,1)%></td>
  </tr>
  <tr>
    <td scope="row">zb_users\c_option.asp</td>
    <td style="text-align:center"><%=Checked123(2,7,0)%></td>
    <td style="text-align:center"><%=Checked123(2,7,1)%></td>
  </tr>
  <tr>
    <th colspan="3" scope="row">数据库连接检查</th>
  </tr>
  <tr>
    <td scope="row">可连接Access</td>
    <td style="text-align:center"><%=Checked123(3,0,0)%></td>
    <td style="text-align:center"><%=Checked123(3,0,1)%></td>
  </tr>

</table>



</div>
<div id="bottom">

<script type="text/javascript">bmx2table();</script>

</div>
<%
If Not bolError Then
%>
<input type="submit" name="next" id="netx" value="下一步" />
<%
End If
%>
</dd>
</dl>
<?php
}
?>

<?php
function Setup3(){
?>

<?php
}
?>

<?php
function Setup4(){
?>

<?php
}
?>

<?php
function Setup5(){
?>

<?php
}
?>

