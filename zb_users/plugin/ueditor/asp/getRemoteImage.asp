<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%
On Error Resume Next
uEditor_i

For Each sAction_Plugin_uEditor_getRemoteImage_Begin in Action_Plugin_uEditor_getRemoteImage_Begin
	If Not IsEmpty(sAction_Plugin_uEditor_getRemoteImage_Begin) Then Call Execute(sAction_Plugin_uEditor_getRemoteImage_Begin)
Next


Dim strResponse
strResponse="{}"


For Each sAction_Plugin_uEditor_getRemoteImage_End in Action_Plugin_uEditor_getRemoteImage_End
	If Not IsEmpty(sAction_Plugin_uEditor_getRemoteImage_End) Then Call Execute(sAction_Plugin_uEditor_getRemoteImage_End)
Next

Response.Write strResponse
Call System_Terminate()
%>