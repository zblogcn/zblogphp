<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('FileSystem')) {$zbp->ShowError(48);die();}

if(isset($_POST['file'])){

	file_put_contents($_POST['file'], $_POST['code']);

	$url = 'main.php?';
	if ($blogpath != $_POST['current_path']) $url = $url . '&path=' . urlencode(str_replace($blogpath, "", $_POST['current_path']));
	Redirect($url);
	die();
}

$blogtitle = '文件管理系统--编辑';

$file = iconv('UTF-8', 'GB2312', $_POST['current_path'].$_POST['cmd_data']);

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
<div class="divHeader"><?php echo $blogtitle;?></div>

<form action="" method="post">
<div id="divMain2">
<textarea style="height:450px;width:96%" name="code" id="code"><?php echo file_get_contents($file)?></textarea>
</div>

<link rel="stylesheet" href="static/codemirror/codemirror.css">
<script src="static/codemirror/codemirror.js"></script>

<input type=hidden name="current_path" value="<?php echo $_POST['current_path'];?>">
<input type=hidden name="file" value="<?php echo $file;?>">
<input type="submit" style="width:80" value="保存">
<input type="button" style="width:80" onclick="returnback()" value="返回">
</form>
<script type="text/javascript">
function returnback(){window.history.back(-1);
}

</script>
	<script type="text/javascript">ActiveLeftMenu("aFileSystem");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_system/image/common/file_1.png';?>");</script>	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>