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

function trim_fist_str($str){
	if ($str[0] == '/') {
		$str = substr($str, 1,1);
		trim_fist_str($str);
	}else{
		return $str;
	}
}
if (isset($_POST['upyun_storagetype']) && $_POST['upyun_storagetype'] != '') {
	$zbp->Config('upyun')->upyun_storagetype = $_POST['upyun_storagetype'];
	$zbp->Config('upyun')->upyun_bucket = $_POST['upyun_bucket'];
	$zbp->Config('upyun')->upyun_dir = $_POST['upyun_dir'];
	$zbp->SaveConfig('upyun');

	$dir = $zbp->Config('upyun')->upyun_dir;
	//$dir = trim_fist_str($dir);

	if ($dir{strlen($dir)-1} != '/') {
		$zbp->Config('upyun')->upyun_dir = $dir.'/';
	}
	$zbp->SaveConfig('upyun');

	if ($zbp->Config('upyun')->upyun_bucket == '') {
		$buff = "请设置空间名！！！";
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
  <div class="SubMenu"><?php upyun_SubMenu(0);?></div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td><b><label for="upyun_storagetype"><p align="center">空间类型</p></label></b></td>
    <td><p align="left">
			<select id="upyun_storagetype" name="upyun_storagetype" style="width:600px;" >
			<option value="1"<?php echo ($zbp->Config('upyun')->upyun_storagetype == '1') ? ' selected="selected"' : '' ;?>>文件空间</option>
			<option value="2"<?php echo ($zbp->Config('upyun')->upyun_storagetype == '2') ? ' selected="selected"' : '' ;?>>图片空间</option>
			</select>
    </p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_bucket"><p align="center">空间名称</p></label></b></td>
    <td><p align="left"><input name="upyun_bucket" type="text" id="upyun_bucket" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_bucket;?>" /></p>附件访问地址格式为：http://<?php echo $zbp->Config('upyun')->upyun_bucket;?>.b0.upaiyun.com/<?php echo $zbp->Config('upyun')->upyun_dir;?></td>
  </tr>
  <tr>
    <td><b><label for="upyun_dir"><p align="center">存储目录</p></label></b></td>
    <td><p align="left"><input name="upyun_dir" type="text" id="upyun_dir" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_dir;?>" /></p></td>
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