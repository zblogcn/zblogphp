<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('x2013')) {
    $zbp->ShowError(48);
    die();
}
$blogtitle = '主题配置';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if (isset($_POST['PostAdHeader'])) {
    $zbp->Config('x2013')->PostAdHeader = $_POST['PostAdHeader'];
    $zbp->Config('x2013')->PostAdFooter = $_POST['PostAdFooter'];
    $zbp->SaveConfig('x2013');
    $zbp->ShowHint('good');
}
?>
<style>
input.text{background:#FFF;border:1px double #aaa;font-size:1em;padding:0.25em;}
p{line-height:1.5em;padding:0.5em 0;}
</style>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle; ?></div>
  	<div class="SubMenu"><?php x2013_SubMenu(3); ?></div>
	<div id="divMain2">
	<form id="form1" name="form1" method="post">	
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">广告位</p></th>
    <th width='70%'><p align="center">内容</p></th>
  </tr>
  <tr>
    <td  rowspan="2" colspan="1"><b><label for="PostAdHeader"><p align="center">文章开始广告位(建议宽度880px)</p></label></b></td>
    <td><p align="left"><textarea name="PostAdHeader" type="text" id="PostAdHeader" style="width: 80%;"><?php echo $zbp->Config('x2013')->PostAdHeader; ?></textarea></p></td>
    
  </tr>
   <tr><td><?php echo $zbp->Config('x2013')->PostAdHeader; ?></td></tr>
  <tr>
    <td rowspan="2" colspan="1"><b><label for="PostAdFooter"><p align="center">文章结束广告位(建议宽度880px)</p></label></b></td>
    <td><p align="left"><textarea name="PostAdFooter" type="text" id="PostAdFooter" style="width: 80%;"><?php echo $zbp->Config('x2013')->PostAdFooter; ?></textarea></p></td>
  </tr>
   <tr><td><?php echo $zbp->Config('x2013')->PostAdFooter; ?></tr>
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>

    </form>
<br />

	</div>
</div>
<script type="text/javascript">ActiveTopMenu("topmenu_x2013");</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>