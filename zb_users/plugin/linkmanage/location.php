<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('linkmanage')) {$zbp->ShowError(48);die();}
$Navs = linkmanageGetNav();
$locals = linkmanage_GetLocation();

$blogtitle = '导航编辑';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if (GetVars('nav', 'POST') != 0) {

	$menuID = GetVars('nav', 'POST');
	$n = json_decode($zbp->Config('linkmanage')->Nav, true);
	$local_set = json_decode($zbp->Config('linkmanage')->local_set, true);

	if (GetVars('location', 'POST') == 0) {
		$zbp->ShowHint('bad', '位置选择错误');
	} else {
		$n['data'][$menuID]['location'] = GetVars('location', 'POST');
		$local_set[GetVars('location', 'POST')] = $menuID;

		$zbp->Config('linkmanage')->Nav = json_encode($n);
		$zbp->Config('linkmanage')->local_set = json_encode($local_set);

		$zbp->SaveConfig('linkmanage');

		//$location[GetVars('location', 'POST')][3] = GetVars('nav', 'POST');
		Redirect('location.php');
	}
}
?>
<link href="style.css" rel="stylesheet" type="text/css" />

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php linkmanage_SubMenu(1);?></div>
  <div id="divMain2">
<form id="edit" name="edit" method="post" action="">
  	<div class="choose-menus">
		<label>请选择位置：</label>
		<select id="select-menu-to-edit" name="location">
<?php

echo '<option value="0">---请选择位置---</option>';

foreach ($locals as $key => $value) {
	echo '<option value="' . $value[1] . '">' . $value[2] . ' (来自' . $value[0] . ')</option>';
}
?>
		</select>


		<label>要使用的导航：</label>
		<select id="select-menu-to-edit" name="nav">
<?php
echo '<option value="0">---要使用的导航---</option>';

foreach ($Navs['data'] as $key => $value) {
	$location = ($value['location'] == '') ? '未使用' : $value['location'];
	echo '<option value="' . $key . '">' . $value['name'] . ' (' . $location . ')</option>';
}
?>
		</select>
		<input type="submit" class="button" value="保存" id="btnPost">
	</div>
</form>
	</div>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>