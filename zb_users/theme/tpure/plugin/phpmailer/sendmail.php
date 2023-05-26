<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpmailer.php';
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'smtp.php';
function tpure_SendEmail($mailto,$subject,$content){
	global $zbp;
	$mail = new tpure_PHPMailer();
	$mail->CharSet  = "UTF-8";
	$mail->Encoding = "base64";
	$mail->Port = $zbp->Config('tpure')->SMTP_PORT;
	$mail->IsSMTP();
	$mail->Host = $zbp->Config('tpure')->SMTP_HOST;
	$mail->SMTPAuth = true;
	$mail->Username = $zbp->Config('tpure')->FROM_EMAIL;
	$mail->Password = $zbp->Config('tpure')->SMTP_PASS;
	$mail->From = $zbp->Config('tpure')->FROM_EMAIL;
	$mail->FromName = $zbp->Config('tpure')->FROM_NAME;
	$mail->AddAddress($mailto);
	$mail->WordWrap = 500;
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body    = $content;
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	if($zbp->Config('tpure')->SMTP_SSL){
		$mail->SMTPSecure = "ssl";
	}
	if (!$mail->Send()) {
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		return false;
	} else {
		return true;
	}
}