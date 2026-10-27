<?php
// 旧后台入口：本插件只提供 admin-next 编辑器，旧后台无需页面
require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->CheckRights('admin')) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

$blogtitle = '区块编辑器';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader">区块编辑器</div>
  <div id="divMain2">
    <p>本插件为新版后台（admin-next）提供写作编辑器，无需单独设置。</p>
    <p>启用后在「新建文章 / 编辑文章」页面自动加载。</p>
    <p>如需回到旧编辑器，请停用本插件。</p>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
