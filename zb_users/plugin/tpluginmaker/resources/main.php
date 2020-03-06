<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin($blogtheme)) {
    $zbp->ShowError(48);
    die();
}
$dir = $usersdir . 'theme/' . $blogtheme . '/include/';
$blogtitle = $blogtheme . "主题 - 配置页面";

if (count($_POST) > 0 || count($_FILES) > 0) {
    /*TEMPLATE_SAVE_CODE*/
    $zbp->SetHint('good');
    Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader2"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"></div>
  <div id="divMain2">
    <form id="form-postdata" name="form-postdata" method="post" enctype="multipart/form-data" action="main.php">
      <table width="100%" border="1" width="100%" class="tableBorder">
      <tr>
        <th scope="col" height="32" width="150px">配置项</th>
        <th scope="col">配置内容</th>
        <th scope="col" width="500px">调用代码</th>
      </tr>
      /*TEMPLATE_TABLE_ROWS*/
      </table>
      <br/>
      <input class="button" type="submit" value="提交" />
    </form>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/theme/' . $blogtheme . '/logo.png'; ?>");</script> 
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();

function upload_file($filename, $tmp)
{
    convert_filename($filename);
    move_uploaded_file($tmp, $GLOBALS['dir'] . $filename);
}

function save_text($filename, $content)
{
    convert_filename($filename);
    file_put_contents($GLOBALS['dir'] . $filename, $content);
}

function convert_filename(&$filename)
{
    if (in_array(PHP_OS, array('WINNT', 'WIN32', 'Windows'))) {
        $filename = iconv('UTF-8', "GBK//IGNORE", $filename);
    }
}
?>
