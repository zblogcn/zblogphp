<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%
'********************************************************
'   uEditor ASP图片上传
'   By ZSX(http://www.zsxsoft.com)
'   Z-Blog (http://www.rainbowsoft.org)
'********************************************************


If Request.QueryString("action")="snapscreen" Then
	Call System_Initialize
	Set BlogUser =New TUser
	BlogUser.LoginType="Self"
	BlogUser.name=CStr(Trim(Request.QueryString("username")))
	BlogUser.Password=CStr(Trim(Request.QueryString("password")))
	BlogUser.Verify()
	If Not CheckRights("FileUpload") Then Call ShowError(6)
	uEditor_ASPFormName="upfile"
	uEditor_ASPCharset="GB2312"
Else
	Call uEditor_i
End If

For Each sAction_Plugin_UEditor_FileUpload_Begin in Action_Plugin_UEditor_FileUpload_Begin
	If Not IsEmpty(sAction_Plugin_UEditor_FileUpload_Begin) Then Call Execute(sAction_Plugin_UEditor_FileUpload_Begin)
Next

Dim Ext
Ext=Replace(ZC_UPLOAD_FILETYPE,"|","/")'设置允许上传扩展名，空为全部

dim uploadPath,PostTime
Randomize
PostTime=GetTime(Now())
Dim strUPLOADDIR
strUPLOADDIR = ZC_UPLOAD_DIRECTORY&"\"&Year(GetTime(Now()))&"\"&Month(GetTime(Now()))
CreatDirectoryByCustomDirectory strUPLOADDIR 


Dim objUpload,isOK
Set objUpload=New UpLoadClass
objUpload.AutoSave=2
objUpload.Charset=uEditor_ASPCharset
objUpload.FileType=Ext
objUpload.savepath=BlogPath & strUPLOADDIR &"\"
objUpload.maxsize=uEditor_ASPMaxSize
objUpload.open

Dim Path
Path=Replace(BlogPath & strUPLOADDIR &"\" & objUpload.form(uEditor_ASPFormName&"_Name")	,"\","/")
Dim s
FileName=BlogHost & strUPLOADDIR &"\" & objUpload.form(uEditor_ASPFormName&"_Name")

If objUpload.Save(uEditor_ASPFormName,0)=True Then
	Dim uf
	Set uf=New TUpLoadFile
	uf.AuthorID=BlogUser.ID
	uf.AutoName=False
	uf.IsManual=True
	uf.FileSize=objUpload.form(uEditor_ASPFormName&"_Size")
	uf.FileName=objUpload.form(uEditor_ASPFormName)
	uf.UpLoad
End If

Dim strJSON
strJSON="{'state':'"& objUpload.Error2Info(uEditor_ASPFormName) & "',"'输出状态,SUCCESS代表成功
strJSON=strJSON&"'url':'"& objUpload.form(uEditor_ASPFormName) &"'," '输出上传后URL
strJSON=strJSON&"'fileType':'"&objUpload.form(uEditor_ASPFormName&"_Ext")&"'," '输出扩展名
strJSON=strJSON&"'title':'"& htmlspecialchars(objUpload.form("pictitle"))&"',"  '输出图片标题
strJSON=strJSON&"'original':'"&objUpload.Form(uEditor_ASPFormName&"_Name")&"'}" '输出源文件名

	
For Each sAction_Plugin_uEditor_FileUpload_End in Action_Plugin_uEditor_FileUpload_End
	If Not IsEmpty(sAction_Plugin_uEditor_FileUpload_End) Then Call Execute(sAction_Plugin_uEditor_FileUpload_End)
Next
response.AddHeader "json",BlogPath & strUPLOADDIR &"\"
response.write strJSON


set objUpload=nothing
Call System_Terminate()


%>