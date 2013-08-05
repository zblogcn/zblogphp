<?php
#ZBP的第一个插件，ueditor插件


#注册插件
RegisterPlugin("UEditor","ActivePlugin_UEditor");


function ActivePlugin_UEditor() {

	Add_Filter_Plugin('Filter_Plugin_Edit_Begin','ueditor_addscript_begin');

	Add_Filter_Plugin('Filter_Plugin_Edit_End','ueditor_addscript_end');


}


function ueditor_addscript_begin(){
	global $zbp;
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
}


function ueditor_addscript_end(){
	global $zbp;

$s=<<<js
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


function getContent(){
  return editor_api.editor.content.get();
}

function getIntro(){
  return editor_api.editor.intro.get();
}

function setContent(s){
	editor_api.editor.content.put(s);
}

function setIntro(s){
  editor_api.editor.intro.put(s);
}

function editor_init(){

editor_api.editor.content.obj=UE.getEditor('editor_content');
editor_api.editor.intro.obj=UE.getEditor('editor_intro',EditorIntroOption);
editor_api.editor.content.get=function(){return this.obj.getContent()};
editor_api.editor.content.put=function(str){return this.obj.setContent(str)};
editor_api.editor.content.focus=function(str){return this.obj.focus()};
editor_api.editor.intro.get=function(){return this.obj.getContent()};
editor_api.editor.intro.put=function(str){return this.obj.setContent(str)};
editor_api.editor.intro.focus=function(str){return this.obj.focus()};
editor_api.editor.content.obj.ready(function(){\$('#contentready').hide();$('#editor_content').prev().show();sContent=editor_api.editor.content.get();});

editor_api.editor.intro.obj.ready(function(){\$('#introready').hide();$('#editor_intro').prev().show();sIntro=editor_api.editor.intro.get();});

$(document).ready(function(){
	$('#edit').submit(function(){if(editor_api.editor.content.obj.queryCommandState('source')==1) editor_api.editor.content.obj.execCommand('source');
	if(editor_api.editor.intro.obj.queryCommandState('source')==1) editor_api.editor.intro.obj.execCommand('source');}) 
	/*源码模式下保存时必须切换*/
});

}

editor_init();
</script>
js;
	echo $s;
}
?>