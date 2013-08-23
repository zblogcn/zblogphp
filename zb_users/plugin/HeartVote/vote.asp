<%@ CODEPAGE=65001 %>
<% Option Explicit %>
<% On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<!-- #include file="../../c_option.asp" -->
<!-- #include file="../../../ZB_SYSTEM/function/c_function.asp" -->
<!-- #include file="../../../ZB_SYSTEM/function/c_system_lib.asp" -->
<!-- #include file="../../../ZB_SYSTEM/function/c_system_base.asp" -->
<!-- #include file="../../../ZB_SYSTEM/function/c_system_plugin.asp" -->
<!-- #include file="../../../ZB_SYSTEM/function/c_system_event.asp" -->
<!-- #include file="../../plugin/p_config.asp" -->
<%
If CheckPluginState("HeartVote")=False Then Response.End

GetReallyDirectory()

Dim id
Dim vote
Dim ip

Dim allvote
Dim alluser

id=CInt(Request.Form("id"))
vote=CInt(Request.Form("vote"))
ip=FilterSQL(Request.ServerVariables("REMOTE_ADDR"))

If vote<1 Then vote=1

If vote>10 Then vote=10

Set objConn = Server.CreateObject("ADODB.Connection")
objConn.Open "Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" & BlogPath & "zb_users/plugin/heartvote/db.asp"


Dim objRS
Set objRS=objConn.Execute("SELECT * FROM [vote] WHERE [aid]=" & id & " AND [ip]='" & ip & "'")

If (Not objRS.bof) And (Not objRS.eof) Then

	Response.Write "你已投过一次了！还想投？^_^"
	Response.End

End If

objRS.Close


objConn.Execute("INSERT INTO [vote]([aid],[vote],[ip]) VALUES ("&id&","&vote&",'"&ip&"')")


Set objRS=Server.CreateObject("ADODB.Recordset")
objRS.CursorType = adOpenKeyset
objRS.LockType = adLockReadOnly
objRS.ActiveConnection=objConn
objRS.Source=""

objRS.Open("SELECT SUM([vote])AS allvote,COUNT([ip]) AS alluser FROM [vote] WHERE [aid]=" & id)

If (Not objRS.bof) And (Not objRS.eof) Then

	alluser=objRS("alluser")
	allvote=objRS("allvote")
	allvote=allvote\alluser

End If

objRS.Close
Set objRS=Nothing

objConn.Close

Response.Write allvote &"|"& alluser

%>