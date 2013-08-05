<!-- #include file="..\..\..\..\zb_users\c_option.asp" -->
<!-- #include file="..\..\..\function\c_function.asp" -->
<!-- #include file="..\..\..\function\c_system_lib.asp" -->
<!-- #include file="..\..\..\function\c_system_base.asp" -->
<!-- #include file="..\..\..\function\c_system_event.asp" -->
<!-- #include file="..\..\..\function\c_system_plugin.asp" -->
<!-- #include file="..\..\..\..\zb_users\plugin\p_config.asp" -->
<%
Sub uEditor_i
	Call System_Initialize()
	Call CheckReference("")
	If Not CheckRights("FileUpload") Then Call ShowError(6)
End Sub
'********************************************************
'   uEditor ASP Include File
'   By ZSX(http://www.zsxsoft.com)
'   Z-Blog (http://www.rainbowsoft.org)
'********************************************************
Dim uEditor_ASPCharset
uEditor_ASPCharset="UTF-8"  '设置编码
Dim uEditor_ASPMaxSize
uEditor_ASPMaxSize=ZC_UPLOAD_FILESIZE  '设置最大上传大小,单位为字节，0表示不受限
Dim uEditor_ASPFormName
uEditor_ASPFormName="edtFileLoad"  '设置表单名
Dim uEditor_Split
uEditor_Split="ue_separate_ue"  '设置UE分隔符
Dim uEditor_tmpImg
uEditor_tmpImg="tmp"   '设置临时文件夹（主要是涂鸦）

Dim uEditor_ASPUploadPath
uEditor_ASPUploadPath="upload\"  '设置上传目录

'得到当前的真实路径
Dim uEditor_ASPPath
uEditor_ASPPath=BlogPath

'把PATH的斜杠改好了。。
Function ReplacePath(path,isLocal)
	If isLocal Then
		path=Replace(path,"\","/")
	Else
		path=Replace(path,"/","\")
	End If
	ReplacePath=Path
End Function
'利用XML对BASE64进行解压
Function UnpackBase64(Base64Code,savePath)
	Dim tmpXMLData
	tmpXMLData="<?xml version=""1.0"" encoding=""utf-8""?><file><stream xmlns:dt=""urn:schemas-microsoft-com:datatypes"" dt:dt=""bin.base64"">"&Base64Code&"</stream></file>"
	
	Dim objXmlFile
	Dim objNodeList
	Dim objStream
	Dim i, j
	Set objXmlFile = CreateObject("Microsoft.XMLDOM")
		objXmlFile.async = False
		objXmlFile.ValidateOnParse = False
			objXmlFile.LoadXML (tmpXMLData)
			If objXmlFile.readyState <> 4 Then
				response.write "error"
			Else
				If objXmlFile.parseError.errorCode <> 0 Then
					response.write "error"
				Else
					Set objNodeList = objXmlFile.documentElement.selectNodes("//file/stream")
							j = objNodeList.length - 1
							For i = 0 To j
								Set objStream = CreateObject("ADODB.Stream")
									With objStream
										.Type = 1
										.Open
										.Write objNodeList(i).nodeTypedvalue
										.SaveToFile savePath, 2
										.Close
									End With
								Set objStream = Nothing
							Next
						Set objNodeList = Nothing
					End If
				End If
			Set objXmlFile = Nothing
		UnpackBase64=True
End Function

'模仿PHP的该函数，去掉反斜杠
Function stripslashes(str)
	stripslashes=Replace(Replace(Replace(str,"\'","'"),"\""",""""),"\\","\")
End Function


'模仿PHP的该函数，并添加了过滤HTML代码
Function htmlspecialchars(str)
	htmlspecialchars=TransferHTML(str,"[&][<][>][""][space][enter][nohtml]")
End Function

'自动生成文件名
Function RandomFileName(Ext)
	Dim m_strDate,m_lngTime,dtmNow
	dtmNow=Date
	m_strDate = Year(dtmNow)&Right("0"&Month(dtmNow),2)&Right("0"&Day(dtmNow),2)
	m_lngTime = Clng(Timer()*1000)
	m_lngTime=m_lngTime+1
	RandomFileName=m_strDate&Right("00000000"&m_lngTime,8)&"."&ext
End Function

'得到HTML数据，配合BytesToBstr使用
function gethtml(strUrl,charset)
	On Error Resume Next
	Dim objXmlHttp
	Set objXmlHttp=server.createobject("MSXML2.ServerXMLHTTP")
	objXmlHttp.setTimeouts 10000,10000,10000,30000
	objXmlHttp.open "GET",strUrl,False
	objXmlHttp.send()
	gethtml=BytesToBstr(objXmlHttp.responseBody,charset)
	Err.Clear
	Set objXmlHttp=Nothing
end function

'格式化BINARY DATA
Function BytesToBstr(body,Cset)
	Dim objstream
	Set objstream = Server.CreateObject("adodb.stream")
	objstream.Type = 1
	objstream.Mode =3
	objstream.Open
	objstream.Write body
	objstream.Position = 0
	objstream.Type = 2
	objstream.Charset = Cset
	BytesToBstr = objstream.ReadText
	objstream.Close
	Set objstream = Nothing
End Function
%>