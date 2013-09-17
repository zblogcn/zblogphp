<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

require 'function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('alipay')) {$zbp->ShowError(48);die();}

$blogtitle='支付宝设置';

if(count($_POST)>0){
	$zbp->Config('alipay')->partner = $_POST['partner'];
	$zbp->Config('alipay')->key = $_POST['key'];
	$zbp->Config('alipay')->alipayaccount = $_POST['alipayaccount'];	
	$zbp->Config('alipay')->payforname = $_POST['payforname'];	
	$zbp->Config('alipay')->notify_add = $_POST['notify_add'];	
	$zbp->SaveConfig('alipay');
	
	$zbp->SetHint('good');
	Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php alipay_SubMenu(1);?></div>
  <div id="divMain2">

<form method="post" action="">
  <table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
    <tr>
      <th width='28%'>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
    <tr>
      <td><p><b>· 合作者身份</b><br/>
          </p></td>
      <td><p>&nbsp;
          <input id="partner" name="partner" style="width:550px;"  type="text" value="<?php echo $zbp->Config('alipay')->partner;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 安全效验码</b></p></td>
      <td><p>&nbsp;
          <input id="key" name="key" style="width:550px;"  type="text" value="<?php echo $zbp->Config('alipay')->key;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 支付宝账号</b></p></td>
      <td><p>&nbsp;
          <input id="alipayaccount" name="alipayaccount" style="width:550px;"  type="text" value="<?php echo $zbp->Config('alipay')->alipayaccount;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 收款名称</b><span class="note">&nbsp;&nbsp;显示在支付宝支付页面.</span></p></td>
      <td><p>&nbsp;
          <input id="payforname" name="payforname" style="width:550px;"  type="text" value="<?php echo $zbp->Config('alipay')->payforname;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 交易完成后跳转地址</b></p></td>
      <td><p>&nbsp;
          <input id="notify_add" name="notify_add" style="width:550px;"  type="text" value="<?php echo $zbp->Config('alipay')->notify_add;?>" />
        </p></td>
    </tr>
  </table>
  <p><br/>
    <input type="submit" class="button" value="提交" id="btnPost" onclick='' />
    <a href="api.php" target="_blank"><input type="button" class="button" value="交易测试" /></a>
	 <a href="auth/api.php" target="_blank"><input type="button" class="button" value="登陆测试" /></a>
  </p>
  <p>&nbsp;</p>
</form>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>