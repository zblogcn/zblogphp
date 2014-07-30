<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'function.php';

$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('wp2zbp')) {$zbp->ShowError(48);die();}

$blogtitle='WordPress数据转移插件';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>

<div id="divMain">
  <div class="divHeader2"><?php echo $blogtitle;?></div>
  <div class="SubMenu"> </div>
  <div id="divMain2">
<?php

$prefix = GetVars('prefix', 'GET');

//convert_article_table($prefix);

//convert_comment_table($prefix);


convert_attachment_table($prefix);
?>

	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/wp2zbp/logo.png';?>");</script>		
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
