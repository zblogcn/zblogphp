<?php
/**
* ZBPDK子扩展
* EditorApi配置页
*
* @author 心扬 <chrishyze@163.com>
*/

require_once '../../../../../zb_system/function/c_system_base.php';
require_once '../../../../../zb_system/function/c_system_admin.php';
header("Cache-Control: no-cache, must-revalidate");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
$zbp->Load();
if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('ZBPDK')) {
    $zbp->ShowError(48);
    die();
}

//检测配置提交
if (strtoupper($_SERVER['REQUEST_METHOD'])=='POST') {
    if (isset($_POST['show'])) $zbp->Config('ZBPDK_EditorApi')->show = $_POST['show'];
    $zbp->SaveConfig('ZBPDK_EditorApi');
}

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
    <div class="divHeader">
        <?php echo $blogtitle;?>
    </div>
    <div class="SubMenu">
        <?php echo $zbpdk->submenu->export('EditorApi');?>
    </div>
    <div id="divMain2">
        <form id="edit" name="edit" method="post" action="main.php">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter">
                <tr>
                    <th class="td25">项目</th>
                    <th>设置</th>
                    <th>说明</th>
                </tr>
                <tr>
                    <td>
                        <p><b>显示调试工具</b></p>
                    </td>
                    <td>
                        <p><input id="show" name="show" class="checkbox" type="text" value="<?php echo $zbp->Config('ZBPDK_EditorApi')->show; ?>"></p>
                    </td>
                    <td>
                        <p><span class="note">在编辑页面显示编辑器Api的调试工具。</span></p>
                    </td>
                </tr>
            </table>
            <hr>
            <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken();?>">
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit'] ?>">
            </p>
        </form>

        <div class="eapi-intro">
            <h2>编辑器接口说明</h2>
            <p>编辑器接口是Z-BlogPHP官方定义的一个Javascript接口，通过该接口可以获取或设置编辑器当前内容。</p>
            <p>系统或第三方应用（指插件和主题）有时需要获取文章编辑页面中编辑器的内容，但是除了官方默认编辑器，用户还可能会安装其他第三方编辑器，而不同的编辑器又提供各自不同的接口来给外部操作其内容，将不同编辑器各自的接口统一成一个通用的，系统或应用通过一个公共的入口访问编辑器中的内容，就避免了要去适配不同编辑器接口的尴尬情况，从而降低耦合度，加强了扩展性。</p>
            <p>编辑器要自行去适配实现这个接口，<strong>本扩展的主要作用就是测试编辑器是否适配该接口，以及外部是否能成功调用该接口等，一般用于辅助第三方编辑器的开发</strong>。</p>
            <p>接口分正文编辑器和摘要编辑器两个操作对象，每个对象下提供四个主要函数，分别是get获取编辑器内容，insert将制定内容插入编辑器光标处，put用制定内容替换编辑器的当前所有内容，focus使编辑器获取焦点。<br>以下是默认的功能细节：</p>
            <h3>editor_api.editor.content.get()</h3>
            <p>参数：无</p>
            <p>返回：string类型，编辑器的所有内容</p>
            <p>说明：获取当前正文编辑器的所有内容，通常是源代码，例如对于HTML编辑器将获得HTML代码，而Markdown编辑器就是Markdown代码。</p>
            <h3>editor_api.editor.intro.get()</h3>
            <p>参数：无</p>
            <p>返回：string类型，编辑器的所有内容</p>
            <p>说明：获取当前摘要编辑器的所有内容，通常是源代码，例如对于HTML编辑器将获得HTML代码，而Markdown编辑器就是Markdown代码。</p>
            <h3>editor_api.editor.content.insert(content)</h3>
            <p>参数：content，string类型，要插入的内容</p>
            <p>返回：undefined</p>
            <p>说明：在正文编辑器的光标处插入指定内容。</p>
            <h3>editor_api.editor.intro.insert(content)</h3>
            <p>参数：content，string类型，要插入的内容</p>
            <p>返回：undefined</p>
            <p>说明：在摘要编辑器的光标处插入指定内容。</p>
            <h3>editor_api.editor.content.put(content)</h3>
            <p>参数：content，string类型，新的内容</p>
            <p>返回：undefined</p>
            <p>说明：将当前正文编辑器的所有内容替换为指定内容。</p>
            <h3>editor_api.editor.intro.put(content)</h3>
            <p>参数：content，string类型，新的内容</p>
            <p>返回：undefined</p>
            <p>说明：将当前摘要编辑器的所有内容替换为指定内容。</p>
            <h3>editor_api.editor.content.focus()</h3>
            <p>参数：无</p>
            <p>返回：undefined</p>
            <p>说明：使正文编辑器获得焦点，默认使光标位于内容末尾。</p>
            <h3>editor_api.editor.intro.focus()</h3>
            <p>参数：无</p>
            <p>返回：undefined</p>
            <p>说明：使摘要编辑器获得焦点，默认使光标位于内容末尾。</p>
            <br>
            <p>以下摘自官方默认UEditor编辑器的接口实现，仅作参考：</p>
            <pre><?php echo htmlentities('<script type="text/javascript">
function getContent() {
    return editor_api.editor.content.get();
}

function getIntro() {
    return editor_api.editor.intro.get();
}

function setContent(s) {
    editor_api.editor.content.put(s);
}

function setIntro(s) {
    editor_api.editor.intro.put(s);
}

function editor_init() {
    editor_api.editor.content.obj = UE.getEditor("editor_content");
    editor_api.editor.intro.obj = UE.getEditor("editor_intro",EditorIntroOption);
    editor_api.editor.content.get = function() {
        return this.obj.getContent()
    };
    editor_api.editor.content.put = function(str) {
        return this.obj.setContent(str)
    };
    editor_api.editor.content.focus = function() {
        return this.obj.focus()
    };
    editor_api.editor.intro.get = function() {
        return this.obj.getContent()
    };
    editor_api.editor.intro.put = function(str) {
        return this.obj.setContent(str)
    };
    editor_api.editor.intro.focus = function() {
        return this.obj.focus()
    };

    editor_api.editor.content.obj.ready(function() {
        sContent = editor_api.editor.content.get();
    });
    editor_api.editor.intro.obj.ready(function(){
        sIntro = editor_api.editor.intro.get();
    });

    $(document).ready(function() {
        $("#edit").submit(function() {
            if (editor_api.editor.content.obj.queryCommandState("source") == 1) editor_api.editor.content.obj.execCommand("source");
            if (editor_api.editor.intro.obj.queryCommandState("source") == 1) editor_api.editor.intro.obj.execCommand("source");
        })
    });
}
</script>');?></pre>
            <p>具体的功能实现取决于开发者，更多详情请参阅源码，其源码位于 zb_system/admin/edit.php 文件中。</p>
        </div>
    </div>
</div>

<style>
.eapi-intro {
  margin-bottom: 50px;
}
.eapi-intro h3 {
  margin-bottom: .35em;
}
.eapi-intro pre {
  margin: 10px 0;
  padding: 10px;
  background: #efefef;
}
</style>

<script>
    ActiveTopMenu('zbpdk');
</script>
<script>
    AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png';?>");
</script>

<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();

//EOF
