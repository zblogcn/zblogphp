<?php


#注册插件
RegisterPlugin("KindEditor","ActivePlugin_KindEditor");


function ActivePlugin_KindEditor() {

	Add_Filter_Plugin('Filter_Plugin_Edit_Begin','KindEditor_addscript_begin');

	Add_Filter_Plugin('Filter_Plugin_Edit_End','KindEditor_addscript_end');


}


//Response_Plugin_Html_Js_Add_CodeHighLight_JS="document.writeln(""<script src='"&BlogHost&"zb_users/plugin/kindeditor/kindeditor/plugins/code/prettify.js' type='text/javascript'></script><link rel='stylesheet' type='text/css' href='"&BlogHost&"zb_users/plugin/kindeditor/kindeditor/plugins/code/prettify.css'/>"");"
//Response_Plugin_Html_Js_Add_CodeHighLight_Action="prettyPrint();"

function KindEditor_addscript_begin(){
	global $zbp;

	echo '<script type="text/javascript" src="' . $zbp->host .'zb_users/plugin/KindEditor/kindeditor/kindeditor.js"></script>';
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/KindEditor/kindeditor/lang/zh_CN.js"></script>';

}



function KindEditor_addscript_end(){

$s=<<<script
<script type="text/javascript">

var editor_api={
	editor:	{
		content:{
			obj:{},
			get:function(){return ""},
			put:function(){return ""},
			focus:function(){return ""}
		},
		intro:{
			obj:{},
			get:function(){return ""},
			put:function(){return ""},
			focus:function(){return ""}
		}
	}
}

var EditorIntroOption = {
	toolbars:[['Source', 'bold', 'italic','link','insertimage','Undo', 'Redo']],
	autoHeightEnabled:false,
	initialFrameHeight:200
}

function editor_init(){
	editor_api.editor.content.get=function(){return this.obj.html()};
	editor_api.editor.content.put=function(str){return this.obj.html(str)};
	editor_api.editor.content.focus=function(str){return this.obj.focus()};
	editor_api.editor.intro.get=function(){return this.obj.html()};
	editor_api.editor.intro.put=function(str){return this.obj.html(str)};
	editor_api.editor.intro.focus=function(str){return this.obj.focus()};
	KindEditor.ready(function(K) {\$('#contentready').hide();
		editor_api.editor.content.obj = K.create('#editor_txt',{uploadJson:'"&BlogHost & "zb_users/plugin/kindeditor/kindeditor/asp/upload_json.asp',fileManagerJson:'"&BlogHost & "zb_users/plugin/kindeditor/kindeditor/asp/file_manager_json.asp',allowFileManager : false,formatUploadUrl : false,width : '980px',height : '450px',emoticonsPath :'"&BlogHost & "zb_users/EMOTION/',	allowPreviewEmoticons : false,items : [ 'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink', '|', 'about']});
		$('#editor_txt').prev().removeAttr('style');
		sContent=editor_api.editor.content.get();
		$('#introready').hide();
		editor_api.editor.intro.obj = K.create('#editor_txt2',	{items : ['source', '|','bold','italic','underline','fontname','fontsize','forecolor','hilitecolor','link']});
		$('#editor_txt2').prev().removeAttr('style');
		sIntro=editor_api.editor.intro.get();
	});
}
editor_init();
</script>
script;

echo $s;

}

?>