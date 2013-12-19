<?php

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','RespondError',PLUGIN_EXITSIGNAL_RETURN);

if (!$zbp->CheckPlugin('RegPage')) {$zbp->ShowError(48);die();}


$name=trim($_POST['name']);
$password=trim($_POST['password']);
$repassword=trim($_POST['repassword']);
$email=trim($_POST['email']);
$homepage=trim($_POST['homepage']);
$invitecode=trim($_POST['invitecode']);
$verifycode=trim($_POST['verifycode']);

if(!$zbp->CheckValidCode($verifycode,'RegPage')){
	$zbp->ShowError('验证码错误，请重新输入.');die();
}

$member=new Member;

$sql=$zbp->db->sql->Select($RegPage_Table,'*',array(array('=','reg_InviteCode',$invitecode),array('=','reg_AuthorID',0)),null,null,null);
$array=$zbp->GetListCustom($RegPage_Table,$RegPage_DataInfo,$sql);
$num=count($array);
if($num==0){
	$zbp->ShowError('邀请码不存在或已被使用.');die();
}
$reg=$array[0];

$member->Guid=$invitecode;
$member->Level=$reg->Level;



if(strlen($name)<3||strlen($name)>20){
	$zbp->ShowError('用户名不能过长或过短.');die();
}

if(!CheckRegExp($name,'[username]')){
	$zbp->ShowError('用户名只能包含字母数字._和中文.');die();
}



if(isset($zbp->membersbyname[$name])){
	$zbp->ShowError('用户名已存在');die();
}

$member->Name=$name;

if(strlen($password)<8||strlen($password)>20){
	$zbp->ShowError('密码必须在8位-20位间.');die();
}

if($password!=$repassword){
	$zbp->ShowError('请核对密码.');die();
}

$member->Password=Member::GetPassWordByGuid($password,$invitecode);

$member->PostTime=time();

$member->IP=GetGuestIP();


if(strlen($email)<5||strlen($email)>50){
	$zbp->ShowError('邮箱不能过长或过短.');die();
}

if(CheckRegExp($email,'[email]')){
	$member->Email=$email;
}else{
	$zbp->ShowError('邮箱格式不正确.');die();
}

if(CheckRegExp($homepage,'[homepage]')){
	$member->HomePage=$homepage;
}

$member->Save();

foreach ($GLOBALS['Filter_Plugin_RegPage_RegSucceed'] as $fpname => &$fpsignal) $fpname($member);

$keyvalue=array();
$keyvalue['reg_AuthorID']=$member->ID;

$sql = $zbp->db->sql->Update($RegPage_Table,$keyvalue,array(array('=','reg_ID',$reg->ID)));
$zbp->db->Update($sql);

//var_dump($member);

echo '恭喜您注册成功,请在登录页面登录.';

?>