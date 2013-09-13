<?php
#注册插件
RegisterPlugin("alipay","ActivePlugin_alipay");


function ActivePlugin_alipay() {
	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','alipay_AddMenu');
}

function alipay_AddMenu(&$m){
	global $zbp;
	$m[]=MakeLeftMenu("root","支付宝",$zbp->host . "zb_users/plugin/alipay/main.php","nav_alipay","aalipay",$zbp->host . "zb_system/image/common/file_1.png");	
}


?>