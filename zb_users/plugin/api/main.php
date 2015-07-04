<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->
Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('api')) {$zbp->ShowError(48);die();}

$blogtitle = 'api';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
	<div class="divHeader">
		<?php echo $blogtitle;?></div>
	<div class="SubMenu"></div>
	<div id="divMain2">
	<?php API::$User->user = $zbp->user; ?>
	请将如下信息填写入你的API客户端。<br/>
	API Key: <?php echo API::$User->key;?><br/>
	API Secret: <?php echo API::$User->secret;?><br/>
	</div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>