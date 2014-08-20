<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('static')) {$zbp->ShowError(48);die();}
$blogtitle='静态化插件';
if (isset($_GET['a'])) {
	$articles = $zbp->GetPostList();
	foreach ($articles as $key => $value) {
		echo $value->ID;
		static_post_build($value);
	}
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
<a href="?a=b">重建</a>

  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>