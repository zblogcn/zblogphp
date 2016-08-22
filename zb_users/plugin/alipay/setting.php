<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';

require_once 'function.php';
require_once 'api.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('alipay')) {$zbp->ShowError(48);die();}

$blogtitle = '支付宝设置';

if (count($_POST) > 0) {
    $zbp->Config('alipay')->partner = $_POST['partner'];
    $zbp->Config('alipay')->key = $_POST['key'];
    $zbp->Config('alipay')->alipayaccount = $_POST['alipayaccount'];
    $zbp->Config('alipay')->payforname = $_POST['payforname'];
    $zbp->Config('alipay')->notify_add = $_POST['notify_add'];
    $zbp->SaveConfig('alipay');

    $zbp->SetHint('good');
    Redirect($_SERVER["HTTP_REFERER"]);
} elseif (count($_GET) > 0) {
    if ($_GET['type'] == 'pay') {
        $parameter = array(
            "out_trade_no" => time(), //订单号
            "subject" => "订单名称",
            "total_fee" => "200", //金额
            "royalty_type" => "10", //提成类型
            "royalty_parameters" => "rainbowsoft@gmail.com^16.8^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243|zsxsoft@outlook.com^8^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243|13586632603^8^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243|huangdi311@sina.com^8^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243|hicaptain@163.com^4.39^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243|laofei369460966@126.com^150^Z-Blog应用中心[全站缓存(静态化)插件]分润---20151028100230141243",
            "body" => "订单描述",
            "show_url" => "http://www.xxx.com/myorder.html",
        );
        AlipayAPI_Start($parameter);
    } elseif ($_GET['type'] == 'login') {
        $parameter = array(
            "service" => "alipay.auth.authorize",
            "target_service" => "user.auth.quick.login",
            "return_url" => $bloghost . "zb_users/plugin/alipay/login_return_url.php",
        );
        AlipayAPI_Start($parameter);
    }
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
    <a href="?type=pay" target="_blank"><input type="button" class="button" value="交易测试" /></a>
	 <a href="?type=login" target="_blank"><input type="button" class="button" value="登陆测试" /></a>
  </p>
  <p>&nbsp;</p>
</form>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>