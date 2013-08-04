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

$action='CategoryEdt';
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}

$blogtitle=$lang['msg']['category_edit'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php

if(isset($_GET['id'])){$cateid = $_GET['id'];}else{$cateid = 0;}

$cate=$zbp->GetCategoryByID($cateid);

$p=null;

if($cate->ParentID==0){$p .='<option value="0">无</option>';}

foreach ($zbp->categorys as $k => $v) {
	if($v->ID==$cate->ID){continue;}
	if($v->ParentID==0){
		$p .='<option ' . ($v->ID==$cate->ParentID?'selected="selected"':'') . ' value="'. $v->ID .'">' . $v->Name . '</option>';
	}
}
var_dump($cate);
?>

<div id="divMain">
  <div class="divHeader2">分类编辑</div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="edtID" type="hidden" value="<?php echo $cateid;?>" />
	  <p>
		<span class="title">名称:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="edtName" maxlength="50" type="text" value="<?php echo $cate->Name;?>" />
	  </p>
	  <p>
		<span class="title">别名:</span><br />
		<input id="edtAlias" class="edit" size="40" name="edtAlias" type="text" value="<?php echo $cate->Alias;?>" />
	  </p>

	  <p>
		<span class="title">排序:</span><br />
		<input id="edtOrder" class="edit" size="40" name="edtOrder" type="text" value="<?php echo $cate->Order;?>" />
	  </p>
	  <p>
		<span class="title">父分类:</span><br />
		<select id="edtPareID" name="edtPareID" class="edit" size="1">
			<?php echo $p;?>
		</select>
	  </p>
	  <p>
		<span class="title">模板:</span><br />
		<select class="edit" size="1" id="cmbTemplate" onchange="edtTemplate.value=this.options[this.selectedIndex].value">
		  <option value="CATALOG" selected="selected">CATALOG (默认模板)</option>
		</select><input type="hidden" name="edtTemplate" id="edtTemplate" value="<?php echo $cate->Template;?>" />
	  </p>
	  <p>
		<span class="title">此目录下文章的默认模板:</span><br />
		<select class="edit" size="1" id="cmbLogTemplate" onchange="edtLogTemplate.value=this.options[this.selectedIndex].value">
		  <option value="SINGLE" selected="selected">SINGLE (默认模板)</option>
		</select><input type="hidden" name="edtLogTemplate" id="edtLogTemplate" value="<?php echo $cate->LogTemplate;?>" />
	  </p>
	  <p>
		<label><input type="checkbox" name="edtAddNavbar" id="edtAddNavbar" value="True" />  <span class="title">加入导航栏菜单</span></label>
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
