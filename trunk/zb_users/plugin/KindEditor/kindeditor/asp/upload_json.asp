<%@ CODEPAGE=65001 %>
<% Option Explicit %>
<% Response.CodePage=65001 %>
<% Response.Charset="UTF-8" %>
<!--#include file="UpLoad_Class.asp"-->
<!--#include file="JSON_2.0.4.asp"-->
<!--#include file="ASPIncludeFile.asp"-->
<%
' KindEditor ASP
'未寒
KEupload

Dim aspUrl, savePath, saveUrl, maxSize, fileName, fileExt, newFileName, filePath, fileUrl, dirName
Dim extStr, imageExtStr, flashExtStr, mediaExtStr, fileExtStr
Dim upload, file, fso, ranNum, hash, ymd, mm, dd, result

aspUrl = Request.ServerVariables("SCRIPT_NAME")
aspUrl = left(aspUrl, InStrRev(aspUrl, "/"))

'文件保存目录路径
savePath = "../../../../../" & ZC_UPLOAD_DIRECTORY & "/"
'文件保存目录URL
saveUrl = ZC_BLOG_HOST & ZC_UPLOAD_DIRECTORY & "/"
'定义允许上传的文件扩展名
imageExtStr = "gif|jpg|jpeg|png|bmp"
flashExtStr = "swf|flv"
mediaExtStr = "swf|flv|mp3|wav|wma|wmv|mid|avi|mpg|asf|rm|rmvb"
fileExtStr = "doc|docx|xls|xlsx|ppt|htm|html|txt|zip|rar|gz|bz2"
'最大文件大小
maxSize = ZC_UPLOAD_FILESIZE '5 * 1024 * 1024 '5M

Set fso = Server.CreateObject("Scripting.FileSystemObject")
If Not fso.FolderExists(Server.mappath(savePath)) Then
	showError(saveUrl&"上传目录不存在。"&savePath)
End If

dirName = Request.QueryString("dir")
If isEmpty(dirName) Then
	dirName = "image"
End If
If instr(lcase("image,flash,media,file"), dirName) < 1 Then
	showError("目录名不正确。")
End If

Select Case dirName
	Case "flash" extStr = flashExtStr
	Case "media" extStr = mediaExtStr
	Case "file" extStr = fileExtStr
	Case Else  extStr = imageExtStr
End Select

set upload = new AnUpLoad
upload.Exe = extStr
upload.MaxSize = maxSize
upload.GetData()
if upload.ErrorID>0 then 
	showError(upload.Description)
end if

'创建文件夹
' savePath = savePath & dirName & "/"
' saveUrl = saveUrl & dirName & "/"
' If Not fso.FolderExists(Server.mappath(savePath)) Then
	' fso.CreateFolder(Server.mappath(savePath))
' End If

mm = month(now)
' If mm < 10 Then
	' mm = "0" & mm
' End If
dd = day(now)
If dd < 10 Then
	dd = "0" & dd
End If
ymd = year(now) & mm & dd

savePath = savePath & year(now) & "/" & mm & "/"
saveUrl = saveUrl & year(now) & "/" & mm & "/"

If Not fso.FolderExists(Server.mappath(savePath)) Then
	fso.CreateFolder(Server.mappath(savePath))
End If

set file = upload.files("imgFile")
if file is nothing then
	showError("请选择文件。")
end if

set result = file.saveToFile(savePath, 0, true)
if result.error then
	showError(file.Exception)
end if

filePath = Server.mappath(savePath & file.filename)
fileUrl = saveUrl & file.filename
Dim filenameupload,filesizeupload

filenameupload=file.filename
filesizeupload=upload.TotalSize

Set upload = nothing
Set file = nothing

If Not fso.FileExists(filePath) Then
	showError("上传文件失败。")
End If

	Dim uf
	Set uf=New TUpLoadFile
	uf.AuthorID=BlogUser.ID
	uf.AutoName=False
	uf.IsManual=True
	uf.FileSize= filesizeupload
	uf.FileName= filenameupload
	uf.UpLoad

Response.AddHeader "Content-Type", "text/html; charset=UTF-8"
Set hash = jsObject()
hash("error") = 0
hash("url") = fileUrl
hash.Flush
Response.End

Function showError(message)
	Response.AddHeader "Content-Type", "text/html; charset=UTF-8"
	Dim hash
	Set hash = jsObject()
	hash("error") = 1
	hash("message") = message
	hash.Flush
	Response.End
End Function
%>
