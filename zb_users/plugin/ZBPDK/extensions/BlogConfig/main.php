<?php
require '../../../../../zb_system/function/c_system_base.php';
require '../../../../../zb_system/function/c_system_admin.php';
require '../../zbpdk_include.php';
header("Cache-Control: no-cache, must-revalidate");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");

$zbp->Load();
$zbpdk = new zbpdk_t();
$zbpdk->scan_extensions();
//var_dump($zbpdk->objects);

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('ZBPDK')) {$zbp->ShowError(48);die();}


if(isset($_GET['act']))
{
	switch($_GET['act'])
	{
		case 'open':
			echo blogconfig_exportlist($_GET['name']);
			exit();
		break;	
		case 'readleft':
			echo blogconfig_left(); 
			exit();
		break;
		case 'rename':
			$sql = $zbp->db->sql->Update($zbp->table['Config'],array('conf_Name' => $_GET['edit']),array(array('=','conf_Name',$_GET['name'])));
			$zbp->db->Update($sql);
			echo '操作成功'; 
			exit();
		break;	
		case 'del':
			$zbp->DelConfig($_GET['name']);
			echo '操作成功'; 
			exit();
		break;	
		case 'new':
			$zbp->SaveConfig($_GET['name']);
			echo blogconfig_exportlist($_GET['name']);
			exit();
		break;	
		default:
	}
}

if(isset($_POST['act']))
{
	switch($_POST['act'])
	{
		case 'e_del':
			$zbp->configs[$_POST['name2']]->Del($_POST['name1']);
			$zbp->SaveConfig($_POST['name2']);
			echo blogconfig_exportlist($_POST['name2']);
			exit();
		break;
		case 'e_edit':
			$config = $zbp->configs[$_POST['name2']]->$_POST['name1'];
			$value = $_POST['post'];
			if (gettype($config) == 'boolean') $value = (bool)$value;
			elseif (gettype($config) == 'integer') $value = (int)$value;
			$zbp->configs[$_POST['name2']]->$_POST['name1'] = $value;
			
			$zbp->SaveConfig($_POST['name2']);
			echo blogconfig_exportlist($_POST['name2']);
			exit();
		default:
	}
}

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<link rel="stylesheet" href="BlogConfig.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="../../css/jquery.contextMenu.css" type="text/css" media="screen"/>
<script type="text/javascript" src="../../js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="BlogConfig.js"></script>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('BlogConfig'); ?></div>
  <div id="divMain2">
    <div class="DIVBlogConfig">
      <div class="DIVBlogConfignav" name="tree" id="tree">
        <ul>
          <?php echo blogconfig_left(); ?>
        </ul>
        <script type="text/javascript">
		$(document).ready(function() {
			$("#tree ul li").contextMenu(
				{menu:'treemenu'},
				function(action, el, pos) {
					run(action,$(el).find("a").attr("id"))
				});
		});
      </script></div>
      <div id="content" class="DIVBlogConfigcontent">
        <div class="DIVBlogConfigcontentbody">请选择</div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
<ul id="treemenu" class="contextMenu">
  <li class="open"> <a href="#open">打开</a> </li>
  <li class="rename"> <a href="#rename">重命名</a> </li>
  <li class="del"> <a href="#del">删除</a> </li>
</ul>
<script>ActiveTopMenu('zbpdk');</script>
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png';?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
