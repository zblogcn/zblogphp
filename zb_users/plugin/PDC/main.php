<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('PDC')) {$zbp->ShowError(48);die();}

$blogtitle='PHP数据中心';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div id="divMain2">
<div class="divHeader2">数据导入</div>
<?php

?>
<div class="divHeader2">数据导出</div>
<?php

?>
<br/>
<p><button type="button" onclick="location.href='output/sql.php'">SQL导出(本程序只导出系统自带的表)</button></p>
<br/>
<p><button type="button">WXR(WordPress eXtended Rss)导出</button></p>
	<script type="text/javascript">ActiveLeftMenu("aPDC");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/PDC/logo.png';?>");</script>	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>