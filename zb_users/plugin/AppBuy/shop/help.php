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
				<li ><a href="?shop&type=account">用户信息</a></li>
				<li ><a href="?shop&type=orderlist">订单列表</a></li>
				<li class="current"><a href="?shop&type=help">帮助</a></li>
			</ul>
		</div>
		<div class="content_wrap">
			<div class="content">
<div class="table_full">
<ol>
	<li><a href="#login">登陆</a></li>
	<li><a href="#pay">支付</a></li>
	<li><a href="#verify">验证</a></li>
</ol>

<hr />
<ul>
	<li>&nbsp; &nbsp; <a id="login" name="login">登陆</a></li>
</ul>

<p>&nbsp; &nbsp; 目前应用中心用户购买收费应用采用支付宝帐号作为唯一用户体系，并且当前无计划开发独立用户系统。作为应用中心收费应用在线支付的接口，支付宝有较高的安全性。</p>

<p>&nbsp; &nbsp; 应用中心目前登陆后会保存登陆状态，除非手动删除浏览器COOKIE，否则此登陆状态会一直保存。</p>

<p>&nbsp; &nbsp; 应用中心用户姓名调用的是<a href="https://my.alipay.com/portal/account/index.htm" target="_blank">支付宝帐户管理</a>处的真实姓名，而非昵称。</p>

<p>&nbsp;</p>

<ul>
	<li>&nbsp; &nbsp; <a id="pay" name="pay">支付</a></li>
</ul>

<p>&nbsp; &nbsp; 应用中心的支付体系采用支付宝在线支付，用户购买收费应用后会将相关费用直接转入开发者支付宝帐户，所有购买相关功能均为实时在线完成，在支付完成后返回本站即可下载应用。</p>

<p>&nbsp;</p>

<ul>
	<li>&nbsp; &nbsp; <a id="verify" name="verify">验证</a></li>
</ul>

<p>&nbsp; &nbsp; 目前应用中心暂不对已购买应用的使用情况进行验证，为保障各购买用户和应用开发者利益，望各用户仅将购买应用本人使用，后期将会采用域名白名单验证。</p>

</div>

		</div>
	</div>
</div>
</body>
</html>