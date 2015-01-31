<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('Howl')) {$zbp->ShowError(48);die();}

$blogtitle='Z-Blog角色分配器';

if(count($_POST)>0){

	if(GetVars('reset' ,'POST')=='1'){

		$zbp->DelConfig('Howl');
		$zbp->SetHint('good','已删除所有的配置!');
		Redirect('./main.php');
		die();
	}

	$a=array();
	$a[1] = array();
	$a[2] = array();
	$a[3] = array();
	$a[4] = array();
	$a[5] = array();
	$a[6] = array();

	for ($i=1; $i < 7 ; $i++) {
		foreach ($actions as $key => $value) {
			$check=GetVars('Group' . $i . '_' . $key ,'POST');
			$a[$i][$key]=(int)$check;
		}
	}
	$zbp->Config('Howl')->Group1 = $a[1];
	$zbp->Config('Howl')->Group2 = $a[2];
	$zbp->Config('Howl')->Group3 = $a[3];
	$zbp->Config('Howl')->Group4 = $a[4];
	$zbp->Config('Howl')->Group5 = $a[5];
	$zbp->Config('Howl')->Group6 = $a[6];
	$zbp->SaveConfig('Howl');

	$zbp->SetHint('good');
	Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php
$zbp->ShowHint('bad','本插件配置不当可能会造成网站被黑等严重后果，请慎用！');
?>
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu" style="display: block;"><a href="main.php"><span class="m-left m-now">系统群组设置</span></a><a href="user.php"><span class="m-left">单独用户设置</span></a></div>
  <div id="divMain2">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
	<th class="td10">权限</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['1'];?>组</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['2'];?>组</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['3'];?>组</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['4'];?>组</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['5'];?>组</th>
	<th class="td10"><?php echo $zbp->lang['user_level_name']['6'];?>组</th>
	<!-- <th class="td10">权限</th> -->
</tr>
<?php

function MakeInput($group,$key){
global $zbp;
$zbp->user->Level=$group;
$check=(int)$zbp->CheckRights($key);
return '<input name="Group'.$group.'_' . $key .'" style="" type="text" value="'.$check.'" class="checkbox"/>';
}


foreach ($actions as $key => $value) {
?>

<tr>
<td class="tdCenter"><?php echo $key?>(<b><?php echo Howl_GetRightName($key);?></b>)</td>
<td class="tdCenter"><?php echo MakeInput(1,$key);?></td>
<td class="tdCenter"><?php echo MakeInput(2,$key);?></td>
<td class="tdCenter"><?php echo MakeInput(3,$key);?></td>
<td class="tdCenter"><?php echo MakeInput(4,$key);?></td>
<td class="tdCenter"><?php echo MakeInput(5,$key);?></td>
<td class="tdCenter"><?php echo MakeInput(6,$key);?></td>
</tr>
<?php
}


?>
</table>
	  <hr/>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button" value="恢复系统默认配置" onclick="$('#reset').val(1);" />
	  </p>

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