<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%

Call uEditor_i

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



If objUpload.Save(uEditor_ASPFormName,IIf(CheckRegExp(objUpload.form(uEditor_ASPFormName&"_Ext"),"(jpg|gif|png|bmp)"),0,1)) Then
	Dim uf
	Set uf=New TUpLoadFile
	uf.AuthorID=BlogUser.ID
	uf.AutoName=False
	uf.IsManual=True
	uf.FileSize=objUpload.form(uEditor_ASPFormName&"_Size")
	uf.FileName=objUpload.form(uEditor_ASPFormName&"_Name")
	uf.UpLoad
End If

	
For Each sAction_Plugin_uEditor_FileUpload_End in Action_Plugin_uEditor_FileUpload_End
	If Not IsEmpty(sAction_Plugin_uEditor_FileUpload_End) Then Call Execute(sAction_Plugin_uEditor_FileUpload_End)
Next
%>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="../dialogs/internal.js"></script>
    <script type="text/javascript" src="../dialogs/tangram.js"></script>

    <link rel="stylesheet" href="../dialogs/image/image.css" type="text/css" />
</head>
<body>
<script type="text/javascript">

	

	
	if(/jpg|gif|bmp|png/.test("<%=objUpload.form(uEditor_ASPFormName&"_Ext")%>")){
		var imgObj={};
		imgObj._src = editor.options.filePath+ "<%=objUpload.form(uEditor_ASPFormName)%>";
		imgObj.src = editor.options.filePath+ "<%=objUpload.form(uEditor_ASPFormName)%>";
		imgObj.width = "<%=objUpload.form(uEditor_ASPFormName&"_Width")%>";
		imgObj.height = "<%=objUpload.form(uEditor_ASPFormName&"_Height")%>";
		imgObj.title = imgObj.alt = "<%=objUpload.form(uEditor_ASPFormName&"_Name")%>";
		imgObj.style = "width:" + imgObj.width + "px;height:" + imgObj.height + "px;";
		editor.fireEvent('beforeInsertImage', imgObj);
		editor.execCommand("insertImage", imgObj);
	}
	else{
        var str = "<p style='line-height: 16px;'>" +
                  "<a href='"+editor.options.filePath + "<%=objUpload.form(uEditor_ASPFormName&"_Name")%>"+"'>" + 
                  "<%=objUpload.form(uEditor_ASPFormName&"_Name")%>" + "</a></p>";
        editor.execCommand("insertHTML",str);
    }
    dialog.close();
    

</script>
</body>
</html>