<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Load();

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
	  <input id="edtGuid" name="Guid" type="hidden" value="<?php echo $member->Guid;?>" />	  
	  <p>
		<span class="title"><?php echo $lang['msg']['member_level']?>:</span><br />
		<select class="edit" size="1" name="Level" id="cmbTemplate">
<?php echo CreateOptoinsOfMemberLevel($member->Level);?>
		</select>
	  </p>	  
	  <p>
		<span class="title"><?php echo $lang['msg']['name']?>:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $member->Name;?>" />
	  </p>
	  <p>
	    <span class='title'><?php echo $lang['msg']['password']?>:</span><br/><input id="edtPassword" class="edit" size="40" name="Password"  type="password" value="" />
	  </p>
	  <p>
	    <span class='title'><?php echo $lang['msg']['re_password']?>:</span><br/><input id="edtPasswordRe" class="edit" size="40" name="PasswordRe"  type="password" value="" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['alias']?>:</span><br />
		<input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $member->Alias;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['email']?>:</span><br />
		<input id="edtEmail" class="edit" size="40" name="Email" type="text" value="<?php echo $member->Email;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['homepage']?>:</span><br />
		<input id="edtHomePage" class="edit" size="40" name="HomePage" type="text" value="<?php echo $member->HomePage;?>" />
	  </p>
	  <p><span class='title'><?php echo $lang['msg']['intro']?>:</span><br/>
	    <textarea  cols="3" rows="6" id="edtIntro" name="Intro" style="width:600px;"><?php echo htmlspecialchars($member->Intro);?></textarea>
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['template']?>:</span><br />
		<select class="edit" size="1" name="Template" id="cmbTemplate">
<?php echo CreateOptoinsOfTemplate($member->Template);?>
		</select>
	  </p>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" id="btnPost" onclick="return checkInfo();" />
	  </p>
	</form>
	<script type="text/javascript">
function checkInfo(){
  document.getElementById("edit").action="../cmd.php?act=MemberPst";

  if(!$("#edtName").val()){
    alert("<?php echo $lang['error']['72']?>");
    return false
  }

  if(!$("#edtPassword").val()==$("#edtPasswordRe").val()){
    alert("<?php echo $lang['error']['73']?>");
    return false
  } 

}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aMemberMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $zbp->host . 'zb_system/image/common/user_32.png';?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
