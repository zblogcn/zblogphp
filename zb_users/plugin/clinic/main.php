<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('clinic')) {
    $zbp->ShowError(48);
    die();
}
require 'clinic.php';
$module = GetVars('module', 'GET');
$module = (isset($clinic->modules[$module]) ? $clinic->modules[$module] : null);
$blogtitle = 'Z-BlogPHP诊断工具' . ($module ? ' - ' . $module['name'] : '');

require $blogpath . 'zb_system/admin/admin_header.php';
echo '<style type="text/css">tr{height: 32px}</style><script type="text/javascript" src="include/bluebird.min.js"></script>';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
<?php

if ($module) {
    require 'include/gui_module.inc';
} else {
    require 'include/gui_main.inc';
}
?>

  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
