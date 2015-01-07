<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require dirname(__FILE__) . '/function.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

if(!$zbp->Config('AppCentre')->shop_username||!$zbp->Config('AppCentre')->shop_password){
	$blogtitle='应用中心-登录应用商城';
}else{
	$blogtitle='应用中心-我的应用仓库';
}


if(GetVars('act')=='shoplogin'){

	$s=Server_Open('shopvaild');

	if($s){

		$zbp->Config('AppCentre')->shop_username=GetVars("shop_username");
		$zbp->Config('AppCentre')->shop_password=$s;
		$zbp->SaveConfig('AppCentre');

		$zbp->SetHint('good','您已成功登录"应用中心"商城.');
		Redirect('./client.php');
		die;
	}else{
		$zbp->SetHint('bad','购买者账户名或密码错误.');
		Redirect('./client.php');
		die;
	}
	
}

if(GetVars('act')=='shoplogout'){
	$zbp->Config('AppCentre')->shop_username='';
	$zbp->Config('AppCentre')->shop_password='';
	$zbp->SaveConfig('AppCentre');
	$zbp->SetHint('good','您已退出"应用中心"商城.');
	Redirect('./main.php');
	die;
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(9);?></div>
  <div id="divMain2">
<?php if(!$zbp->Config('AppCentre')->shop_username||!$zbp->Config('AppCentre')->shop_password){ ?>
            <form action="?act=shoplogin" method="post">
              <table style="line-height:3em;" width="100%" border="0">
                <tr height="32">
                  <th  align="center">请填写您在"<a href="http://app.rainbowsoft.org/?shop&amp;type=account" target="_blank">应用中心</a>"的购买者账号(Email)和密码
                    </th>
                </tr>
                <tr height="32">
                  <td align="center">&nbsp;&nbsp;账号:
                    <input type="text" name="shop_username" value="" style="width:35%"/></td>
                </tr>
                <tr height="32">
                  <td align="center">&nbsp;&nbsp;密码:
                    <input type="password" name="shop_password" value="" style="width:35%" /></td>
                </tr>
                <tr height="32" align="center">
                  <td align="center"><input type="submit" value="登录" class="button" /></td>
                </tr>
              </table>
            </form>
<?php }else{ 

//已登录
Server_Open('shoplist');

      }?>



	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>