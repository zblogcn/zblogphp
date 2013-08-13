<?php
header('Content-type: application/x-javascript; Charset=utf8');  

require '../function/c_system_base.php';
/*
<% If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_JS%>

<%If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_Action%>
*/
//Http304(__FILE__,$zbp->cache->refesh);

ob_clean();
?>
var bloghost="<?php echo $bloghost; ?>";
var cookiespath="<?php echo $cookiespath; ?>";
var str01="<?php echo $lang['error']['72']; ?>";
var str02="<?php echo $lang['error']['29']; ?>";
var str03="<?php echo $lang['error']['46']; ?>";

$(document).ready(function(){ 
	if(GetCookie("username")!=""&&GetCookie("password")!=""){$.getScript(bloghost + "zb_system/cmd.php?act=misc&type=autoinfo",function(){AutoinfoComplete();})}
});

<?php
foreach ($GLOBALS['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {$fpname();}
die();
?>