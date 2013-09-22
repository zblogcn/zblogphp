<?php
require_once dirname(__FILE__).'../../../../../zb_system/function/c_system_base.php';
require_once dirname(__FILE__).'../../../../../zb_system/function/c_system_admin.php';

global $zbp;
include_once $zbp->usersdir . 'plugin/AppBuy/appbuy_user.lib.php';

if(isset($_GET['type']) && $_GET['type'] == 'login'){
	include_once $zbp->usersdir . 'plugin/alipay/api.php';
	$parameter = array(
		"service" => "alipay.auth.authorize",
		"target_service"	=> "user.auth.quick.login",
		"return_url"	=> $zbp->host."zb_users/plugin/alipay/login_return_url.php",
	);
	AlipayAPI_Start($parameter);
}
if(isset($_COOKIE["appbuy_id"])){
	$AppBuyUser = new AppBuyUser;
	$login_status = $AppBuyUser->VerifyUser();
	if( $login_status ){
		header('Location: '.$zbp->host.'?shop&type=account');
	}
}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录 - Z-Blog应用中心</title>
<link rel="stylesheet" type="text/css" href="<?php echo $zbp->host . 'zb_users/plugin/AppBuy/shop/';?>static/css/login.css" />
</head>
<body>
	<div class="wrap">
		<div class="content_wrap">
			<div class="content">
				<h1 class="logo">应用中心</h1>
			</div>
		</div>
		<div class="login_wrap">
			<div class="login">
				<br><br><br><br><br><br><br>
					<p class="login_btn"><a href="?shop&type=login"/><span class="btn"><button type="submit">支　付　宝　帐　号　登　录</button></span></a></p>
				<!--<div class="helplink">
					<a href="?shop&type=download&data=document">开发文档下载</a>
					<a href="?shop&type=download&data=sdk">开发者  SDK 下载</a>	
				</div>-->
			</div>
		</div>
	</div>
</body>
</html>