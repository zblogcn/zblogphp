<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('linkmanage')) {
    $zbp->ShowError(48);
    die();
}

$Menus = linkmanage_getMenus();

if (GetVars('creat', 'POST') == 'new') {
    linkmanage_creatMenu(GetVars('id', 'POST'));
}

if (GetVars('del', 'GET')) {
    linkmanage_deleteMenu(GetVars('del', 'GET'));
}

$blogtitle = '菜单链接管理';

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript" src="jquery.mjs.nestedSortable.js"></script>
<script type="text/javascript" src="js.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
  	<span class="m-left m-now">菜单管理</span>
  	<a href="config.php"><span class="m-left">配置选项</span></a>
  </div>

  <div id="divMain2">
	<table border="1" class="tableFull tableBorder table_hover table_striped tableBorder-thcenter tdCenter">
	<tbody>
		<tr>
			<th>ID</th>
			<th>标题</th>
			<th>模块标签</th>
			<th>编辑</th>
		</tr>
		<?php
            foreach ($Menus['data'] as $key => $value) {
                $menuid = $value['id'];
                $linkmanage_str = linkmanage_editSys_str($menuid);
                //$location = ($value['location'] == '') ? '未使用' : $value['location'];
                $button = linkmanage_edit_button($menuid);
                echo <<<MENULIST
			<tr><td class="td15"> $menuid </td>
			<td class="td25"> $value[name] </td>
			<td class="td20"> {module:$linkmanage_str$menuid} </td>
			<td class="td30"> $button </td></tr>
MENULIST;
            }
        ?>
	</tbody></table>
	<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" onclick="add_menu();">创建新导航</button>
	</div>
  </div>
</div>

<div id="dialog" title="创建新导航" style="display:none;">
	<form id="edit" name="edit" method="post" action="">
	  <input id="edtID" name="creat" type="hidden" value="new">
	  <p>
		<span class="title">htmlID(英文标识):</span><span class="star">(*)</span><br>
		<input id="id" class="edit" size="40" name="id" maxlength="20" type="text" value="">
	  </p>

	  <p>
		<span class="title">名称:</span><span class="star">(*)</span><br>
		<input id="name" class="edit" size="40" name="name" maxlength="20" type="text" value="">
	  </p>
	  <p>
		<input type="submit" class="button" value="提交" id="btnPost">
	  </p>
	</form>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>