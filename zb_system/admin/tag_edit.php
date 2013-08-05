<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Initialize();

$action='TagEdt';
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}

$blogtitle=$lang['msg']['tag_edit'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php

$cateid=null;
if(isset($_GET['id'])){$cateid = $_GET['id'];}else{$cateid = 0;}

$cate=$zbp->GetCategoryByID($cateid);

$p=null;

$p .='<option value="0">无</option>';

foreach ($zbp->categorysbyorder as $k => $v) {
	if($v->ID==$cate->ID){continue;}
	#if($v->RootID==$cate->ID){continue;}
	#if($cate->RootID>0){if($v->RootID==$cate->RootID){continue;}}
	if($v->Level<3){
		$p .='<option ' . ($v->ID==$cate->ParentID?'selected="selected"':'') . ' value="'. $v->ID .'">' . $v->SymbolName . '</option>';
	}
}
#var_dump($cate);
?>

<div id="divMain">
  <div class="divHeader2">分类编辑</div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $cate->ID;?>" />
	  <p>
		<span class="title">名称:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $cate->Name;?>" />
	  </p>
	  <p>
		<span class="title">别名:</span><br />
		<input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $cate->Alias;?>" />
	  </p>

	  <p>
		<span class="title">排序:</span><br />
		<input id="edtOrder" class="edit" size="40" name="Order" type="text" value="<?php echo $cate->Order;?>" />
	  </p>
	  <p>
		<span class="title">父分类:</span><br />
		<select id="edtParentID" name="ParentID" class="edit" size="1">
			<?php echo $p;?>
		</select>
	  </p>
	  <p>
		<span class="title">模板:</span><br />
		<select class="edit" size="1" name="Template" id="cmbTemplate">
		  <option value="CATALOG" selected="selected">CATALOG (默认模板)</option>
		</select><input type="hidden" name="edtTemplate" id="edtTemplate" value="<?php echo $cate->Template;?>" />
	  </p>
	  <p>
		<span class="title">此目录下文章的默认模板:</span><br />
		<select class="edit" size="1" name="LogTemplate" id="cmbLogTemplate">
		  <option value="SINGLE" selected="selected">SINGLE (默认模板)</option>
		</select><input type="hidden" name="edtLogTemplate" id="edtLogTemplate" value="<?php echo $cate->LogTemplate;?>" />
	  </p>
	  <p>
		<label><input type="checkbox" name="AddNavbar" id="edtAddNavbar" value="True" />  <span class="title">加入导航栏菜单</span></label>
	  </p>
	  <p>
		<input type="submit" class="button" value="提交" id="btnPost" onclick="return checkCateInfo();" />
	  </p>
	</form>
	<script type="text/javascript">
function checkCateInfo(){
  document.getElementById("edit").action="../cmd.php?act=CategoryPst";

  if(!$("#edtName").val()){
    alert("名称不能为空");
    return false
  }

}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
