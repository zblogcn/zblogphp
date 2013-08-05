<%
'************************************
' Powered by ThemePluginEditor 1.1
' zsx http://www.zsxsoft.com
'************************************
Dim HTML5CSS3_theme(1)
HTML5CSS3_theme(0)=Array("浏览器不支持HTML5时的提示")
HTML5CSS3_theme(1)=Array("html5_warn.html")

Call RegisterPlugin("HTML5CSS3","ActivePlugin_HTML5CSS3")

Function ActivePlugin_HTML5CSS3()
	'如果插件需要include代码，则直接在这里加。
    Call Add_Response_Plugin("Response_Plugin_Admin_Top",MakeTopMenu(1,"主题配置",BlogHost & "/zb_users/theme/HTML5CSS3/plugin/editor.asp","aHTML5CSS3Manage",""))
	'这里加文件管理
	If CheckPluginState("FileManage") Then
		Call Add_Action_Plugin("Action_Plugin_FileManage_ExportInformation_NotFound","HTML5CSS3_exportdetail(""{path}"",""{f}"")")
	End If
    '这里是给后台加管理按钮
    If BlogVersion<=121028 Then Call Add_Response_Plugin("Response_Plugin_ThemeMng_SubMenu","<script type='text/javascript'>$(document).ready(function(){$(""#theme-HTML5CSS3 .theme-name"").append('<input class=""button"" style=""float:right;margin:0;padding-left:10px;padding-right:10px;"" type=""button"" value=""配置"" onclick=""location.href=\'"&BlogHost&"/zb_users/theme/HTML5CSS3/plugin/editor.asp\'"">')})</script>")
End Function

Function HTML5CSS3_exportdetail(p,f)
	On Error Resume Next
	dim z,k,l,i
	z=LCase(f)
	k=LCase(p)
	l=lcase(blogpath)
	k=IIf(Right(k,1)="\",Left(k,Len(k)-1),k)
	l=IIf(Right(l,1)="\",Left(l,Len(l)-1),l)
	if k=l & "\zb_users\theme\HTML5CSS3\include" Then
		For i=0 To Ubound(HTML5CSS3_theme(1))
			If HTML5CSS3_theme(1)(i)=z Then HTML5CSS3_exportdetail=HTML5CSS3_theme(0)(i)
		Next
	End If
End Function
%>