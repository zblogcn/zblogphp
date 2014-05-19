<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('upyun')) {$zbp->ShowError(48);die();}
$blogtitle='又拍云存储';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
if (isset($_POST['upyun_operator_name']) && $_POST['upyun_operator_name'] != '') {
	$zbp->Config('upyun')->upyun_operator_name = $_POST['upyun_operator_name'];
	$zbp->Config('upyun')->upyun_operator_password = $_POST['upyun_operator_password'];
	$zbp->SaveConfig('upyun');

	if ($zbp->Config('upyun')->upyun_operator_name == '') {
		$buff = "请设置操作员帐号！！！";
	}

	if(isset($buff)){
		$zbp->ShowHint('bad', $buff);
	}else{
		$zbp->ShowHint('good', "保存成功！");
	}
}
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php upyun_SubMenu(3);?></div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td><b><label for="upyun_operator_name"><p align="center">操作员帐号</p></label></b></td>
    <td><p align="left"><input name="upyun_operator_name" type="text" id="upyun_operator_name" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_operator_name;?>" /></p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_operator_password"><p align="center">操作员密码</p></label></b></td>
    <td><p align="left"><input name="upyun_operator_password" type="text" id="upyun_operator_password" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_operator_password;?>" /></p></td>
  </tr>
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>
    </form>
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>