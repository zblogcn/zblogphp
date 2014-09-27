<?php
require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->CheckGzip();
$zbp->Load();
header('Content-Type: text/html; charset=utf-8');

$action='ModuleEdt';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6,__FILE__,__LINE__);die();}

$mod=new Module;

if(isset($_POST['source'])){
	if(GetVars('source','POST')=='theme'){
		$mod->Name=GetVars('filename','POST');
		$mod->FileName=GetVars('filename','POST');
		$mod->HtmlID=GetVars('filename','POST');
		$mod->Source='theme';
		if($mod->FileName){
			$mod->Content=file_get_contents($zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $mod->FileName . '.php');
		}
	}elseif(isset($_POST['filename'])){
		$array=$zbp->GetModuleList(
			array('*'),
			array(array('=','mod_FileName',GetVars('filename','POST'))),
			null,
			array(1),
			null
		);
		if(count($array)==0){
			$zbp->ShowError(69);
		}
		$mod =$array[0];
	}
}

if($mod->Type=='ul'){
	$mod->Content=str_replace("</li>", "</li>\r\n", $mod->Content);
}

$islock='';
$ishide='';

if($mod->Source=='system'||$mod->Source=='theme'){
	$islock='readonly';
}

if($mod->Source=='theme'){
	$ishide='style="display:none;"';
	if($mod->FileName==''){
		$islock='';
		$mod->Name='newmodule';
		$mod->HtmlID='newmodule';
	}
}
	?>
<form id="moduleedit" name="moduleedit">
	<input id="edtID" name="ID" type="hidden" value="<?php echo $mod->ID;?>" />
	<input id="edtSource" name="Source" type="hidden" value="<?php echo $mod->Source;?>" />
	<div class="form-group" <?php echo $ishide; ?>>
		<label class="title" for="edtName"><?php echo $zbp->lang['msg']['name']?></label>
		<input id="edtName" class="edit" size="40" name="Name" maxlength="50" type="text" value="<?php echo $mod->Name;?>" /> <span class="star">*</span>
	</div>
	<div class="form-group" <?php echo $ishide; ?>>
		<label class="title" for="IsHideTitle"><?php echo $zbp->lang['msg']['hide_title']?></label>
		<input id="IsHideTitle" name="IsHideTitle" class="checkbox"  type="text" value="<?php echo $mod->IsHideTitle;?>"/>
	</div>
	<div class="form-group">
		<label class="title" for="edtFileName"><?php echo $zbp->lang['msg']['filename']?></label>
		<input id="edtFileName"  class="edit" size="40" name="FileName" type="text" value="<?php echo $mod->FileName;?>" <?php echo $islock; ?>/> <span class="star">*</span>
	</div>
	<div class="form-group" <?php echo $ishide; ?>>
		<label class="title" for="edtHtmlID"><?php echo $zbp->lang['msg']['htmlid']?></label>
		<input id="edtHtmlID" class="edit" size="40" name="HtmlID" type="text" value="<?php echo $mod->HtmlID;?>" /> <span class="star">*</span>
	</div>
	<div class="form-group"  <?php echo $ishide; ?>>
		<label class="title" for="Type"><?php echo $zbp->lang['msg']['type']?></label>
		<label><input name="Type" type="radio" value="div" onclick="$('#max-line').hide();" <?php echo $mod->Type=='div'?'checked':'';?>/>混合内容 </label>
		<label><input name="Type" type="radio" value="ul" onclick="$('#max-line').show();" <?php echo $mod->Type=='ul'?'checked':'';?>/>列表</label>
	</div>
	<div class="form-group" id="max-line" <?php echo $mod->Type=="div"?"style='display:none;'":"";?>>
		<label class="title" for="MaxLi"><?php echo $zbp->lang['msg']['max_li_in_ul']?></label>
		<input id="MaxLi" name="MaxLi" type="number" size="20" min="0" value="<?php echo $mod->MaxLi;?>" />
	</div>
	<?php
	if($mod->FileName=='catalog'){
	?>
	<div  class="form-group" id="catalog-style">
		<label class="title" for="catalog_style"><?php echo $zbp->lang['msg']['style']?></label>
		<label><input name="catalog_style" type="radio" value="0" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE']=='0'?'checked="checked"':'';?> /><?php echo $zbp->lang['msg']['catalog_style_normal']?> </label>
		<label><input name="catalog_style" type="radio" value="1" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE']=='1'?'checked="checked"':'';?> /><?php echo $zbp->lang['msg']['catalog_style_tree']?> </label>
		<label><input name="catalog_style" type="radio" value="2" <?php echo $zbp->option['ZC_MODULE_CATALOG_STYLE']=='2'?'checked="checked"':'';?> /><?php echo $zbp->lang['msg']['catalog_style_ul']?> </label>
	</div>
	<?php
	}
	?>
	<div class="form-group">
		<label class="title" for="Content"><?php echo $zbp->lang['msg']['content']?></label>
		<textarea name="Content" id="Content"  cols="78" rows="10"  ><?php echo htmlspecialchars($mod->Content);?></textarea>
	</div>
	<div class="form-group">
		<label class="title" for="NoRefresh"><?php echo $zbp->lang['msg']['no_refresh_content']?></label>
		<input type="text" id="NoRefresh" name="NoRefresh" class="checkbox" value="<?php echo $mod->NoRefresh;?>"/>
	</div>
</form>
<?php
RunTime();
?>