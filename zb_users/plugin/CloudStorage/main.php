<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('CloudStorage')) {$zbp->ShowError(48);die();}

$blogtitle='云存储设置';


require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
 <form method="post" action="../cmd.php?act=SettingSav">
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
                <!-- End .content-box-header -->

                <div class="content-box-content">
<div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td class="td25"><p><b>空间选择</b></p></td><td><p>
			<select id="ZC_TIME_ZONE_NAME" name="ZC_TIME_ZONE_NAME" style="width:600px;" >
			<option value="Kwajalein">阿里云OSS</option>
			<option value="Pacific/Midway">七牛云存储</option>
			<option value="Pacific/Honolulu">百度云存储</option>
			</select>
		</p></td></tr>
		<tr><td><p><b>空间Bucket</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="imzhou" /></p></td></tr>
		<tr><td><p><b>存储根目录</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="file" /></p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab2">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>Access Key ID</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="" /></p>http://i.aliyun.com/access_key/</td></tr>
		<tr><td><p><b>Access Key Secret</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="" /></p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab3">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>AccessKey</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="" /></p>https://portal.qiniu.com/setting/key</td></tr>
		<tr><td><p><b>SecretKey</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="" /></p></td></tr>
	</table>
</div>
<div class="tab-content" style="border:none;padding:0px;margin:0;" id="tab4">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>Access Key</b></p></td><td><p><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" style="width:600px;" type="text" value="" /></p>http://developer.baidu.com/bae/ref/key/</td></tr>
		<tr><td><p><b>Secure Key</b></p></td><td><p><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" style="width:600px;"  type="text" value="" /></p></td></tr>
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
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>