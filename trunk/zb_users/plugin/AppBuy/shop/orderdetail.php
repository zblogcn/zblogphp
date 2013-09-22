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
				<li><a href="?shop&type=account">用户信息</a></li>
				<li ><a href="?shop&type=orderlist">订单列表</a></li>
				<li class="current"><a href="?shop&type=orderdetail">订单详情</a></li>
				<li ><a href="?shop&type=help">帮助</a></li>
			</ul>
		</div>
		<div class="content_wrap">
			<div class="content">
<div class="table_full">
	<table width="100%">
		<tr class="tr">
			<th class="th">ID</th>
			<td class="td"><?php echo $AppBuyUser->ID;?></td>
		</tr>
		<tr class="tr">
			<th class="th">购买者</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">开发者</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">应用名称</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">订单号</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">支付宝交易号</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">交易金额</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">应用简介</th>
			<td class="td"><?php echo $AppBuyUser->AlipayName;?></td>
		</tr>
		<tr class="tr">
			<th class="th">订单创建时间</th>
			<td class="td"><?php echo $AppBuyUser->AlipayID;?></td>
		</tr>
		<tr class="tr">
			<th class="th">订单支付时间</th>
			<td class="td"><?php echo  date("Y年m月d日 H:m:s",$AppBuyUser->CreatTime);?></td>
		</tr>
		<tr class="tr">
			<th class="th">购买者支付宝ID</th>
			<td class="td"><?php echo date("Y年m月d日 H:m:s",$AppBuyUser->LoginTime);?></td>
		</tr>
		<tr class="tr">
			<th class="th">购买者支付宝帐号</th>
			<td class="td"><?php echo $_SESSION['appbuy_lastip'];?></td>
		</tr>
		<tr class="tr">
			<th class="th">开发者支付宝帐号</th>
			<td class="td"><?php echo $_SESSION['appbuy_lastip'];?></td>
		</tr>
		<tr class="tr">
			<th class="th">交易状态</th>
			<td class="td"><?php echo $_SESSION['appbuy_lastip'];?></td>
		</tr>
	</table>
</div>
<div class="tac mb10"><span class="btn"><span><button type="submit">在线支付</button></span></span></div>
<div class="tac mb10"><span class="btn"><span><button type="submit">下载应用</button></span></span></div>
		</div>
	</div>
</div>
</body>
</html>