<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('duoshuo')) {$zbp->ShowError(48);die();}

$blogtitle='多说';

if(count($_POST)>0){


	$zbp->Config('duoshuo')->commoncode=$_POST['commoncode'];	
	$zbp->SaveConfig('duoshuo');

	$zbp->SetHint('good');
	Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"></div>
  <div id="divMain2">
	<form id="edit" name="edit" method="post" action="#">
<input id="reset" name="reset" type="hidden" value="" />
<table border="1" class="tableFull tableBorder tableBorder-thcenter">
<tr>
	<th class="td25"></th>
	<th>设置</th>
</tr>
<tr>
<td><p align='left'><b>·填入从多说网站获取的通用代码</b><br/><span class='note'></span></p></td>
<td><p><textarea name='commoncode' style="width:95%;height:400px;"><?php echo htmlspecialchars($zbp->Config('duoshuo')->commoncode);?></textarea></p></td>
</tr>

</table>
	  <hr/>
	  <p>
		<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" />
	  </p>
	</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/duoshuo/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>