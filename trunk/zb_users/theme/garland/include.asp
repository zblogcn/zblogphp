<%
'************************************
' Powered by ThemePluginEditor 1.1
' zsx http://www.zsxsoft.com
'************************************
Dim garland_theme(1)
garland_theme(0)=Array("公告")
garland_theme(1)=Array("new.html")

Call RegisterPlugin("garland","ActivePlugin_garland")

Function ActivePlugin_garland()
	'如果插件需要include代码，则直接在这里加。
    Call Add_Response_Plugin("Response_Plugin_Admin_Top",MakeTopMenu(1,"主题配置",BlogHost & "/zb_users/theme/garland/plugin/editor.asp","agarlandManage",""))
	'这里加文件管理
	If CheckPluginState("FileManage") Then
		Call Add_Action_Plugin("Action_Plugin_FileManage_ExportInformation_NotFound","garland_exportdetail(""{path}"",""{f}"")")
	End If
    '这里是给后台加管理按钮
    If BlogVersion<=121028 Then Call Add_Response_Plugin("Response_Plugin_ThemeMng_SubMenu","<script type='text/javascript'>$(document).ready(function(){$(""#theme-garland .theme-name"").append('<input class=""button"" style=""float:right;margin:0;padding-left:10px;padding-right:10px;"" type=""button"" value=""配置"" onclick=""location.href=\'"&BlogHost&"/zb_users/theme/garland/plugin/editor.asp\'"">')})</script>")
End Function

Function garland_exportdetail(p,f)
	On Error Resume Next
	dim z,k,l,i
	z=LCase(f)
	k=LCase(p)
	l=lcase(blogpath)
	k=IIf(Right(k,1)="\",Left(k,Len(k)-1),k)
	l=IIf(Right(l,1)="\",Left(l,Len(l)-1),l)
	if k=l & "\zb_users\theme\garland\include" Then
		For i=0 To Ubound(garland_theme(1))
			If garland_theme(1)(i)=z Then garland_exportdetail=garland_theme(0)(i)
		Next
	End If
End Function
%>