<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('api')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'API-说明';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div id='bg' style="background-image:url('bg.png');opacity:0.05;width:512px;height:512px;z-index: -999;position:absolute;left:30%;"></div>
  <div id="divMain2">
    <p>&nbsp;</p>
    <p>API接口列表及说明</p>
    <p>&nbsp;</p>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/api/logo.png'; ?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
