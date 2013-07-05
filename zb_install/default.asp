<%@ CODEPAGE=65001 %>
<%
'///////////////////////////////////////////////////////////////////////////////
'//              Z-Blog
'///////////////////////////////////////////////////////////////////////////////
%>
<% Option Explicit %>
<% 'On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<!-- #include file="../zb_users/c_option.asp" -->
<!-- #include file="../zb_system/function/c_function.asp" -->
<!-- #include file="../zb_system/function/c_system_lib.asp" -->
<!-- #include file="../zb_system/function/c_system_base.asp" -->
<!-- #include file="../zb_system/function/c_system_plugin.asp" -->
<%

Dim username,password,userguid
Dim dbtype,dbpath,dbserver,dbname,dbusername,dbpassword
Dim Checked123(3,7,2),strErrorMsg,bolError
bolError=False
strErrorMsg=""

Dim zblogstep
zblogstep=Request.QueryString("step")

If ZC_DATABASE_PATH<>"" Or ZC_MSSQL_DATABASE<>"" Then
	zblogstep=0
End If

If zblogstep="" Then zblogstep=1

%><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<%=ZC_BLOG_LANGUAGE%>" lang="<%=ZC_BLOG_LANGUAGE%>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<%=ZC_BLOG_LANGUAGE%>" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="generator" content="Z-Blog <%=ZC_BLOG_VERSION%>" />
	<meta name="robots" content="nofollow" />
	<script language="JavaScript" src="../zb_system/script/common.js" type="text/javascript"></script>
	<script language="JavaScript" src="../zb_system/function/c_admin_js_add.asp" type="text/javascript"></script>
	<script language="JavaScript" src="../zb_system/script/md5.js" type="text/javascript"></script>
    <script language="JavaScript" src="../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/jquery-ui.custom.css"  type="text/css" media="screen" />
	<link rel="stylesheet" rev="stylesheet" href="../zb_system/css/admin3.css" type="text/css" media="screen" />
	<title>Z-Blog <%=ZC_BLOG_VERSION%> 安装程序</title>
    
</head>
<body>
  <div class="setup"><form method="post" action="default.asp?step=<%=zblogstep+1%>">
<%

Select Case zblogstep
Case 0 Call Setup0
Case 1 Call Setup1
Case 2 Call Setup2
Case 3 Call Setup3
Case 4 Call Setup4
Case 5 Call Setup5
End  Select
%>
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
<%




Function Setup0()
%>
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
<%
End Function












Function Setup1()
%>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup1"  class="left"></div>
<p><b>安装协议</b> » 环境检查 » 数据库建立与设置 » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">Z-Blog <%=Left(ZC_BLOG_VERSION,3)%> 安装协议</div>
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
<%
End Function


Function Setup2()
%>
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

<%
End Function

Sub ExportError(strData,bError)
	strErrorMsg=strErrorMsg& "<li>"&strData&"</li>"
	If bError=True Then bolError=True
End Sub

Function CheckServer()
	'On Error Resume Next
	Dim a,b
	Dim Pass,strTemp,strDesc
	For a=0 To 3
		For b=0 To 7
			Select Case a
				Case 0
					Select Case b
						Case 0
							strTemp=Request.ServerVariables("SERVER_SOFTWARE")
							Checked123(a,b,0)=strTemp
							strTemp=LCase(StrTemp)
							If CheckRegExp(strTemp,"iis") Then 'IIS
								Checked123(a,b,2)=True
							ElseIf CheckRegExp(strTemp,"kangle") Then 'Kangle
								Call ExportError("Kangle下运行Z-Blog可能有未知问题，建议在IIS下使用",False)
							ElseIf CheckRegExp(strTemp,"apache|win32") Then 'Apache + SeilSoft AHTML
								Call ExportError("Apache下运行Z-Blog可能有未知问题，建议在IIS下使用",False)
							Else
								Call ExportError("非IIS可能有未知问题，建议在IIS下使用",False)
							End If
						Case 1
							Checked123(a,b,2)=IIf(vbsunescape("Z-Blog")<>"Z-Blog",False,True)
							Checked123(a,b,0)="需要服务端同时支持支持Microsoft VBScript和Microsoft JScript"
							If Not Checked123(a,b,2) Then Call ExportError("服务软件不支持Microsoft JScript",True)
						Case 2
							Checked123(a,b,0)=Request.ServerVariables("PATH_TRANSLATED")
							Checked123(a,b,2)=Not(CheckRegExp(Request.ServerVariables("PATH_TRANSLATED"),"[^\x00-\xff]"))
							If Not Checked123(a,b,2) Then Call ExportError("Z-Blog不允许放在中文文件夹下",True)
						Case 3,4,5,6,7 Checked123(a,b,2)=True
					End Select
				Case 1
				
					Select Case b
						Case 0 strTemp="ADODB.Stream":strDesc="用于对数据流进行访问"
						Case 1 strTemp="ADODB.Connection":strDesc="用于对数据库进行操作"
						Case 2 strTemp="ADODB.RecordSet":strDesc="用于对数据库进行操作"
						Case 3 strTemp="Scripting.FileSystemObject":strDesc="用于对文件进行操作"
						Case 4 strTemp="Scripting.Dictionary":strDesc="用于数据缓存、排序"
						Case 5 strTemp="MSXML2.ServerXMLHTTP":strDesc="用于公告、应用中心"
						Case 6 strTemp="Microsoft.XMLDOM":strDesc="用于XML操作"
					End Select
					Checked123(a,b,2)=IIf(b<=6,IsObjInstalled(strTemp),True)
					Checked123(a,b,0)=strDesc
					If Not Checked123(a,b,2) Then
						Call ExportError("Z-Blog使用必备基础组件未注册",True)
					End If
				Case 2
					If Checked123(1,3,2) And Checked123(1,0,2) And Checked123(0,2,2) Then
						Select Case b
							Case 0
								Call CheckVrs("zb_users","Folder",Checked123,a,b)
'								Checked123(a,b,0)="用户文件夹"
							Case 1
								Call CheckVrs("zb_users\cache","Folder",Checked123,a,b)
'								Checked123(a,b,0)="缓存文件夹"
							Case 2
								Call CheckVrs("zb_users\data","Folder",Checked123,a,b)
'								Checked123(a,b,0)="Access文件夹"
							Case 3
								Call CheckVrs("zb_users\include","Folder",Checked123,a,b)
'								Checked123(a,b,0)="引用文件夹"
							Case 4
								Call CheckVrs("zb_users\theme","Folder",Checked123,a,b)
'								Checked123(a,b,0)="模板文件夹"
							Case 5
								Call CheckVrs("zb_users\plugin","Folder",Checked123,a,b)
'								Checked123(a,b,0)="插件文件夹"
							Case 6
								Call CheckVrs("zb_users\upload","Folder",Checked123,a,b)
'								Checked123(a,b,0)="上传文件夹"
							Case 7
								Call CheckVrs("zb_users\c_option.asp","File",Checked123,a,b)
'								Checked123(a,b,0)="系统配置"
						End Select
						If Not Checked123(a,b,2) Then
							Call ExportError(Checked123(a,b,0),True)
						End If
					Else
						Checked123(a,b,2)=False
					End If
				Case 3
					If b=0 Then
						ZC_MSSQL_ENABLE=False
						ZC_DATABASE_PATH="zb_install\zblog.mdb"
						Dim strConnection
						strConnection="Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" & BlogPath & ZC_DATABASE_PATH
						Checked123(a,b,0)=strConnection
						If OpenConnect Then
							Checked123(a,b,2)=True
							CloseConnect
						End If
					Else
						Checked123(a,b,2)=True
					End If
					If Not Checked123(a,b,2) Then
						Call ExportError("IIS可能不运行于32位模式下，无法使用Access数据库。若您准备使用MSSQL数据库，可无视本条错误提示。",False)
					End If
					
			End Select
			If Checked123(a,b,2)=True Then 
				Checked123(a,b,1)="<span class=""bingo""></span>"
			Else
				Checked123(a,b,1)="<span class=""error""></span>"
			End If
		Next
	Next

End Function



Function CheckVrs(ByVal Path,ByVal Method,ByRef ary,ByVal itemA,ByVal itemB)
	Dim objFSO,sSub,strData
	ary(itemA,itemB,2)=False
	Set objFSO=Server.CreateObject("Scripting.FileSystemObject")
	If Method="File" Then 
		If objFSO.FileExists(BlogPath & Path) Then
			strData=LoadFromFile(BlogPath & Path,"utf-8")
			If strData<>"" Then
				Call SaveToFile(BlogPath & Path,strData & "Hello Z-Blog(test)","utf-8",False)
				If InStr(LoadFromFile(BlogPath & Path,"utf-8"),"Hello Z-Blog(test)")>0 Then
					Call SaveToFile(BlogPath & Path,strData,"utf-8",False)
					ary(itemA,itemB,2)=True
				Else
					ary(itemA,itemB,0)="没有覆盖文件的权限"
				End If
			Else
				ary(itemA,itemB,0)="没有读取文件的权限"
			End If
		Else
			ary(itemA,itemB,0)="文件不存在，请检查是否上传完整"
		End If	
	Else
		If objFSO.FolderExists(BlogPath & Path) Then
			On Error Resume Next
			If objFSO.FolderExists(BlogPath & Path& "\testfolder") Then
				Call objFSO.DeleteFolder(BlogPath & Path & "\testfolder")
			End If
			Err.Clear
			Call objFSO.CreateFolder(BlogPath & Path & "\testfolder")
			If Err.Number=0 Then
				Call SaveToFile(BlogPath & Path & "\testfolder\test.html","Hello Z-Blog","utf-8",False)
				If objFSO.FileExists(BlogPath & Path & "\testfolder\test.html") Then
					If LoadFromFile(BlogPath & Path & "\testfolder\test.html","utf-8")="Hello Z-Blog" Then
						Call SaveToFile(BlogPath & Path & "\testfolder\test.html","Hello Z-Blog(test)","utf-8",False)
						If LoadFromFile(BlogPath & Path & "\testfolder\test.html","utf-8")="Hello Z-Blog(test)" Then
							Call objFSO.DeleteFile(BlogPath & Path & "\testfolder\test.html")
							If Err.Number=0 Then
								Call objFSO.DeleteFolder(BlogPath & Path & "\testfolder")
								If Err.Number=0 Then
									ary(itemA,itemB,2)=True
								Else
									ary(itemA,itemB,0)="没有删除文件夹的权限"
								End If
							Else
								ary(itemA,itemB,0)="没有删除文件的权限"
							End If
						Else
							ary(itemA,itemB,0)="没有覆盖文件的权限"
						End If
					Else
						ary(itemA,itemB,0)="没有读取文件的权限"
					End If
				Else
					ary(itemA,itemB,0)="没有创建文件的权限"
				End If
			Else
				ary(itemA,itemB,0)="没有创建文件夹的权限"
			End If
		Else
			ary(itemA,itemB,0)="文件夹不存在，请检查是否上传完整"
		End If	
	End If
	ary(itemA,itemB,0)=BlogPath & Path & ary(itemA,itemB,0)
	CheckVrs=True
End Function



Function Setup3()
%>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup3"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » <b>数据库建立与设置</b> » 安装结果</p>
</dd>
<dd id="ddright">
<div id="title">数据库建立与设置</div>
<div id="content">
<input type="hidden" name="dbtype" id="dbtype" value="access" />
<p><b>类型选择</b>:&nbsp;&nbsp;<label onClick="$('#mssql').hide();$('#access').show();$('#dbtype').val('access');"><input type="radio" name="db" checked="checked" />Access</label>&nbsp;&nbsp;&nbsp;&nbsp;<label onClick="$('#access').hide();$('#mssql').show();$('#dbtype').val('mssql');"><input type="radio" name="db" />MSSQL</label></p>
<div id="access">
<p><b>数&nbsp;据&nbsp;库:</b>&nbsp;&nbsp;<input type="text" name="dbpath" id="dbpath" value="#%20<%=LCase(Replace(RndGuid(),"-",""))%>.mdb" readonly style="width:350px;" /></p>
</div>
<div id="mssql" style="display:none;">
<p><b>数据库主机:</b><input type="text" name="dbserver" id="dbserver" value="(local)" style="width:350px;" /></p>
<p><b>数据库名称:</b><input type="text" name="dbname" id="dbname" value="" style="width:350px;" /></p>
<p><b>用户名称:</b>&nbsp;&nbsp;<input type="text" name="dbusername" id="dbusername" value="" style="width:350px;" /></p>
<p><b>用户密码:</b>&nbsp;&nbsp;<input type="text" name="dbpassword" id="dbpassword" value="" style="width:350px;" /></p>
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
<%
End Function













Function Setup4()
On Error Resume Next
%>
<dl>
<dd id="ddleft">
<img src="../zb_system/image/admin/install.png" alt="Z-Blog2.0在线安装" />
<div class="left">安装进度： </div><div id="setup4"  class="left"></div>
<p><b>安装协议</b> » <b>环境检查</b> » <b>数据库建立与设置</b> » <b>安装结果</b></p>
</dd>
<dd id="ddright">

<div id="title">安装结果</div>
<div id="content">
<%
ZC_BLOG_TITLE=Request.Form("blogtitle")
ZC_BLOG_NAME=ZC_BLOG_TITLE


userguid=RndGuid()
password=MD5(MD5(Request.Form("password")) & userguid)
username=Request.Form("username")

dbtype=Request.Form("dbtype")
dbpath=Request.Form("dbpath")
dbserver=Request.Form("dbserver")
dbname=Request.Form("dbname")
dbusername=Request.Form("dbusername")
dbpassword=Request.Form("dbpassword")

If dbtype="access" Then

	Dim fso
	Set fso = CreateObject("Scripting.FileSystemObject")

	fso.CopyFile BlogPath & "\zb_install\zblog.mdb", BlogPath & "\zb_users\data\" & dbpath

	ZC_DATABASE_PATH="zb_users\data\" & dbpath

	ZC_MSSQL_ENABLE=False

ElseIf dbtype="mssql" Then

	ZC_MSSQL_DATABASE=dbname

	ZC_MSSQL_USERNAME=dbusername

	ZC_MSSQL_PASSWORD=dbpassword

	ZC_MSSQL_SERVER=dbserver

	ZC_MSSQL_ENABLE=True

End If


If OpenConnect()=False Then
	If ZC_MSSQL_ENABLE Then
		Response.Write("<p>抱歉，连接数据库失败！</p><p>您提供的数据库用户名和密码可能不正确，或者无法连接到 "&ZC_MSSQL_SERVER&" 上的数据库服务器，这意味着您的主机数据库服务器已停止工作。</p><p><ul><li>您确认您提供的用户名和密码正确么？</li><li>您确认您提供的主机名正确么？</li><li>您确认数据库服务器运行正常么？</li><li>您确认您购买的数据库是MSSQL而不是MYSQL么？</li></ul></p><p>请您联系您的空间商，或者到<a href='http://bbs.rainbowsoft.org' target='_blank'>Z-Blogger BBS</a>寻求帮助</p><div id='bottom'><input type=""button"" name=""next"" onClick=""history.go(-1)"" id=""netx"" value=""返回"" /></div>")
	Else
		Response.Write("<p>抱歉，连接数据库失败！</p><ul><li>您确定您的IIS运行在32位模式下吗？（<a href='http://www.baidu.com/s?wd=64%E4%BD%8D+ACCESS+asp' target='_blank'>相关帮助</a>）</li><li>您确定您有权限操作该文件夹和临时文件夹吗？</li><li>您确定你的网站空间足够吗？</li></ul><p>请您联系您的空间商，或者到<a href='http://bbs.rainbowsoft.org' target='_blank'>Z-Blogger BBS</a>寻求帮助</p><div id='bottom'><input type=""button"" name=""next"" onClick=""history.go(-1)"" id=""netx"" value=""返回"" /></div>")
	End If
	Response.End

End If


If ZC_MSSQL_ENABLE=False Then
	Call CreateAccessTable()
ElseIf ZC_MSSQL_ENABLE=True Then
	Call CreateMssqlTable()
End If
%><p>数据库表创建成功!</p><%
Call InsertFunctions()
%><p>默认侧栏数据导入成功!</p><%
Call InsertOptions()
%><p>默认配置数据导入成功!</p><%
Call InsertArticleAndPage()
%><p>用户信息导入成功!</p><p>Hell World文章导入成功!</p><p>留言本页面导入成功!</p><%
Call SaveConfigs()
%><p>配置文件c_option.asp保存成功!</p><%

Response.Cookies("password")=""
Response.Cookies("username")=""


Application.Contents.RemoveAll

%>





<p>Z-Blog 2.0安装成功了,现在您可以点击"完成"进入网站首页.</p>

</div>
<div id="bottom">
<input type="button" name="next" onClick="window.location.href='<%=BlogHost%>'" id="netx" value="完成" />
</div>


</dd>
</dl>
<%
End Function

Function setup5()
	Response.Redirect BlogHost
End Function


Function CreateAccessTable()

	objConn.BeginTrans

	objConn.execute("CREATE TABLE [blog_Tag] (tag_ID AutoIncrement primary key,tag_Name VARCHAR(255) default """",tag_Intro text default """",tag_ParentID int default 0,tag_URL VARCHAR(255) default """",tag_Order int default 0,tag_Count int default 0,tag_Template VARCHAR(50) default """",tag_FullUrl VARCHAR(255) default """",tag_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_Article] (log_ID AutoIncrement primary key,log_CateID int default 0,log_AuthorID int default 0,log_Level int default 0,log_Url VARCHAR(255) default """",log_Title VARCHAR(255) default """",log_Intro text default """",log_Content text default """",log_IP VARCHAR(15) default """",log_PostTime datetime default now(),log_CommNums int default 0,log_ViewNums int default 0,log_TrackBackNums int default 0,log_Tag VARCHAR(255) default """",log_IsTop YESNO DEFAULT 0,log_Yea int default 0,log_Nay int default 0,log_Ratting int default 0,log_Template VARCHAR(50) default """",log_FullUrl VARCHAR(255) default """",log_Type int DEFAULT 0,log_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_Category] (cate_ID AutoIncrement primary key,cate_Name VARCHAR(50) default """",cate_Order int default 0,cate_Intro text default """",cate_Count int default 0,cate_URL VARCHAR(255) default """",cate_ParentID int default 0,cate_Template VARCHAR(50) default """",cate_LogTemplate VARCHAR(50) default """",cate_FullUrl VARCHAR(255) default """",cate_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_Comment] (comm_ID AutoIncrement primary key,log_ID int default 0,comm_AuthorID int default 0,comm_Author VARCHAR(20) default """",comm_Content text default """",comm_Email VARCHAR(50) default """",comm_HomePage VARCHAR(255) default """",comm_PostTime datetime default now(),comm_IP VARCHAR(15) default """",comm_Agent text default """",comm_Reply text default """",comm_LastReplyIP VARCHAR(15) default """",comm_LastReplyTime datetime default now(),comm_Yea int default 0,comm_Nay int default 0,comm_Ratting int default 0,comm_ParentID int default 0,comm_IsCheck YESNO DEFAULT FALSE,comm_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_TrackBack] (tb_ID AutoIncrement primary key,log_ID int default 0,tb_URL VARCHAR(255) default """",tb_Title VARCHAR(100) default """",tb_Blog VARCHAR(50) default """",tb_Excerpt text default """",tb_PostTime datetime default now(),tb_IP VARCHAR(15) default """",tb_Agent text default """",tb_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_UpLoad] (ul_ID AutoIncrement primary key,ul_AuthorID int default 0,ul_FileSize int default 0,ul_FileName VARCHAR(255) default """",ul_PostTime datetime default now(),ul_Quote VARCHAR(255) default """",ul_DownNum int default 0,ul_FileIntro VARCHAR(255) default """",ul_DirByTime YESNO DEFAULT 0,ul_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_Counter] (coun_ID AutoIncrement primary key,coun_IP VARCHAR(15) default """",coun_Agent text default """",coun_Refer VARCHAR(255) default """",coun_PostTime datetime default now(),coun_Content text default """",coun_UserID int default 0,coun_PostData  text default """",coun_URL  text default """",coun_AllRequestHeader  text default """",coun_LogName text default """")")

	objConn.execute("CREATE TABLE [blog_Keyword] (key_ID AutoIncrement primary key,key_Name VARCHAR(255) default """",key_Intro text default """",key_URL VARCHAR(255) default """")")

	objConn.execute("CREATE TABLE [blog_Member] (mem_ID AutoIncrement primary key,mem_Level int default 0,mem_Name VARCHAR(20) default """",mem_Password VARCHAR(32) default """",mem_Sex int default 0,mem_Email VARCHAR(50) default """",mem_MSN VARCHAR(50) default """",mem_QQ VARCHAR(50) default """",mem_HomePage VARCHAR(255) default """",mem_LastVisit datetime default now(),mem_Status int default 0,mem_PostLogs int default 0,mem_PostComms int default 0,mem_Intro text default """",mem_IP VARCHAR(15) default """",mem_Count int default 0,mem_Template VARCHAR(50) default """",mem_FullUrl VARCHAR(255) default """",mem_Url VARCHAR(255) default """",mem_Guid VARCHAR(36) default """",mem_Meta text default """")")

	objConn.execute("CREATE TABLE [blog_Config] (conf_Name VARCHAR(255) default """" not null,conf_Value text default """")")
	'objConn.execute("CREATE UNIQUE INDEX index_conf_Name ON [blog_Config](conf_Name)")

	objConn.execute("CREATE TABLE [blog_Function] (fn_ID AutoIncrement primary key,fn_Name VARCHAR(50) default """",fn_FileName VARCHAR(50) default """",fn_Order int default 0,fn_Content text default """",fn_IsHidden YESNO DEFAULT 0,fn_SidebarID int default 0,fn_HtmlID VARCHAR(50) default """",fn_Ftype VARCHAR(5) default """",fn_MaxLi int default 0,fn_Source VARCHAR(50) default """",fn_ViewType VARCHAR(50) default """",fn_IsHideTitle YESNO DEFAULT 0,fn_Meta text default """")")

	objConn.Execute("INSERT INTO [blog_Member]([mem_Level],[mem_Name],[mem_PassWord],[mem_Email],[mem_HomePage],[mem_Intro],[mem_Guid]) VALUES (1,'"&username&"','"&password&"','null@null.com','','','"&userguid&"')")

	objConn.CommitTrans

End Function


Function CreateMssqlTable()

	objConn.BeginTrans

	objConn.execute("CREATE TABLE [blog_Tag] (tag_ID int identity(1,1) not null primary key,tag_Name nvarchar(255) default '',tag_Intro ntext default '',tag_ParentID int default 0,tag_URL nvarchar(255) default '',tag_Order int default 0,tag_Count int default 0,tag_Template nvarchar(50) default '',tag_FullUrl nvarchar(255) default '',tag_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_Article] (log_ID int identity(1,1) not null primary key,log_CateID int default 0,log_AuthorID int default 0,log_Level int default 0,log_Url nvarchar(255) default '',log_Title nvarchar(255) default '',log_Intro ntext default '',log_Content ntext default '',log_IP nvarchar(15) default '',log_PostTime datetime default getdate(),log_CommNums int default 0,log_ViewNums int default 0,log_TrackBackNums int default 0,log_Tag nvarchar(255) default '',log_IsTop bit DEFAULT 0,log_Yea int default 0,log_Nay int default 0,log_Ratting int default 0,log_Template nvarchar(50) default '',log_FullUrl nvarchar(255) default '',log_Type int default 0,log_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_Category] (cate_ID int identity(1,1) not null primary key,cate_Name nvarchar(50) default '',cate_Order int default 0,cate_Intro ntext default '',cate_Count int default 0,cate_URL nvarchar(255) default '',cate_ParentID int default 0,cate_Template nvarchar(50) default '',cate_LogTemplate nvarchar(50) default '',cate_FullUrl nvarchar(255) default '',cate_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_Comment] (comm_ID int identity(1,1) not null primary key,log_ID int default 0,comm_AuthorID int default 0,comm_Author nvarchar(20) default '',comm_Content ntext default '',comm_Email nvarchar(50) default '',comm_HomePage nvarchar(255) default '',comm_PostTime datetime default getdate(),comm_IP nvarchar(15) default '',comm_Agent ntext default '',comm_Reply ntext default '',comm_LastReplyIP nvarchar(15) default '',comm_LastReplyTime datetime default getdate(),comm_Yea int default 0,comm_Nay int default 0,comm_Ratting int default 0,comm_ParentID int default 0,comm_IsCheck bit default 0,comm_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_TrackBack] (tb_ID int identity(1,1) not null primary key,log_ID int default 0,tb_URL nvarchar(255) default '',tb_Title nvarchar(100) default '',tb_Blog nvarchar(50) default '',tb_Excerpt ntext default '',tb_PostTime datetime default getdate(),tb_IP nvarchar(15) default '',tb_Agent ntext default '',tb_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_UpLoad] (ul_ID int identity(1,1) not null primary key,ul_AuthorID int default 0,ul_FileSize int default 0,ul_FileName nvarchar(255) default '',ul_PostTime datetime default getdate(),ul_Quote nvarchar(255) default '',ul_DownNum int default 0,ul_FileIntro nvarchar(255) default '',ul_DirByTime bit DEFAULT 0,ul_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_Counter] (coun_ID int identity(1,1) not null primary key,coun_IP nvarchar(15) default '',coun_Agent ntext default '',coun_Refer nvarchar(255) default '',coun_PostTime datetime default getdate(),coun_Content ntext default '',coun_UserID int default 0,coun_PostData ntext default '',coun_URL ntext default '',coun_AllRequestHeader ntext default '',coun_LogName ntext default '')")


	objConn.execute("CREATE TABLE [blog_Keyword] (key_ID int identity(1,1) not null primary key,key_Name nvarchar(255) default '',key_Intro ntext default '',key_URL nvarchar(255) default '')")

	objConn.execute("CREATE TABLE [blog_Member] (mem_ID int identity(1,1) not null primary key,mem_Level int default 0,mem_Name nvarchar(20) default '',mem_Password nvarchar(32) default '',mem_Sex int default 0,mem_Email nvarchar(50) default '',mem_MSN nvarchar(50) default '',mem_QQ nvarchar(50) default '',mem_HomePage nvarchar(255) default '',mem_LastVisit datetime default getdate(),mem_Status int default 0,mem_PostLogs int default 0,mem_PostComms int default 0,mem_Intro ntext default '',mem_IP nvarchar(15) default '',mem_Count int default 0,mem_Template nvarchar(50) default '',mem_FullUrl nvarchar(255) default '',mem_Url nvarchar(255) default '',mem_Guid  nvarchar(36) default '',mem_Meta ntext default '')")

	objConn.execute("CREATE TABLE [blog_Config] (conf_Name nvarchar(255) not null default '',conf_Value text default '')")

	objConn.execute("CREATE TABLE [blog_Function] (fn_ID int identity(1,1) not null primary key,fn_Name nvarchar(50) default '',fn_FileName nvarchar(50) default '',fn_Order int default 0,fn_Content ntext default '',fn_IsHidden bit DEFAULT 0,fn_SidebarID int default 0,fn_HtmlID nvarchar(50) default '',fn_Ftype nvarchar(5) default '',fn_MaxLi int default 0,fn_Source nvarchar(50) default '',fn_ViewType nvarchar(50) default '',fn_IsHideTitle bit DEFAULT 0,fn_Meta ntext default '')")

	objConn.Execute("INSERT INTO [blog_Member]([mem_Level],[mem_Name],[mem_PassWord],[mem_Email],[mem_HomePage],[mem_Intro],[mem_Guid]) VALUES (1,'"&username&"','"&password&"','null@null.com','','','"&userguid&"')")

	objConn.CommitTrans

End Function


Function InsertFunctions()

Dim t

Set t=new Tfunction
t.Name="导航栏"
t.FileName="navbar"
t.IsHidden=False
t.Source="system"
t.SidebarID=0
t.Order=1
t.Content="<li><a href=""<#ZC_BLOG_HOST#>"">首页</a></li><li><a href=""<#ZC_BLOG_HOST#>tags.asp"">标签</a></li><li id=""menu-page-2""><a href=""<#ZC_BLOG_HOST#>guestbook.html"">留言本</a></li>"
t.HtmlID="divNavBar"
t.Ftype="ul"
t.post


Set t=new Tfunction
t.Name="日历"
t.FileName="calendar"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=2
t.Content=""
t.HtmlID="divCalendar"
t.Ftype="div"
t.IsHideTitle=True
t.post




Set t=new Tfunction
t.Name="控制面板"
t.FileName="controlpanel"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=3
t.Content="<span class=""cp-hello"">您好,欢迎到访网站!</span><br/><span class=""cp-login""><a href=""<#ZC_BLOG_HOST#>zb_system/cmd.asp?act=login"">[<#ZC_MSG009#>]</a></span>&nbsp;&nbsp;<span class=""cp-vrs""><a href=""<#ZC_BLOG_HOST#>zb_system/cmd.asp?act=vrs"">[<#ZC_MSG021#>]</a></span>"
t.HtmlID="divContorPanel"
t.Ftype="div"
t.post




Set t=new Tfunction
t.Name="网站分类"
t.FileName="catalog"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=4
t.Content=""
t.HtmlID="divCatalog"
t.Ftype="ul"
t.post


Set t=new Tfunction
t.Name="搜索"
t.FileName="searchpanel"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=5
t.Content="<form method=""post"" action=""<#ZC_BLOG_HOST#>zb_system/cmd.asp?act=Search""><input type=""text"" name=""edtSearch"" id=""edtSearch"" size=""12"" /> <input type=""submit"" value=""<#ZC_MSG087#>"" name=""btnPost"" id=""btnPost"" /></form>"
t.HtmlID="divSearchPanel"
t.Ftype="div"
t.post


Set t=new Tfunction
t.Name="最新留言"
t.FileName="comments"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=6
t.Content=""
t.HtmlID="divComments"
t.Ftype="ul"
t.post




Set t=new Tfunction
t.Name="文章归档"
t.FileName="archives"
t.IsHidden=True
t.Source="system"
t.SidebarID=1
t.Order=7
t.Content=""
t.HtmlID="divArchives"
t.Ftype="ul"
t.post



Set t=new Tfunction
t.Name="站点统计"
t.FileName="statistics"
t.IsHidden=False
t.Source="system"
t.SidebarID=0
t.Order=8
t.Content=""
t.HtmlID="divStatistics"
t.Ftype="ul"
t.post




Set t=new Tfunction
t.Name="网站收藏"
t.FileName="favorite"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=9
t.Content="<li><a href=""http://bbs.rainbowsoft.org/"" target=""_blank"">ZBlogger社区</a></li><li><a href=""http://download.rainbowsoft.org/"" target=""_blank"">菠萝的海</a></li><li><a href=""http://t.qq.com/zblogcn"" target=""_blank"">Z-Blog微博</a></li>"
t.HtmlID="divFavorites"
t.Ftype="ul"
t.post




Set t=new Tfunction
t.Name="友情链接"
t.FileName="link"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=10
t.Content="<li><a href=""http://www.dbshost.cn/"" target=""_blank"" title=""独立博客服务 Z-Blog官方主机"">DBS主机</a></li><li><a href=""http://www.dutory.com/blog/"" target=""_blank"">Dutory官方博客</a></li>"
t.HtmlID="divLinkage"
t.Ftype="ul"
t.post



Set t=new Tfunction
t.Name="图标汇集"
t.FileName="misc"
t.IsHidden=False
t.Source="system"
t.SidebarID=1
t.Order=11
t.Content="<li><a href=""http://www.rainbowsoft.org/"" target=""_blank""><img src=""<#ZC_BLOG_HOST#>zb_system/image/logo/zblog.gif"" height=""31"" width=""88"" alt=""RainbowSoft Studio Z-Blog"" /></a></li><li><a href=""<#ZC_BLOG_HOST#>feed.asp"" target=""_blank""><img src=""<#ZC_BLOG_HOST#>zb_system/image/logo/rss.png"" height=""31"" width=""88"" alt=""订阅本站的 RSS 2.0 新闻聚合"" /></a></li>"
t.HtmlID="divMisc"
t.Ftype="ul"
t.post




Set t=new Tfunction
t.Name="作者列表"
t.FileName="authors"
t.IsHidden=False
t.Source="system"
t.SidebarID=0
t.Order=12
t.Content=""
t.HtmlID="divAuthors"
t.Ftype="ul"
t.post




Set t=new Tfunction
t.Name="最近发表"
t.FileName="previous"
t.IsHidden=False
t.Source="system"
t.SidebarID=0
t.Order=13
t.Content=""
t.HtmlID="divPrevious"
t.Ftype="ul"
t.post



Set t=new Tfunction
t.Name="Tags列表"
t.FileName="tags"
t.IsHidden=False
t.Source="system"
t.SidebarID=0
t.Order=14
t.Content=""
t.HtmlID="divTags"
t.Ftype="ul"
t.post


End Function





Function InsertOptions()

BlogConfig.Load("Blog")

'---------------------------------网站基本设置-----------------------------------
Call BlogConfig.Write("ZC_BLOG_HOST","http://localhost/")
Call BlogConfig.Write("ZC_BLOG_TITLE","My Blog")
Call BlogConfig.Write("ZC_BLOG_SUBTITLE","Hello, world!")
Call BlogConfig.Write("ZC_BLOG_NAME","My Blog")
Call BlogConfig.Write("ZC_BLOG_SUB_NAME","Hello, world!")
Call BlogConfig.Write("ZC_BLOG_THEME","default")
Call BlogConfig.Write("ZC_BLOG_CSS","default")
Call BlogConfig.Write("ZC_BLOG_COPYRIGHT","Copyright Your WebSite. Some Rights Reserved.")
Call BlogConfig.Write("ZC_BLOG_MASTER","zblogger")
Call BlogConfig.Write("ZC_BLOG_LANGUAGE","zh-CN")
Call BlogConfig.Write("ZC_BLOG_LANGUAGEPACK","SimpChinese")





'----------------------------数据库配置---------------------------------------
Call BlogConfig.Write("ZC_DATABASE_PATH","")
Call BlogConfig.Write("ZC_MSSQL_ENABLE",False)
Call BlogConfig.Write("ZC_MSSQL_DATABASE","")
Call BlogConfig.Write("ZC_MSSQL_USERNAME","")
Call BlogConfig.Write("ZC_MSSQL_PASSWORD","")
Call BlogConfig.Write("ZC_MSSQL_SERVER","(local)")





'---------------------------------插件----------------------------------------
Call BlogConfig.Write("ZC_USING_PLUGIN_LIST","AppCentre|FileManage|GuestBook|Totoro")








'-------------------------------全局配置-----------------------------------
Call BlogConfig.Write("ZC_BLOG_CLSID","BB1C5669-6E37-460C-F415-D287D7BBB59E")
Call BlogConfig.Write("ZC_TIME_ZONE","+0800")
Call BlogConfig.Write("ZC_HOST_TIME_ZONE","+0800")
Call BlogConfig.Write("ZC_UPDATE_INFO_URL","http://update.rainbowsoft.org/info/")
Call BlogConfig.Write("ZC_MULTI_DOMAIN_SUPPORT",False)
Call BlogConfig.Write("ZC_PERMANENT_DOMAIN_ENABLE",False)



'留言评论
Call BlogConfig.Write("ZC_COMMENT_TURNOFF",False)
Call BlogConfig.Write("ZC_COMMENT_VERIFY_ENABLE",False)
Call BlogConfig.Write("ZC_COMMENT_REVERSE_ORDER_EXPORT",False)
Call BlogConfig.Write("ZC_COMMNET_MAXFLOOR",4)


'验证码
Call BlogConfig.Write("ZC_VERIFYCODE_STRING","0123456789")
Call BlogConfig.Write("ZC_VERIFYCODE_WIDTH",60)
Call BlogConfig.Write("ZC_VERIFYCODE_HEIGHT",20)


Call BlogConfig.Write("ZC_DISPLAY_COUNT",10)
Call BlogConfig.Write("ZC_RSS2_COUNT",10)
Call BlogConfig.Write("ZC_SEARCH_COUNT",25)
Call BlogConfig.Write("ZC_PAGEBAR_COUNT",10)
Call BlogConfig.Write("ZC_MUTUALITY_COUNT",10)
Call BlogConfig.Write("ZC_COMMENTS_DISPLAY_COUNT",50)


Call BlogConfig.Write("ZC_USE_NAVIGATE_ARTICLE",True)
Call BlogConfig.Write("ZC_RSS_EXPORT_WHOLE",False)
Call BlogConfig.Write("ZC_DEFAULT_PAGES_TEMPLATE","")
Call BlogConfig.Write("ZC_ARCHIVES_OLD_LISTTYPE","")





'后台管理
Call BlogConfig.Write("ZC_MANAGE_COUNT",50)
Call BlogConfig.Write("ZC_REBUILD_FILE_COUNT",50)
Call BlogConfig.Write("ZC_REBUILD_FILE_INTERVAL",1)




'侧栏
Call BlogConfig.Write("ZC_SIDEBAR_ORDER","calendar:controlpanel:catalog:searchpanel:comments:archives:favorite:link:misc")
Call BlogConfig.Write("ZC_SIDEBAR_ORDER2","")
Call BlogConfig.Write("ZC_SIDEBAR_ORDER3","")
Call BlogConfig.Write("ZC_SIDEBAR_ORDER4","")
Call BlogConfig.Write("ZC_SIDEBAR_ORDER5","")





'UBB转换
Call BlogConfig.Write("ZC_UBB_ENABLE",False)
Call BlogConfig.Write("ZC_UBB_LINK_ENABLE",False)
Call BlogConfig.Write("ZC_UBB_FONT_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_CODE_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_FACE_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_IMAGE_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_MEDIA_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_FLASH_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_TYPESET_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_AUTOLINK_ENABLE",True)
Call BlogConfig.Write("ZC_UBB_AUTOKEY_ENABLE",False)




'表情相关


Call BlogConfig.Write("ZC_EMOTICONS_FILENAME","default")
Call BlogConfig.Write("ZC_EMOTICONS_FILETYPE","png|jpg|gif")
Call BlogConfig.Write("ZC_EMOTICONS_FILESIZE",16)




'上传相关
Call BlogConfig.Write("ZC_UPLOAD_FILETYPE","jpg|gif|png|jpeg|bmp|psd|wmf|ico|rpm|deb|tar|gz|sit|7z|bz2|zip|rar|xml|xsl|svg|svgz|doc|xls|wps|chm|txt|pdf|mp3|avi|mpg|rm|ra|rmvb|mov|wmv|wma|swf|fla|torrent|zpi|zti|zba")
Call BlogConfig.Write("ZC_UPLOAD_FILESIZE",10485760)
Call BlogConfig.Write("ZC_UPLOAD_DIRBYMONTH",True)
Call BlogConfig.Write("ZC_UPLOAD_DIRECTORY","zb_users\upload")



'当前 Z-Blog 版本
Call BlogConfig.Write("ZC_BLOG_VERSION","2.0 Doomsday Build 121221")



'用户名,密码,评论长度等限制
Call BlogConfig.Write("ZC_USERNAME_MIN",4)
Call BlogConfig.Write("ZC_USERNAME_MAX",20)
Call BlogConfig.Write("ZC_PASSWORD_MIN",8)
Call BlogConfig.Write("ZC_PASSWORD_MAX",14)
Call BlogConfig.Write("ZC_EMAIL_MAX",30)
Call BlogConfig.Write("ZC_HOMEPAGE_MAX",100)
Call BlogConfig.Write("ZC_CONTENT_MAX",1000)





Call BlogConfig.Write("ZC_UNCATEGORIZED_NAME","未分类")
Call BlogConfig.Write("ZC_UNCATEGORIZED_ALIAS","")
Call BlogConfig.Write("ZC_UNCATEGORIZED_COUNT","0")
Call BlogConfig.Write("ZC_SYNTAXHIGHLIGHTER_ENABLE",False)
Call BlogConfig.Write("ZC_CODEMIRROR_ENABLE",False)
Call BlogConfig.Write("ZC_ARTICLE_EXCERPT_MAX",250)
Call BlogConfig.Write("ZC_HTTP_LASTMODIFIED",False)


'---------------------------------静态化配置-----------------------------------


'{asp html shtml}
Call BlogConfig.Write("ZC_STATIC_TYPE","html")

Call BlogConfig.Write("ZC_STATIC_DIRECTORY","post")

Call BlogConfig.Write("ZC_TEMPLATE_DIRECTORY","template")



'ACTIVE MIX REWRITE
Call BlogConfig.Write("ZC_STATIC_MODE","ACTIVE")
Call BlogConfig.Write("ZC_POST_STATIC_MODE","STATIC")
Call BlogConfig.Write("ZC_ARTICLE_REGEX","{%host%}/{%post%}/{%alias%}.html")
Call BlogConfig.Write("ZC_PAGE_REGEX","{%host%}/{%alias%}.html")
Call BlogConfig.Write("ZC_CATEGORY_REGEX","{%host%}/catalog.asp?cate={%id%}")
Call BlogConfig.Write("ZC_USER_REGEX","{%host%}/catalog.asp?auth={%id%}")
Call BlogConfig.Write("ZC_TAGS_REGEX","{%host%}/catalog.asp?tags={%alias%}")
Call BlogConfig.Write("ZC_DATE_REGEX","{%host%}/catalog.asp?date={%date%}")
Call BlogConfig.Write("ZC_DEFAULT_REGEX","{%host%}/catalog.asp")


BlogConfig.Save

End Function










Function InsertArticleAndPage()

Set Categorys(0)=New TCategory

Dim a

Set a = New TArticle
a.AuthorID=1
a.CateID=0
a.id=0
a.Title="Hello, world!"
a.FType=0
a.Content="<p>欢迎使用Z-Blog,这是程序自动生成的文章.您可以删除或是编辑它,在没有进行&quot;文件重建&quot;前,无法打开该文章页面的,这不是故障:)</p><p>系统总共生成了一个&quot;留言本&quot;页面,和一个&quot;Hello, world!&quot;文章,祝您使用愉快!</p>"'<p>默认管理员账号和密码为:zblogger.</p>"
a.Intro=a.Content
a.Level=4
a.post
Set a=Nothing

Call BlogConfig.Write("ZC_UNCATEGORIZED_COUNT","1")
BlogConfig.Save


Set a = New TArticle
a.AuthorID=1
a.CateID=0
a.id=0
a.title="留言本"
a.FType=1
a.Content="<p>这是我的留言本,欢迎给我留言.</p>"
a.Level=4
a.Alias="guestbook"
a.post
Set a=Nothing



End Function




Function SaveConfigs()

	On Error Resume Next
	Dim a,b
	b=LoadFromFile(BlogPath &"zb_users\c_option.asp","utf-8")
	For Each a In BlogConfig.Meta.Names
		If a="ZC_BLOG_CLSID" Then ZC_BLOG_CLSID=RndGuid()
		If InStr(b,"Dim "& a)>0 Then
			Call Execute("Call BlogConfig.Write("""&a&""","&a&")")
		End If
	Next

	Call BlogConfig.Save()
	Err.Clear


	Call SaveConfig2Option()

End Function


%>
