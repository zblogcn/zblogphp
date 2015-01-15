<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';

$zbp->Verify();

ob_clean();

header('Content-Type: application/x-javascript; charset=utf-8');
?>
var bloghost="<?php echo $zbp->host; ?>";
var cookiespath="<?php echo $zbp->cookiespath; ?>";
var ajaxurl="<?php echo $zbp->ajaxurl; ?>";
var lang_comment_name_error="<?php echo $lang['error']['72']; ?>";
var lang_comment_email_error="<?php echo $lang['error']['29']; ?>";
var lang_comment_content_error="<?php echo $lang['error']['46']; ?>";

<?php
echo '$(document).ready(function(){';

if ($zbp->CheckRights('admin')){
	echo "$('.cp-hello').html('" . $zbp->lang['msg']['welcome'] . ' ' . $zbp->user->Name .  " ("  . $zbp->user->LevelName  . ")');";
	echo "if($('.cp-login').find('a').length==1 && $('.cp-login').find('a').html().indexOf('[')>-1)$('.cp-login').find('a').html('[" . $zbp->lang['msg']['admin'] . "]');else $('.cp-login').find('a').html('" . $zbp->lang['msg']['admin'] . "');";
}
if ($zbp->CheckRights('ArticleEdt')){
	echo "if($('.cp-login').find('a').length==1 && $('.cp-vrs').find('a').html().indexOf('[')>-1)$('.cp-vrs').find('a').html('[" . $zbp->lang['msg']['new_article'] . "]');else $('.cp-vrs').find('a').html('" . $zbp->lang['msg']['new_article'] . "');";
	echo "$('.cp-vrs').find('a').attr('href','" . $zbp->host . "zb_system/cmd.php?act=ArticleEdt');";
}

	echo "SetCookie('timezone',(new Date().getTimezoneOffset()/60)*(-1));";

if ($zbp->user->ID==0){
	echo "LoadRememberInfo();";
}

echo '});' . "\r\n";

foreach ($GLOBALS['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {$fpname();}

die();

?>