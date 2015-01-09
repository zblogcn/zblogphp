<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('Howl')) {$zbp->ShowError(48);die();}

$blogtitle='Z-Blog角色分配器';

if(!isset($_GET['id'])){

if(count($_POST)>0){
	Redirect('./user.php?id=' . $_POST['userid']);
}

}
//var_dump($_POST);die;
if(count($_POST)>0){
	$userid = 'User' . $_GET['id'];
	$useractions = array();
	foreach($_POST as $key=>$value){
		$useractions[$key]=$value;
	}
	$zbp->Config('Howl')->$userid = $useractions;
	$zbp->SaveConfig('Howl');
	$zbp->SetHint('good');
	Redirect('./user.php?id=' . $_GET['id']);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php
$zbp->ShowHint('bad','本插件配置不当可能会造成网站被黑等严重后果，请慎用！');
?>
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu" style="display: block;"><a href="main.php"><span class="m-left">群组设置</span></a><a href="user.php"><span class="m-left m-now">单独用户设置</span></a></div>
  <div id="divMain2">
	<form id="edit2" name="edit2" method="post" action="#">
<?php
if(!isset($_GET['id'])){
	echo '<select name="userid">';
	foreach ($zbp->members as $key => $value) {
			echo '<option value="' . $key . '" >' . $zbp->members[$key]->Name . '</option>';
	}
	echo '</select>';
	echo '<input type="submit" class="button" value="选择用户" />';
}else{
	echo '当前用户：' . $zbp->GetMemberByID($_GET['id'])->Name;
}

?>
	</form>
	<form id="edit" name="edit" method="post" action="#">
<?php
if(isset($_GET['id'])){

$userid = 'User' . $_GET['id'];
if($zbp->Config('Howl')->HasKey($userid))
	$useractions = $zbp->Config('Howl')->$userid;
else
	$useractions = array();

foreach($useractions as $key=>$value){
	echo '<p>' . $key . '<input name="' . $key .'" style="" type="text" value="'.(int)$value.'" class="checkbox"/></p>';
}

echo '<p><select onchange="$(\'#addact\').attr(\'name\',$(this).val());"><option value=""></option>';

foreach ($actions as $key => $value) {

echo '<option value="'.$key.'">'.$key.'</option>';

}

echo'</select><input id="addact" type="text" value="1" class="checkbox"/><input type="submit" class="button" value="添加" /></p>';

}
?>
	  <hr/>

	</form>
	<script type="text/javascript">

	</script>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Howl/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>