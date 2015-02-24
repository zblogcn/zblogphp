<?php
$editor4comment_closeerror = false;
$editor4comment_commenthtml = '';

RegisterPlugin("editor4comment", "ActivePlugin_editor4comment");

function ActivePlugin_editor4comment() {
	Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'editor4comment_html_js_add');
	Add_Filter_Plugin('Filter_Plugin_PostComment_Core', 'editor4comment_postcomment_core');
	//Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'editor4comment_zbp_showerror');
	Add_Filter_Plugin('Filter_Plugin_Comment_Save', 'editor4comment_comment_save');
}

function InstallPlugin_editor4comment() {

}

function UninstallPlugin_editor4comment() {

}

function editor4comment_postcomment_core(&$comment) {

	global $bloghost;

	// 过滤XSS
	require 'xsshtml.class.php';
	$xss = new XssHtml($comment->Content);
	$xss->setHost($bloghost);
	$xss->setDefaultImg($bloghost . 'zb_system/image/admin/error.png');
	$comment->Content = $xss->getHtml();

	// 对评论进行备份
	global $editor4comment_commenthtml;
	$editor4comment_commenthtml = $comment->Content;
	//$editor4comment_commenthtml = substr($editor4comment_commenthtml, 0, 1000);
	$editor4comment_commenthtml = trim($editor4comment_commenthtml);

	// 声明变量，标记目前状态为评论添加//，要求关闭错误提示
	$GLOBALS['editor4comment_closeerror'] = true;

}

// 评论为纯表情的时候需要该函数，注释掉
/*function editor4comment_zbp_showerror($idortext, $file = null, $line = null) {

// 关闭错误提示并恢复评论
if ((int)$idortext == 46 && $GLOBALS['editor4comment_closeerror'])
{
$GLOBALS['Filter_Plugin_Zbp_ShowError']['editor4comment_zbp_showerror'] = PLUGIN_EXITSIGNAL_RETURN;
return true;
}

}*/

function editor4comment_comment_save(&$comment) {

	// 恢复评论为未过滤HTML代码前的数据
	if ($GLOBALS['editor4comment_closeerror']) {
		$comment->Content = $GLOBALS['editor4comment_commenthtml'];
	}

	$GLOBALS['editor4comment_commenthtml'] = '';
	$GLOBALS['editor4comment_closeerror'] = false;

}

function editor4comment_html_js_add() {
	global $zbp;
	?>
window.UMEDITOR_CONFIG = {UMEDITOR_HOME_URL : bloghost + "zb_users/plugin/editor4comment/ueditor/",toolbar: ['bold italic underline forecolor link unlink | emotion drafts'],minWidth: parseInt('<?php echo ((int) $zbp->Config('editor4comment')->minWidth == 0 ? 500 : $zbp->Config('editor4comment')->minWidth)?>'),minHeight: parseInt('<?php echo ((int) $zbp->Config('editor4comment')->minHeight == 0 ? 500 : $zbp->Config('editor4comment')->minHeight)?>')};
;$(document).ready(function(){
		<?php //UE压缩过了，再找哪个option好麻烦，直接UMEDITOR_CONFIG方便?>
	window.COMMENT = UM.getEditor('txaArticle');;
});
<?php
echo "\r\n" . ';document.writeln("<script src=\'' . $zbp->host . 'zb_users/plugin/editor4comment/ueditor/umeditor.min.js\' type=\'text/javascript\'></script><link rel=\'stylesheet\' type=\'text/css\' href=\'' . $zbp->host . 'zb_users/plugin/editor4comment/ueditor/themes/default/css/umeditor.min.css\'/>");' . "\r\n";

}
