<?php
#///////////////////////////////////////////////////////////////////////////////
#//              Z-BlogPHP 在线安装程序
#///////////////////////////////////////////////////////////////////////////////

error_reporting(0);
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<title>Z-BlogPHP 在线安装程序</title>
<style type="text/css">
<!--
*{
	font-size:14px;
	border:none;
}
body{
	margin:0;
	padding:0;
	color: #000000;
	background:#fff;
	font-family:"微软雅黑","宋体";
}
h1,h2,h3,h4,h5,h6{
	font-size:18px;
	padding:0;
	margin:0;
}
h1{
font-size:28px;
}
div{
	position:absolute;
	left: 50%;
	top: 50%;
	margin: -150px 0px 0px -150px;
	padding:0;
	overflow:hidden;
	width:300px;
	background-color:white;
	text-align:center;
}
-->
</style>
</head>
<body>
<div>
<h1>Z-BlogPHP 在线安装</h1>
<p><img src="http://update.rainbowsoft.org/zblog2/loading.gif" alt=""></p>

<?php

install();
install2();

echo '<script>location="/"</script>';

function install(){

echo "<p>正在努力地下载数据包...</p>";
ob_flush();
sleep(1);
$a=file_get_contents('compress.zlib://' . 'http://update.rainbowsoft.org/zblog2/?install');
file_put_contents('release.xml',$a);

}

function install2(){

echo "<p>正在解压和安装文件...</p>";
ob_flush();
sleep(1);
if (file_exists('release.xml')) {
    $xml = simplexml_load_file('release.xml');

	foreach ($xml->file as $f) {
		$d= dirname(str_replace('\\','/',$f->attributes()));
		mkdir($d,'0777',true);
		file_put_contents(iconv("UTF-8","gb2312",$f->attributes()),base64_decode($f));
	}

	unlink('release.xml');
	unlink('install.php');

} else {
    exit('release.xml文件不存在!');
}


}

/*
Const InstallerVersion="1.0"
Dim fso
Set fso = CreateObject("Scripting.FileSystemObject")
If fso.FileExists(Server.MapPath(".") & "\" & "Release.log")=True Then
	Response.Write "<p>已运行过安装程序，将删除安装程序......</p>"
	fso.Deletefile(Server.MapPath(".") & "\" & "Release.log") 
	fso.Deletefile(Server.MapPath(".") & "\" & "Release.xml") 
		fso.Deletefile(Server.MapPath(Request.ServerVariables("PATH_INFO"))) 
Else

	If fso.FileExists(Server.MapPath(".") & "\" & "Release.xml")=True Then
		Install2
	Else
		Install1
		Install2
	End If

End If



Function Install2

	Response.Write "<p>正在解压和安装文件...</p>"
	Response.Flush

	Dim objXmlFile,strXmlFile
	Dim fso, f, f1, fc, s
	Set fso = CreateObject("Scripting.FileSystemObject")

	strXmlFile =Server.MapPath(".") & "\" & "Release.xml"
	
	If fso.FileExists(strXmlFile) Then

		Set objXmlFile=Server.CreateObject("Microsoft.XMLDOM")
		objXmlFile.async = False
		objXmlFile.ValidateOnParse=False
		objXmlFile.load(strXmlFile)
		If objXmlFile.readyState=4 Then
			If objXmlFile.parseError.errorCode <> 0 Then
			Else



				Dim objXmlFiles,item,objStream
				Set objXmlFiles=objXmlFile.documentElement.SelectNodes("file")
				for each item in objXmlFiles
				Set objStream = CreateObject("ADODB.Stream")
					With objStream
					.Type = 1
					.Mode = 3
					.Open
					.Write item.nodeTypedvalue
					
					Dim i,j,k,l
					i=item.getAttributeNode("name").Value

					j=Left(i,InstrRev(i,"\"))
					k=Replace(i,j,"")
					Call CreatDirectoryByCustomDirectory("" & j)

					.SaveToFile Server.MapPath(".") & "\" & item.getAttributeNode("name").Value,2

					's=s& "释放 " & k & ";"
					.Close
					End With
					Set objStream = Nothing
					l=l+1
				next


			End If
		End If
	End If


	Call fso.CreateTextFile(Server.MapPath(".") & "\" & "Release.log", True)
	fso.Deletefile(Server.MapPath(".") & "\" & "Release.xml") 
	fso.Deletefile(Server.MapPath(Request.ServerVariables("PATH_INFO"))) 
	Response.Write "<script>location=""zb_install/default.asp""</script>"

End Function

Function IIf(a,b,c)
	If a Then IIf=b Else IIf=c
End Function
'*********************************************************
' 目的：    按照CustomDirectory指示创建相应的目录
'*********************************************************
Sub CreatDirectoryByCustomDirectory(ByVal strCustomDirectory)

	On Error Resume Next

	Dim s
	Dim t
	Dim i
	Dim j

	Dim fso
	Set fso = CreateObject("Scripting.FileSystemObject")

	s=Server.MapPath(".") & "\"

	strCustomDirectory=Replace(strCustomDirectory,"/","\")

	t=Split(strCustomDirectory,"\")

	j=0
	For i=LBound(t) To UBound(t)
		If (IsEmpty(t(i))=False) And (t(i)<>"") Then
			s=s & t(i) & "\"
			If (fso.FolderExists(s)=False) Then
				Call fso.CreateFolder(s)
			End If
			j=j+1
		End If
	Next

	Set fso = Nothing

	Err.Clear

End Sub
'*********************************************************

*/

?>
</div>
</body>
</html>