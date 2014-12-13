<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('LargeData')) {$zbp->ShowError(48);die();}

if(count($_POST)>0){
	$zbp->option['ZC_LARGE_DATA'] = (boolean)$_POST['ZC_LARGE_DATA'];	
	$zbp->SaveOption();
	$zbp->SetHint('good');
	Redirect('./main.php');
}

$blogtitle='LargeData';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader2"><?php echo $blogtitle;?></div>
<div class="SubMenu"></div>
  <div id="divMain2">
<form method="post" action="main.php">
<?php
	echo '<table style="padding:0px;margin:0px;width:100%;">';
	echo '<tr><td class="td25"><p><b>开启大数据支持</b></p></td>
	<td><p><input id="ZC_LARGE_DATA" name="ZC_LARGE_DATA" type="text" value="'.$zbp->option['ZC_LARGE_DATA'].'" class="checkbox"/></p></td></tr>';
	echo '</table>';
?>
			  <hr/>
			  <p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p>
		  </form>
大数据模式的优化
<br/>
1.后台统计信息的优化
<br/>
2.数据分页的优化和限制浏览
<br/>
3.文章tag的优化（独立成表并重写分页）
<br/>
4.搜索的限制（限制搜索正文和摘要）
<br/>
5.生成sitemap的分包

	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/LargeData/logo.png';?>");</script>	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>