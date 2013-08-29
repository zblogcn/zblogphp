<?php
session_start();

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
include 'function.php';

$zbp->Load();

if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('FileSystem')) {$zbp->ShowError(48);die();}

$blogtitle='文件管理系统';
$root_path = $blogpath;
$file_list = array();
//根据path参数，设置各路径和URL
if (empty($_GET['path'])) {
	$current_path = $root_path;
	$current_url = $zbp->host;
	$current_dir_path = '';
	$moveup_dir_path = '';
} else {
	$current_path = $root_path . urldecode($_GET['path']);
	$current_url = $zbp->host . urldecode($_GET['path']);
	$current_dir_path = urldecode($_GET['path']);
	$moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
}

$file_list = Get_Filelist($current_path);

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php
if (empty($_GET['path'])) {
$zbp->ShowHint('bad','本插件配置不当可能会造成网站被黑等严重后果，请慎用！');
}
if (!empty($_GET['error'])) {
$zbp->ShowHint('bad',$_GET['error']);
}
?>
<link href="static/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="static/action.js" type="text/javascript"></script>
<div class="divHeader"><?php echo $blogtitle;?></div>
<form name="form_action" action="action.php" method="post" onsubmit="return test_action();">
            <input type=hidden name="action" value=0>
            <input type=hidden name="cmd_data" value="">
            <input type=hidden name="cmd_arg" value="">
            <input type=hidden name="current_path" value="<?php echo $current_path;?>">
            <input type=hidden name="selected_file_list" value="">
<nobr>
	<input type="button" onclick="show_phpinfo()" value="服务器信息">
	<input type="button" onclick="create_file()" value="新建文件">
	<input type="button" onclick="create_dir()" value="新建文件夹">
	<input type="button" value="上传文件">
	<input type="button" value="执行命令">
	<input type="button" value="运行Shell">
</nobr>
<div id="divMain2" class="edit category_edit">
<?php
	format_dir($current_path, $root_path);
?>
</div>
<table class="table table-condensed">
<?php
	if(isset($file_list['dir'])){
		echo '<thead><tr class="success"><th>#</th><th>文件名</th><th>权限</th><th>文件大小</th><th>修改时间</th><th>文件类型</th><th>操作</th></tr></thead><tbody>';
		foreach($file_list['dir'] as $k=>$v){
			echo "<tbody><tr>";
			echo "<td>$k</td>";
			echo "<td><img src='".$zbp->host."zb_system/image/filetype/folder.png'><a href='".$zbp->host."zb_users/plugin/FileSystem/main.php?path=".urlencode($current_dir_path.$v['filename']."/")."'> $v[filename]</a></td>";
			echo "<td>$v[fileperms]</td>";
			echo "<td></td>";
			echo "<td>$v[datetime]</td>";
			echo "<td>文件夹</td>";
			echo "<td><img src='".$zbp->host."zb_system/image/admin/document-rename.png'>&nbsp;&nbsp;<img src='".$zbp->host."zb_system/image/admin/delete.png'></td>";
			echo "</tr></tbody>";
		}
	}
	if(isset($file_list['file'])){
		echo '<thead><tr class="error"><th>#</th><th>文件名</th><th>权限</th><th>文件大小</th><th>修改时间</th><th>文件类型</th><th>操作</th></tr></thead><tbody>';
		foreach($file_list['file'] as $k=>$v){
			echo "<tbody><tr>";
			echo "<td><input type='checkbox' name='$v[filename]'></td>";
			echo "<td><img src='".$zbp->host."zb_system/image/filetype/".GetFileimg($v['filetype']).".png'><a href='$current_url$v[filename]' target='_blank'> $v[filename]</a></td>";
			echo "<td>$v[fileperms]</td>";
			echo "<td>$v[filesize]</td>";
			echo "<td>$v[datetime]</td>";
			echo "<td>$v[filetype]</td>";
			echo "<td><img src='".$zbp->host."zb_system/image/admin/download.png'>&nbsp;&nbsp;<img src='".$zbp->host."zb_system/image/admin/document-rename.png'>&nbsp;&nbsp;<img src='".$zbp->host."zb_system/image/admin/page_edit.png'>&nbsp;&nbsp;<img src='".$zbp->host."zb_system/image/admin/delete.png'></td>";
			echo "</tr></tbody>";
		}
	}
?>
</table>
<nobr>
	<input type="button" style="width:80" onclick="selectANI(this)" value="全选">
	<input type="button" style="width:80" onclick="selectANI(this)" value="反选">
	<input type="button" style="width:80" onclick="del()" value="删除">
	<input type="button" style="width:80" onclick="copy()" value="复制">
	<input type="button" style="width:80" onclick="copy()" value="移动">
	<input type="button" style="width:100" onclick="tar()" value="打包">
	<input type="button" style="width:100" onclick="chmod()" value="权限">
</nobr>
<br><br><br>
</form>
	<script type="text/javascript">ActiveLeftMenu("aFileSystem");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_system/image/common/file_1.png';?>");</script>	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>