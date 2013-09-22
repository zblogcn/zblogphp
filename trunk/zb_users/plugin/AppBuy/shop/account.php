<?php
require_once dirname(__FILE__).'../../../../../zb_system/function/c_system_base.php';
require_once dirname(__FILE__).'../../../../../zb_system/function/c_system_admin.php';

global $zbp;
include_once $zbp->usersdir . 'plugin/AppBuy/appbuy_user.lib.php';

if(isset($_COOKIE["appbuy_id"])){
	$AppBuyUser = new AppBuyUser;
	$login_status = $AppBuyUser->VerifyUser();
	if( !$login_status ){
		header('Location: '.$zbp->host.'?shop');
	}
}else{
	header('location: '.$zbp->host.'?shop');
}
if(!isset($_COOKIE['appbuy_loginstatus'])){
	$AppBuyUser->LoadInfoByID($_COOKIE["appbuy_id"]);
	$_SESSION['appbuy_lasttime'] = $AppBuyUser->LoginTime;
	$_SESSION['appbuy_lastip'] = $AppBuyUser->LoginIP;
	$AppBuyUser->LoginTime = time();
	$AppBuyUser->LoginIP = GetGuestIP();
	$AppBuyUser->Save();
	setcookie("appbuy_loginstatus", 1, time()+3600,$zbp->cookiespath);
}
if(isset($_POST['id'])){
	$AppBuyUser->LoadInfoByID($_POST['id']);
	$AppBuyUser->Email = $_POST['email'];
	//$AppBuyUser->Domain = $_POST['domain'];
	$AppBuyUser->Save();
}
	$AppBuyUser->LoadInfoByID($_COOKIE["appbuy_id"]);
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Z-Blog应用中心</title>
<link href="<?php echo $zbp->host . 'zb_users/plugin/AppBuy/shop/';?>static/css/style.css" rel="stylesheet" />
</head>
<body>
<div class="wrap">
		<div class="top">
			<span class="fr"><a href="index.php?a=account" class="mr10"><?php echo $AppBuyUser->AlipayName;?></a><a href="?shop&type=quit">[注销]</a></span>
			<h1>控制面板</h1>
		</div>
		<div class="menubar">
			<ul>
				<li class="current"><a href="?shop&type=account">用户信息</a></li>
				<li ><a href="?shop&type=orderlist">订单列表</a></li>
				<li ><a href="?shop&type=orderdetail">订单列表</a></li>
				<li ><a href="?shop&type=help">帮助</a></li>
			</ul>
		</div>
		<div class="content_wrap">
			<div class="content">
<form action="?shop&type=account" method="post" enctype="multipart/form-data">
<input name="id" value="<?php echo $AppBuyUser->ID;?>" type="hidden" />
<div class="table_full">
	<table width="100%">
		<tr class="tr">
			<th class="th">ID</th>
			<td class="td"><?php echo $AppBuyUser->ID;?></td>
		</tr>
		<tr class="tr">
			<th class="th">Email</th>
			<td class="td"><input name="email" class="input" value="<?php echo $AppBuyUser->Email;?>"/> [重要，请务必准确填写!]</td>
		</tr>
		<tr class="tr">
			<th class="th">姓名</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">支付宝ID</th>
			<td class="td"><?php echo $AppBuyUser->AlipayID;?></td>
		</tr>
		<!--<tr class="tr">
			<th class="th">域名绑定</th>
			<td class="td"><input name="domain" class="input" disabled value="<?php echo $AppBuyUser->Domain;?>"/> [多域名使用“|”分割]</td>
		</tr>-->
		<tr class="tr">
			<th class="th">注册时间</th>
			<td class="td"><?php echo  date("Y年m月d日 H:m:s",$AppBuyUser->CreatTime);?></td>
		</tr>
		<tr class="tr">
			<th class="th">上次登陆时间</th>
			<td class="td"><?php echo date("Y年m月d日 H:m:s",$AppBuyUser->LoginTime);?></td>
		</tr>
		<tr class="tr">
			<th class="th">上次登陆IP</th>
			<td class="td"><?php echo $_SESSION['appbuy_lastip'];?></td>
		</tr>
	</table>
</div>
<div class="tac mb10"><span class="btn"><span><button type="submit">保存</button></span></span></div>
</form>
		</div>
	</div>
</div>
</body>
</html>