<?php
session_start(); 
include_once "function.php";
include_once $zbp->usersdir . 'plugin/AppBuy/appbuy_user.lib.php';

$status_code=array(
	0 => '未知状态',
	1 => '已创建',
	2 => '待付款',
	3 => '',
	4 => '已付款',
	5 => '',	
	6 => '',	
	7 => '未提交',	
	8 => '已审核',	
	9 => '未审核',	
);
#注册插件
RegisterPlugin("AppBuy","ActivePlugin_AppBuy");

function ActivePlugin_AppBuy() {
	Add_Filter_Plugin('Filter_Plugin_AlipayLogin_Succeed','AlipayLogin_Succeed');
	Add_Filter_Plugin('Filter_Plugin_AlipayLogin_Failed','AlipayLogin_Failed');
	Add_Filter_Plugin('Filter_Plugin_AlipayPayReturn_Succeed','AlipayPayReturn_Succeed');
	Add_Filter_Plugin('Filter_Plugin_AlipayPayReturn_Failed','AlipayPayReturn_Failed');
	Add_Filter_Plugin('Filter_Plugin_AlipayPayNotice_Succeed','AlipayPayNotice_Succeed');
	
	Add_Filter_Plugin('Filter_Plugin_Member_Edit_Response','Dev_Alipy_Set');
	Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu','AppBuy_AddMenu');
	global $zbp;

	if(isset($_GET['shop'])){
		AppBuy_Login($_GET);
		die();
	}

}

function InstallPlugin_AppBuy() {}
function UninstallPlugin_AppBuy() {}

function AppBuy_Login($getdata){
	global $zbp;
	
	if(isset($getdata['type']) && $getdata['type'] == 'account'){
		include_once $zbp->usersdir . 'plugin/AppBuy/shop/account.php';
	}elseif(isset($getdata['type']) && $getdata['type'] == 'help'){
		include_once $zbp->usersdir . 'plugin/AppBuy/shop/help.php';
	}elseif(isset($getdata['type']) && $getdata['type'] == 'orderdetail'){
		include_once $zbp->usersdir . 'plugin/AppBuy/shop/orderdetail.php';	
	}elseif(isset($getdata['type']) && $getdata['type'] == 'orderlist'){
		include_once $zbp->usersdir . 'plugin/AppBuy/shop/orderlist.php';	
	}elseif(isset($getdata['type']) && $getdata['type'] == 'quit'){
		setcookie("appbuy_id", '', time()-3600, $zbp->cookiespath);
		setcookie("appbuy_pw", '', time()-3600, $zbp->cookiespath);
		header('location: '.$zbp->host.'?shop');
	}elseif(isset($getdata['type']) && $getdata['type'] == 'buy'){
		$call = Alipay_Pay_Call($getdata);
		if(!$call) Show_Tips('请使用支付宝登陆后再购买', '?shop');
	}else{
		include_once $zbp->usersdir . 'plugin/AppBuy/shop/index.php';
	}
}

function Alipay_Pay_Call($getdata){
	global $zbp;
	include_once $zbp->usersdir . 'plugin/AppBuy/appbuy_user.lib.php';
	if(isset($_COOKIE["appbuy_id"])){
		$AppBuyUser = new AppBuyUser;
		$login_status = $AppBuyUser->VerifyUser();
		if( !$login_status ){
			$_SESSION['appbuy_returnurl'] = $zbp->host .'?shop&type=buy&id='.$getdata['id'];
			return false;
		}else{
			include_once $zbp->usersdir . 'plugin/alipay/api.php';
			$app = new Post;
			$dev = new Member;
			$app->LoadInfoByID($getdata['id']);
			$dev->LoadInfoByID($app->AuthorID);
			$parameter = array(
					"service" => "create_direct_pay_by_user",
					"payment_type"	=> "1",
					"notify_url"	=> $zbp->host."/zb_users/plugin/alipay/pay_notify_url.php",
					"return_url"	=> $zbp->host ."/zb_users/plugin/alipay/pay_return_url.php",
					"seller_email"	=> $zbp->Config('alipay')->alipayaccount,
					"out_trade_no"	=>  "12345678",	//订单号
					"subject"	=> $app->Title,
					"total_fee"	=> "111",	//金额
					"royalty_type" => "10",//提成类型
					"royalty_parameters" => $dev->Metas->alipay ."^0.1^[名称]分润备注啊啊啊|",
					"body"	=> "订单描述",
					"show_url"	=> "http://www.xxx.com/myorder.html",
			);
			print_r($parameter);
			return true;
			//AlipayAPI_Start($parameter);
		}
	}else{
		return false;
	}
}

function AlipayLogin_Succeed($data){
	global $zbp;
	//print_r($data);//die();

	$AppBuyUser = new AppBuyUser;
	$sql = $zbp->db->sql->Select($GLOBALS['table']['appbuyuser'],array('*'),array(array('=','sp_AlipayID',$data['user_id'])),null,null,null);
	$list = $zbp->GetList('AppBuyUser',$sql);
	if(count($list) > 0){
		$_SESSION['appbuy_lasttime'] = $list[0]->LoginTime;
		$_SESSION['appbuy_lastip'] = $list[0]->LoginIP;
		$AppBuyUser->LoadInfoByID($list[0]->ID);
		$AppBuyUser->AlipayName = $data['real_name'];
		$AppBuyUser->LoginTime = time();
		$AppBuyUser->LoginIP = GetGuestIP();
		$AppBuyUser->Save();
	}else{
		$AppBuyUser->LoadInfoByID(0);
		$AppBuyUser->AlipayID = $data['user_id'];
		$AppBuyUser->AlipayName = $data['real_name'];
		$AppBuyUser->CreatTime = time();
		$AppBuyUser->LoginTime = time();
		$AppBuyUser->CreatIP = GetGuestIP();
		$AppBuyUser->LoginIP = GetGuestIP();
		$guid = GetGuid();
		$AppBuyUser->Guid =$guid;
		$AppBuyUser->Password = $AppBuyUser::AppBuyGetPassWord($AppBuyUser->AlipayID, $guid);
		$AppBuyUser->Save();
		$_SESSION['appbuy_lasttime'] = time();
		$_SESSION['appbuy_lastip'] = GetGuestIP();
	}
	$_SESSION['appbuy_token'] = $data['token'];
	$_SESSION['appbuy_sign'] = $data['sign'];
	setcookie("appbuy_loginstatus", 1, time()+3600,$zbp->cookiespath);
	setcookie("appbuy_id", $AppBuyUser->ID, time()+3600*24*365,$zbp->cookiespath);
	setcookie("appbuy_pw", md5($AppBuyUser->Password), time()+3600*24*365,$zbp->cookiespath);
	header('Location: '.$zbp->host.'?shop&type=account');
}
function AlipayPayReturn_Succeed(&$data){
//执行日期：20130923000302
//&buyer_email=13736011311@139.com
//buyer_id=2088502266658644
//exterface=create_direct_pay_by_user
//is_success=T
//notify_id=RqPnCoPT3K9%2Fvwbh3I72ICpJ3emJ7B6csESHZJLDdBacDbhqmGW8oqPn9zADxC1W%2F%2F8V
//notify_time=2013-09-23 00:03:03
//notify_type=trade_status_sync
//out_trade_no=12345601
//payment_type=1
//seller_email=nbfhzj19901101@126.com
//seller_id=2088002070241624
//subject=订单名称
//total_fee=1.00
//trade_no=2013092349491164
//trade_status=TRADE_SUCCESS
//sign=7559715bcd67126ceb98032cc779c2fa
//sign_type=MD5



}
function AlipayPayNotice_Succeed(&$data){


}
function AlipayLogin_Failed(){
	global $zbp;
	header('Content-Type: text/html; Charset=utf-8');
	echo '<script type="text/javascript">alert("登陆失败，请重试!");location.href=\''.$zbp->host.'?shop\';</script>';
	die();
}
function AlipayPayReturn_Failed(){
	global $zbp;
	header('Content-Type: text/html; Charset=utf-8');
	echo '<script type="text/javascript">alert("订单支付状态异常,请登录后台查看!");location.href=\''.$zbp->host.'?shop&type=orderlist\';</script>';
	die();
}

function Dev_Alipy_Set(){
	global $zbp,$member,$status_code;
	echo '<label for="" class="editinputname" >开发者收费应用支付选项:</label>';
	echo '<p style="width:70%"><input type="text" readonly="readonly" style="width:16%;border:none;" value="支付宝帐号"/><input type="text" name="meta_alipay" value="'.htmlspecialchars($member->Metas->alipay).'"  style="width:50%;"/></p>';
	echo '<p style="width:70%"><input type="text" readonly="readonly" style="width:16%;border:none;" value="支付宝帐号审核状态"/><input type="text" readonly="readonly"  value="'.$status_code[$member->Metas->alipaystatus].'"  style="width:35%;"/><a href=""/><input class="button" value="提交审核" style="width:8%;height:20px"></a></p>';
}

function AppBuy_AddMenu(&$m){
	global $zbp;
	$m[]=MakeLeftMenu("root","订单管理",$zbp->host . "zb_users/plugin/AppBuy/main.php","nav_AppBuy","aAppBuy",$zbp->host . "zb_system/image/common/file_1.png");	
}
?>