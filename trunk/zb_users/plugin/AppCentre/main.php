<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require 'function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心';

if(count($_POST)>0){

	$zbp->SetHint('good');
	Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(GetVars('method','GET')=='check'?2:1);?></div>
  <div id="divMain2" class="edit category_edit">

<?php
$method=GetVars('method','GET');
if(!$method)$method='view';
Server_Open($method);
?>

	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>

<?php if($zbp->Config('AppCentre')->username<>""){?>
<script type='text/javascript'>$('div.footer_nav p').html('&nbsp;&nbsp;&nbsp;<b><?php echo $zbp->Config('AppCentre')->username;?></b>您好,欢迎来到APP应用中心!<a href=\'setting.php?act=logout\'>[退出登录]</a>').css('visibility','inherit');</script>
<?php } ?>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>