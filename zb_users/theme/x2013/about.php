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
?>
<style>p{line-height:1.5em;padding:0.5em 0;}</style>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle; ?></div>
  	<div class="SubMenu"><?php x2013_SubMenu(2); ?></div>
	<div id="divMain2">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
	  <tr>
		<th width="20%">
	<ol>
  <li>1、本主题移植自WordPress同名主题<a href="http://lisizhang.com/x2013" target="_blank">X2013</a>,主题涉及的图片、CSS等版权归原作者<a href="http://lisizhang.com/" target="_blank">菠萝</a>所有，同时感谢好友菠萝为本主题所做的设计。</li>
  <li>2、本主题Z-Blog、Z-BlogPHP版本版权归<a href="http://imzhou.com"  target="_blank">未寒</a>所有，包括但不限于主题附带插件等。</li>
  <li>3、本主题自带一个设置插件，可设置博客前台部分显示资料，如果设置内容为空则不会显示该项内容。</li>
  <li>4、当切换为其他主题，即禁用本主题时，使用本主题时产生的所有配置文件将自动删除。</li>
</ol>	
		</th>
	  </tr>
	</table>
	</div>
</div>
<script type="text/javascript">ActiveTopMenu("topmenu_x2013");</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>