<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('AppBuy')) {$zbp->ShowError(48);die();}
$blogtitle='AppBuy';


require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"><?php AppBuy_SubMenu(0);?></div>
  <div id="divMain2">
	<table border="1" class="tableFull tableBorder tableBorder-thcenter">
		<tbody> 
		<tr class="color1"> 
			<th class="td5 tdCenter">编号</th> 
			<th class="td15 tdCenter">订单号</th> 
			<th class="td10 tdCenter">类别</th> 
			<th class="td15 tdCenter">应用ID</th> 
			<th class="td5">金额</th> 
			<th class="td15 tdCenter">创建时间</th> 
			<th class="td15 tdCenter">支付时间</th> 
			<th class="td5">状态</th> 
			<th class="td5 tdCenter">操作</th> 
		</tr> 
		<tr class="color3">
			<td>17</td>
			<td>分类二</td>
			<td>admin</td>
			<td>admin</td>
			<td>￥</td>
			<td>2013-08-29 22:10:09</td>
			<td>2013-08-29 22:10:09</td>
			<td>已审核</td>
			<td>删除</td>
		</tr>
		</tbody>
	</table>

  </div>
</div>
<script type="text/javascript">ActiveLeftMenu("aAppBuy");</script>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>