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

if (isset($_POST)) {
    foreach ($_POST as $key => $value) {
        $zbp->Config('upyun')->$key = $value;
    }
    $zbp->SaveConfig('upyun');
}
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php upyun_SubMenu(0); ?></div>
  <div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td><b><label for="upyun_bucket"><p align="center">空间名称</p></label></b></td>
    <td><p align="left"><?php zbpform::text('upyun_bucket', $zbp->Config('upyun')->upyun_bucket, '500px'); ?></p><p>附件访问地址格式为：http://<?php echo $zbp->Config('upyun')->upyun_bucket; ?>.b0.upaiyun.com/<?php echo $zbp->Config('upyun')->upyun_dir; ?></p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_operator_name"><p align="center">操作员帐号</p></label></b></td>
    <td><p align="left"><?php zbpform::text('upyun_operator_name', $zbp->Config('upyun')->upyun_operator_name, '500px'); ?></p><p>获取地址：<a href="https://console.upyun.com/account/operators/" target="_black">https://console.upyun.com/account/operators/</a></p></td>
  </tr>
  <tr>
    <td><b><label for="upyun_operator_password"><p align="center">操作员密码</p></label></b></td>
    <td><p align="left"><?php zbpform::text('upyun_operator_password', $zbp->Config('upyun')->upyun_operator_password, '500px'); ?></p></td>
  </tr>
    <tr>
    <td><b><label for="upyun_enable_domain"><p align="center">域名绑定</p></label></b></td>
    <td><p align="left"><?php zbpform::zbradio('upyun_enable_domain', $zbp->Config('upyun')->upyun_enable_domain . '" onChange="show_domain()'); zbpform::text('upyun_domain', $zbp->Config('upyun')->upyun_domain, '450px'); ?><p>不需要以&nbsp;/&nbsp;结尾</p>
    </p></td>
  </tr>
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>
    </form>
  </div>
</div>
<script>
function show_domain(){var hello = document.getElementById("upyun_enable_domain").value;var input_domain = document.getElementById('upyun_domain');if(hello==1){input_domain.style.display="";}else{input_domain.style.display="none";}}show_domain();
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>