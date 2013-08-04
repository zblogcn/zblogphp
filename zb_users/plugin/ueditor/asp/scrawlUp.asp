<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%
'********************************************************
'   uEditor ASP涂鸦上传
'   By ZSX(http://www.zsxsoft.com)
'   Z-Blog (http://www.rainbowsoft.org)
'********************************************************
uEditor_i
Dim Ext
Ext=Replace(ZC_UPLOAD_FILETYPE,"|","/")'设置允许上传扩展名，空为全部


'在这里插入权限控制代码。
dim uploadPath,PostTime
Randomize
PostTime=GetTime(Now())
Dim strUPLOADDIR
strUPLOADDIR = ZC_UPLOAD_DIRECTORY&"\"&Year(GetTime(Now()))&"\"&Month(GetTime(Now()))
CreatDirectoryByCustomDirectory(strUPLOADDIR)

Dim Path,s
Dim t
Select Case Request.QueryString("action")
	Case "tmpImg"
		Dim objUpload,isOK
		CreatDirectoryByCustomDirectory strUPLOADDIR &"\" & uEditor_tmpImg&"\" '创建上传文件夹
		Set objUpload=New UpLoadClass
		objUpload.AutoSave=2
		objUpload.Charset=uEditor_ASPCharset
		objUpload.FileType=Ext
		objUpload.savepath=BlogPath &  strUPLOADDIR &"\" & uEditor_tmpImg&"\"
		objUpload.maxsize=uEditor_ASPMaxSize
		objUpload.open
		
		Path=Replace(BlogPath &  strUPLOADDIR &"\" & objUpload.form("upfile_Name"),"\","/")
		FileName=BlogHost& strUPLOADDIR &"\" & objUpload.form("upfile")
		
		objUpload.Save "upfile",0
		Response.Write "<script>parent.ue_callback('" & ReplacePath(uEditor_tmpImg,True) & "/"&objUpload.form("upfile") & "','" &objUpload.Error2Info("upfile")&"')</script>"
		Set objUpload=Nothing
	Case Else
		t=RandomFileName("jpg")
		CreatDirectoryByCustomDirectory uEditor_ASPUploadPath
		Call UnpackBase64(Request.Form("content"),BlogPath &  strUPLOADDIR &"\" &t)
		Response.Write "{'url':'" & ReplacePath(t,True)& "',state:'SUCCESS'}"
		Dim uf,oFso
		Set oFSO=Server.CreateObject("scripting.filesystemobject")
		Set uf=New TUpLoadFile
		uf.AuthorID=BlogUser.ID
		uf.AutoName=False
		uf.IsManual=True
		uf.FileSize=oFso.getFile(BlogPath &  strUPLOADDIR &"\" &t).size
		uf.FileName=t
		uf.UpLoad
		On Error Resume Next		
		oFSO.DeleteFolder BlogPath &  strUPLOADDIR &"\" & uEditor_tmpImg,True  '删除临时文件夹
		Set oFSO=Nothing
End Select

%>