<%@ CODEPAGE=65001 %>
<!--#include file="ASPIncludeFile.asp"-->
<%

For Each sAction_Plugin_ueditor_getmovie_Begin in Action_Plugin_ueditor_getmovie_Begin
	If Not IsEmpty(sAction_Plugin_ueditor_getmovie_Begin) Then Call Execute(sAction_Plugin_ueditor_getmovie_Begin)
Next
Dim strResponse

Dim searchKey,videoType
searchKey=Trim(Request.Form("searchKey"))
videoType=Trim(Request.Form("videoType"))

strResponse=gethtml("http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw="&searchKey&"&pageNo=1&pageSize=20&channelId="&videoType&"&media=v&sort=s","utf-8")  '从土豆下载数据

For Each sAction_Plugin_ueditor_getmovie_End in Action_Plugin_ueditor_getmovie_End
	If Not IsEmpty(sAction_Plugin_ueditor_getmovie_End) Then Call Execute(sAction_Plugin_ueditor_getmovie_End)
Next
Response.Write strResponse

Call System_Terminate()
%>