<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->LoadData();

$action='ModuleEdt';
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}

$blogtitle=$lang['msg']['module_edit'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php

$modid=null;


if(isset($_GET['filename'])){

	$array=$zbp->GetModuleList(
		array('*'),
		array(array('=','mod_FileName',$_GET['filename'])),
		null,
		array(1),
		null
	);

	if(count($array)==0){
		throw new Exception($zbp->lang['error'][69], 1);
	}

	$mod =$array[0];


	//'$mod=
}else{
	if(isset($_GET['id'])){$modid = (integer)$_GET['id'];}else{$modid = 0;}
	$mod=$zbp->GetTagByID($tagid);
}



?>

<div id="divMain">
  <div class="divHeader2"><?php echo $lang['msg']['module_edit']?></div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit tag_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $mod->ID;?>" />
	  <p>
		<span class="title"><?php echo $lang['msg']['name']?>:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $mod->Name;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['filename']?>:</span><br />
		<input id="edtAlias" class="edit" size="40" name="FileName" type="text" value="<?php echo $mod->FileName;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['htmlid']?>:</span><br />
		<input id="edtAlias" class="edit" size="40" name="FileName" type="text" value="<?php echo $mod->HtmlID;?>" />
	  </p>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" id="btnPost" onclick="return checkCateInfo();" />
	  </p>
	</form>
	<script type="text/javascript">
function checkCateInfo(){
  document.getElementById("edit").action="../cmd.php?act=TagPst";

  if(!$("#edtName").val()){
    alert("<?php echo $lang['error']['72']?>");
    return false
  }

}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aModuleMng");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
