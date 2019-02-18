<?php
/**
 * ZBPDK子扩展
 * 编辑器API（EditorApi）调试器
 * 
 * @author 心扬 <chrishyze@163.com>
 */

/**
 * 子扩展信息
 * url:后台文件
 * description:描述
 * id:ID
 */
$GLOBALS['zbpdk']->add_extension(array(
    'url' => 'main.php',
    'description' => '用于调试编辑器Api',
    'id' => 'EditorApi'
));

/**
 * 子扩展菜单列表
 * url:相对后台地址
 * float:菜单停靠位置
 * id:ID
 * title:标题
 */
$GLOBALS['zbpdk']->submenu->add(array(
    'url' => 'EditorApi/main.php',
    'float' => 'left',
    'id' => 'EditorApi',
    'title' => 'EditorApi'
));

/**
 * 子扩展在插件激活时的执行函数
 */
function ActivePlugin_EditorApi() {
    //5号输出接口，在编辑页面标题与正文之间
    Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'test_content_editorapi');
    //1号输出接口，在正文与别名之间
    Add_Filter_Plugin('Filter_Plugin_Edit_Response', 'test_intro_editorapi');
}

/**
 * 正文编辑器调试
 */
function test_content_editorapi() {
    global $zbp;
    
    $script = <<<EOF
<div class="EditorApiTool">
    <div><label for="ea_content_text" class="editinputname">正文编辑器调试工具</label></div>
    <div><textarea class="ea_texterea" id="ea_content_text" row="3" placeholder="正文测试文本框"></textarea></div>
    <div>
        <span class="ea_button" id="ea_content_get">editor_api.editor.content.get()</span>
        获取正文编辑器中的内容至测试文本框
    </div>
    <div>
        <span class="ea_button" id="ea_content_insert">editor_api.editor.content.insert()</span>
        将测试文本框的内容插入至正文编辑器的光标处
    </div>
    <div>
        <span class="ea_button" id="ea_content_put">editor_api.editor.content.put()</span>
        用测试文本框的内容去替换正文编辑器的所有内容
    </div>
    <div>
        <span class="ea_button" id="ea_content_focus">editor_api.editor.content.focus()</span>
        使正文编辑器获取焦点
    </div>
</div>

<script>
$(function(){
    $("#ea_content_get").click(function(){
        $("#ea_content_text").val(editor_api.editor.content.get());
        console.log("editor_api.editor.content.get()");
    });
    $("#ea_content_insert").click(function(){
        editor_api.editor.content.insert($("#ea_content_text").val());
        console.log("editor_api.editor.content.insert()");
    });
    $("#ea_content_put").click(function(){
        editor_api.editor.content.put($("#ea_content_text").val());
        console.log("editor_api.editor.content.put()");
    });
    $("#ea_content_focus").click(function(){
        editor_api.editor.content.focus();
        console.log("editor_api.editor.content.focus()");
    });
});
</script>
EOF;

    if ($zbp->Config('ZBPDK_EditorApi')->show == '1') echo $script;
}

/**
 * 摘要编辑器调试
 */
function test_intro_editorapi() {
    global $zbp;
    
    $script = <<<EOF
<div class="EditorApiTool">
    <div><label for="ea_intro_text" class="editinputname">摘要编辑器调试工具</label></div>
    <div><textarea class="ea_texterea" id="ea_intro_text" row="3" placeholder="摘要测试文本框"></textarea></div>
    <div>
        <span class="ea_button" id="ea_intro_get">editor_api.editor.intro.get()</span>
        获取摘要编辑器中的内容至测试文本框
    </div>
    <div>
        <span class="ea_button" id="ea_intro_insert">editor_api.editor.intro.insert()</span>
        将测试文本框的内容插入至摘要编辑器的光标处
    </div>
    <div>
        <span class="ea_button" id="ea_intro_put">editor_api.editor.intro.put()</span>
        用测试文本框的内容去替换摘要编辑器的所有内容
    </div>
    <div>
        <span class="ea_button" id="ea_intro_focus">editor_api.editor.intro.focus()</span>
        使摘要编辑器获取焦点
    </div>
</div>
<style>
.EditorApiTool {
    margin-top: 15px;
}
.ea_texterea {
    line-height: 150%;
    height: 85px;
    width: 100%;
    padding: 10px;
}
.ea_button {
    display: inline-block;
    color: #ffffff;
    font-size: 16px;
    height: 35px;
    line-height: 35px;
    padding: 0 10px;
    margin-bottom: 10px;
    background: #3399cc;
    cursor: pointer;
}
</style>
<script>
$(function(){
    $("#ea_intro_get").click(function(){
        $("#ea_intro_text").val(editor_api.editor.intro.get());
        console.log("editor_api.editor.intro.get()");
    });
    $("#ea_intro_insert").click(function(){
        editor_api.editor.intro.insert($("#ea_intro_text").val());
        console.log("editor_api.editor.intro.insert()");
    });
    $("#ea_intro_put").click(function(){
        editor_api.editor.intro.put($("#ea_intro_text").val());
        console.log("editor_api.editor.intro.put()");
    });
    $("#ea_intro_focus").click(function(){
        editor_api.editor.intro.focus();
        console.log("editor_api.editor.intro.focus()");
    });
});
</script>
EOF;

    if ($zbp->Config('ZBPDK_EditorApi')->show == '1') echo $script;
}

/**
 * 子扩展在插件安装时的执行函数
 */
function InstallPlugin_EditorApi() {
    global $zbp;
    if (!$zbp->HasConfig('ZBPDK_EditorApi')) {
        $zbp->Config('ZBPDK_EditorApi')->show = '0';
        $zbp->SaveConfig('ZBPDK_EditorApi');
    }
}

/**
 * 子扩展在插件卸载时的执行函数
 */
function UnInstallPlugin_EditorApi() {
    global $zbp;
    if ($zbp->HasConfig('ZBPDK_EditorApi')) {
        $zbp->DelConfig('ZBPDK_EditorApi');
    }
}

//EOF
