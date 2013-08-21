<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('Totoro')) {$zbp->ShowError(48);die();}

$blogtitle='Totoro反垃圾评论';

if(count($_POST)>0){


	$zbp->Config('Totoro')->BlackWord_List=Trim(Trim(GetVars('BlackWord_List','POST')),'|');
	$zbp->Config('Totoro')->Op_BlackWord_Audit=GetVars('Op_BlackWord_Audit','POST');
	$zbp->Config('Totoro')->Op_BlackWord_Throw=GetVars('Op_BlackWord_Throw','POST');
	$zbp->Config('Totoro')->Op_Chinese_None=GetVars('Op_Chinese_None','POST');
	$zbp->SaveConfig('Totoro');

	$zbp->SetHint('good','Totoro已保存设置');
	Redirect('./main.php');
}


require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"></div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder">
<tr>
	<th class="td30"><p align='left'><b>选项</b><br><span class='note'></span></p></th>
	<th>
	</th>
</tr>
<tr>
	<td class="td30"><p align='left'><b>没有中文字符直接进入审核</b></p></td>
	<td><input type="text" name="Op_Chinese_None" value="<?php echo $zbp->Config('Totoro')->Op_Chinese_None;?>" class="checkbox" />
	</td>
</tr>
<tr>
	<td class="td30"><p align='left'><b>有N个黑词直接进入审核</b></p></td>
	<td>N=<input type="text" name="Op_BlackWord_Audit" value="<?php echo $zbp->Config('Totoro')->Op_BlackWord_Audit;?>" /> 个黑词
	</td>
</tr>
<tr>
	<td class="td30"><p align='left'><b>有N个黑词直接丢掉</b></p></td>
	<td>N=<input type="text" name="Op_BlackWord_Throw" value="<?php echo $zbp->Config('Totoro')->Op_BlackWord_Throw;?>" /> 个黑词
	</td
</tr>
<tr>
	<td class="td30"><p align='left'><b>黑词列表</b><br><span class='note'>列表为正则表达则,黑词间用|分隔.</span></p></td>
	<td><textarea name="BlackWord_List" style="width:95%;height:400px"><?php echo $zbp->Config('Totoro')->BlackWord_List;?></textarea>
	</td>
</tr>

</table>
	  <hr/>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
	  </p>

	</form>
	<script type="text/javascript">
function changeOptions(i){
	$('input[name^=ZC_]').each(function(){
		var s='radio' + $(this).prop('name');
		$(this).val( $("input[type='radio'][name='"+s+"']").eq(i).val() );
	});
	if(i=='0'){
		$("input[name^='radio']").prop('disabled',true);
		$("input[name='ZC_STATIC_MODE']").val('ACTIVE');
	}else{
		$("input[name^='radio']").prop('disabled',false);
		$("input[name='ZC_STATIC_MODE']").val('REWRITE');
	}

}
	</script>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/Totoro/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>