<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('editor4comment')) {$zbp->ShowError(48);die();}

$blogtitle='editor4comment';
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<link href="ueditor/themes/default/css/umeditor.min.css" type="text/css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="ueditor/umeditor.min.js"></script>

<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
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