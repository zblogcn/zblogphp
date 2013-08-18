<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('CustomMeta')) {$zbp->ShowError(48);die();}

$blogtitle='CustomMeta自定义作用域';

if(count($_GET)==0){
	Redirect('./main.php?type=post');
}

if(count($_POST)>0){
	$type=$_GET['type'];

	$array=$_POST['meta'];
	$array2=array();
	foreach ($array as $key => $value) {
		if(trim($value)<>''){
			if(CheckRegExp(trim($value),'/^[a-zA-Z][a-zA-Z0-9_]{0,30}$/')){
				$array2[]=trim($value);
			}
		}
	}
	$array2=array_unique($array2);

	$zbp->Config('CustomMeta')->$type=$array2;
	$zbp->SaveConfig('CustomMeta');

	$zbp->SetHint('good');
	Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
 <a href="?type=post"><span class="m-left <?php if($_GET['type']=='post')echo 'm-now';?>">文章类自定义</span></a>
 <a href="?type=category"><span class="m-left <?php if($_GET['type']=='category')echo 'm-now';?>">目录类自定义</span></a>
 <a href="?type=tag"><span class="m-left <?php if($_GET['type']=='tag')echo 'm-now';?>">标签类自定义</span></a>
 <a href="?type=member"><span class="m-left <?php if($_GET['type']=='member')echo 'm-now';?>">会员类自定义</span></a>
  </div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
	<th style='width:45%'>自定义作用域名称</th>
	<th style='width:45%'>标签调用说明</th>
	<th class="td10"></th>	
</tr>
<?php

	$type=$_GET['type'];

	$array=$zbp->Config('CustomMeta')->$type;

if(is_array($array)){
foreach ($array as $key => $value) {

	echo '<tr>';
	echo '<td><p><input type="text" style="width:95%" name="meta[]" value="'.$value.'" /></p></td>';
	echo '<td><p>';
if($type=='post'){
	echo '{$article.Metas.'.$value.'}';
}
if($type=='category'){
	echo '{$article.Category.Metas.'.$value.'}';
}
if($type=='tag'){
	echo '{tag.Metas.'.$value.'}';
}
if($type=='member'){
	echo '{$article.Author.Metas.'.$value.'}';
}
	echo '</p></td>';
	echo '<td><p><input type="submit" value="删除" onclick="$(this).parent().parent().parent().remove();" /></p></td>';
	echo '</tr>';

}

}

?>
<tr>
<td><p><input type="text" style="width:95%" name="meta[]" value="" /></p></td>
<td><p></p></td>
<td><p><!--<input type="submit" value="添加" />--></p></td>
</tr>

</table>
	  <hr/>
	  <p style="color:blue;"><input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />自定义作用域名称只能用字母，数字和下划线，且必须是字母打头。</p>
	</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/CustomMeta/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>