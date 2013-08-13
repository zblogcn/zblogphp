<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('STACentre')) {$zbp->ShowError(68);die();}

$blogtitle='静态管理中心';

if(count($_POST)>0){

	Redirect('./list.php');
}





require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"> <a href="main.php"><span class="m-left">配置页面</span></a><a href="list.php"><span class="m-left m-now">ReWrite规则</span></a><a href="help.php"><span class="m-right">帮助</span></a> </div>
  <div id="divMain2" class="edit category_edit">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />

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
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>