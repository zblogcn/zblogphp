<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';

require_once 'function.php';
require_once 'api.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('alipay')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '支付宝设置';

if (count($_POST) > 0) {
    $zbp->Config('alipay')->partner = $_POST['partner'];
    $zbp->Config('alipay')->key = $_POST['key'];
    $zbp->Config('alipay')->alipayaccount = $_POST['alipayaccount'];
    $zbp->Config('alipay')->alipayname = $_POST['alipayname'];
    $zbp->Config('alipay')->transport = $_POST['transport'];
    $zbp->Config('alipay')->savelogs = $_POST['savelogs'];
    $zbp->SaveConfig('alipay');

    $zbp->SetHint('good');
    Redirect($_SERVER["HTTP_REFERER"]);
} elseif (count($_GET) > 0) {
    if ($_GET['type'] == 'pay') {
        $parameter = array(
            "out_trade_no" => time(), //订单号
            "subject"      => "订单名称",
            "total_fee"    => "200", //金额
            "body"         => "订单描述",
            "show_url"     => $zbp->host,
        );
        AlipayAPI_Start($parameter);
    } elseif ($_GET['type'] == 'login') {
        $parameter = array(
            "service"        => "alipay.auth.authorize",
            "target_service" => "user.auth.quick.login",
            "return_url"     => $bloghost . "zb_users/plugin/alipay/login_return_url.php",
        );
        AlipayAPI_Start($parameter);
    }
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
<div class="SubMenu"><?php alipay_SubMenu(1); ?></div>
  <div id="divMain2">

<form method="post" action="">
  <table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
    <tr>
      <th width='28%'>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
    <tr>
      <td><p><b>· 合作者身份（Partner）</b><br/>
          </p></td>
      <td><p>&nbsp;
            <?php zbpform::text('partner', $zbp->Config('alipay')->partner, '550px'); ?>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 安全效验码（key）</b></p></td>
      <td><p>&nbsp;
            <?php zbpform::text('key', $zbp->Config('alipay')->key, '550px'); ?>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 支付宝账号</b></p></td>
      <td><p>&nbsp;
            <?php zbpform::text('alipayaccount', $zbp->Config('alipay')->alipayaccount, '550px'); ?>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 支付宝账户名</b><span class="note"><br>&nbsp;&nbsp;必填，个人支付宝账号是真实姓名公司支付宝账号是公司名称</span></p></td>
      <td><p>&nbsp;
            <?php zbpform::text('alipayname', $zbp->Config('alipay')->alipayname, '550px'); ?>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 访问模式</b><span class="note"><br>&nbsp;&nbsp;根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http</span></p></td>
      <td><p>&nbsp;
            <?php zbpform::select('transport', array('http'=>'http', 'https'=>'https'), $zbp->Config('alipay')->transport); ?>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 是否记录日志</b><span class="note"><br>&nbsp;&nbsp;保存位置：zb_users/logs</span></p></td>
      <td><p>&nbsp;
            <?php zbpform::select('savelogs', array('1'=>'是', '0'=>'否'), $zbp->Config('alipay')->savelogs); ?>
        </p></td>
    </tr>
  </table>
  <p><br/>
    <input type="submit" class="button" value="提交" id="btnPost" />
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
