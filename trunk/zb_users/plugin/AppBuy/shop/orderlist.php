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
<title>应用中心购物平台</title>
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
				<li class="current"><a href="?shop&type=orderlist">订单列表</a></li>
				<li ><a href="?shop&type=help">帮助</a></li>
			</ul>
		</div>
		<div class="content_wrap">
			<div class="content">
<div class="table_full">
	<table width="100%">
		<tbody>
			<tr class="tr">
				<td class="td"><b>编号</b></td>
				<td class="td"><b>订单号</b></td>
				<td class="td"><b>应用名称</b></td>
				<td class="td"><b>金额</b></td>
				<td class="td"><b>创建时间</b></td>
				<td class="td"><b>支付时间</b></td>
				<td class="td"><b>状态</b></td>
				<td class="td"><b>操作</b></td>
			</tr>
			<tr class="tr">
				<td class="td">111</td>
				<td class="td">987654321</td>
				<td class="td">微信</td>
				<td class="td">￥10.88</td>
				<td class="td">2013-09-22 01:09:57</td>
				<td class="td">2013-09-22 01:09:57</td>
				<td class="td">已支付</td>
				<td class="td"><a href="#">详情</a></td>
			</tr>
		</tbody>
	</table>
</div>
		</div>
	</div>
</div>
</body>
</html>