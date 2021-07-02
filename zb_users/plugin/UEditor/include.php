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
    echo "document.addEventListener('readystatechange',y=>{
                let e = document,
                    l = l => e.querySelectorAll(l),
                    s = l => e.createElement(l),
                    z = l('pre.prism-highlight'),
                    X = {as3:'actionscript','c#':'csharp',delphi:'pascal',html:'markup',xml:'markup',vb:'basic',js:'javascript',plain:'markdown',pl:'perl',ps:'powershell'};
                    /**
                     * SyntaxHighlighter
                     * X = {markup:'html'}
                    */
                    if(z.length<1) return;
                    z.forEach(l => {
                            l.classList;
                            let s = l.className.match(/prism-language-([0-9a-zA-Z]+)/)[1],
                                a = X[s]||s;
                            l.className = '';
                            /*l.className='line-numbers';显示代码行数有BUG*/
                            l.innerHTML = '<code class=\"language-'+a+'\">'+l.innerHTML+'</code>';
                            /**
                             * SyntaxHighlighter
                             * l.className='brush: '+a.toLowerCase();
                            */
                        });
                let a = s('script'),
                    r = s('link'),
                    i = '".$zbp->host."zb_users/plugin/UEditor/third-party/prism/prism',
                    h = h=>e.head.appendChild(h);
                    a.src = i + '.js',
                    r.href = i + '.css',
                    r.rel = 'stylesheet',
                    r.type = 'text/css',
                    h(r),
                    h(a);
                    /**
                     * SyntaxHighlighter
                     * a.onload = (n=> {SyntaxHighlighter.highlight();});*/
            },false);";

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
    
    
    	if ((bloghost).indexOf(location.host.toLowerCase()) < 0)
    		alert("您设置了域名固化，请使用" + bloghost + "访问或进入后台修改域名，否则图片无法上传。");
    });

}
</script>
js;
    echo $s;
}
