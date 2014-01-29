<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);exit();}
if (!$zbp->CheckPlugin('duoshuo')) {$zbp->ShowError(48);exit();}
$blogtitle='多说社会化评论';
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<style type="text/css">
tr {height: 32px;}
#divMain2 ul li {margin-top: 6px;margin-bottom: 6px}
.bold {font-weight: bold;}
.note {margin-left: 10px}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
$duoshuo->init();
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php echo $duoshuo->export_submenu(GetVars("act","GET"));?></div>
  <div id="divMain2">
<?php
if ($zbp->config('duoshuo')->short_name=="")
{
	echo '<iframe id="duoshuo-remote-window" src="' . $duoshuo->export_connect_url() . '" style="border:0; width:100%; height:580px;"></iframe>';
}
else
{
	switch(GetVars("act","GET"))
	{
		case "settings":
		case "users":
		case "statistics":
			echo '<iframe id="duoshuo-remote-window" src="' . $duoshuo->export_admin(GetVars("act","GET")) . '" style="border:0; width:100%; height:580px;"></iframe>';
		break;
		case "setting":
			require '_setting.inc';
		break;
		default:
			echo '<iframe id="duoshuo-remote-window" src="' . $duoshuo->export_admin(GetVars("act","GET")) . '" style="border:0; width:100%; height:580px;"></iframe>';
		break;
	}
}
?>
    <script type="text/javascript">ActiveLeftMenu("a<?php echo !isset($_GET['act'])?'Comment':'Plugin' ?>Mng");</script> 
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/duoshuo/logo.png';?>");</script> 
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
