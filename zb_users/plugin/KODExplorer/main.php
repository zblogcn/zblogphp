<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('KODExplorer')) {$zbp->ShowError(48);die();}
$blogtitle='KODExplorer';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
<iframe id="mainIframe" onload="this.height=this.contentWindow.document.documentElement.scrollHeight"  name="mainIframe" src="index.php" frameborder="0" scrolling="auto"></iframe>

  </div>
</div>
<style type="text/css">
#mainIframe{width:100%;height:800px;}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>