<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';

ob_clean();

?>
var bloghost = "<?php echo $zbp->host; ?>";
var cookiespath = "<?php echo $zbp->cookiespath; ?>";
var ajaxurl = "<?php echo $zbp->ajaxurl; ?>";
var lang_comment_name_error = "<?php echo $lang['error']['72']; ?>";
var lang_comment_email_error = "<?php echo $lang['error']['29']; ?>";
var lang_comment_content_error = "<?php echo $lang['error']['46']; ?>";

<?php
echo '$(function () {';
echo 'var $cpLogin = $(".cp-login").find("a");';
echo 'var $cpVrs = $(".cp-vrs").find("a");';
echo 'SetCookie("timezone", (new Date().getTimezoneOffset()/60)*(-1));';
echo 'var $addoninfo = GetCookie("addinfo' . str_replace('/','',$zbp->cookiespath) . '");';
echo 'if(!$addoninfo){LoadRememberInfo();return ;}';
echo '$addoninfo = eval("("+$addoninfo+")");';
echo 'if($addoninfo.chkadmin){';
	echo '$(".cp-hello").html("' . $zbp->lang['msg']['welcome'] . ' " + $addoninfo.useralias + " (" + $addoninfo.levelname  + ")");';
	echo 'if ($cpLogin.length == 1 && $cpLogin.html().indexOf("[") > -1) { ';
	echo '$cpLogin.html("[' . $zbp->lang['msg']['admin'] . ']");';
	echo '} else {';
	echo '$cpLogin.html("' . $zbp->lang['msg']['admin'] . '");';
	echo '};';
echo '}';

echo 'if($addoninfo.chkarticle){';
	echo 'if ($cpLogin.length == 1 && $cpVrs.html().indexOf("[") > -1) {';
	echo '$cpVrs.html("[' . $zbp->lang['msg']['new_article'] . ']"); ';
	echo '} else {';
	echo '$cpVrs.html("' . $zbp->lang['msg']['new_article'] . '");';
	echo '};';
	echo '$cpVrs.attr("href", bloghost + "zb_system/cmd.php?act=ArticleEdt");';
echo '}';

echo '});' . "\r\n";

foreach ($GLOBALS['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {$fpname();}

$s = ob_get_clean();
$m = md5($s);

header('Content-Type: application/x-javascript; charset=utf-8');
header('Etag: ' . $m);

if( isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $m ){
	SetHttpStatusCode(304);
	die;
}

$zbp->CheckGzip();
$zbp->StartGzip();
	
echo $s;

die();
?>