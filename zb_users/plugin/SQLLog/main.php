<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('SQLLog')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'SQLLog';
require $blogpath.'zb_system/admin/admin_header.php';
require $blogpath.'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"> <a href="edit_data.php?act=delete_all" target="_self"><span class="m-left">删除全部日志</span></a> </div>
  <div id="divMain2">
    <p>当前日志存放路径：<?php echo htmlspecialchars(SQLLOG_PATH.'\logs'); ?></p>
    <table border="1" class="tableFull tableBorder">
      <tr>
        <th style="width:50%"><p align="left"><b>文件名</b></p></th>
        <th style="width:30%"><p align="left"><b>大小</b></p></th>
        <th style="width:20%"><p align="left"><b>操作</b></p></th>
      </tr>
      <?php 
        $handle = opendir(SQLLOG_LOGPATH);
        if ($handle) {
            while (($filename = readdir($handle))) {
                if (!is_dir($filename) && preg_match("/\d+_zbp_.+?\.php/", $filename)) {
                    $format_filename = htmlspecialchars($filename); ?>
	  <tr>
      <td><?php echo $format_filename; ?></td>
      <td><?php echo format_size(filesize(SQLLOG_LOGPATH.$filename))?></td>
      <td>
      	<a href="edit_data.php?act=download&filename=<?php echo $format_filename?>">
        	<img src="<?php echo $zbp->host?>zb_system/image/admin/download.png" alt="download" />
        </a>
      	<a href="edit_data.php?act=delete&filename=<?php echo $format_filename?>">
        	<img src="<?php echo $zbp->host?>zb_system/image/admin/delete.png" alt="download" />
        </a>
      </td>
      </tr>
      <?php
                }
            }
        }

      ?>
    </table>
  </div>
</div>
<?php
require $blogpath.'zb_system/admin/admin_footer.php';
RunTime();

function format_size($bytes)
{
    $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
    $units[-1] = '';
    $i = 0;
    while ($bytes >= pow(1024, $i)) {
        $i++;
    }

    return round($bytes / pow(1024, $i - 1) * 100) / 100 .' '.$units[$i - 1];
}
?>
