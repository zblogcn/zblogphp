<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */
header('Content-Type: application/x-javascript; Charset=utf8');  

require_once '../function/c_system_base.php';

/*
<% If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_JS%>
var bloghost="<%=BlogHost%>";
var cookiespath="<%=CookiesPath()%>";
var str00="<%=BlogHost%>";
var str01="<%=ZC_MSG033%>";
var str02="<%=ZC_MSG034%>";
var str03="<%=ZC_MSG035%>";
var str06="<%=ZC_MSG057%>";
var intMaxLen="<%=ZC_CONTENT_MAX%>";
var strFaceName="<%=ZC_EMOTICONS_FILENAME%>";
var strFaceSize="<%=ZC_EMOTICONS_FILESIZE%>";
var strFaceType="<%=ZC_EMOTICONS_FILETYPE%>";
var strBatchView="";
var strBatchInculde="";
var strBatchCount="";

$(document).ready(function(){ 
	$("img[src*='zb_system/function/c_validcode.asp?name=commentvalid']").css("cursor","pointer").click( function(){$(this).attr("src","<%=BlogHost%>zb_system/function/c_validcode.asp?name=commentvalid"+"&amp;random="+Math.random());});
	sidebarloaded.add(function(){
		if(GetCookie("username")!=""&&GetCookie("password")!=""){$.getScript("<%=BlogHost%>zb_system/function/c_html_js.asp?act=autoinfo",function(){AutoinfoComplete();})}else{AutoinfoComplete();}
	});
	$.getScript("<%=BlogHost%>zb_system/function/c_html_js.asp?act=batch"+unescape("%26")+"view=" + escape(strBatchView)+unescape("%26")+"inculde=" + escape(strBatchInculde)+unescape("%26")+"count=" + escape(strBatchCount),function(){BatchComplete();});
	<%If ZC_SYNTAXHIGHLIGHTER_ENABLE Then Response.Write Response_Plugin_Html_Js_Add_CodeHighLight_Action%>
});

<%=Response_Plugin_Html_Js_Add%>
*/



?>
var bloghost="<?php echo $bloghost; ?>";
var cookiespath="<?php echo $cookiespath; ?>";
<?php die(); ?>