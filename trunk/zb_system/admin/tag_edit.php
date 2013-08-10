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

$tagid=null;
if(isset($_GET['id'])){$tagid = $_GET['id'];}else{$tagid = 0;}

$tag=$zbp->GetTagByID($tagid);

?>

<div id="divMain">
  <div class="divHeader2"><?php echo $lang['msg']['tag_edit']?></div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit tag_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $tag->ID;?>" />
	  <p>
		<span class="title"><?php echo $lang['msg']['name']?>:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $tag->Name;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['alias']?>:</span><br />
		<input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $tag->Alias;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['template']?>:</span><br />
		<select class="edit" size="1" name="Template" id="cmbTemplate">
<?php echo CreateOptoinsOfTemplate($tag->Template);?>
		</select>
	  </p>
	  <p>
		<label><span class="title"><?php echo $lang['msg']['add_to_navbar']?>:</span>   <input type="text" name="AddNavbar" id="edtAddNavbar" value="0" class="checkbox" /></label>
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
	<script type="text/javascript">ActiveLeftMenu("aTagMng");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
