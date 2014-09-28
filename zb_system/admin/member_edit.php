<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->CheckGzip();
$zbp->Load();

$action='';
if(GetVars('act','POST')=='MemberEdt')$action='MemberEdt';
if(GetVars('act','POST')=='MemberNew')$action='MemberNew';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6,__FILE__,__LINE__);die();}
$blogtitle=$lang['msg']['member_edit'];

$memberid=null;
if(isset($_POST['id'])){$memberid = (integer)GetVars('id','POST');}else{$memberid = 0;}

if(!$zbp->CheckRights('MemberAll')){
	if((int)$memberid<>(int)$zbp->user->ID) {$zbp->ShowError(6,__FILE__,__LINE__);}
}

$member=$zbp->GetMemberByID($memberid);
?>
	<form id="memberedit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $member->ID;?>" />
	  <input id="edtGuid" name="Guid" type="hidden" value="<?php echo $member->Guid;?>" />
	  <div class="form-group">
		<label class="title" for="cmbLevel"><?php echo $lang['msg']['member_level']?></label>
		<select class="edit" size="1" name="Level" id="cmbLevel">
			<?php echo CreateOptoinsOfMemberLevel($member->Level);?>
		</select>
<?php if($zbp->CheckRights('MemberAll') && $zbp->user->ID<>$member->ID)
		{
		?>
		<div class="form-group">
		<label class="title" for="Status"><?php echo $lang['msg']['status']?></label>
		<label><input name="Status" type="radio" value="0" <?php echo $member->Status==0?'checked="checked"':''; ?> /><?php echo $lang['user_status_name'][0]?></label>
		<label><input name="Status" type="radio" value="1" <?php echo $member->Status==1?'checked="checked"':''; ?> /><?php echo $lang['user_status_name'][1]?></label>
		<label><input name="Status" type="radio" value="2" <?php echo $member->Status==2?'checked="checked"':''; ?> /><?php echo $lang['user_status_name'][2]?></label>
		</div>
<?php 
		}
?>
	  </div>
	  <div class="form-group">
		<label class="title" for="edtName"><?php echo $lang['msg']['name']?></label><span class="star">*</span>
		<input id="edtName" class="edit" size="40" name="Name" maxlength="20" type="text" value="<?php echo $member->Name;?>" <?php if(!$zbp->CheckRights('MemberAll'))echo 'readonly="readonly"';?> />
	  </div>
	  <div class="form-group">
	    <label class="title" for="edtPassword"><?php echo $lang['msg']['password']?></label><input id="edtPassword" class="edit" size="40" name="Password"  type="password" value="" /><?php echo  ($action=='MemberNew'?'<span class="star">*</span>':'') ?>
	  </div>
	  <div class="form-group">
	    <label class="title" for="edtPasswordRe"><?php echo $lang['msg']['re_password']?></label><input id="edtPasswordRe" class="edit" size="40" name="PasswordRe"  type="password" value="" /><?php echo  ($action=='MemberNew'?'<span class="star">*</span>':'') ?>
	  </div>
	  <div class="form-group">
		<label class="title" for="edtAlias"><?php echo $lang['msg']['alias']?></label>
		<input id="edtAlias" class="edit" size="40" name="Alias" type="text" value="<?php echo $member->Alias;?>" />
	  </div>
	  <div class="form-group">
		<label class="title" for="edtEmail"><?php echo $lang['msg']['email']?></label><span class="star">*</span>
		<input id="edtEmail" class="edit" size="40" name="Email" type="text" value="<?php echo $member->Email;?>" />
	  </div>
	  <div class="form-group">
		<label class="title" for="edtHomePage"><?php echo $lang['msg']['homepage']?></label>
		<input id="edtHomePage" class="edit" size="40" name="HomePage" type="text" value="<?php echo $member->HomePage;?>" />
	  </div>
	  <div class="form-group">
		<label class="title" for="edtIntro"><?php echo $lang['msg']['intro']?></label>
	    <textarea  cols="3" rows="6" id="edtIntro" name="Intro" ><?php echo htmlspecialchars($member->Intro);?></textarea>
	  </div>
	  <div class="form-group">
		<label class="title" for="cmbTemplate"><?php echo $lang['msg']['template']?></label>
		<select class="edit" size="1" name="Template" id="cmbTemplate">
<?php echo CreateOptoinsOfTemplate($member->Template);?>
		</select>
	  </div>
       <div id='response' class='editmod2'>
<?php
		foreach ($GLOBALS['Filter_Plugin_Member_Edit_Response'] as $fpname => &$fpsignal) {$fpname();}
?>
	   </div>
	  <div class="form-group">
		<label><?php echo $lang['msg']['default_avatar']?></label>&nbsp;<?php echo $member->Avatar;?>
	  </div>
	</form>
<?php
RunTime();
?>