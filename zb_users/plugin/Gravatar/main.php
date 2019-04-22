<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('Gravatar')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'Gravatar头像';

if (count($_POST) > 0) {
    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    }
    $zbp->Config('Gravatar')->default_url = $_POST['default_url'];
    $zbp->Config('Gravatar')->source = $_POST['source'];
    $zbp->Config('Gravatar')->local_priority = $_POST['local_priority'];
    $zbp->SaveConfig('Gravatar');

    $zbp->SetHint('good');
    Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"></div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="#">
        <?php if (function_exists('CheckIsRefererValid')) {
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';
}?>

<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
    <th class="td25"></th>
    <th>设置</th>
</tr>
<tr>
<td><p align='left'><b>·Gravatar URL</b><br/><span class='note'></span></p></td>
<td><p><input id='default_url' name='default_url' style='width:90%;' type='text' value='<?php echo $zbp->Config('Gravatar')->default_url ?>' /></p></td>
</tr>
<tr>
<td><span class='note'>可选值: </span></td>
<td>
    <p><b>V2EX</b>：<a href="javascript:void(0)" class="enterGravatar">https://cdn.v2ex.com/gravatar/{%emailmd5%}.png?s=60&d=mm&r=G</a></p>
    <p><b>loli.net</b>：<a href="javascript:void(0)" class="enterGravatar">https://gravatar.loli.net/avatar/{%emailmd5%}?s=60&amp;d=mm&amp;r=G</a></p>
    <p><b>官方站点1</b>：<a href="javascript:void(0)" class="enterGravatar">https://cn.gravatar.com/avatar/{%emailmd5%}?s=60&amp;d=mm&amp;r=G</a></p>
    <p><b>官方站点2</b>：<a href="javascript:void(0)" class="enterGravatar">https://secure.gravatar.com/avatar/{%emailmd5%}?s=60&amp;d=mm&amp;r=G</a></p>
        </td>
</tr>
<tr>
<td><p align='left'><b>·无邮箱时的替换图片地址</b><br/><span class='note'></span></p></td>
<td><p><input id='source' name='source' style='width:90%;' type='text' value='<?php echo $zbp->Config('Gravatar')->source ?>' /></p></td>
</tr>
<tr>
<td><span class='note'>默认值: </span></td>
<td><p>{%host%}zb_users/avatar/0.png</p></td>
</tr>
<tr>
<td><p align='left'><b>·注册会员优先查找本地头像</b><br/><span class='note'></span></p></td>
<td><p><input id='local_priority' name='local_priority' class="checkbox" type='text' value='<?php echo $zbp->Config('Gravatar')->local_priority ?>' /></p></td>
</tr>

</table>
      <hr/>
      <p>
        <input type="submit" class="button" value="<?php echo $lang['msg']['submit'] ?>" />
      </p>
    </form>
    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Gravatar/logo.png'; ?>");</script>
  </div>
</div>

<script>
$(function() {
    $(".enterGravatar").click(function() {
        var $this = $(this);
        $("#default_url").val($this.text());
    });
});
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
