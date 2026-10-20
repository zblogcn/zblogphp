<?php
// 旧后台入口：未声明 admin-next 的版本或回退时使用
require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->CheckRights('admin')) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$blogtitle = '后台面板示例';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader">后台面板示例</div>
  <div id="divMain2">
    <p>该插件已提供 admin-next 前端页面，请在新版后台使用。</p>
    <p>当前为旧版后台，仅用于回退。</p>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
