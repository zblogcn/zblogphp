<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('Howl')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'Z-Blog角色分配器';

$group_nums = count($zbp->lang['user_level_name']);
$group_key = array();
foreach ($zbp->lang['user_level_name'] as $key => $value) {
    $group_key[] = $key;
}

if (count($_POST) > 0) {
    if (GetVars('reset', 'POST') == '1') {
        $zbp->DelConfig('Howl');
        $zbp->SetHint('good', '已删除所有的配置!');
        Redirect('./main.php');
        die();
    }

    $a = array();
    foreach ($group_key as $key) {
        $a[$key] = array();
    }

    foreach ($group_key as $groupkey) {
        foreach ($actions as $key => $value) {
            $check = GetVars('Group' . $groupkey . '_' . $key, 'POST');
            $a[$groupkey][$key] = (int) $check;
        }
    }

    foreach ($group_key as $groupkey) {
        $name = 'Group' . $groupkey;
        $zbp->Config('Howl')->$name = $a[$groupkey];
    }

    $zbp->SaveConfig('Howl');

    $zbp->SetHint('good');
    Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu" style="display: block;"><a href="main.php"><span class="m-left m-now">系统群组设置</span></a><a href="user.php"><span class="m-left">单独用户设置</span></a></div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
    <th class="td10">权限</th>
<?php
foreach ($group_key as $key) {
    echo '<th class="td10">';
    echo $zbp->lang['user_level_name'][$key];
    echo "组</th>\r\n";
}
?>
</tr>
<?php
function MakeInput($group, $key)
{
    global $zbp;
    $zbp->user->Level = $group;
    $check = (int) $zbp->CheckRights($key);

    return '<input name="Group' . $group . '_' . $key . '" style="" type="text" value="' . $check . '" class="checkbox"/>';
}

foreach ($actions as $key => $value) {
    ?>

<tr>
<td class="tdCenter"><?php echo $key?>(<b><?php echo Howl_GetRightName($key); ?></b>)</td>
<?php
foreach ($group_key as $groupkey) {
        echo '<td class="tdCenter">';
        echo MakeInput($groupkey, $key);
        echo "</td>\r\n";
    } ?>
</tr>
<?php
}

?>
</table>
      <hr/>
      <p>
        <input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" class="button" value="恢复系统默认配置" onclick="$('#reset').val(1);" />
      </p>
      <hr/>
      <p>
本插件配置不当可能会造成网站被黑等严重后果，请慎用！
      </p>
    </form>
    <script type="text/javascript">

    </script>
    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Howl/logo.png'; ?>");</script> 
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
