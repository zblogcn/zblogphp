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


Dim s
For Each s In Request.Form
	If Left(s,7)="include" Then
		Call SaveToFile(BlogPath & "zb_users/theme/garland/include/" & Right(s,Len(s)-8),Request.Form(s),"utf-8",False)
	End If
Next
ClearGlobeCache
LoadGlobeCache
BlogRebuild_Default
Call SetBlogHint(True,Empty,Empty)
Response.Redirect "editor.asp"
%>

<%Call System_Terminate()%>
