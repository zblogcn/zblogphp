<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'zbpdk_include.php';

$zbp->Load();
$zbpdk = new zbpdk_t();
$zbpdk->scan_extensions();
//var_dump($zbpdk->objects);

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('ZBPDK')) {
    $zbp->ShowError(48);
    die();
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('main'); ?></div>
  <div id="divMain2">
    <p>ZBPDK，全称Z-Blog PHP Development Kit，是为Z-BlogPHP开发人员开发的一套工具包。它集合了许多开发中常用的工具，可以帮助开发者更好地进行开发。</p>
    <p>该插件有一定的危险性，一旦进行了误操作可能导致博客崩溃，请谨慎使用。</p>
    <p>&nbsp;</p>
    <p>工具列表：</p>
    <p>&nbsp;</p>
    <table width="100%">
      <tr height="40">
        <td width="50">ID</td>
        <td width="120">工具名</td>
        <td>信息</td>
      </tr>
        <?php
        foreach ($zbpdk->objects as $k => $v) {
            echo '<tr height="40">';
            echo '<td>' . ($k + 1) . '</td>';
            echo '<td>' . "<a href=\"extensions/$v->id/$v->url\" target=\"_blank\">$v->id</a>" . '</td>';
            echo '<td>' . $v->description . '</td>';
            echo '</tr>';
        }
?>
    </table>
  </div>
</div>
<script>ActiveTopMenu('zbpdk');</script>
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png'; ?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
