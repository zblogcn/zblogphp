<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
header('Content-Type: application/x-javascript; Charset=utf8');  

require '../function/c_system_base.php';
/*
<% If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_JS%>

<%If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_Action%>
*/
ob_clean();
?>
var bloghost="<?php echo $bloghost; ?>";
var cookiespath="<?php echo $cookiespath; ?>";

$(document).ready(function(){ 
	if(GetCookie("username")!=""&&GetCookie("password")!=""){$.getScript(bloghost + "zb_system/cmd.php?act=misc&type=autoinfo",function(){AutoinfoComplete();})}
});

<?php
foreach ($GLOBALS['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {$fpname();}
die();
?>