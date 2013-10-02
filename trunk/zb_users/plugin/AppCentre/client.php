<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'function.php';
include "shop.class.lib.php";
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心-商城';

if(GetVars('act')=='shoplogin'){
	$pw = md5($_POST["shoppassword"]);
	AppCentre_Shop::init($_POST["shopemail"], $pw);
	$userinfo = AppCentre_Shop::userinfo();
	if($userinfo){
		$zbp->Config('AppCentre')->shopemail = $_POST["shopemail"];
		$zbp->Config('AppCentre')->shoppassword = $pw;
		$zbp->SaveConfig('AppCentre');
		//print_r($userinfo);
	}else{
		$zbp->Config('AppCentre')->shopemail = '';
		$zbp->Config('AppCentre')->shoppassword = '';
		$zbp->SaveConfig('AppCentre');
		$zbp->SetHint('bad','帐号或者密码错误.');
		Redirect('./client.php');
		die;
	}
}

if(GetVars('act')=='shoplogout'){
	$zbp->Config('AppCentre')->shopemail='';
	$zbp->Config('AppCentre')->shoppassword='';
	$zbp->SaveConfig('AppCentre');
	$zbp->SetHint('good','您已退出APP应用中心商城.');
	Redirect('./client.php');
	die;
}

if(!$zbp->Config('AppCentre')->shopemail){
	AppCentre_Shop::init($zbp->Config('AppCentre')->shopemail, $zbp->Config('AppCentre')->shoppassword);
	$userinfo = AppCentre_Shop::userinfo();
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(9);?></div>
  <div id="divMain2">
<?php if(!$zbp->Config('AppCentre')->shopemail){ ?>
            <form action="?act=shoplogin" method="post">
              <table style="line-height:3em;" width="100%" border="0">
                <tr height="32">
                  <th  align="center">请填写您在"<a href="http://app.rainbowsoft.org/?shop&type=account" target="_blank">APP应用中心</a>"的Email和密码
                    </td>
                </tr>
                <tr height="32">
                  <td align="center">Email:
                    <input type="text" name="shopemail" value="" style="width:35%"/></td>
                </tr>
                <tr height="32">
                  <td align="center">&nbsp;&nbsp;密码:
                    <input type="password" name="shoppassword" value="" style="width:35%" /></td>
                </tr>
                <tr height="32" align="center">
                  <td align="center"><input type="submit" value="登陆" class="button" /></td>
                </tr>
              </table>
            </form>
<?php }else{ 
	AppCentre_Shop::init($zbp->Config('AppCentre')->shopemail, $zbp->Config('AppCentre')->shoppassword);
	$userinfo = AppCentre_Shop::userinfo();
	$orderlist = AppCentre_Shop::orderlist();
?>
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
		<tbody> 
		<tr class="color1"> 
			<th class="td5 tdCenter">编号</th> 
			<th class="td15 tdCenter">订单号</th> 
			<th class="td10 tdCenter">应用ID</th> 
			<th class="td15 tdCenter">应用名称</th> 
			<th class="td5">金额</th> 
			<th class="td15 tdCenter">购买时间</th>
		</tr> 
		<?php
		if(count($orderlist['data']) > 0){
			foreach($orderlist['data'] as $k=>$v){
				echo '<tr class="color3">';
				echo '<td>'. ($k+1) ."</td>\n\r";
				echo '<td>'. $v['tradenum'] ."</td>\n\r";
				echo '<td>'. $v['appid'] ."</td>\n\r";
				echo '<td>'. $v['title'] ."</td>\n\r";
				echo '<td>￥'. $v['price'] ."</td>\n\r";
				echo '<td>'. date("Y-m-d H:i:s", $v['paytime']). "</tr>\n\r";
			}
		}
		?>
		</tbody>
	</table>
            <form action="?act=shoplogout" method="post">
              <p><b><?php echo $userinfo['data']['alipayname']; ?></b> 您好,您已经在当前应用中心客户端登录Z-BlogPHP官方应用商城.</p>
              <p>
                <input name="submit" type="submit" value="退出登录" class="button" />
              </p>
            </form>
<?php }?>



	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>