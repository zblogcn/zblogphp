<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('upyun')) {
    $zbp->ShowError(48);
    die();
}
$blogtitle = '又拍云存储';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

// function trim_fist_str($str){
// 	if ($str[0] == '/') {
// 		$str = substr($str, 1,1);
// 		trim_fist_str($str);
// 	}else{
// 		return $str;
// 	}
// }
if (isset($_POST['upyun_enable_thumbnail']) && $_POST['upyun_enable_thumbnail'] != '') {
    $zbp->Config('upyun')->upyun_enable_thumbnail = $_POST['upyun_enable_thumbnail'];
    $zbp->Config('upyun')->upyun_cutname = $_POST['upyun_cutname'];
    $zbp->Config('upyun')->upyun_ver_name = $_POST['upyun_ver_name'];
    $zbp->SaveConfig('upyun');

    if ($zbp->Config('upyun')->upyun_ver_name == '' && $zbp->Config('upyun')->upyun_enable_thumbnail == 1) {
        $buff = "请设置版本名！！！";
    }

    if (isset($buff)) {
        $zbp->ShowHint('bad', $buff);
    } else {
        $zbp->ShowHint('good', "保存成功！");
    }
}
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php upyun_SubMenu(1); ?></div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td><b><label for="upyun_enable_thumbnail"><p align="center">启用略所图</p></label></b></td>
    <td><p align="left">
		<input id="upyun_enable_thumbnail" name="upyun_enable_thumbnail" style="display:none;" type="text" value="<?php echo $zbp->Config('upyun')->upyun_enable_thumbnail; ?>" class="checkbox">
    </p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_cutname"><p align="center">间隔标识符</p></label></b></td>
    <td><p align="left">
			<select id="upyun_cutname" name="upyun_cutname" style="width:600px;" >
			<option value="!"<?php echo ($zbp->Config('upyun')->upyun_cutname == '!') ? ' selected="selected"' : ''; ?>>（！）感叹号</option>
			<option value="-"<?php echo ($zbp->Config('upyun')->upyun_cutname == '-') ? ' selected="selected"' : ''; ?>>（-）中划线</option>
			<option value="_"<?php echo ($zbp->Config('upyun')->upyun_cutname == '_') ? ' selected="selected"' : ''; ?>>（_）下划线</option>


			</select>
    </p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_ver_name"><p align="center">自定义版本名称</p></label></b></td>
    <td><p align="left"><input name="upyun_ver_name" type="text" id="upyun_ver_name" size="100%" value="<?php echo $zbp->Config('upyun')->upyun_ver_name; ?>" /></p>请在又拍云创建相应的版本名称：https://console.upyun.com/services/<?php echo $zbp->Config('upyun')->upyun_bucket; ?>/thumb/</td>
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