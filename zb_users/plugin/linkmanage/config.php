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
		<a href="main.php"><span class="m-left ">菜单管理</span></a>
		<span class="m-left m-now">配置选项</span>
	</div>
	<div id="divMain2">
		<form id="config" name="config" method="post" action="save.php?type=save_config">
		<table border="1" class="tableFull tableBorder table_hover table_striped tableBorder-thcenter tdCenter">
			<tbody>
				<tr>
					<th class="td30">参数</th>
					<th class="td20">配置</th>
					<th>说明</th>
				</tr>
				<tr>
					<td> 是否直接编辑系统模块链接 </td>
					<td> <input id="editsystem" name="editsystem" type="text" value="<?php echo $zbp->Config('linkmanage')->editsystem; ?>" class="checkbox" style="display: none;"></td>
					<td> 注意：打开该选项后，再次编辑并保存系统同名菜单将覆盖原模块中的链接内容（如navbar将覆盖默认导航内容）。原先如有生成插件模块不会删除，并在关闭该选项后继续更新。请谨慎操作。 </td>
				</tr>
				<tr>
					<td> 是否允许直接删除非空菜单 </td>
					<td> <input id="forcedel" name="forcedel" type="text" value="<?php echo $zbp->Config('linkmanage')->forcedel; ?>" class="checkbox" style="display: none;"></td>
					<td> 打开该选项后，将允许直接删除含有链接配置的菜单，请谨慎操作。 </td>
				</tr>
				<tr>
					<td> 选择菜单编辑中要显示的系统链接类型 </td>
					<td>
					<?php
                    $showtype = linkmanage_showtype();
                    $showoption = $zbp->Config('linkmanage')->showoption;
                    foreach ($showtype as $item) {
                        $chk = (strstr($showoption, $item[0])) ? 'checked' : '';
                        $tmp = '<div class="checkbox">
					        <label>
					          <input type="checkbox" name="showoption[]" value="' . $item[0] . '" ' . $chk . '>' . $item[1] . '
					        </label>
					      </div>';
                        echo $tmp;
                    }
                    ?>
					</td>
					<td>  </td>
				</tr>
			</tbody>
		</table>
		<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="sumbit">保存配置</button>
		</form>
	</div>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>