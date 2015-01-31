<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<% Option Explicit %>
<% 'On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<!-- #include file="..\..\..\c_option.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_function.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_lib.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_base.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_event.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_plugin.asp" -->
<!-- #include file="..\..\..\plugin\p_config.asp" -->
<%
Call System_Initialize()
'检查非法链接
Call CheckReference("")
'检查权限
If BlogUser.Level>1 Then Call ShowError(6)

Dim strc
strc=Request.Form("color")

'Tconfig
Dim c
Set c = New TConfig
c.Load("SimplePro")
If c.Exists("Color")=True Then
	c.Write "Color",strc
	c.Save
End If 
Set c =Nothing

'******************************************************************************************
' 保存css文件
'******************************************************************************************
	'替换模版标签
	Dim strContent
	strContent=LoadFromFile(BlogPath & "zb_users\theme\SimplePro\plugin\style.css.html" ,"utf-8")
	strContent=Replace(strContent,"{%Color%}",strc)
	Call SaveToFile(BlogPath & "zb_users\theme\SimplePro\style\style.css",strContent,"utf-8",False)



Call SetBlogHint(True,Empty,Empty)

Response.Redirect "editor.asp"

%>

<%Call System_Terminate()%>
