<?php

//注册插件
RegisterPlugin("KindEditor", "ActivePlugin_KindEditor");

function ActivePlugin_KindEditor()
{
    Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'KindEditor_addscript_begin');
    Add_Filter_Plugin('Filter_Plugin_Edit_End', 'KindEditor_addscript_end');
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'CodeHighLight_print_KindEditor');
}

function KindEditor_addscript_begin()
{
    global $zbp;

    echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/KindEditor/kindeditor/kindeditor.js"></script>';
    echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/KindEditor/kindeditor/lang/zh-CN.js"></script>';
}

function CodeHighLight_print_KindEditor()
{
    global $zbp;
    if ($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']) {
        echo 'document.writeln("<script src=\''.$zbp->host.'zb_users/plugin/KindEditor/kindeditor/plugins/code/prettify.js\' type=\'text/javascript\'></script><link rel=\'stylesheet\' type=\'text/css\' href=\''.$zbp->host.'zb_users/plugin/KindEditor/kindeditor/plugins/code/prettify.css\'/>");'."\n";
        echo "$(document).ready(function(){prettyPrint();});\n";
    }
}

function KindEditor_addscript_end()
{
    global $zbp;
    $zbphost = $zbp->host;
    $s = <<<script
<script type="text/javascript">
function editor_init(){
	editor_api.editor.content.get=function(){return this.obj.html()};
	editor_api.editor.content.put=function(str){return this.obj.html(str)};
	editor_api.editor.content.insert=function(str){return this.obj.insertHtml(str)};
	editor_api.editor.content.focus=function(){return this.obj.focus()};
	editor_api.editor.intro.get=function(){return this.obj.html()};
	editor_api.editor.intro.put=function(str){return this.obj.html(str)};
	editor_api.editor.intro.focus=function(){return this.obj.focus()};
	KindEditor.ready(function(K) {\$('#contentready').hide();
		editor_api.editor.content.obj = K.create('#editor_content',{uploadJson:'{$zbphost}zb_users/plugin/KindEditor/kindeditor/php/upload_json.php',fileManagerJson:'{$zbphost}zb_users/plugin/KindEditor/kindeditor/php/file_manager_json.php',allowFileManager : true,formatUploadUrl : false,width : '100%',height : '450px',emoticonsPath :'{$zbphost}zb_users/emotion/',	allowPreviewEmoticons : false,items : [ 'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink', '|', 'about']});
		sContent=editor_api.editor.content.get();
		$('#introready').hide();
		editor_api.editor.intro.obj = K.create('#editor_intro',	{items : ['source', '|','bold','italic','underline','fontname','fontsize','forecolor','hilitecolor','link']});
		$('#editor_intro').prev().removeAttr('style');
		sIntro=editor_api.editor.intro.get();
	});
}
</script>
script;

    echo $s;
}
