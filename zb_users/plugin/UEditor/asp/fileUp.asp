<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%
Call System_Initialize
Dim Ext
Ext=Replace(ZC_UPLOAD_FILETYPE,"|","/")'设置允许上传扩展名，空为全部

dim objUpload,uploadPath,PostTime
Randomize
PostTime=GetTime(Now())
Dim strUPLOADDIR
strUPLOADDIR = ZC_UPLOAD_DIRECTORY&"\"&Year(GetTime(Now()))&"\"&Month(GetTime(Now()))
CreatDirectoryByCustomDirectory(strUPLOADDIR)

Set objUpload=New UpLoadClass
objUpload.AutoSave=2
objUpload.Charset=uEditor_ASPCharset
objUpload.FileType=Ext
objUpload.savepath=BlogPath & strUPLOADDIR &"\"
objUpload.maxsize=uEditor_ASPMaxSize

objUpload.open

Set BlogUser=Nothing
Set BlogUser =New TUser
BlogUser.LoginType="Self"
BlogUser.name=vbsunescape(CStr(Trim(objUpload.form("username"))))
BlogUser.Password=vbsunescape(CStr(Trim(objUpload.form("password"))))
BlogUser.Verify()


If Not CheckRights("FileUpload") Then Call ShowError(6)


For Each sAction_Plugin_UEditor_FileUpload_Begin in Action_Plugin_UEditor_FileUpload_Begin
	If Not IsEmpty(sAction_Plugin_UEditor_FileUpload_Begin) Then Call Execute(sAction_Plugin_UEditor_FileUpload_Begin)
Next


Dim Path
Path=Replace(BlogPath &  strUPLOADDIR &"\" & objUpload.form(uEditor_ASPFormName&"_Name"),"\","/")
Dim s
FileName=BlogHost& strUPLOADDIR &"\" & objUpload.form(uEditor_ASPFormName&"_Name")

If objUpload.Save(uEditor_ASPFormName,1)=True Then
	Dim uf
	Set uf=New TUpLoadFile
	uf.AuthorID=BlogUser.ID
	uf.AutoName=False
	uf.IsManual=True
	uf.FileSize=objUpload.form(uEditor_ASPFormName&"_Size")
	uf.FileName=objUpload.form(uEditor_ASPFormName&"_Name")
	uf.UpLoad
End If
Dim strJSON
strJSON="{'state':'"& objUpload.Error2Info(uEditor_ASPFormName) & "',"  '输出状态,SUCCESS代表成功
strJSON=strJSON&"'url':'"& objUpload.form(uEditor_ASPFormName&"_Name") &"',"  '输出上传后URL
strJSON=strJSON&"'fileType':'."&objUpload.form(uEditor_ASPFormName&"_Ext")&"',"  '输出扩展名
strJSON=strJSON&"'original':'"&objUpload.Form(uEditor_ASPFormName&"_Name")&"'}"  '输出源文件

For Each sAction_Plugin_uEditor_FileUpload_End in Action_Plugin_uEditor_FileUpload_End
	If Not IsEmpty(sAction_Plugin_uEditor_FileUpload_End) Then Call Execute(sAction_Plugin_uEditor_FileUpload_End)
Next

Response.write strJSON

set upload=nothing
Call System_Terminate()

%>