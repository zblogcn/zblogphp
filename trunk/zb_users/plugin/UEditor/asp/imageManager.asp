<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%
uEditor_i

For Each sAction_Plugin_uEditor_imageManager_Begin in Action_Plugin_uEditor_imageManager_Begin
	If Not IsEmpty(sAction_Plugin_uEditor_imageManager_Begin) Then Call Execute(sAction_Plugin_uEditor_imageManager_Begin)
Next
	Dim strResponse,objUpload,objRS,intPageAll
	If CheckRights("Root")=False Then strSQL="WHERE ([ul_AuthorID] = " & BlogUser.ID & ")"
	Set objRS=Server.CreateObject("ADODB.Recordset")
	objRS.CursorType = adOpenKeyset
	objRS.LockType = adLockReadOnly
	objRS.ActiveConnection=objConn
	objRS.Source=""
	objRS.Open("SELECT TOP 100 * FROM [blog_UpLoad] " & strSQL & " ORDER BY [ul_PostTime] DESC")
	objRS.PageSize=ZC_MANAGE_COUNT
	Call CheckParameter(intPage,"int",1)
	If objRS.PageCount>0 Then objRS.AbsolutePage = intPage
	intPageAll=objRS.PageCount

	If (Not objRS.bof) And (Not objRS.eof) Then
		For i=1 to objRS.PageSize
			If CheckRegExp(objRS("ul_FileName"),"\.(jpe?g|gif|bmp|png)$")=True And CheckRegExp(objRS("ul_FileName"),"%\d+")=False Then
				If IsNull(objRS("ul_DirByTime"))=False Then
					If objRS("ul_DirByTime")<>"" Then
						If CBool(objRS("ul_DirByTime"))=True Then
							strResponse=strResponse&Replace(ZC_UPLOAD_DIRECTORY &"/"&Year(objRS("ul_PostTime")) & "/" & Month(objRS("ul_PostTime")) & "/"&objRS("ul_FileName")&uEditor_Split,"%","%25")
						Else
							strResponse=strResponse&Replace(ZC_UPLOAD_DIRECTORY &"/"&objRS("ul_FileName")&uEditor_Split,"%","%25")
						End If
					End If
				Else
					strResponse=strResponse&Replace(ZC_UPLOAD_DIRECTORY &"/"&objRS("ul_FileName")&uEditor_Split,"%","%25")
				End If
			End If
			objRS.MoveNext
			If objRS.eof Then Exit For
		Next
	End If
For Each sAction_Plugin_uEditor_imageManager_End in Action_Plugin_uEditor_imageManager_End
	If Not IsEmpty(sAction_Plugin_uEditor_imageManager_End) Then Call Execute(sAction_Plugin_uEditor_imageManager_End)
Next
	strResponse=ReplacePath(strResponse,True)
	Response.Write strResponse
	
Call System_Terminate()
%>