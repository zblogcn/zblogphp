<?php

require './zb_system/function/c_system_base.php';
$zbp->Load();

if(isset($_GET['uid'])){
	$m=$zbp->members[$_GET['uid']];
	$un=$m->Name;
	if($blogversion>131221){
		$ps=md5($m->Password . $zbp->guid);
	}else{
		$ps=md5($m->Password . $zbp->path);
	}
	setcookie("username", $un,0,$zbp->cookiespath);
	setcookie("password", $ps,0,$zbp->cookiespath);
	Redirect('zb_system/admin/?act=admin');
	die();
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache,must-revalidate">
	<meta http-equiv="expires" content="0">
	<meta name="robots" content="none" />
	<title>Z-BlogPHP密码重置工具</title>
<style type="text/css">
<!--
*{
	font-size:14px;
}
body{
	margin:0;
	padding:0;
	color: #FFFFFF;
	font-size:12px;
	background:#FFFFFF;
	font-family:"微软雅黑","黑体","宋体";
}
h1,h2,h3,h4,h5,h6{
	font-size:18px;
	padding:0;
	margin:0;
}
a{
	text-decoration: none;
}
a:link {
	color:#FFFFFF;
	text-decoration: none;
}
a:visited {
	color:#FFFFFF;
	text-decoration: none;
}
a:hover {
	color:yellow;
	text-decoration: underline;
}
a:active {
	color:yellow;
	text-decoration: underline;
}
p{
	margin:0;
	padding:5px;
}
table {
	border-collapse: collapse;
	border:0px solid #333333;
	background:#ffffff;
	margin-top:10px;
}
td{
	border:0px solid #333333;
	margin:0;
	padding:3px;
}
img{
	border:0;
}
hr{
	border:0px;
	border-top:1px solid #666666;
	background:#666666;
	margin:2px 0 4px 0;
	padding:0;
	height:0px;
}
img{
	margin:0;
	padding:0;
}
form{
	margin:0;
	padding:0;
}


#frmLogin{
	position:absolute;
	left: 50%;
	top: 40%;
	margin: -150px 0px 0px -300px;
	padding:0;
	overflow:hidden;
	width:600px;
	height:400px;
	background-color:#3a6ea5;
	border:0px solid #B3C3CD;
	box-shadow: 0px 0px 15px black;
}

#frmLogin h3{
	padding:15px 0 5px 0;
	margin:0;
	text-align:center;
	color:white;
	font-size:24px;
	height:30px;
}

#divHeader{
	margin:0 0;
	padding:8px;
}
#divMain{
	height:280px;
}
#divFooter{
	margin:5px 0px 0 0px;
	text-align:center;
	padding:2px;
}

#divMain_Top{
	padding:8px;
	padding-bottom:0;
}
#divMain_Center{
	padding:5px;
}
#divMain_Bottom{
	text-align:right;
	padding:5px;
}
#txaContent{
	border:1px solid #A1B0B9;
	background:#FFFFFF;
}
-->
</style>
</head>
<body>


<form id="frmLogin" method="post">
<h3>Z-BlogPHP免输入密码登录工具</h3>
<div id="divHeader">&nbsp;&nbsp;<a href="http://www.zblogcn.com/" target="_blank">Z-Blog主页</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://bbs.zblogcn.com" class="here" target="_blank">Zblogger社区</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://wiki.zblogcn.com/" target="_blank">Z-Wiki</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://blog.zblogcn.com/" target="_blank">菠萝阁</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.dbshost.cn/" target="_blank">DBS主机</a></div>
<div id="divMain">
<input type="hidden" name="userid" id="userid" value="0" />
<?php

echo '<p></p>';

foreach ($zbp->members as $key => $m) {
	if($m->Level < 2)
		echo '<p style="padding:10px;">[管理员]' . $m->Name . '<input style="float:right;" type="button" value="&nbsp;&nbsp;登录&nbsp;&nbsp;" onclick="window.location=\'?uid='. $m->ID .'\'" /></p>';
}

?>
</div>
<div id="divFooter"><b>[注意]&nbsp;<font color="yellow"> 此工具非常危险,使用后请立刻通过<u>FTP</u>删除或改名.</font></b></div>
</form>
</body>
</html>