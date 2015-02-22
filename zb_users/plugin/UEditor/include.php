<?php
#ZBP的第一个插件，ueditor插件


#注册插件
RegisterPlugin("UEditor","ActivePlugin_UEditor");


function ActivePlugin_UEditor() {

	Add_Filter_Plugin('Filter_Plugin_Edit_Begin','ueditor_addscript_begin');

	Add_Filter_Plugin('Filter_Plugin_Edit_End','ueditor_addscript_end');

	Add_Filter_Plugin('Filter_Plugin_Html_Js_Add','ueditor_SyntaxHighlighter_print');

}

function ueditor_SyntaxHighlighter_print(){

	global $zbp;
	if(!$zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'])return ;
	echo "\r\n".'document.writeln("<script src=\'' . $zbp->host .'zb_users/plugin/UEditor/third-party/SyntaxHighlighter/shCore.pack.js\' type=\'text/javascript\'></script><link rel=\'stylesheet\' type=\'text/css\' href=\'' . $zbp->host .'zb_users/plugin/UEditor/third-party/SyntaxHighlighter/shCoreDefault.pack.css\'/>");'."\r\n";
	echo "\r\n$(document).ready(function(){SyntaxHighlighter.highlight();for(var i=0,di;di=SyntaxHighlighter.highlightContainers[i++];){var tds = di.getElementsByTagName('td');for(var j=0,li,ri;li=tds[0].childNodes[j];j++){ri = tds[1].firstChild.childNodes[j];ri.style.height = li.style.height = ri.offsetHeight + 'px';}}});\r\n";

}


function InstallPlugin_UEditor(){

}

function UninstallPlugin_UEditor(){

}

function ueditor_addscript_begin()
{
	global $zbp;
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
	echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
	echo '<style type="text/css">#editor_content{padding:0;height:auto}#editor_intro{padding:0;}</style>';
}


function ueditor_addscript_end()
{
	global $zbp;

$s = <<<js
<script type="text/javascript">

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
editor_api.editor.content.focus=function(){return this.obj.focus()};
editor_api.editor.intro.get=function(){return this.obj.getContent()};
editor_api.editor.intro.put=function(str){return this.obj.setContent(str)};
editor_api.editor.intro.focus=function(){return this.obj.focus()};


editor_api.editor.content.obj.ready(function(){sContent=editor_api.editor.content.get();});
editor_api.editor.intro.obj.ready(function(){sIntro=editor_api.editor.intro.get();});

$(document).ready(function(){
	$('#edit').submit(function(){if(editor_api.editor.content.obj.queryCommandState('source')==1) editor_api.editor.content.obj.execCommand('source');
	if(editor_api.editor.intro.obj.queryCommandState('source')==1) editor_api.editor.intro.obj.execCommand('source');}) 
	/*源码模式下保存时必须切换*/


	if (("http://" + bloghost + "/").indexOf(location.host.toLowerCase()) < 0)
		alert("您设置了域名固化，请使用" + bloghost + "访问或进入后台修改域名，否则图片无法上传。");
});

}
</script>
js;
	echo $s;
}
?>