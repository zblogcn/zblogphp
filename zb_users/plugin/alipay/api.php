<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝交易</title>
</head>
<?php
/* *
 * 功能：即时到账交易接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
require_once("alipay.aconfig.php");
require_once("lib/alipay_submit.class.php");


//构造要请求的参数数组，登陆
$parameter = array(
		"service" => "alipay.auth.authorize",
		"partner" => trim($alipay_config['partner']),
		"target_service"	=> "user.auth.quick.login",
		"return_url"	=> $bloghost."zb_users/plugin/alipay/login_return_url.php",
		"anti_phishing_key"	=> "",	//防钓鱼时间戳//若要使用请调用类文件submit中的query_timestamp函数
		"exter_invoke_ip"	=> GetGuestIP(),	//客户端的IP地址
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);


//构造要请求的参数数组，支付
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> "1",
		"notify_url"	=> $blogpath."/zb_users/plugin/alipay/pay_notify_url.php",
		"return_url"	=> $blogpath."/zb_users/plugin/alipay/pay_return_url.php",
		"seller_email"	=> $zbp->Config('alipay')->alipayaccount,
		"out_trade_no"	=>  "12345678",	//订单号
		"subject"	=> "订单名称",
		"total_fee"	=> "111",	//金额
		"body"	=> "订单描述",
		"show_url"	=> "http://www.xxx.com/myorder.html",
		"anti_phishing_key"	=> "",	//防钓鱼时间戳//若要使用请调用类文件submit中的query_timestamp函数
		"exter_invoke_ip"	=> GetGuestIP(),	//客户端的IP地址
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>