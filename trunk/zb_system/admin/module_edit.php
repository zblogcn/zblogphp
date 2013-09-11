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

$action='ModuleEdt';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

$blogtitle=$lang['msg']['module_edit'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php

$modid=null;
$mod=null;

if(isset($_GET['source'])){
	if(GetVars('source','GET')=='theme'){
		$mod=new Module;
		$mod->Name=GetVars('filename','GET');
		$mod->FileName=GetVars('filename','GET');
		$mod->HtmlID=GetVars('filename','GET');
		$mod->Source='theme';
		if($mod->FileName){
			$mod->Content=file_get_contents($zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $mod->FileName . '.php');
		}
	}
}elseif(isset($_GET['filename'])){

	$array=$zbp->GetModuleList(
		array('*'),
		array(array('=','mod_FileName',GetVars('filename','GET'))),
		null,
		array(1),
		null
	);

	if(count($array)==0){
		$zbp->ShowError(69);
	}

	$mod =$array[0];


	//'$mod=
}else{
	if(isset($_GET['id'])){$modid = (integer)GetVars('id','GET');}else{$modid = 0;}

	$mod=$zbp->GetModuleByID($modid);
}
if($mod->Type=='ul'){
	$mod->Content=str_replace("</li>", "</li>\r\n", $mod->Content);
}

$islock='';
if($mod->Source=='system'||$mod->Source=='theme'){
	$islock='readonly="readonly"';
}
if($mod->Source=='theme'&&$mod->FileName==''){
	$islock='';
	$mod->Name='newmodule';
	$mod->HtmlID='newmodule';
}
$ishide='';
if($mod->Source=='theme'){
	$ishide='style="display:none;"';
}
?>

<div id="divMain">
  <div class="divHeader2"><?php echo $lang['msg']['module_edit']?></div>
  <div class="SubMenu"></div>
  <div id="divMain2" class="edit tag_edit">
	<form id="edit" name="edit" method="post" action="#">
	  <input id="edtID" name="ID" type="hidden" value="<?php echo $mod->ID;?>" />
	  <input id="edtSource" name="Source" type="hidden" value="<?php echo $mod->Source;?>" />
	  <p <?php echo $ishide?>>
		<span class="title"><?php echo $lang['msg']['name']?>:</span><span class="star">(*)</span><br />
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $mod->Name;?>" />
	  </p>
	  <p>
		<span class="title"><?php echo $lang['msg']['filename']?>:</span><span class="star">(*)</span><br />
		<input id="edtFileName" <?php echo $islock?> class="edit" size="40" name="FileName" type="text" value="<?php echo $mod->FileName;?>" />
	  </p>
	  <p <?php echo $ishide?>>
		<span class="title"><?php echo $lang['msg']['htmlid']?>:</span><span class="star">(*)</span><br />
		<input id="edtHtmlID" class="edit" size="40" name="HtmlID" type="text" value="<?php echo $mod->HtmlID;?>" />
	  </p>

	  <p <?php echo $ishide?>>
	  	<span class='title'><?php echo $lang['msg']['type']?>:</span><br/>
	    <label><input name="Type" type="radio" value="div" <?php echo $mod->Type=='div'?'checked="checked"':'';?> onclick="$('#pMaxLi').css('display','none');" />&nbsp;DIV </label>
	    &nbsp;&nbsp;&nbsp;&nbsp;
	    <label><input type="radio" name="Type" value="ul" <?php echo $mod->Type=='div'?'':'checked="checked"';?> onclick="$('#pMaxLi').css('display','block');" />&nbsp;UL</label>
	  </p>
	  <p id="pMaxLi" style="<?php echo $mod->Type=='div'?'display:none;':'';?>" >
	    <span class='title'>UL内LI的最大行数:</span><br/>
	    <input type="text" name="MaxLi" value="<?php echo $mod->MaxLi;?>" size="40"  />
	  </p>
	  <p >
		<span class="title"><?php echo $lang['msg']['content']?>:</span><br />
		<textarea name="Content" id="Content"  cols="80" rows="12"  ><?php echo htmlspecialchars($mod->Content);?></textarea>
	  </p>

	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" id="btnPost" onclick="return checkInfo();" />
	  </p>
	</form>
	<script type="text/javascript">
function checkInfo(){
  document.getElementById("edit").action="../cmd.php?act=ModulePst";

  if(!$("#edtName").val()){
    alert("<?php echo $lang['error']['72']?>");
    return false
  }
  if(!$("#edtFileName").val()){
    alert("<?php echo $lang['error']['75']?>");
    return false
  }
  if(!$("#edtHtmlID").val()){
    alert("<?php echo $lang['error']['76']?>");
    return false
  }
}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aModuleMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $zbp->host . 'zb_system/image/common/link_32.png';?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>