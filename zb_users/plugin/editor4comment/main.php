<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('editor4comment')) {$zbp->ShowError(48);die();}

$blogtitle = 'editor4comment';
require $blogpath . 'zb_system/admin/admin_header.php';

if (GetVars('act', 'GET') == 'save') {
	$zbp->Config('editor4comment')->minHeight = GetVars('e4c_minHeight', 'POST');
	$zbp->Config('editor4comment')->minWidth = GetVars('e4c_minWidth', 'POST');
	$zbp->SaveConfig('editor4comment');
	$zbp->SetHint('good');
}
?>
<link href="ueditor/themes/default/css/umeditor.min.css" type="text/css" rel="stylesheet">
<script type="text/javascript">window.UMEDITOR_CONFIG = {UMEDITOR_HOME_URL : bloghost + "zb_users/plugin/editor4comment/ueditor/",toolbar: ['bold italic underline forecolor ','link unlink | emotion drafts'],minWidth: parseInt('<?php echo ((int) $zbp->Config('editor4comment')->minWidth == 0 ? 500 : $zbp->Config('editor4comment')->minWidth)?>'),minHeight: parseInt('<?php echo ((int) $zbp->Config('editor4comment')->minHeight == 0 ? 500 : $zbp->Config('editor4comment')->minHeight)?>')};
</script>
<script type="text/javascript" charset="utf-8" src="ueditor/umeditor.min.js"></script>

<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
	<form action="main.php?act=save" method="post">
		<table border="1" class="tableFull tableBorder">
			<thead>
				<tr>
					<th>配置名</th>
					<th>配置项</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>最小高度</td>
					<td><input type="text" name="e4c_minHeight" style="width:80%" value="<?php echo $zbp->Config('editor4comment')->minHeight;?>"></td>
				</tr>
				<tr style="display:none">
					<td>最小宽度</td>
					<td><input type="text" name="e4c_minWidth" style="width:80%" value="<?php echo $zbp->Config('editor4comment')->minWidth;?>"></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" class="button" value="提交" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<br/>
<textarea id="myEditor" style="width:90%">
这是在前台显示的效果。
&lt;p&gt;表情包请复制到zb_users/emotion文件夹内，编辑器会自动加载。&lt;/p&gt;

&lt;p&gt;编辑器组件被疯狂阉割，开关配置是不起作用的。&lt;/p&gt;
&lt;p&gt;所有的链接都会被加上rel="nofollow"和target="_blank"，&lt;/p&gt;
&lt;p&gt;图片仅限当前站点内图片可使用，非当前站点会直接加上域名。&lt;/p&gt;

</textarea>
<?php
/*$str = '<img src="http://test.zsxsoft.com/zbphp/zb_system/image/admin/warning.png"/><img src="http://bbs.zblogcn.com/uc_server/avatar.php?uid=76496&amp;size=small" style="word-wrap: break-word; border-style: solid; border-width: 1px; border-color: rgb(242, 242, 242) rgb(205, 205, 205) rgb(205, 205, 205) rgb(242, 242, 242); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; padding: 2px; width: 48px; height: 48px; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;"></a></div><p style="word-wrap: break-word; margin-top: 0px; margin-bottom: 0px; text-align: right;">';
require('xsshtml.class.php');
$xss = new XssHtml($str);
$xss->m_host = $bloghost;
$xss->m_defaultsrc = $bloghost . 'zb_system/image/admin/error.png';
$str = $xss->getHtml();
echo $str;*/
?>
<script type="text/javascript">
$(document).ready(function(){UM.getEditor('myEditor');})
</script>
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>