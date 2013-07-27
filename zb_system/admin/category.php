<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require_once '../function/c_system_base.php';
require_once '../function/c_system_admin.php';

$zbp->Initialize();

$action='CategoryMng';
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}

$blogtitle='分类管理';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php
//不要吐槽，我会改的！！！
//哦漏！

$cate = new Category();

$a = $cate->GetLibIDArray(array($cate->datainfo['Order'][0] => 'ASC'), null);
foreach ($a as $key => $value) {
	$cate->LoadInfoByID($value);
	foreach ($cate->datainfo as $k => $v) {
		$cata_value[$value][$k] = $cate->Data[$k];
	}
}

foreach ($cata_value as $key => $value) {
	$cata[$value['ParentID']][] = $value;
}

//var_dump($cata);
//echo "<pre>";
//print_r($cata_parent);print_r($cata_child);
//echo "</pre>";
?>

<div id="divMain"> 
<div class="divHeader">分类管理</div>
<div class="SubMenu" style="display: block;">
<a href="../cmd.php?act=CategoryEdt&id=0"><span class="m-left">新建分类</span></a>
</div>
<div id="divMain2">
<table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
 <tbody>
  <tr class="color1">
   <th width="5%"></th>
   <th width="10%">ID</th>
   <th width="10%">排序</th>
   <th>名称</th>
   <th>别名</th>
   <th width="14%"></th>
  </tr>

<?php

getTableRows(0);

?>
 </tbody>
</table>
<p>&nbsp;</p>
</div>
<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script> 
</div>




</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();


function getTableRows($cataid){

	global $cata;
	global $bloghost;

	if(isset($cata[$cataid])){

		for( $i=0; $i<sizeof($cata[$cataid]); $i++){
			$v=$cata[$cataid][$i];

			echo "<tr class=\"color2\">\r\n";
			echo '  <td align="center"><img width="16" src="../image/admin/'.($v['ParentID']==0?'folder':'arrow_turn_right').'.png" alt="" /></td>'."\r\n";
			echo "  <td>{$v['ID']}</td>\r\n";
			echo "  <td>{$v['Order']}</td>\r\n";
			echo "  <td><a href=\"{$bloghost}catalog.php?cate={$v['ID']}\" target=\"_blank\">{$v['Name']}</a></td>\r\n";
			echo "  <td>{$v['Alias']}</td>\r\n";
			echo "  <td align=\"center\"><a href=\"../cmd.php?act=CategoryEdt&amp;id={$v['ID']}\" class=\"button\">";
			echo '<img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a>&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<a onclick="return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);" href="../cmd.php?act=CategoryDel&amp;id=1" class="button">';
			echo '<img src="../image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td>'."\r\n";
			echo '  </tr>'."\r\n";


			if(isset($cata[$v['ID']])){
				getTableRows($v['ID']);
			}


		}


	}
}
?>
