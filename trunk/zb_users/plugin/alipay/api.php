<?php
/* 
//构造要请求的参数数组，登陆
$parameter = array(
		"service" => "alipay.auth.authorize",
		"target_service"	=> "user.auth.quick.login",
		"return_url"	=> $bloghost."zb_users/plugin/alipay/login_return_url.php",
);


//构造要请求的参数数组，支付
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"payment_type"	=> "1",
		"notify_url"	=> $blogpath."/zb_users/plugin/alipay/pay_notify_url.php",
		"return_url"	=> $blogpath."/zb_users/plugin/alipay/pay_return_url.php",
		"seller_email"	=> $zbp->Config('alipay')->alipayaccount,
		"out_trade_no"	=>  "12345678",	//订单号
		"subject"	=> "订单名称",
		"total_fee"	=> "111",	//金额
		"body"	=> "订单描述",
		"show_url"	=> "http://www.xxx.com/myorder.html",
);
*/
function AlipayAPI_Start($parameter){
	global $zbp;
	require_once("alipay.aconfig.php");
	require_once("lib/alipay_submit.class.php");
	//公共$parameter
	$parameter["partner"] = trim($alipay_config['partner']);
	$parameter["anti_phishing_key"]	= "";	//防钓鱼时间戳//若要使用请调用类文件submit中的query_timestamp函数
	$parameter["exter_invoke_ip"] = GetGuestIP();	//客户端的IP地址
	$parameter["_input_charset"] = trim(strtolower($alipay_config['input_charset']));

	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "...");
	echo $html_text;
}
?>