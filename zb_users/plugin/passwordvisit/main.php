<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('passwordvisit')) {$zbp->ShowError(48);die();}

$blogtitle = '密码访问';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if(isset($_POST['all_encrypt']) && $_POST['all_encrypt'] != ''){
	$zbp->Config('passwordvisit')->all_encrypt = $_POST['all_encrypt'];
	$zbp->Config('passwordvisit')->default_password = $_POST['default_password'];
	$zbp->Config('passwordvisit')->default_text = $_POST['default_text'];
	$zbp->SaveConfig('passwordvisit');
	$zbp->ShowHint('good', '保存成功！');
}

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>

  <tr>
    <td><b><label for="all_encrypt"><p align="center">全站加密</p></label></b></td>
    <td><p align="left"><input id="all_encrypt" name="all_encrypt" style="display:none;" type="text" value="<?php echo $zbp->Config('passwordvisit')->all_encrypt;?>" class="checkbox"><br>启用后访问全站均需要输入密码</p></td>
  </tr>
  <tr>
    <td><b><label for="default_password"><p align="center">默认密码</p></label></b></td>
    <td><p align="left"><input name="default_password" type="text" id="default_password" size="100%" value="<?php echo $zbp->Config('passwordvisit')->default_password;?>" /></p></td>
  </tr>
  <tr>
    <td><b><label for="default_text"><p align="center">默认提示内容</p></label></b></td>
   <td><p align="left"><textarea name="default_text" type="text" id="default_text" style="width: 80%;"><?php echo $zbp->Config('passwordvisit')->default_text;?></textarea><br>支持输入HTML代码,可以根据主题自定义CSS</p></td>
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