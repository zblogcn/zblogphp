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

$zbp->SetHint('good');
Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php alipay_SubMenus(6);?></div>
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
          <input id="app_id" name="app_id" style="width:550px;"  type="text" value="" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 安全效验码</b></p></td>
      <td><p>&nbsp;
          <input id="app_name" name="app_name" style="width:550px;"  type="text" value="" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 支付宝账号</b></p></td>
      <td><p>&nbsp;
          <input id="app_url" name="app_url" style="width:550px;"  type="text" value="" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 收款名称</b><span class="note">&nbsp;&nbsp;显示在支付宝支付页面.</span></p></td>
      <td><p>&nbsp;
          <input id="app_note" name="app_note" style="width:550px;"  type="text" value="" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 交易完成后跳转地址</b></p></td>
      <td><p>&nbsp;
          <input id="app_note" name="app_note" style="width:550px;"  type="text" value="" />
        </p></td>
    </tr>
  </table>
  <p><br/>
    <input type="submit" class="button" value="提交" id="btnPost" onclick='' />
  </p>
  <p>&nbsp;</p>
</form>
	<script type="text/javascript">ActiveLeftMenu("aalipay");</script>
	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>