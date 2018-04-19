<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('HeartVote')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = "用“心”打分";

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader2"><?php echo $blogtitle; ?></div>
<div class="SubMenu"></div>
  <div id="divMain2">
	<form id="edit" name="edit" method="post" action="#">
<p>使用说明：</p>
	  <p>
将 <b style="font-size:1.5em;">{$article.HeartVote()}</b> 标签放入你当前主题的<b>post-single.php<b>模板里的合适的位置，并在后台点[更新缓存]编译模板即可生效。
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
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/HeartVote/logo.png'; ?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>