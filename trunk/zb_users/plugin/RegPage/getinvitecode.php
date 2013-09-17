<?php
session_start();
header('Content-Type: text/html; Charset=utf-8');  
if(!isset($_POST['yz'])){
?>
<form action="getinvitecode.php" method="POST" name="form">
<p>请输入验证码：<img src="captcha.php?rand=<?php echo rand();?>" alt=""/></p>
<p></p>
<p><input name="yz" type="text" size="16" />&nbsp;&nbsp;<input value="提交" type="submit" /></p>
</form>
<?php
}else{
 if(empty($_SESSION['6_letters_code'] ) || strcasecmp($_SESSION['6_letters_code'], $_POST['yz']) != 0)
    { 
        echo "验证失败！";
    }else{
	
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

if (!$zbp->CheckPlugin('RegPage')) {$zbp->ShowError(48);die();}



if(!$zbp->Config('RegPage')->open_reg){
	echo '<p>本网站不开放会员注册.</p>';
	die();
}


$sql=$zbp->db->sql->Select($RegPage_Table,'*',array(array('=','reg_AuthorID',0)),null,array(1),null);
$array=$zbp->GetListCustom($RegPage_Table,$RegPage_DataInfo,$sql);
$num=count($array);
if($num==0){
	echo '<p>邀请码派发完了.</p>';
}else{
	echo '<p>邀请码: <br/>'.$array[0]->InviteCode .'</p><p>请选中邀请码并复制后点OK按钮.</p>';
}

die();
}}
?>