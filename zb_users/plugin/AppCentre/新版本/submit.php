<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require dirname(__FILE__) . '/function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心-提交应用';

$s='';
$t='';

$app=New App;
$app->LoadInfoByXml($_GET['type'],$_GET['id']);

$t['id']=$app->id;
$t['author']=$app->author_name;
$t['modified']=$app->modified;

$t=json_encode($t);

if($_SERVER['REQUEST_METHOD']=='GET'){
$s=Server_Open('submitpre');
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$url=Server_Open('submit');
	if(substr($url,0,4)=='http'){
		Redirect($url);
	}else{
		echo '<script type="text/javascript">alert(\''.$url.'\')</script>';
	}
}

if(!$s)$s='{"id":"未提交","author":"未提交","modified":"未提交"}';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus('');?></div>
  <div id="divMain2">


<form method="post" action="">
<table class="tableFull tableBorder tableBorder-thcenter">
<tr><th colspan="2">&nbsp;拟提交发布或更新的应用信息<a href="<?php echo GetVars('type', 'GET') == 'plugin' ? 'plugin' : 'theme' ?>_edit.php?id=<?php  echo $app->id?>">[编辑]</a></th></tr>

<tr><td class="td30"><p><b>· 应用ID</b></p></td><td><p>&nbsp;<input id="local_app_id" name="local_app_id" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
<tr><td><p><b>· 应用文件名</b></p></td><td><p>&nbsp;<input id="local_app_user" name="local_app_user" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
<tr><td><p><b>· 最后更新日期</b></p></td><td><p>&nbsp;<input id="local_app_date" name="local_app_date" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
</table>


<table class="tableFull tableBorder tableBorder-thcenter">
<tr><th colspan="2">&nbsp;“Z-Blog应用中心”目标应用的相关信息</th></tr>
<tr><td class="td30"><p><b>· 应用发布ID</b></p></td><td><p>&nbsp;<input id="zblog_app_id" name="zblog_app_id" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
<tr><td><p><b>· 应用提交用户</b></p></td><td><p>&nbsp;<input id="zblog_app_user" name="zblog_app_user" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
<tr><td><p><b>· 最后更新日期</b></p></td><td><p>&nbsp;<input id="zblog_app_date" name="zblog_app_date" style="width:550px;"  type="text" value="" readonly /></p></td></tr>
</table>
<script type="text/javascript">
var jsoninfo=eval(<?php echo $t;?>);
$("#local_app_id").val(jsoninfo.id);
$("#local_app_user").val(jsoninfo.author);
$("#local_app_date").val(jsoninfo.modified);

var jsoninfo=eval(<?php echo $s;?>);
$("#zblog_app_id").val(jsoninfo.id);
$("#zblog_app_user").val(jsoninfo.author);
$("#zblog_app_date").val(jsoninfo.modified);
</script>


<p> 提示:金牌开发者、银牌开发者、铜牌开发者和铁牌开发者只能更新和提交自己的应用,白金开发者可以更新和提交所有应用.</p>
<p><br/><input type="submit" class="button" value="提交" id="btnPost" onclick='return confirm("您确定要提交吗？")' /></p><p>&nbsp;</p>


</form>


	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>