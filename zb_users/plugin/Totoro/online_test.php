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
    $comment = new Comment();
    $comment->Name = GetVars('name', 'POST');
    $comment->HomePage = GetVars('url', 'POST');
    $comment->IP = GetVars('ip', 'POST');
    $comment->Content = GetVars('string', 'POST');

    //	var_dump($comment);
    $score = $Totoro->get_score($comment, true);
    echo "\n" . 'MAX_SCORE: ' . $score;
    if ($score >= $Totoro->config_array['SV_SETTING']['SV_THRESHOLD']['VALUE']) {
        echo "\n该评论被审核";
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
  <div class="SubMenu"><?php echo $Totoro->export_submenu('online_test'); ?></div>
  <div id="divMain2">
    <table width="100%" class="table_striped table_hover" style="padding:0px;margin:1px;line-height:20px" cellspacing="0" cellpadding="0">
      <tr height="40">
        <td width="50%"><label for="username">· 用户名</label>
          <input type="text" name="username" id="username" style="width:90%" /></td>
        <td>结果</td>
      </tr>

      <tr>
        <td><label for="url">· 网址　</label>
            <input type="text" name="url" id="url" style="width:90%"/></td>
        <td rowspan="5" style="text-indent:0;vertical-align:top"><div id="result"></div></td>
      </tr>
      <tr>
        <td><label for="ip">· IP　　</label>
            <input type="text" name="ip" id="ip" value="<?php echo GetVars('REMOTE_ADDR', 'SERVER'); ?>" style="width:90%"/></td>
        <td rowspan="5" style="text-indent:0;vertical-align:top"><div id="result"></div></td>
      </tr>
      <tr height="40">
        <td>· 内容</td>
      </tr>
      <tr>
        <td><textarea rows="6" name="regexp" id="regexp" style="width:99%" ></textarea></td>
      </tr>
      <tr>
        <td><input type="button" class="button" value="提交测试" id="buttonsubmit"/></td>
      </tr>
    </table>
    <script type="text/javascript">
    $(function() {
        $("#buttonsubmit").bind("click",function(){
            $("#result").html("Testing...");
            var o = $.ajax({
                url : "?type=test",
                async : false,
                type : "POST",
                data : {
          "name":$("#username").val(),
          "url":$("#url").val(),
          "ip":$("#ip").val(),
          "string":$("#regexp").val()
        },
                dataType : "script",
            });
            $("#result").html(o.responseText.replace(/\n/g, "<br/>"));
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
