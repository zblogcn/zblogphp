<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('Totoro')) {
    $zbp->ShowError(48);
    die();
}
Totoro_init();
$blogtitle = 'Totoro反垃圾评论';

if (GetVars('type', 'GET') == 'test') {
    set_error_handler(create_function('', ''));
    set_exception_handler(create_function('', ''));
    register_shutdown_function(create_function('', ''));
    $regex = GetVars('regexp', 'POST');
    $regex = "/(" . $regex . ")/si";
    $matches = array();
    $string = GetVars('string', 'POST');
    $value = preg_match_all($regex, $string, $matches);
    if ($value) {
        foreach ($matches[0] as $v) {
            //echo $v;
            $string = str_replace($v, '$$$fuabcdeck$$a$' . $v . '$$a$fuckd$b$', $string);
        }
        $string = TransferHTML($string, '[html-format]');
        $string = str_replace('$$$fuabcdeck$$a$', '<span style="background-color:#92d050">', $string);
        $string = str_replace('$$a$fuckd$b$', '</span>', $string);
        echo $string;
    } else {
        echo "正则有误或未匹配到：<br/><br/>可能的情况是：<ol><li>少打了某个符号</li><li>没有在[ ] ( ) ^ . ? !等符号前加\</li></ol>";
    }

    exit();
}
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<style type="text/css">
.text-config {
    width: 95%
}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';

?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo $Totoro->export_submenu('regex_test'); ?></div>
  <div id="divMain2">
    <table width="100%" style="padding:0px;margin:1px;line-height:20px" cellspacing="0" cellpadding="0">
      <tr height="40">
        <td width="50%">输入待测试内容</td>
        <td>结果</td>
      </tr>
      <tr>
        <td><textarea rows="6" name="test" id="test" style="width:99%" ></textarea></td>
        <td rowspan="4" style="text-indent:0;vertical-align:top"><div id="result"></div></td>
      </tr>
      <tr height="40">
        <td>输入黑词列表或过滤词列表</td>
      </tr>
      <tr>
        <td><textarea rows="6" name="regexp" id="regexp" style="width:99%" ></textarea></td>
      </tr>
      <tr>
        <td><input type="button" class="button" value="提交测试" id="buttonsubmit"/></td>
      </tr>
    </table>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(e) {
    $("#buttonsubmit").bind("click",function(){
        var o = $.ajax({
            url : "regex_test.php?type=test",
            async : false,
            type : "POST",
            data : {"string":$("#test").attr("value"),"regexp":$("#regexp").attr("value")},
            dataType : "script"
        });
        $("#result").html(o.responseText);
    });
});
</script>
<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Totoro/logo.png'; ?>");</script>
</div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
