<%@ CODEPAGE=65001 %>
<%
'///////////////////////////////////////////////////////////////////////////////
'//              Z-Blog
'// 作    者:    朱煊(zx.asd)
'// 版权所有:    RainbowSoft Studio
'// 技术支持:    rainbowsoft@163.com
'// 程序名称:    
'// 程序版本:    
'// 单元名称:    cmd.asp
'// 开始时间:    2004.07.27
'// 最后修改:    
'// 备    注:    命令执行&跳转页
'///////////////////////////////////////////////////////////////////////////////
%>
<% Option Explicit %>
<% 'On Error Resume Next %>
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
Call System_Initialize()

'plugin node
For Each sAction_Plugin_Command_Begin in Action_Plugin_Command_Begin
	If Not IsEmpty(sAction_Plugin_Command_Begin) Then Call Execute(sAction_Plugin_Command_Begin)
Next

Dim strAct
strAct=Request.QueryString("act")

'如果不是"接收引用"就要检查非法链接
If (strAct<>"tb") And (strAct<>"search") Then Call CheckReference("")

'权限检查
If Not CheckRights(strAct) Then Call ShowError(6)


Select Case strAct

	'命令列表

	Case "login" 

		Call BlogLogin()

	Case "verify"

		Call BlogVerify()

	Case "logout"

		Call BlogLogout()

	Case "admin" 

		Call BlogAdmin()

	Case "cmt"

		Call CommentPost()

	Case "tb"
		Call TrackBackPost()

	Case "vrs"
		Call ViewRights()

	Case "ArticleMng"

		Call ArticleMng()

	Case "ArticleEdt"

		Call ArticleEdt()

	Case "ArticlePst"

		Call ArticlePst()

	Case "ArticleDel"

		Call ArticleDel()

	Case "CategoryMng"

		Call CategoryMng()

	Case "CategoryEdt"

		Call CategoryEdt()

	Case "CategoryPst"

		Call CategoryPst()

	Case "CategoryDel"

		Call CategoryDel()

	Case "CommentMng"

		Call CommentMng()

	Case "CommentDel"

		Call CommentDel()

	Case "CommentEdt"

		Call CommentEdt()

	Case "CommentSav"

		Call CommentSav()

	Case "CommentGet"

		Call CommentGet()

	Case "CommentAudit"
		
		Call CommentAudit()

	Case "TrackBackMng"

		Call TrackBackMng()

	Case "TrackBackDel"

		Call TrackBackDel()

	Case "TrackBackSnd"

		Call TrackBackSnd()

	Case "UserMng"

		Call UserMng()

	Case "UserCrt"

		Call UserCrt()

	Case "UserEdt"

		Call UserEdt()

	Case "UserMod"

		Call UserMod()

	Case "UserDel"

		Call UserDel()

	Case "FileMng"

		Call FileMng()

	Case "FileSnd"

		Call FileSnd()

	Case "FileUpload"

		Call FileUpload()

	Case "FileDel"

		Call FileDel()

	Case "Search"

		Call Search()

	Case "SettingMng"

		Call SettingMng()

	Case "SettingSav"

		Call SettingSav()

	Case "TagMng"

		Call TagMng()

	Case "TagEdt"

		Call TagEdt()

	Case "TagPst"

		Call TagPst()

	Case "TagDel"

		Call TagDel()

	Case "PlugInMng"

		Call PlugInMng()

	Case "SiteInfo"

		Call SiteInfo()

	Case "SiteFileMng"

		Call SiteFileMng()

	Case "SiteFileEdt"

		Call SiteFileEdt()

	Case "SiteFilePst"

		Call SiteFilePst()

	Case "SiteFileDel"

		Call SiteFileDel()


	Case "gettburl"
		Call TrackBackUrlGet()

	Case "CommentDelBatch"

		Call CommentDelBatch()

	Case "TrackBackDelBatch"

		Call TrackBackDelBatch()

	Case "FileDelBatch"

		Call FileDelBatch()

	Case "ThemeMng"

		Call ThemeMng()

	Case "ThemeSav"

		Call ThemeSav()


	Case "LinkMng"

		Call LinkMng()

	Case "LinkSav"

		Call LinkSav()


	Case "PlugInActive"

		Call PlugInActive()

	Case "PlugInDisable"

		Call PlugInDisable()

	Case "FunctionMng"

		Call FunctionMng()

	Case "FunctionEdt"

		Call FunctionEdt()

	Case "FunctionSav"

		Call FunctionSav()

	Case "FunctionDel"

		Call FunctionDel()

	Case "AskFileReBuild"

		Call AskFileReBuild()

	Case "BlogReBuild"

		Call BlogReBuild()

	Case "FileReBuild"

		Call FileReBuild()

	Case "batch"

		Call Batch()

End Select



Function BlogLogin

	'plugin node
	For Each sAction_Plugin_BlogLogin_Begin in Action_Plugin_BlogLogin_Begin
		If Not IsEmpty(sAction_Plugin_BlogLogin_Begin) Then Call Execute(sAction_Plugin_BlogLogin_Begin)
		If bAction_Plugin_BlogLogin_Begin=True Then Exit Function
	Next

	If BlogUser.Verify=False Then
	'验证失败清空cookies密码
		Response.Cookies("password")=Empty
'		Response.Cookies("password").expires = now-1
		Response.Cookies("password").Path = CookiesPath()
		Response.Redirect "login.asp"
	Else
		Call BlogAdmin()
	End If

End Function

Function BlogVerify()

	'plugin node
	For Each sAction_Plugin_BlogVerify_Begin in Action_Plugin_BlogVerify_Begin
		If Not IsEmpty(sAction_Plugin_BlogVerify_Begin) Then Call Execute(sAction_Plugin_BlogVerify_Begin)
		If bAction_Plugin_BlogVerify_Begin=True Then Exit Function
	Next

	If Login=True Then

		'plugin node
		For Each sAction_Plugin_BlogVerify_Succeed in Action_Plugin_BlogVerify_Succeed
			If Not IsEmpty(sAction_Plugin_BlogVerify_Succeed) Then Call Execute(sAction_Plugin_BlogVerify_Succeed)
			If bAction_Plugin_BlogVerify_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=admin"
	Else
		
		Call ShowError(8)
	End If

End Function

Function BlogLogout

	'plugin node
	For Each sAction_Plugin_BlogLogout_Begin in Action_Plugin_BlogLogout_Begin
		If Not IsEmpty(sAction_Plugin_BlogLogout_Begin) Then Call Execute(sAction_Plugin_BlogLogout_Begin)
		If bAction_Plugin_BlogLogout_Begin=True Then Exit Function
	Next

	If Logout=True Then

		'plugin node
		For Each sAction_Plugin_BlogLogout_Succeed in Action_Plugin_BlogLogout_Succeed
			If Not IsEmpty(sAction_Plugin_BlogLogout_Succeed) Then Call Execute(sAction_Plugin_BlogLogout_Succeed)
			If bAction_Plugin_BlogLogout_Succeed=True Then Exit Function
		Next

		Response.Redirect BlogHost

	End If

End Function

Function BlogAdmin

	'plugin node
	For Each sAction_Plugin_BlogAdmin_Begin in Action_Plugin_BlogAdmin_Begin
		If Not IsEmpty(sAction_Plugin_BlogAdmin_Begin) Then Call Execute(sAction_Plugin_BlogAdmin_Begin)
		If bAction_Plugin_BlogAdmin_Begin=True Then Exit Function
	Next

	'Call MakeBlogReBuild_Core()

	Response.Redirect "admin/admin.asp"

End Function

Function ViewRights()

	'plugin node
	For Each sAction_Plugin_ViewRights_Begin in Action_Plugin_ViewRights_Begin
		If Not IsEmpty(sAction_Plugin_ViewRights_Begin) Then Call Execute(sAction_Plugin_ViewRights_Begin)
		If bAction_Plugin_ViewRights_Begin=True Then Exit Function
	Next

	Call ListUser_Rights()
End Function


Function ArticleMng

	'plugin node
	For Each sAction_Plugin_ArticleMng_Begin in Action_Plugin_ArticleMng_Begin
		If Not IsEmpty(sAction_Plugin_ArticleMng_Begin) Then Call Execute(sAction_Plugin_ArticleMng_Begin)
		If bAction_Plugin_ArticleMng_Begin=True Then Exit Function
	Next

	If LCase(Request.QueryString("type"))="page" Then
		Response.Redirect "admin/admin.asp?act=ArticleMng&type=Page&page=" & Request.QueryString("id")
	End If

	Response.Redirect "admin/admin.asp?act=ArticleMng&page=" & Request.QueryString("id")

End Function

Function ArticleEdt

	'plugin node
	For Each sAction_Plugin_ArticleEdt_Begin in Action_Plugin_ArticleEdt_Begin
		If Not IsEmpty(sAction_Plugin_ArticleEdt_Begin) Then Call Execute(sAction_Plugin_ArticleEdt_Begin)
		If bAction_Plugin_ArticleEdt_Begin=True Then Exit Function
	Next

	If Request.QueryString("webedit")<>"" Then
		If IsEmpty(Request.QueryString("id"))=False Then
			Response.Redirect "admin/edit_article.asp?id="& Request.QueryString("id") & IIf(Request.QueryString("type")="Page","&type=Page","")
		Else
			Response.Redirect "admin/edit_article.asp" & IIf(Request.QueryString("type")="Page","?type=Page","")
		End If
	End If

End Function

Function ArticlePst

	'plugin node
	For Each sAction_Plugin_ArticlePst_Begin in Action_Plugin_ArticlePst_Begin
		If Not IsEmpty(sAction_Plugin_ArticlePst_Begin) Then Call Execute(sAction_Plugin_ArticlePst_Begin)
		If bAction_Plugin_ArticlePst_Begin=True Then Exit Function
	Next

	If PostArticle Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_ArticlePst_Succeed in Action_Plugin_ArticlePst_Succeed
			If Not IsEmpty(sAction_Plugin_ArticlePst_Succeed) Then Call Execute(sAction_Plugin_ArticlePst_Succeed)
			If bAction_Plugin_ArticlePst_Succeed=True Then Exit Function
		Next

		If Request.QueryString("type")="Page" Then
			Response.Redirect "cmd.asp?act=ArticleMng&type=Page"
		Else
			Response.Redirect "cmd.asp?act=ArticleMng"
		End If
	Else
		Call ShowError(11)
	End If
End Function

Function ArticleDel

	'plugin node
	For Each sAction_Plugin_ArticleDel_Begin in Action_Plugin_ArticleDel_Begin
		If Not IsEmpty(sAction_Plugin_ArticleDel_Begin) Then Call Execute(sAction_Plugin_ArticleDel_Begin)
		If bAction_Plugin_ArticleDel_Begin=True Then Exit Function
	Next

	If DelArticle(Request.QueryString("id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_ArticleDel_Succeed in Action_Plugin_ArticleDel_Succeed
			If Not IsEmpty(sAction_Plugin_ArticleDel_Succeed) Then Call Execute(sAction_Plugin_ArticleDel_Succeed)
			If bAction_Plugin_ArticleDel_Succeed=True Then Exit Function
		Next

		If Request.QueryString("type")="Page" Then
			Response.Redirect "cmd.asp?act=ArticleMng&type=Page"
		Else
			Response.Redirect "cmd.asp?act=ArticleMng"
		End If
	Else
		Call ShowError(11)
	End If
End Function


Function CategoryMng

	'plugin node
	For Each sAction_Plugin_CategoryMng_Begin in Action_Plugin_CategoryMng_Begin
		If Not IsEmpty(sAction_Plugin_CategoryMng_Begin) Then Call Execute(sAction_Plugin_CategoryMng_Begin)
		If bAction_Plugin_CategoryMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=CategoryMng&page=" & Request.QueryString("id")
End Function

Function CategoryEdt

	'plugin node
	For Each sAction_Plugin_CategoryEdt_Begin in Action_Plugin_CategoryEdt_Begin
		If Not IsEmpty(sAction_Plugin_CategoryEdt_Begin) Then Call Execute(sAction_Plugin_CategoryEdt_Begin)
		If bAction_Plugin_CategoryEdt_Begin=True Then Exit Function
	Next

	If IsEmpty(Request.QueryString("id"))=False Then
		Response.Redirect "admin/edit_catalog.asp?id=" & Request.QueryString("id")
	Else
		Response.Redirect "admin/edit_catalog.asp"
	End If
End Function

Function CategoryPst

	'plugin node
	For Each sAction_Plugin_CategoryPst_Begin in Action_Plugin_CategoryPst_Begin
		If Not IsEmpty(sAction_Plugin_CategoryPst_Begin) Then Call Execute(sAction_Plugin_CategoryPst_Begin)
		If bAction_Plugin_CategoryPst_Begin=True Then Exit Function
	Next

	If PostCategory Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_CategoryPst_Succeed in Action_Plugin_CategoryPst_Succeed
			If Not IsEmpty(sAction_Plugin_CategoryPst_Succeed) Then Call Execute(sAction_Plugin_CategoryPst_Succeed)
			If bAction_Plugin_CategoryPst_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=CategoryMng"
	Else
		Call ShowError(12)
	End If
End Function

Function CategoryDel

	'plugin node
	For Each sAction_Plugin_CategoryDel_Begin in Action_Plugin_CategoryDel_Begin
		If Not IsEmpty(sAction_Plugin_CategoryDel_Begin) Then Call Execute(sAction_Plugin_CategoryDel_Begin)
		If bAction_Plugin_CategoryDel_Begin=True Then Exit Function
	Next

	If DelCategory(Request.QueryString("id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_CategoryDel_Succeed in Action_Plugin_CategoryDel_Succeed
			If Not IsEmpty(sAction_Plugin_CategoryDel_Succeed) Then Call Execute(sAction_Plugin_CategoryDel_Succeed)
			If bAction_Plugin_CategoryDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=CategoryMng"
	Else
		Call ShowError(12)
	End If
End Function


Function CommentMng

	'plugin node
	For Each sAction_Plugin_CommentMng_Begin in Action_Plugin_CommentMng_Begin
		If Not IsEmpty(sAction_Plugin_CommentMng_Begin) Then Call Execute(sAction_Plugin_CommentMng_Begin)
		If bAction_Plugin_CommentMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=CommentMng&page=" & Request.QueryString("id")
End Function

Function CommentAudit

	'plugin node
	For Each sAction_Plugin_CommentAudit_Begin in Action_Plugin_CommentAudit_Begin
		If Not IsEmpty(sAction_Plugin_CommentAudit_Begin) Then Call Execute(sAction_Plugin_CommentAudit_Begin)
		If bAction_Plugin_CommentAudit_Begin=True Then Exit Function
	Next

	If AuditComment() Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_CommentAudit_Success in Action_Plugin_CommentAudit_Success
			If Not IsEmpty(sAction_Plugin_CommentAudit_Success) Then Call Execute(sAction_Plugin_CommentAudit_Success)
			If bAction_Plugin_CommentAudit_Success=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=CommentMng"
	End If

	
End Function

Function CommentPost

	'plugin node
	For Each sAction_Plugin_CommentPost_Begin in Action_Plugin_CommentPost_Begin
		If Not IsEmpty(sAction_Plugin_CommentPost_Begin) Then Call Execute(sAction_Plugin_CommentPost_Begin)
		If bAction_Plugin_CommentPost_Begin=True Then Exit Function
	Next

	If PostComment(Request.QueryString("key")) Then

		Call ClearGlobeCache
		Call LoadGlobeCache

		'plugin node
		For Each sAction_Plugin_CommentPost_Succeed in Action_Plugin_CommentPost_Succeed
			If Not IsEmpty(sAction_Plugin_CommentPost_Succeed) Then Call Execute(sAction_Plugin_CommentPost_Succeed)
			If bAction_Plugin_CommentPost_Succeed=True Then Exit Function
		Next

		If IsEmpty(Request.Form("inpAjax"))=False Then
			Response.End
		End If

		Response.Redirect Request.ServerVariables("HTTP_REFERER")

	Else
		Call ShowError(14)
	End If

End Function

Function CommentDel

	'plugin node
	For Each sAction_Plugin_CommentDel_Begin in Action_Plugin_CommentDel_Begin
		If Not IsEmpty(sAction_Plugin_CommentDel_Begin) Then Call Execute(sAction_Plugin_CommentDel_Begin)
		If bAction_Plugin_CommentDel_Begin=True Then Exit Function
	Next

	If DelComment(Request.QueryString("id"),Request.QueryString("log_id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_CommentDel_Succeed in Action_Plugin_CommentDel_Succeed
			If Not IsEmpty(sAction_Plugin_CommentDel_Succeed) Then Call Execute(sAction_Plugin_CommentDel_Succeed)
			If bAction_Plugin_CommentDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=CommentMng"
	Else
		Call ShowError(18)
	End If
End Function


Function CommentEdt

	'plugin node
	For Each sAction_Plugin_CommentEdt_Begin in Action_Plugin_CommentEdt_Begin
		If Not IsEmpty(sAction_Plugin_CommentEdt_Begin) Then Call Execute(sAction_Plugin_CommentEdt_Begin)
		If bAction_Plugin_CommentEdt_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/edit_comment.asp?id="& Request.QueryString("id") & "&log_id="& Request.QueryString("log_id")  & "&revid=" & Request.QueryString("revid")

End Function


Function CommentSav

	'plugin node
	For Each sAction_Plugin_CommentSav_Begin in Action_Plugin_CommentSav_Begin
		If Not IsEmpty(sAction_Plugin_CommentSav_Begin) Then Call Execute(sAction_Plugin_CommentSav_Begin)
		If bAction_Plugin_CommentSav_Begin=True Then Exit Function
	Next

	If CLng(Request.QueryString("revid"))<>0 Then
		If SaveRevComment() Then
			Call SetBlogHint(True,True,Empty)
			Call MakeBlogReBuild_Core()

			'plugin node
			For Each sAction_Plugin_CommentSav_Succeed in Action_Plugin_CommentSav_Succeed
				If Not IsEmpty(sAction_Plugin_CommentSav_Succeed) Then Call Execute(sAction_Plugin_CommentSav_Succeed)
				If bAction_Plugin_CommentSav_Succeed=True Then Exit Function
			Next

			Response.Redirect "cmd.asp?act=CommentMng"
		Else
			Call ShowError(60)
		End If
	Else
		If SaveComment() Then
			Call SetBlogHint(True,True,Empty)
			Call MakeBlogReBuild_Core()

			'plugin node
			For Each sAction_Plugin_CommentSav_Succeed in Action_Plugin_CommentSav_Succeed
				If Not IsEmpty(sAction_Plugin_CommentSav_Succeed) Then Call Execute(sAction_Plugin_CommentSav_Succeed)
				If bAction_Plugin_CommentSav_Succeed=True Then Exit Function
			Next

			Response.Redirect "cmd.asp?act=CommentMng"
		Else
			Call ShowError(42)
		End If
	End If

End Function


Function TrackBackMng

	'plugin node
	For Each sAction_Plugin_TrackBackMng_Begin in Action_Plugin_TrackBackMng_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackMng_Begin) Then Call Execute(sAction_Plugin_TrackBackMng_Begin)
		If bAction_Plugin_TrackBackMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=TrackBackMng&page=" & Request.QueryString("id")
End Function

Function TrackBackPost

	'plugin node
	For Each sAction_Plugin_TrackBackPost_Begin in Action_Plugin_TrackBackPost_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackPost_Begin) Then Call Execute(sAction_Plugin_TrackBackPost_Begin)
		If bAction_Plugin_TrackBackPost_Begin=True Then Exit Function
	Next

	If PostTrackBack(Request.QueryString("id"),Request.QueryString("key"))=True Then 
		'plugin node
		For Each sAction_Plugin_TrackBackPost_Succeed in Action_Plugin_TrackBackPost_Succeed
			If Not IsEmpty(sAction_Plugin_TrackBackPost_Succeed) Then Call Execute(sAction_Plugin_TrackBackPost_Succeed)
			If bAction_Plugin_TrackBackPost_Succeed=True Then Exit Function
		Next
	End If

End Function

Function TrackBackDel

	'plugin node
	For Each sAction_Plugin_TrackBackDel_Begin in Action_Plugin_TrackBackDel_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackDel_Begin) Then Call Execute(sAction_Plugin_TrackBackDel_Begin)
		If bAction_Plugin_TrackBackDel_Begin=True Then Exit Function
	Next

	If DelTrackBack(Request.QueryString("id"),Request.QueryString("log_id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_TrackBackDel_Succeed in Action_Plugin_TrackBackDel_Succeed
			If Not IsEmpty(sAction_Plugin_TrackBackDel_Succeed) Then Call Execute(sAction_Plugin_TrackBackDel_Succeed)
			If bAction_Plugin_TrackBackDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=TrackBackMng"
	Else
		Call ShowError(19)
	End If
End Function

Function TrackBackSnd

	'plugin node
	For Each sAction_Plugin_TrackBackSnd_Begin in Action_Plugin_TrackBackSnd_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackSnd_Begin) Then Call Execute(sAction_Plugin_TrackBackSnd_Begin)
		If bAction_Plugin_TrackBackSnd_Begin=True Then Exit Function
	Next

	If SendTrackBack() Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_TrackBackSnd_Succeed in Action_Plugin_TrackBackSnd_Succeed
			If Not IsEmpty(sAction_Plugin_TrackBackSnd_Succeed) Then Call Execute(sAction_Plugin_TrackBackSnd_Succeed)
			If bAction_Plugin_TrackBackSnd_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=ArticleMng"
	Else
		Call ShowError(20)
	End If
End Function


Function UserMng()

	'plugin node
	For Each sAction_Plugin_UserMng_Begin in Action_Plugin_UserMng_Begin
		If Not IsEmpty(sAction_Plugin_UserMng_Begin) Then Call Execute(sAction_Plugin_UserMng_Begin)
		If bAction_Plugin_UserMng_Begin=True Then Exit Function
	Next
	Response.Redirect "admin/admin.asp?act=UserMng&page=" & Request.QueryString("id")
End Function

Function UserCrt()

	'plugin node
	For Each sAction_Plugin_UserCrt_Begin in Action_Plugin_UserCrt_Begin
		If Not IsEmpty(sAction_Plugin_UserCrt_Begin) Then Call Execute(sAction_Plugin_UserCrt_Begin)
		If bAction_Plugin_UserCrt_Begin=True Then Exit Function
	Next

	If EditUser Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_UserCrt_Succeed in Action_Plugin_UserCrt_Succeed
			If Not IsEmpty(sAction_Plugin_UserCrt_Succeed) Then Call Execute(sAction_Plugin_UserCrt_Succeed)
			If bAction_Plugin_UserCrt_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=UserMng"
	Else
		Call ShowError(16)
	End If
End Function


Function UserEdt()

	'plugin node
	For Each sAction_Plugin_UserEdt_Begin in Action_Plugin_UserEdt_Begin
		If Not IsEmpty(sAction_Plugin_UserEdt_Begin) Then Call Execute(sAction_Plugin_UserEdt_Begin)
		If bAction_Plugin_UserEdt_Begin=True Then Exit Function
	Next
	Response.Redirect "admin/edit_user.asp?id=" & Request.QueryString("id")
End Function


Function UserMod()

	'plugin node
	For Each sAction_Plugin_UserMod_Begin in Action_Plugin_UserMod_Begin
		If Not IsEmpty(sAction_Plugin_UserMod_Begin) Then Call Execute(sAction_Plugin_UserMod_Begin)
		If bAction_Plugin_UserMod_Begin=True Then Exit Function
	Next

	If EditUser Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_UserMod_Succeed in Action_Plugin_UserMod_Succeed
			If Not IsEmpty(sAction_Plugin_UserMod_Succeed) Then Call Execute(sAction_Plugin_UserMod_Succeed)
			If bAction_Plugin_UserMod_Succeed=True Then Exit Function
		Next

		If CLng(Request.Form("edtID"))=BlogUser.ID And Trim(Request.Form("edtPassWord"))<>"" Then
			Response.Redirect "cmd.asp?act=login"
		End If

		Response.Redirect "cmd.asp?act=UserMng"
	Else
		Call ShowError(16)
	End If
End Function

Function UserDel()

	'plugin node
	For Each sAction_Plugin_UserDel_Begin in Action_Plugin_UserDel_Begin
		If Not IsEmpty(sAction_Plugin_UserDel_Begin) Then Call Execute(sAction_Plugin_UserDel_Begin)
		If bAction_Plugin_UserDel_Begin=True Then Exit Function
	Next

	If DelUser(Request.QueryString("id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_UserDel_Succeed in Action_Plugin_UserDel_Succeed
			If Not IsEmpty(sAction_Plugin_UserDel_Succeed) Then Call Execute(sAction_Plugin_UserDel_Succeed)
			If bAction_Plugin_UserDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=UserMng"
	Else
		Call ShowError(17)
	End If
End Function


Function FileMng()

	'plugin node
	For Each sAction_Plugin_FileMng_Begin in Action_Plugin_FileMng_Begin
		If Not IsEmpty(sAction_Plugin_FileMng_Begin) Then Call Execute(sAction_Plugin_FileMng_Begin)
		If bAction_Plugin_FileMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=FileMng&page=" & Request.QueryString("id")
End Function

Function FileSnd()

	'plugin node
	For Each sAction_Plugin_FileSnd_Begin in Action_Plugin_FileSnd_Begin
		If Not IsEmpty(sAction_Plugin_FileSnd_Begin) Then Call Execute(sAction_Plugin_FileSnd_Begin)
		If bAction_Plugin_FileSnd_Begin=True Then Exit Function
	Next

	Call SendFile()
End Function

Function FileUpload()

	'plugin node
	For Each sAction_Plugin_FileUpload_Begin in Action_Plugin_FileUpload_Begin
		If Not IsEmpty(sAction_Plugin_FileUpload_Begin) Then Call Execute(sAction_Plugin_FileUpload_Begin)
		If bAction_Plugin_FileUpload_Begin=True Then Exit Function
	Next

	Server.ScriptTimeout = 1200
	'Response.Write CBool(Request.QueryString("autoname"))
	'Response.End
	If UploadFile(CBool(Request.QueryString("autoname"))) Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_FileUpload_Succeed in Action_Plugin_FileUpload_Succeed
			If Not IsEmpty(sAction_Plugin_FileUpload_Succeed) Then Call Execute(sAction_Plugin_FileUpload_Succeed)
			If bAction_Plugin_FileUpload_Succeed=True Then Exit Function
		Next

		Response.Redirect "admin/admin.asp?act=FileMng"
	Else
		Call ShowError(21)
	End If
End Function

Function FileDel()

	'plugin node
	For Each sAction_Plugin_FileDel_Begin in Action_Plugin_FileDel_Begin
		If Not IsEmpty(sAction_Plugin_FileDel_Begin) Then Call Execute(sAction_Plugin_FileDel_Begin)
		If bAction_Plugin_FileDel_Begin=True Then Exit Function
	Next

	If DelFile(Request.QueryString("id")) Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_FileDel_Succeed in Action_Plugin_FileDel_Succeed
			If Not IsEmpty(sAction_Plugin_FileDel_Succeed) Then Call Execute(sAction_Plugin_FileDel_Succeed)
			If bAction_Plugin_FileDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=FileMng"
	Else
		Call ShowError(22)
	End If
End Function


Function Search()

	'plugin node
	For Each sAction_Plugin_Search_Begin in Action_Plugin_Search_Begin
		If Not IsEmpty(sAction_Plugin_Search_Begin) Then Call Execute(sAction_Plugin_Search_Begin)
		If bAction_Plugin_Search_Begin=True Then Exit Function
	Next

	Dim a,s
	For Each a In Request.Form 
		If a="edtSearch" Then
			s=s & "q" & "=" & Server.URLEncode(Request.Form(a)) & "&"
		Else
			If a<>"btnPost" Then
				s=s & a & "=" & Server.URLEncode(Request.Form(a)) & "&"
			End If
		End If
	Next

	RedirectBy301(BlogHost & "search.asp?" & s)
End Function

Function SettingMng()

	'plugin node
	For Each sAction_Plugin_SettingMng_Begin in Action_Plugin_SettingMng_Begin
		If Not IsEmpty(sAction_Plugin_SettingMng_Begin) Then Call Execute(sAction_Plugin_SettingMng_Begin)
		If bAction_Plugin_SettingMng_Begin=True Then Exit Function
	Next

	If Not IsEmpty(Request.QueryString("ok")) Then
		Call SetBlogHint(True,Empty,Empty)
	End If

	Call RefreshOptionFormFileToDB()

	If Not IsEmpty(Request.QueryString("update")) Then

		If SaveSetting Then
			Call SetBlogHint(True,True,True)
			Response.Redirect "cmd.asp?act=SettingMng&ok"
		End If

	End If

	Response.Redirect "admin/edit_setting.asp"
End Function

Function SettingSav()

	'plugin node
	For Each sAction_Plugin_SettingSav_Begin in Action_Plugin_SettingSav_Begin
		If Not IsEmpty(sAction_Plugin_SettingSav_Begin) Then Call Execute(sAction_Plugin_SettingSav_Begin)
		If bAction_Plugin_SettingSav_Begin=True Then Exit Function
	Next

	If SaveSetting Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_SettingSav_Succeed in Action_Plugin_SettingSav_Succeed
			If Not IsEmpty(sAction_Plugin_SettingSav_Succeed) Then Call Execute(sAction_Plugin_SettingSav_Succeed)
			If bAction_Plugin_SettingSav_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=SettingMng&ok"
	Else
		Call ShowError(25)
	End If
End Function

Function TagMng()

	'plugin node
	For Each sAction_Plugin_TagMng_Begin in Action_Plugin_TagMng_Begin
		If Not IsEmpty(sAction_Plugin_TagMng_Begin) Then Call Execute(sAction_Plugin_TagMng_Begin)
		If bAction_Plugin_TagMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=TagMng&page=" & Request.QueryString("id")
End Function

Function TagEdt()

	'plugin node
	For Each sAction_Plugin_TagEdt_Begin in Action_Plugin_TagEdt_Begin
		If Not IsEmpty(sAction_Plugin_TagEdt_Begin) Then Call Execute(sAction_Plugin_TagEdt_Begin)
		If bAction_Plugin_TagEdt_Begin=True Then Exit Function
	Next

	If IsEmpty(Request.QueryString("id"))=False Then
		Response.Redirect "admin/edit_tag.asp?id=" & Request.QueryString("id")
	Else
		Response.Redirect "admin/edit_tag.asp"
	End If
End Function

Function TagPst()

	'plugin node
	For Each sAction_Plugin_TagPst_Begin in Action_Plugin_TagPst_Begin
		If Not IsEmpty(sAction_Plugin_TagPst_Begin) Then Call Execute(sAction_Plugin_TagPst_Begin)
		If bAction_Plugin_TagPst_Begin=True Then Exit Function
	Next

	If PostTag Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_TagPst_Succeed in Action_Plugin_TagPst_Succeed
			If Not IsEmpty(sAction_Plugin_TagPst_Succeed) Then Call Execute(sAction_Plugin_TagPst_Succeed)
			If bAction_Plugin_TagPst_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=TagMng"
	Else
		Call ShowError(35)
	End If
End Function

Function TagDel()

	'plugin node
	For Each sAction_Plugin_TagDel_Begin in Action_Plugin_TagDel_Begin
		If Not IsEmpty(sAction_Plugin_TagDel_Begin) Then Call Execute(sAction_Plugin_TagDel_Begin)
		If bAction_Plugin_TagDel_Begin=True Then Exit Function
	Next

	If DelTag(Request.QueryString("id")) Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_TagDel_Succeed in Action_Plugin_TagDel_Succeed
			If Not IsEmpty(sAction_Plugin_TagDel_Succeed) Then Call Execute(sAction_Plugin_TagDel_Succeed)
			If bAction_Plugin_TagDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=TagMng"
	Else
		Call ShowError(36)
	End If
End Function


Function SiteInfo()

	'plugin node
	For Each sAction_Plugin_SiteInfo_Begin in Action_Plugin_SiteInfo_Begin
		If Not IsEmpty(sAction_Plugin_SiteInfo_Begin) Then Call Execute(sAction_Plugin_SiteInfo_Begin)
		If bAction_Plugin_SiteInfo_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=SiteInfo"
End Function



Function TrackBackUrlGet()

	'plugin node
	For Each sAction_Plugin_TrackBackUrlGet_Begin in Action_Plugin_TrackBackUrlGet_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackUrlGet_Begin) Then Call Execute(sAction_Plugin_TrackBackUrlGet_Begin)
		If bAction_Plugin_TrackBackUrlGet_Begin=True Then Exit Function
	Next

	Call GetRealUrlofTrackBackUrl(Request.QueryString("id"))
End Function

Function CommentDelBatch

	'plugin node
	For Each sAction_Plugin_CommentDelBatch_Begin in Action_Plugin_CommentDelBatch_Begin
		If Not IsEmpty(sAction_Plugin_CommentDelBatch_Begin) Then Call Execute(sAction_Plugin_CommentDelBatch_Begin)
		If bAction_Plugin_CommentDelBatch_Begin=True Then Exit Function
	Next

	If DelCommentBatch() Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_CommentDelBatch_Succeed in Action_Plugin_CommentDelBatch_Succeed
			If Not IsEmpty(sAction_Plugin_CommentDelBatch_Succeed) Then Call Execute(sAction_Plugin_CommentDelBatch_Succeed)
			If bAction_Plugin_CommentDelBatch_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=CommentMng"
	End If
End Function

Function TrackBackDelBatch

	'plugin node
	For Each sAction_Plugin_TrackBackDelBatch_Begin in Action_Plugin_TrackBackDelBatch_Begin
		If Not IsEmpty(sAction_Plugin_TrackBackDelBatch_Begin) Then Call Execute(sAction_Plugin_TrackBackDelBatch_Begin)
		If bAction_Plugin_TrackBackDelBatch_Begin=True Then Exit Function
	Next

	If DelTrackBackBatch() Then
		Call SetBlogHint(True,True,Empty)
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_TrackBackDelBatch_Succeed in Action_Plugin_TrackBackDelBatch_Succeed
			If Not IsEmpty(sAction_Plugin_TrackBackDelBatch_Succeed) Then Call Execute(sAction_Plugin_TrackBackDelBatch_Succeed)
			If bAction_Plugin_TrackBackDelBatch_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=TrackBackMng"
	End If
End Function

Function FileDelBatch

	'plugin node
	For Each sAction_Plugin_FileDelBatch_Begin in Action_Plugin_FileDelBatch_Begin
		If Not IsEmpty(sAction_Plugin_FileDelBatch_Begin) Then Call Execute(sAction_Plugin_FileDelBatch_Begin)
		If bAction_Plugin_FileDelBatch_Begin=True Then Exit Function
	Next

	If DelFileBatch() Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_FileDelBatch_Succeed in Action_Plugin_FileDelBatch_Succeed
			If Not IsEmpty(sAction_Plugin_FileDelBatch_Succeed) Then Call Execute(sAction_Plugin_FileDelBatch_Succeed)
			If bAction_Plugin_FileDelBatch_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=FileMng"
	End If
End Function


Function ThemeMng()

	'plugin node
	For Each sAction_Plugin_ThemeMng_Begin in Action_Plugin_ThemeMng_Begin
		If Not IsEmpty(sAction_Plugin_ThemeMng_Begin) Then Call Execute(sAction_Plugin_ThemeMng_Begin)
		If bAction_Plugin_ThemeMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=ThemeMng&installed=" & Server.URLEncode(Request.QueryString("installed"))
End Function


Function ThemeSav()

	'plugin node
	For Each sAction_Plugin_ThemeSav_Begin in Action_Plugin_ThemeSav_Begin
		If Not IsEmpty(sAction_Plugin_ThemeSav_Begin) Then Call Execute(sAction_Plugin_ThemeSav_Begin)
		If bAction_Plugin_ThemeSav_Begin=True Then Exit Function
	Next

	If SaveTheme Then
		Call SetBlogHint(True,True,Empty)

		'plugin node
		For Each sAction_Plugin_ThemeSav_Succeed in Action_Plugin_ThemeSav_Succeed
			If Not IsEmpty(sAction_Plugin_ThemeSav_Succeed) Then Call Execute(sAction_Plugin_ThemeSav_Succeed)
			If bAction_Plugin_ThemeSav_Succeed=True Then Exit Function
		Next

		
	Else
		'Call ShowError(25)
	End If
	
	Response.Redirect "cmd.asp?act=ThemeMng&installed=" & Server.URLEncode(Request.Form("edtZC_BLOG_THEME"))

End Function



Function LinkMng()

	'plugin node
	For Each sAction_Plugin_LinkMng_Begin in Action_Plugin_LinkMng_Begin
		If Not IsEmpty(sAction_Plugin_LinkMng_Begin) Then Call Execute(sAction_Plugin_LinkMng_Begin)
		If bAction_Plugin_LinkMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/edit_link.asp"
End Function


Function LinkSav()

	'plugin node
	For Each sAction_Plugin_LinkSav_Begin in Action_Plugin_LinkSav_Begin
		If Not IsEmpty(sAction_Plugin_LinkSav_Begin) Then Call Execute(sAction_Plugin_LinkSav_Begin)
		If bAction_Plugin_LinkSav_Begin=True Then Exit Function
	Next

	If SaveLink Then
		Call SetBlogHint(True,Empty,Empty)

		'plugin node
		For Each sAction_Plugin_LinkSav_Succeed in Action_Plugin_LinkSav_Succeed
			If Not IsEmpty(sAction_Plugin_LinkSav_Succeed) Then Call Execute(sAction_Plugin_LinkSav_Succeed)
			If bAction_Plugin_LinkSav_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=LinkMng"
	Else
		Call ShowError(25)
	End If
End Function


Function PlugInMng()

	'plugin node
	For Each sAction_Plugin_PlugInMng_Begin in Action_Plugin_PlugInMng_Begin
		If Not IsEmpty(sAction_Plugin_PlugInMng_Begin) Then Call Execute(sAction_Plugin_PlugInMng_Begin)
		If bAction_Plugin_PlugInMng_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=PlugInMng&installed=" & Server.URLEncode(Request.QueryString("installed"))
End Function


Function PlugInActive()

	'plugin node
	For Each sAction_Plugin_PlugInActive_Begin in Action_Plugin_PlugInActive_Begin
		If Not IsEmpty(sAction_Plugin_PlugInActive_Begin) Then Call Execute(sAction_Plugin_PlugInActive_Begin)
		If bAction_Plugin_PlugInActive_Begin=True Then Exit Function
	Next
	

	If ActivePlugInByName(Request.QueryString("name"))=True Then

		'plugin node
		For Each sAction_Plugin_PlugInActive_Succeed in Action_Plugin_PlugInActive_Succeed
			If Not IsEmpty(sAction_Plugin_PlugInActive_Succeed) Then Call Execute(sAction_Plugin_PlugInActive_Succeed)
			If bAction_Plugin_PlugInActive_Succeed=True Then Exit Function
		Next

		

	End If
	Response.Redirect "cmd.asp?act=PlugInMng&installed=" & Server.URLEncode(Request.QueryString("name"))
End Function


Function PlugInDisable()

	'plugin node
	For Each sAction_Plugin_PlugInDisable_Begin in Action_Plugin_PlugInDisable_Begin
		If Not IsEmpty(sAction_Plugin_PlugInDisable_Begin) Then Call Execute(sAction_Plugin_PlugInDisable_Begin)
		If bAction_Plugin_PlugInDisable_Begin=True Then Exit Function
	Next

	If DisablePlugInByName(Request.QueryString("name"))=True Then

		'plugin node
		For Each sAction_Plugin_PlugInDisable_Succeed in Action_Plugin_PlugInDisable_Succeed
			If Not IsEmpty(sAction_Plugin_PlugInDisable_Succeed) Then Call Execute(sAction_Plugin_PlugInDisable_Succeed)
			If bAction_Plugin_PlugInDisable_Succeed=True Then Exit Function
		Next

		
	End If
	Response.Redirect "cmd.asp?act=PlugInMng"
End Function



Function FunctionMng()

	'plugin node
	For Each sAction_Plugin_FunctionMng_Begin in Action_Plugin_FunctionMng_Begin
		If Not IsEmpty(sAction_Plugin_FunctionMng_Begin) Then Call Execute(sAction_Plugin_FunctionMng_Begin)
		If bAction_Plugin_FunctionMng_Begin=True Then Exit Function
	Next


	If Request.Form.Count>=5 Then
		Dim s1,s2,s3,s4,s5
		s1=ZC_SIDEBAR_ORDER
		s2=ZC_SIDEBAR_ORDER2
		s3=ZC_SIDEBAR_ORDER3
		s4=ZC_SIDEBAR_ORDER4
		s5=ZC_SIDEBAR_ORDER5
		Call SaveFunctionType()
		If SaveSidebarOrder(Request.Form("edtSidebar"),Request.Form("edtSidebar2"),Request.Form("edtSidebar3"),Request.Form("edtSidebar4"),Request.Form("edtSidebar5")) Then
			Call SaveSetting
			Call MakeBlogReBuild_Core()
			If s1<>ZC_SIDEBAR_ORDER Or s2<>ZC_SIDEBAR_ORDER2 Or s3<>ZC_SIDEBAR_ORDER3 Or s4<>ZC_SIDEBAR_ORDER4 Or s5<>ZC_SIDEBAR_ORDER5 Then
				Call SetBlogHint(Empty,Empty,True)
			End If
			Response.End
		End If
	End If


	Response.Redirect "admin/admin.asp?act=FunctionMng"

End Function



Function FunctionEdt()

	'plugin node
	For Each sAction_Plugin_FunctionEdt_Begin in Action_Plugin_FunctionEdt_Begin
		If Not IsEmpty(sAction_Plugin_FunctionEdt_Begin) Then Call Execute(sAction_Plugin_FunctionEdt_Begin)
		If bAction_Plugin_FunctionEdt_Begin=True Then Exit Function
	Next

	Response.Redirect "admin/edit_function.asp?id=" & Request.QueryString("id")

End Function




Function FunctionSav()

	'plugin node
	For Each sAction_Plugin_FunctionSav_Begin in Action_Plugin_FunctionSav_Begin
		If Not IsEmpty(sAction_Plugin_FunctionSav_Begin) Then Call Execute(sAction_Plugin_FunctionSav_Begin)
		If bAction_Plugin_FunctionSav_Begin=True Then Exit Function
	Next

	If SaveFunction()=True Then
		Call SetBlogHint(True,True,Empty)
		Call SaveFunctionType()
		Call MakeBlogReBuild_Core()

		'plugin node
		For Each sAction_Plugin_FunctionSav_Succeed in Action_Plugin_FunctionSav_Succeed
			If Not IsEmpty(sAction_Plugin_FunctionSav_Succeed) Then Call Execute(sAction_Plugin_FunctionSav_Succeed)
			If bAction_Plugin_FunctionSav_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=FunctionMng"
	End If

End Function




Function FunctionDel()

	'plugin node
	For Each sAction_Plugin_FunctionDel_Begin in Action_Plugin_FunctionDel_Begin
		If Not IsEmpty(sAction_Plugin_FunctionDel_Begin) Then Call Execute(sAction_Plugin_FunctionDel_Begin)
		If bAction_Plugin_FunctionDel_Begin=True Then Exit Function
	Next


	If DelFunction(CLng(Request.QueryString("id")))=True Then
		Call SetBlogHint(True,True,Empty)
		Call SaveFunctionType()
		Call MakeBlogReBuild_Core()

		For Each sAction_Plugin_FunctionDel_Succeed in Action_Plugin_FunctionDel_Succeed
			If Not IsEmpty(sAction_Plugin_FunctionDel_Succeed) Then Call Execute(sAction_Plugin_FunctionDel_Succeed)
			If bAction_Plugin_FunctionDel_Succeed=True Then Exit Function
		Next

		Response.Redirect "cmd.asp?act=FunctionMng"
	End If

End Function



Function BlogReBuild()

	'plugin node
	For Each sAction_Plugin_BlogReBuild_Begin in Action_Plugin_BlogReBuild_Begin
		If Not IsEmpty(sAction_Plugin_BlogReBuild_Begin) Then Call Execute(sAction_Plugin_BlogReBuild_Begin)
		If bAction_Plugin_BlogReBuild_Begin=True Then Exit Function
	Next

	Call AddBatch(ZC_MSG072,"Call MakeBlogReBuild()")


	'plugin node
	For Each sAction_Plugin_BlogReBuild_End in Action_Plugin_BlogReBuild_End
		If Not IsEmpty(sAction_Plugin_BlogReBuild_End) Then Call Execute(sAction_Plugin_BlogReBuild_End)
		If bAction_Plugin_BlogReBuild_End=True Then Exit Function
	Next

	Response.Redirect "admin/admin.asp?act=AskFileReBuild"

End Function

Function FileReBuild()

	'plugin node
	For Each sAction_Plugin_FileReBuild_Begin in Action_Plugin_FileReBuild_Begin
		If Not IsEmpty(sAction_Plugin_FileReBuild_Begin) Then Call Execute(sAction_Plugin_FileReBuild_Begin)
		If bAction_Plugin_FileReBuild_Begin=True Then Exit Function
	Next

	Call AddBatch(ZC_MSG072,"Call MakeBlogReBuild()")

	Call BeforeFileReBuild()

	'plugin node
	For Each sAction_Plugin_FileReBuild_End in Action_Plugin_FileReBuild_End
		If Not IsEmpty(sAction_Plugin_FileReBuild_End) Then Call Execute(sAction_Plugin_FileReBuild_End)
		If bAction_Plugin_FileReBuild_End=True Then Exit Function
	Next

	Call AddBatch(ZC_MSG259,"Call BlogReBuild_Default()")

	Response.Redirect "admin/admin.asp?act=AskFileReBuild"

End Function



Function AskFileReBuild()

	'plugin node
	For Each sAction_Plugin_AskFileReBuild_Begin in Action_Plugin_AskFileReBuild_Begin
		If Not IsEmpty(sAction_Plugin_AskFileReBuild_Begin) Then Call Execute(sAction_Plugin_AskFileReBuild_Begin)
		If bAction_Plugin_AskFileReBuild_Begin=True Then Exit Function
	Next

	Call ClearGlobeCache
	Call LoadGlobeCache

	Call SetBlogHint(Empty,Empty,False)

	Response.Redirect "admin/admin.asp?act=AskFileReBuild"

End Function


Function Batch()

	'plugin node
	For Each sAction_Plugin_Batch_Begin in Action_Plugin_Batch_Begin
		If Not IsEmpty(sAction_Plugin_Batch_Begin) Then Call Execute(sAction_Plugin_Batch_Begin)
		If bAction_Plugin_Batch_Begin=True Then Exit Function
	Next

	Call BatchAll()

	'plugin node
	For Each sAction_Plugin_Batch_End in Action_Plugin_Batch_End
		If Not IsEmpty(sAction_Plugin_Batch_End) Then Call Execute(sAction_Plugin_Batch_End)
		If bAction_Plugin_Batch_End=True Then Exit Function
	Next


End Function



Function CommentGet()

	'plugin node
	For Each sAction_Plugin_CommentGet_Begin in Action_Plugin_CommentGet_Begin
		If Not IsEmpty(sAction_Plugin_CommentGet_Begin) Then Call Execute(sAction_Plugin_CommentGet_Begin)
		If bAction_Plugin_CommentGet_Begin=True Then Exit Function
	Next

	Call GetComment(Request.QueryString("logid"),CLng(Request.QueryString("page")))

End Function




'plugin node
For Each sAction_Plugin_Command_End in Action_Plugin_Command_End
	If Not IsEmpty(sAction_Plugin_Command_End) Then Call Execute(sAction_Plugin_Command_End)
Next

Call System_Terminate()

If Err.Number<>0 then
	'Call ShowError(0)
End If
%>