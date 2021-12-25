<?php

//ZBP的第一个插件，ueditor插件

//注册插件
RegisterPlugin("UEditor", "ActivePlugin_UEditor");

function ActivePlugin_UEditor()
{
    Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'ueditor_addscript_begin');
    Add_Filter_Plugin('Filter_Plugin_Edit_End', 'ueditor_addscript_end');
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'ueditor_SyntaxHighlighter_print');
}

function ueditor_SyntaxHighlighter_print()
{
    global $zbp;
    if (!$zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']) {
        return;
    }

    echo "\r\n" . 'document.writeln("<script src=\'' . $zbp->host . 'zb_users/plugin/UEditor/third-party/prism/prism.js\' type=\'text/javascript\'><\/script><link rel=\'stylesheet\' type=\'text/css\' href=\'' . $zbp->host . 'zb_users/plugin/UEditor/third-party/prism/prism.css\'/>");';
    echo '$(function(){var compatibility={as3:"actionscript","c#":"csharp",delphi:"pascal",html:"markup",xml:"markup",vb:"basic",js:"javascript",plain:"markdown",pl:"perl",ps:"powershell"};var runFunction=function(doms,callback){doms.each(function(index,unwrappedDom){var dom=$(unwrappedDom);var codeDom=$("<code>");if(callback)callback(dom);var languageClass="prism-language-"+function(classObject){if(classObject===null)return"markdown";var className=classObject[1];return compatibility[className]?compatibility[className]:className}(dom.attr("class").match(/prism-language-([0-9a-zA-Z]+)/));codeDom.html(dom.html()).addClass("prism-line-numbers").addClass(languageClass);dom.html("").addClass(languageClass).append(codeDom)})};runFunction($("pre.prism-highlight"));runFunction($(\'pre[class*="brush:"]\'),function(preDom){var original;if((original=preDom.attr("class").match(/brush:([a-zA-Z0-9\#]+);/))!==null){preDom.get(0).className="prism-highlight prism-language-"+original[1]}});Prism.highlightAll()});';
    echo "\r\n";
}

function InstallPlugin_UEditor()
{
}

function UninstallPlugin_UEditor()
{
}

function ueditor_addscript_begin()
{
    global $zbp;
    echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
    echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    echo '<style type="text/css">#editor_content{height:auto}</style>';
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

    function addButton(id){
        var s=this;
        UE.registerUI(s.name, function(editor, uiName) {
            return new UE.ui.Button({
                name: uiName,
                title: uiName,
                cssRules: "background: rgba(0, 0, 0, 0) url("+s.icon+") no-repeat center / 16px 16px !important;",
                onclick: function() {
                    s.callback.call(editor)
                }
            });
        },void 0,id);
    }
    
    typeof contentBarBtn === 'undefined' || $.each(contentBarBtn, function(index, obj){
        UEDITOR_CONFIG["toolbars"][0].push(obj.name);
        addButton.call(obj,'editor_content');
    })
    
    typeof introBarBtn === 'undefined' || $.each(introBarBtn, function(index, obj){
        EditorIntroOption.toolbars[0].push(obj.name);
        addButton.call(obj,'editor_intro');
    })
        
    editor_api.editor.content.obj=UE.getEditor('editor_content');
    editor_api.editor.intro.obj=UE.getEditor('editor_intro',EditorIntroOption);
    editor_api.editor.content.get=function(){return this.obj.getContent()};
    editor_api.editor.content.put=function(str){return this.obj.setContent(str)};
    editor_api.editor.content.focus=function(){return this.obj.focus()};
    editor_api.editor.content.insert=function(str){return this.obj.execCommand("insertHtml", str)};
    editor_api.editor.intro.get=function(){return this.obj.getContent()};
    editor_api.editor.intro.put=function(str){return this.obj.setContent(str)};
    editor_api.editor.intro.focus=function(){return this.obj.focus()};
    editor_api.editor.intro.insert=function(str){return this.obj.execCommand("insertHtml", str)};
    
    
    editor_api.editor.content.obj.ready(function(){sContent=editor_api.editor.content.get();});
    editor_api.editor.intro.obj.ready(function(){sIntro=editor_api.editor.intro.get();});
    
    $(document).ready(function(){
    	$('#edit').submit(function(){if(editor_api.editor.content.obj.queryCommandState('source')==1) editor_api.editor.content.obj.execCommand('source');
    	if(editor_api.editor.intro.obj.queryCommandState('source')==1) editor_api.editor.intro.obj.execCommand('source');})
    	/*源码模式下保存时必须切换*/
    
    
    	if (bloghost != "/" && (bloghost).indexOf(location.host.toLowerCase()) < 0)
    		alert("您设置了域名固化，请使用" + bloghost + "访问或进入后台修改域名，否则图片无法上传。");
    });

}
</script>
js;
    echo $s;
}
