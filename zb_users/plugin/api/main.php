<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('api')) {$zbp->ShowError(48);die();}

$blogtitle = 'api';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
<?php
include 'api.php';
//API::$Route::$debug = true;
API::$Route::get('/hello/world', function () {
	var_dump('a');
});
API::$Route::use ('/hello', function () {
	var_dump('Hello2');
});
API::$Route::post('/hello', function () {
	var_dump('Hello2');
});
API::$Route::checkPath('GET', '/hello/world');
?>
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>