<?php
header('Content-type: application/x-javascript; Charset=utf-8');  

require '../function/c_system_base.php';

$zbp->Load();

//Http304(__FILE__,$zbp->cache->refesh);

ob_clean();

?>
var bloghost="<?php echo $bloghost; ?>";
var cookiespath="<?php echo $cookiespath; ?>";
var str01="<?php echo $lang['error']['72']; ?>";
var str02="<?php echo $lang['error']['29']; ?>";
var str03="<?php echo $lang['error']['46']; ?>";

<?php
echo '$(document).ready(function(){';

if ($zbp->CheckRights('admin')){
	echo "$('.cp-hello').html('" . $zbp->lang['msg']['welcome'] . ' ' . $zbp->user->Name .  " ("  . $zbp->user->LevelName  . ")');";	
	echo "$('.cp-login').find('a').html('[" . $zbp->lang['msg']['admin'] . "]');";
}
if ($zbp->CheckRights('ArticleEdt')){
	echo "$('.cp-vrs').find('a').html('[" . $zbp->lang['msg']['new_article'] . "]');";
	echo "$('.cp-vrs').find('a').attr('href','" . $zbp->host . "zb_system/cmd.php?act=ArticleEdt');";
}
echo '});';

foreach ($GLOBALS['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {$fpname();}

die();

?>