<?php

require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
header("Content-type: text/html; charset=utf-8");

if (!$zbp->CheckPlugin('RegPage')) {$zbp->ShowError(48);die();}

if(!$zbp->Config('RegPage')->open_reg){
	echo '本网站不开放会员注册.';
	die();
}


$sql=$zbp->db->sql->Select($RegPage_Table,'*',array(array('=','reg_AuthorID',0)),null,array(1),null);
$array=$zbp->GetListCustom($RegPage_Table,$RegPage_DataInfo,$sql);
$num=count($array);
if($num==0){
	echo '邀请码派发完了.';
}else{
	echo '邀请码:'.$array[0]->InviteCode;
}

die();
?>