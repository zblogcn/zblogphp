<%
Response_Plugin_Html_Js_Add_CodeHighLight_JS="document.writeln(""<script src='"&BlogHost&"zb_users/plugin/kindeditor/kindeditor/plugins/code/prettify.js' type='text/javascript'></script><link rel='stylesheet' type='text/css' href='"&BlogHost&"zb_users/plugin/kindeditor/kindeditor/plugins/code/prettify.css'/>"");"
Response_Plugin_Html_Js_Add_CodeHighLight_Action="prettyPrint();"
'注册插件
Call RegisterPlugin("KindEditor","ActivePlugin_KindEditor")
'挂口部分
Function ActivePlugin_KindEditor()
	Call Add_Action_Plugin("Action_Plugin_Edit_Form","KindEditor()")
End Function

Sub KindEditor()
	Response_Plugin_Edit_Article_Header="<script src="""&BlogHost & "zb_users/plugin/kindeditor/kindeditor/kindeditor.js""></script><script src="""&BlogHost & "zb_users/plugin/kindeditor/kindeditor/lang/zh_CN.js""></script>"
	Response_Plugin_Edit_Article_EditorInit="function editor_init(){editor_api.editor.content.get=function(){return this.obj.html()};editor_api.editor.content.put=function(str){return this.obj.html(str)};editor_api.editor.content.focus=function(str){return this.obj.focus()};editor_api.editor.intro.get=function(){return this.obj.html()};editor_api.editor.intro.put=function(str){return this.obj.html(str)};editor_api.editor.intro.focus=function(str){return this.obj.focus()};KindEditor.ready(function(K) {$('#contentready').hide();editor_api.editor.content.obj = K.create('#editor_txt',{uploadJson:'"&BlogHost & "zb_users/plugin/kindeditor/kindeditor/asp/upload_json.asp',fileManagerJson:'"&BlogHost & "zb_users/plugin/kindeditor/kindeditor/asp/file_manager_json.asp',allowFileManager : false,formatUploadUrl : false,width : '980px',height : '450px',emoticonsPath :'"&BlogHost & "zb_users/EMOTION/',	allowPreviewEmoticons : false,items : [ 'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink', '|', 'about']});$('#editor_txt').prev().removeAttr('style');sContent=editor_api.editor.content.get();$('#introready').hide();editor_api.editor.intro.obj = K.create('#editor_txt2',	{items : ['source', '|','bold','italic','underline','fontname','fontsize','forecolor','hilitecolor','link']});$('#editor_txt2').prev().removeAttr('style');sIntro=editor_api.editor.intro.get();});}"
End Sub

%>