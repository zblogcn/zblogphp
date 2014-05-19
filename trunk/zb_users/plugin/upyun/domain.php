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

if (isset($_POST['upyun_enable_domain'])) {
	$zbp->Config('upyun')->upyun_enable_domain = $_POST['upyun_enable_domain'];
	$zbp->Config('upyun')->upyun_domain = $_POST['upyun_domain'];
	$zbp->SaveConfig('upyun');

	$domain = $zbp->Config('upyun')->upyun_domain;

	if ($_POST['upyun_enable_domain'] == 1) {
		if ($_POST['upyun_domain'] == '') {
			$buff = "请设置域名！！！";
			$zbp->Config('upyun')->upyun_enable_domain = 0;
			$zbp->SaveConfig('upyun');
		}
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
  <div class="SubMenu"><?php upyun_SubMenu(2);?></div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td><b><label for="upyun_enable_domain"><p align="center">域名绑定</p></label></b></td>
    <td><p align="left">
		<input id="upyun_enable_domain" name="upyun_enable_domain" style="display:none;" type="text" value="<?php echo $zbp->Config('upyun')->upyun_enable_domain;?>" class="checkbox">
    </p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_domain"><p align="center">域名地址</p></label></b></td>
    <td><p align="left"><input name="upyun_domain" type="text" id="upyun_domain" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_domain;?>" /></p>域名格式：http://images.rainbowsoft.org</td>
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