<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<%
'************************************
' Powered by ThemePluginEditor 1.1
' zsx http://www.zsxsoft.com
'************************************
%>
<% Option Explicit %>
<% 'On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<!-- #include file="..\..\..\c_option.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_function.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_lib.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_base.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_event.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_manage.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_plugin.asp" -->
<!-- #include file="..\..\..\plugin\p_config.asp" -->

<%

Call System_Initialize()
'检查非法链接
Call CheckReference("")
'检查权限
If BlogUser.Level>1 Then Call ShowError(6)


Dim s,f,o,k
Set o=New RegExp
o.Global=True
o.IgnoreCase=True
f=LoadFromFile(BlogPath & "zb_users/theme/html5css3/source/language.asp","utf-8")

For Each s In Request.Form
	o.Pattern="blog\.(" & Replace(s,"_","\.") & ")=""(.+?)?"";"
	If o.Test(f) Then
		f=o.replace(f,"blog.$1=""" & vbsescape(Request.Form(s)) & """;")
	End If
Next
Call SaveToFile(BlogPath & "zb_users/theme/html5css3/source/language.asp",f,"utf-8",False)
Call SetBlogHint(True,Empty,Empty)
Response.Redirect "editor.asp"
%>

<%Call System_Terminate()%>
