<%@ CODEPAGE=65001 %>
<%
'///////////////////////////////////////////////////////////////////////////////
'//              Z-Blog
'// 作    者:    朱煊(zx.asd)
'// 版权所有:    RainbowSoft Studio
'// 技术支持:    rainbowsoft@163.com
'// 程序名称:    
'// 程序版本:    
'// 单元名称:    login.asp
'// 开始时间:    2004.07.27
'// 最后修改:    
'// 备    注:    登陆页
'///////////////////////////////////////////////////////////////////////////////
%>
<% Option Explicit %>
<% On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<!-- #include file="../zb_users/c_option.asp" -->
<!-- #include file="function/c_function.asp" -->
<!-- #include file="function/c_system_lib.asp" -->
<!-- #include file="function/c_system_base.asp" -->
<!-- #include file="function/c_system_event.asp" -->
<!-- #include file="function/c_system_plugin.asp" -->
<!-- #include file="../zb_users/plugin/p_config.asp" -->
<%
Call CheckReference("")
Call System_Initialize()
If BlogUser.Verify=True Then Response.Redirect "cmd.asp?act=admin"
%><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<%=ZC_BLOG_LANGUAGE%>" lang="<%=ZC_BLOG_LANGUAGE%>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="<%=ZC_BLOG_LANGUAGE%>" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="generator" content="Z-Blog <%=ZC_BLOG_VERSION%>" />
	<meta name="robots" content="none" />
	<link rel="stylesheet" rev="stylesheet" href="css/admin.css" type="text/css" media="screen" />
	<script language="JavaScript" src="script/common.js" type="text/javascript"></script>
	<script language="JavaScript" src="script/md5.js" type="text/javascript"></script>
	<title><%=ZC_BLOG_TITLE & ZC_MSG044 & ZC_MSG009%></title>
</head>
<body>
<div class="bg">
<div id="wrapper">
  <div class="logo"><img src="image/admin/none.gif" title="Z-Blog<%=ZC_MSG009%>" alt="Z-Blog<%=ZC_MSG009%>"/></div>
  <div class="login">
    <form id="frmLogin" method="post" action="">
    <dl>
      <dd><label for="edtUserName"><%=ZC_MSG003%>:</label><input type="text" id="edtUserName" name="edtUserName" size="20" tabindex="1" /></dd>
      <dd><label for="edtPassWord"><%=ZC_MSG002%>:</label><input type="password" id="edtPassWord" name="edtPassWord" size="20" tabindex="2" /></dd>
    </dl>
    <dl>
      <dd class="checkbox"><input type="checkbox" name="chkRemember" id="chkRemember"  tabindex="3" /><label for="chkRemember"><%=ZC_MSG114%></label></dd>
      <dd class="submit"><input id="btnPost" name="btnPost" type="submit" value="<%=ZC_MSG260%>" class="button" tabindex="4"/></dd>
    </dl>
	<input type="hidden" name="username" id="username" value="" />
	<input type="hidden" name="password" id="password" value="" />
	<input type="hidden" name="savedate" id="savedate" value="0" />
    </form>
  </div>
</div>
</div>

<script language="JavaScript" type="text/javascript">

if(GetCookie("username")){$("#edtUserName").val(unescape(GetCookie("username")))};

$("#btnPost").click(function(){

	var strUserName=$("#edtUserName").val();
	var strPassWord=$("#edtPassWord").val();
	var strSaveDate=$("#savedate").val()

	if((strUserName=="")||(strPassWord=="")){
		alert("<%=ZC_MSG010%>");
		return false;
	}

	$("#edtUserName").remove();
	$("#edtPassWord").remove();

	strUserName=strUserName;
	strPassWord=MD5(strPassWord);

	$("#frmLogin").attr("action","cmd.asp?act=verify");
	$("#username").val(strUserName);
	$("#password").val(strPassWord);
	$("#savedate").val(strSaveDate);
})

$(document).ready(function(){ 
	if($.browser.msie){
		$(":checkbox").css("margin-top","4px");
	}
});

$("#chkRemember").click(function(){
	$("#savedate").attr("value",$("#chkRemember").attr("checked")=="checked"?30:0);
})
</script>
</body>
</html>
<%
Call System_Terminate()

If Err.Number<>0 then
	Call ShowError(0)
End If
%>