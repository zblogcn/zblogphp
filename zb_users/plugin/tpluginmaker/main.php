<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'function.php';
$zbp->Load(); $action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('tpluginmaker')) {$zbp->ShowError(48);die();}

$step = (int) GetVars('step', 'GET');
$blogtitle = '主题插件生成器 - STEP ' . (string) ($step + 1) . ' . ' . $message[$step];
require $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript">

	var current_theme = "<?php echo htmlspecialchars($blogtheme);?>",
		plugin_exist = <?php echo(check_plugin_exists($blogtheme) ? "true" : "false")?>;
		
</script>
<style type="text/css">
.text-note li {
	list-style-type: decimal;
}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"> </div>
  <div id="divMain2">
    <?php
    switch((string) GetVars('step', 'GET'))
    {
        case '1': step1(); break;
        case '2': step2(); break;
        default: step0(); break;
    }
    ?>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
<?php 
function step2() {
    global $zbp;
    if(count($_POST) > 0)
    {
        $zbp->Config('tpluginmaker')->temp_data = serialize($_POST);
        $zbp->SaveConfig('tpluginmaker');
    }
?>
<div class="div-wait" style="position:fixed; left: 300px; top: 300px">
  <h1>美好的事情正在开启</h1>
  <div style="text-align:center"> <img src="../../../zb_system/image/admin/loading.gif"/></div>
</div>
<script type="text/javascript">$.get("ajax.php?rnd=" + Math.random(), function(data){eval(data)});</script>
<?php
}

function step1() {
?>
<div style="text-indent:2em;" class="text-note">在主题include文件夹内的文件将在这里即时显示。</div>
<div id="help001" style="display:none">
  <p>文本文件指扩展名为txt、htm、js、css等允许用户直接使用记事本修改的文件。多媒体文件指图片、视频等无法直接修改的、只能由用户上传的文件。 </p>
  <p>&nbsp;</p>
  <p>建议图片等使用“多媒体”，广告位、标语等用“文本”。</p>
</div>
<div id="help002" style="display:none">
  <p>指展现给用户看的文字</p>
</div>
<form action="?step=2" method="post">
  <table width="100%" border="1" width="100%" class="tableBorder">
  <tr>
    <th scope="col" height="32" width="150px">文件名</th>
    <th scope="col" width="100px">文件类型 <a id="help01" href="$help001?width=320" class="betterTip" title="帮助">？</a></th>
    <th scope="col">文件注释 <a id="help02" href="$help002?width=320" class="betterTip" title="帮助">？</a></th>
    <th scope="col" width="100px"></th>
  </tr>
  <?php $array = get_theme_data($GLOBALS['blogtheme']);foreach($array as $name => $value){$encode = htmlspecialchars($value['name']);?>
  <tr>
    <td><?php echo $encode?></td>
    <td><select name="tplugin_type_<?php echo $encode?>">
        <option value="1">文本</option>
        <option value="2"<?php echo $value["type"] == 1 ? "" : " selected=\"selected\")";?>>多媒体</option>
      </select></td>
    <td><input type="text" id="<?php echo $encode?>" name="tplugin_include_<?php echo $encode?>" value="<?php echo TransferHTML($value["value"], '[textarea]');?>" style="width:98%"/></td>
    </td>
    <td align="center"><a href="javascript:;" class="href-rename" data-name="<?php echo $encode?>">改名</a>&nbsp;&nbsp;<a href="javascript:;" class="href-delete" data-name="<?php echo $encode?>">删除</a></td>
    <input type="hidden" value="<?php echo $encode?>" name="tplugin_name_<?php echo $encode?>" />
  </tr>
  <?php
}
?>
  </table>
  <input type="submit" class="button" value="提交" />
</form>
<script type="text/javascript">
(function(){
	
	$(".href-rename").click(function(){
		var that = $(this)
			p = that.parent().parent().children("td"),
			filename = that.attr("data-name");
			
		$(p[0]).html((function(){
			var str = ['<input type="text" name="tplugin_rename_' + filename + '" ',
					   'value="' + filename + '" />'];
			return str.join("");
		})());
	});
	
	$(".href-delete").click(function(){
		var that = $(this);
		if( that.attr("data-confirm") != "1" )
		{
			that.text("确定？").css("color", "red").attr("data-confirm", 1);
			return false;
		}
		that.parent().parent().fadeOut(1000, function(){
			$(this).html('<input type="hidden" name="tplugin_delete_' + that.attr("data-name") + '" value="1"/>');
		});
	});
	
})();
</script>
<?php
}


function step0() {
?>
<div style="text-indent:2em;" class="text-note">
  <p>&nbsp;</p>
  <ol>
    <li>本插件<b>不面向普通用户</b>开发。如果您没有任何开发主题的经验，请立即停用该插件。</li>
    <li>本插件的用途在于给主题快速添加一个配置后台，免去不太懂PHP的开发者制作后台之苦。</li>
    <li>如主题本身有插件，请提前备份。本插件不保证主题原有插件的代码完整性，也不负责备份。</li>
    <li>停用方式：
      <ol>
        <li>主题出现两个配置按钮：让用户点击“网站设置”-->“提交”即可。</li>
        <li>如何停用本主题插件：直接删除include.php即可。</li>
      </ol>
    </li>
  </ol>
  <p>&nbsp;</p>
  <form method="GET" action="main.php" id="form-nextstep">
    <input type="button" class="button" value="我已阅读上面注意事项，现在立刻开始使用" id="submit-button"/>
    <input type="hidden" name="step" value="1" />
  </form>
</div>
<script type="text/javascript">
(function(){
	if(!/^[a-zA-Z0-9_]+$/g.test(current_theme)) $("#submit-button").val("您的主题" + current_theme + "不能使用本插件。主题ID只能有英文字母、数字和下划线。").attr("disabled", "disabled");
	$("#submit-button").click(function(){
		if ( (plugin_exist) && ($(this).attr("data-rewrite") != "1"))
		{
			$("#submit-button").val("主题下已有插件，点击按钮确认覆盖").attr("data-rewrite", "1");
			return false;
		}
		$("#form-nextstep").submit();
	});
})();
</script>
<?php
    }
?>
