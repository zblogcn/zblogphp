<?php
require '../../../../zb_system/function/c_system_base.php';
$zbp->Load();
$action = 'root';

///////////////////////////////////
$appid='tpure';
///////////////////////////////////

if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin($appid)) {$zbp->ShowError(48);die();}

$data=$zbp->Config($appid)->GetData();
$ua = $_SERVER["HTTP_USER_AGENT"];
$filename = $appid.'.config';
header('Content-Type: application/octet-stream');
if(preg_match("/MSIE/", $ua)){
	header('Content-Disposition: attachment; filename="' . $filename . '"');
}elseif(preg_match("/Firefox/", $ua)){
	header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
}else{
header('Content-Disposition: attachment; filename="' . $filename . '"');
}
ob_clean();
echo base64_encode(json_encode($data));