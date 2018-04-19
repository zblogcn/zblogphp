<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('CloudStorage')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '云存储设置';
require $blogpath.'zb_system/admin/admin_header.php';
require $blogpath.'zb_system/admin/admin_top.php';

if (isset($_POST['CS_Storage']) && $_POST['CS_Storage'] != '') {
    foreach ($_POST as $key => $value) {
        $zbp->Config('CloudStorage')->$key = $value;
    }
    $zbp->SaveConfig('CloudStorage');

    switch ($zbp->Config('CloudStorage')->CS_Storage) {
        case '1':
            if ($zbp->Config('CloudStorage')->CS_Ali_KeyID == '') {
                $buff = "请设置阿里云OSS帐号！";
            }
            break;
        case '2':
            if ($zbp->Config('CloudStorage')->CS_QNiu_KeyID == '') {
                $buff = "请设置七牛云帐号！";
            }
            break;
        case '3':
            if ($zbp->Config('CloudStorage')->CS_Baidu_KeyID == '') {
                $buff = "请设置百度云帐号！";
            }
            break;
        default:
            break;
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
  <div class="SubMenu">
  </div>
 <form method="post">
            <div id="divMain2">
              <div class="content-box"><!-- Start Content Box -->
                <div class="content-box-header">
                  <ul class="content-box-tabs">
                    <li><a href="#tab1" class="default-tab"><span>空间选择</span></a></li>
                    <li><a href="#tab2"><span>阿里云OSS</span></a></li>
                    <li><a href="#tab3"><span>七牛云存储</span></a></li>
                    <li><a href="#tab4"><span>百度云存储</span></a></li>
                  </ul>
                  <div class="clear"></div>
                </div>
                <div class="content-box-content">
<div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td class="td25"><p><b>空间选择</b></p></td><td><p>
			<select id="CS_Storage" name="CS_Storage" style="width:600px;" >
			<option value="1">阿里云OSS</option>
			<option value="2">七牛云存储</option>
			<option value="3">百度云存储</option>
			</select>
		</p></td></tr>
		<tr><td><p><b>空间Bucket</b></p></td><td><p><input id="CS_Bucket" name="CS_Bucket" style="width:600px;" type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Bucket; ?>" /></p></td></tr>
		<tr><td><p><b>存储根目录</b></p></td><td><p><input id="CS_Dir" name="CS_Dir" style="width:600px;"  type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Dir; ?>" /></p>如果是根目录请留空，子目录前面不需要加“/”,后面需要加“/”</td></tr>
		<tr><td><p><b>说明</b></p></td><td><p>1、选择相应空间<br />2、填入Bucket名称<br />3、在对于云存储空间创建Bucket，并且设置为公开可读</p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>Access Key ID</b></p></td><td><p><input id="CS_Ali_KeyID" name="CS_Ali_KeyID" style="width:600px;" type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Ali_KeyID; ?>" /></p>获取地址：http://i.aliyun.com/access_key/</td></tr>
		<tr><td><p><b>Access Key Secret</b></p></td><td><p><input id="CS_Ali_KeySecret" name="CS_Ali_KeySecret" style="width:600px;"  type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Ali_KeySecret; ?>" /></p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>AccessKey</b></p></td><td><p><input id="CS_QNiu_KeyID" name="CS_QNiu_KeyID" style="width:600px;" type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_QNiu_KeyID; ?>" /></p>获取地址：https://portal.qiniu.com/setting/key</td></tr>
		<tr><td><p><b>SecretKey</b></p></td><td><p><input id="CS_QNiu_KeySecret" name="CS_QNiu_KeySecret" style="width:600px;"  type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_QNiu_KeySecret; ?>" /></p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>Access Key</b></p></td><td><p><input id="CS_Baidu_KeyID" name="CS_Baidu_KeyID" style="width:600px;" type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Baidu_KeyID; ?>" /></p>获取地址：http://developer.baidu.com/bae/ref/key/</td></tr>
		<tr><td><p><b>Secure Key</b></p></td><td><p><input id="CS_Baidu_KeySecret" name="CS_Baidu_KeySecret" style="width:600px;"  type="text" value="<?php echo $zbp->Config('CloudStorage')->CS_Baidu_KeySecret; ?>" /></p></td></tr>
	</table>
</div>
</div>
              </div>
              <hr/>
			  <p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p>
            </div>
          </form>

</div>


<?php
require $blogpath.'zb_system/admin/admin_footer.php';
RunTime();
?>