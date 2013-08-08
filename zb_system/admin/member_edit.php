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

$action='';
if(GetVars('act','GET')=='MemberEdt')$action='MemberEdt';
if(GetVars('act','GET')=='MemberNew')$action='MemberNew';
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}
$blogtitle=$lang['msg']['member_edit'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

$memberid=null;
if(isset($_GET['id'])){$memberid = $_GET['id'];}else{$memberid = 0;}

if(!$zbp->CheckRights('MemberAll')){
	if((int)$memberid<>(int)$zbp->user->ID) {throw new Exception($lang['error'][6]);}
}

$member=$zbp->GetMemberByID($memberid);

?>

<div id="divMain">
  <div class="divHeader2"><?php echo $lang['msg']['member_edit']?></div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit tag_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $member->ID;?>" />
	  <p>
		<span class="title"><?php echo $lang['msg']['name']?>:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $member->Name;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['alias']?>:</span><br />
		<input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $member->Alias;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['template']?>:</span><br />
		<select class="edit" size="1" name="Template" id="cmbTemplate">
<?php echo $zbp->CreateOptoinsOfTemplate($member->Template);?>
		</select><input type="hidden" name="edtTemplate" id="edtTemplate" value="<?php echo $member->Template;?>" />
	  </p>
	  <p>
		<input type="submit" class="button" value="提交" id="btnPost" onclick="return checkCateInfo();" />
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
	<script type="text/javascript">ActiveLeftMenu("aMemberMng");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
