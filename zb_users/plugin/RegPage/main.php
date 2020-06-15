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
    $zbp->Config('RegPage')->open_reg = (int) $_POST['open_reg'];
    $zbp->Config('RegPage')->default_level = (int) $_POST['default_level'];
    $zbp->Config('RegPage')->readme_text = $_POST['readme_text'];
    $zbp->Config('RegPage')->title_text = $_POST['title_text'];
    $zbp->Config('RegPage')->loginpage_addon = (int) $_POST['loginpage_addon'];
    $zbp->Config('RegPage')->loginpage_text = $_POST['loginpage_text'];
    $zbp->Config('RegPage')->only_one_ip = (int) $_POST['only_one_ip'];
    $zbp->Config('RegPage')->disable_website = $_POST['disable_website'];
    $zbp->Config('RegPage')->disable_validcode = $_POST['disable_validcode'];
    $zbp->Config('RegPage')->rewrite_url = trim($_POST['rewrite_url']);
    $zbp->SaveConfig('RegPage');

    if (GetVars('addnavbar')) {
        $zbp->AddItemToNavbar('item', 'regpage', $zbp->Config('RegPage')->title_text, $zbp->host . '?reg');
    } else {
        $zbp->DelItemToNavbar('item', 'regpage');
    }

    $zbp->SetHint('good');
    Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
<div class="SubMenu">
 <a href="main.php"><span class="m-left m-now">主设置页</span></a>
 <a href="main_code.php"><span class="m-left ">邀请码管理</span></a>
</div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="#">
        <?php if (function_exists('CheckIsRefererValid')) {
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
}?>
        <input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder">
<tr>
    <th class="td30"><p align='left'><b>选项</b><br><span class='note'></span></p></th>
    <th>
    </th>
</tr>
<tr>
    <td class="td30"><p align='left'><b>默认注册的会员等级</b></p></td>
    <td>
    <select name="default_level" style="width:200px;">
        <option value='5' <?php if ($zbp->Config('RegPage')->default_level == 5) {
    echo 'selected="selected"';
}?>><?php echo $zbp->lang['user_level_name'][5]; ?></option>
        <option value='4' <?php if ($zbp->Config('RegPage')->default_level == 4) {
    echo 'selected="selected"';
}?>><?php echo $zbp->lang['user_level_name'][4]; ?></option>
        <option value='3' <?php if ($zbp->Config('RegPage')->default_level == 3) {
    echo 'selected="selected"';
}?>><?php echo $zbp->lang['user_level_name'][3]; ?></option>
    </select>
    </td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>开放注册</b></p></td>
    <td><input type="text" class="checkbox" name="open_reg" value="<?php echo $zbp->Config('RegPage')->open_reg; ?>" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>将注册链接加入导航栏</b></p></td>
    <td><input type="checkbox" name="addnavbar" value="ok" <?php if ($zbp->CheckItemToNavbar('item', 'regpage')) {
    ?>checked="checked"<?php
}?> /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>注册页面标题</b></p></td>
    <td><input type="text" name="title_text" value="<?php echo htmlspecialchars($zbp->Config('RegPage')->title_text); ?>" style="width:89%;" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>注册相关说明文字</b></p></td>
    <td><textarea name="readme_text" style="width:90%;height:100px;" /><?php echo htmlspecialchars($zbp->Config('RegPage')->readme_text); ?></textarea></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>在登录页引导用户注册</b></p></td>
    <td><input type="text" name="loginpage_addon" class="checkbox" value="<?php echo $zbp->Config('RegPage')->loginpage_addon; ?>" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>在登录页引导用户注册的链接文字</b></p></td>
    <td><input type="text" name="loginpage_text" value="<?php echo htmlspecialchars($zbp->Config('RegPage')->loginpage_text); ?>" style="width:89%;" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>伪静态下的注册页地址</b></p></td>
    <td><input type="text" name="rewrite_url" value="<?php echo htmlspecialchars($zbp->Config('RegPage')->rewrite_url); ?>" style="width:89%;" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>同一IP一天只能注册一个账号</b></p></td>
    <td><input type="text" class="checkbox" name="only_one_ip" value="<?php echo $zbp->Config('RegPage')->only_one_ip; ?>" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>不用填写网址</b></p></td>
    <td><input type="text" class="checkbox" name="disable_website" value="<?php echo $zbp->Config('RegPage')->disable_website; ?>" /></td>
</tr>
<tr>
    <td class="td30"><p align='left'><b>不用填写验证码</b></p></td>
    <td><input type="text" class="checkbox" name="disable_validcode" value="<?php echo $zbp->Config('RegPage')->disable_validcode; ?>" /></td>
</tr>
</table>
      <hr/>
      <p>
        <input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
      </p>

    </form>
    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/RegPage/logo.png'; ?>");</script>  
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
