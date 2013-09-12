<?php

#注册插件
RegisterPlugin("baidu_bcms","ActivePlugin_baidu_bcms");


function ActivePlugin_baidu_bcms() {
	Add_Filter_Plugin('Filter_Plugin_PostMember_End','sendmail');
}


require_once ( "Bcms.class.php" ) ;
$accessKey = '761ec0d78e6d844e102b0d9cb25d1175';
$secretKey = '8f1058c629c1ef2046f0f09cd1ebf080';
$host = 'bcms.api.duapp.com';

function sendmail($MemberInfo){
	global $accessKey, $secretKey, $host;
	$bcms = new Bcms ( $accessKey, $secretKey, $host ) ;
	
	$message = <<<EOT
<!--HTML--><div><div><div><span style="line-height:1.6em">{$MemberInfo['Name']}</span></div></div><div><br />非常感谢您申请 <a href="http://www.rainbowsoft.org/zblogphp/" target="_blank">Z-BlogPHP</a>的内测申请！<br /><span style="line-height:1.6em">您的内测申请已经成功登记，目前内测工作处于准备阶段，我们会在正式开始内测后向您发送邮件通知，请注意查收。</span></div><div><br />以下是&nbsp;&nbsp;{$MemberInfo['Name']}&nbsp;&nbsp;的帐户信息。请妥善保管。</div><div><br />邮件地址: <a href="mailto:{$MemberInfo['Email']}" target="_blank">{$MemberInfo['Email']}</a><br />密码&nbsp;&nbsp;&nbsp; : ********<br />登录URL&nbsp;&nbsp; : <a href="http://zdevo.com/zb_system/login.php" target="_blank">http://zdevo.com/zb_system/login.php</a><br /><br />-----------------------------<br /><strong><a href="http://bbs.rainbowsoft.org/" style="word-wrap: break-word; color: rgb(51, 51, 51); text-decoration: none;" target="_blank">RainbowSoft Studio</a></strong>&nbsp; <a href="http://www.rainbowsoft.org/" target="_blank">http://www.rainbowsoft.org/</a></div></div>
EOT;
	
	$ret = $bcms->mail ( '2734d2a0a0f0517ee6bfe8eee0c4efc8', $message, array($MemberInfo['Email']), array(Bcms::FROM => 'rain@zb.com',Bcms::MAIL_SUBJECT => 'Z-BlogPHP内测申请') ) ;
	if ( false === $ret ) 
	{
		return false;
	}
	else
	{
		return true;
	}	

}

?>