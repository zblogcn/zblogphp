<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('RegPage')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '注册组件';

if (count($_POST) > 0) {
    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    }
    if (GetVars('reset', 'POST') == 'add') {
        RegPage_CreateCode(500);
    }

    if (GetVars('reset', 'POST') == 'del') {
        RegPage_DelUsedCode();
    }

    if (GetVars('reset', 'POST') == 'ept') {
        RegPage_EmptyCode();
    }

    $zbp->SetHint('good');
    Redirect('./main_code.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
<div class="SubMenu">
 <a href="main.php"><span class="m-left">主设置页</span></a>
 <a href="main_code.php"><span class="m-left m-now">邀请码管理</span></a>
</div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="#">
<?php if (function_exists('CheckIsRefererValid')) {
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
}?>


<input id="reset" name="reset" type="hidden" value="" />
      <hr/>
      <p>
        <input type="submit" class="button" onclick="$('#reset').val('add');" value="生成500个邀请码" />

        <input type="submit" class="button" onclick="$('#reset').val('del');" value="删除已使用过的邀请码" />
        
        <input type="submit" class="button" onclick="$('#reset').val('ept');" value="清空所有邀请码" />
      </p>
      <hr/>
<table border="1" class="tableFull tableBorder">
<tr>
    <th class="td10"></th>
    <th >邀请码</th>
    <th >用户级别(组)</th>
    <th >注册用户</th>
</tr>
<?php
$sql = $zbp->db->sql->Select($RegPage_Table, '*', null, null, null, null);
$array = $zbp->GetListCustom($RegPage_Table, $RegPage_DataInfo, $sql);
foreach ($array as $key => $reg) {
    echo '<tr>';
    echo '<td class="td15">' . $reg->ID . '</td>';
    echo '<td>' . $reg->InviteCode . '</td>';
    echo '<td class="td20">' . $zbp->lang['user_level_name'][$reg->Level] . '</td>';
    echo '<td class="td20">' . ($reg->AuthorID == 0 ? '' : $zbp->GetMemberByID($reg->AuthorID)->Name) . '</td>';
    echo '</tr>';
}
?>
</table>




    </form>
    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/RegPage/logo.png'; ?>");</script>  
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
