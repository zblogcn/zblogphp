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
<!-- #include file="..\..\..\..\zb_system\function\c_system_plugin.asp" -->
<!-- #include file="..\..\..\plugin\p_config.asp" -->
<%
Call System_Initialize()
'检查非法链接
Call CheckReference("")
'检查权限
If BlogUser.Level>1 Then Call ShowError(6)
'检查过滤
Dim s
'For Each s In Request.Form
	'FilterSQL(Request.Form(s))
'Next 

Dim strb,strc,strh,strl

strl=Request.Form("layout")
strb=Request.Form("bodybg0")&"|"&Request.Form("bodybg1")&"|"&Replace(Request.Form("bodybg2"),","," ")&"|"&Request.Form("bodybg3")&"|"&Request.Form("bodybg4")&"|"&Request.Form("bodybg5")

strh=Request.Form("hdbg0")&"|"&Request.Form("hdbg1")&"|"&Replace(Request.Form("hdbg2"),","," ")&"|"&Request.Form("hdbg3")&"|"&Request.Form("hdbg4")&"|"&Request.Form("hdbg5")&"|"&Request.Form("hdbg6")

strc=Request.Form("color")
strc=Replace(Trim(strc),",","|")

'Tconfig
Dim c
Set c = New TConfig
c.Load("metro")
If c.Exists("custom_color")=True Then
	c.Write "custom_layout",strl
	c.Write "custom_bodybg",strb
	c.Write "custom_hdbg",strh
	c.Write "custom_color",strc
	c.Save
End If 
Set c =Nothing

Call metro_savetofile("style.css")
Call SetBlogHint(True,Empty,Empty)

Response.Redirect "editor.asp"
%>

<%Call System_Terminate()%>
