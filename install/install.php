<?php
#///////////////////////////////////////////////////////////////////////////////
#//              Z-BlogPHP 在线安装程序
#///////////////////////////////////////////////////////////////////////////////

error_reporting(0);
//ob_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Z-BlogPHP 在线安装程序</title>
<style type="text/css">
<!--
*{
	font-size:14px;
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
	color:#3a6ea5;
}
h1{
font-size:28px;
}
input{
	padding:15px 80px;
}
div{
	position:absolute;
	left: 50%;
	top: 50%;
	margin: -200px 0px 0px -150px;
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
<p><img src="http://update.rainbowsoft.org/zblogphp/loading.png" alt=""/></p>
<form method="post" action="#">
<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	install1();
	install2();
	install3();
	die();
}


install0();
?>
<p><input type="submit" value="开始安装" onclick="this.style.display='none';" /></p>
</form>
<?php

?>

<?php


$s=null;

function install0(){

	$d=dirname(__FILE__);

	if(substr((string)decoct(fileperms($d)),-3)<>'755'&&substr((string)decoct(fileperms($d)),-3)<>'777'){
		echo "<p>警告:安装目录权限" . $d . "不是755,可能无法运行在线安装程序.</p>";
	}

}


function install1(){

	echo "<p>正在努力地下载数据包...</p>";
	ob_flush();
	$GLOBALS['s']=file_get_contents('compress.zlib://' . 'http://update.rainbowsoft.org/zblogphp/?install');
	//file_put_contents('release.xml',$GLOBALS['s']);

}

function install2(){

	echo "<p>正在解压和安装文件...</p>";
	ob_flush();
	if ($GLOBALS['s']) {
		$xml = simplexml_load_string($GLOBALS['s'],'SimpleXMLElement');
		$old = umask(0);
		foreach ($xml->file as $f) {
			$filename=str_replace('\\','/',$f->attributes());
			$dirname= dirname($filename);
			mkdir($dirname,0755,true);
			if(PHP_OS=='WINNT'||PHP_OS=='WIN32'||PHP_OS=='Windows'){
				$fn=iconv("UTF-8","GBK//IGNORE",$filename);
			}else{
				$fn=$filename;
			}
			file_put_contents($fn,base64_decode($f));
		}
		umask($old);
	} else {
		exit('release.xml不存在!');
	}

}

function install3(){

	#unlink('release.xml');
	unlink('install.php');
	echo '<script type="text/javascript">location="./zb_install/"</script>';
	
}

?>
</div>
</body>
</html>