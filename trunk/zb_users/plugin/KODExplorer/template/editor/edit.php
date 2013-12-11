<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <head>  	
	<script src="<?php echo STATIC_PATH;?>js/jquery-1.8.0.min.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/common.js"></script><!-- 跨窗口函数调用 -->
	<script src="<?php echo STATIC_PATH;?>js/artDialog/jquery.artDialog.js"></script>	

    <link href="<?php echo STATIC_PATH;?>js/codemirror/lib/codemirror.css" rel="stylesheet" >
    <script src="<?php echo STATIC_PATH;?>js/codemirror/lib/codemirror.js"></script>    
    <script src="<?php echo STATIC_PATH;?>js/codemirror/self/addon.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/codemirror/self/zen_codemirror.min.js"></script>
    <script src="<?php echo STATIC_PATH;?>js/codemirror/self/my_search.js"></script>
	<link href="<?php echo STATIC_PATH;?>style/font-awesome/style.css" rel="stylesheet"/>
	<link href="<?php echo STATIC_PATH;?>js/codemirror/theme/all.css" rel="stylesheet" >

	
	<link href="<?php echo STATIC_PATH;?>style/skin/<?php echo $value['config']['theme'];?>/app_code_edit.css" rel="stylesheet" id='link_css_list'/>
	
  </head>
  <script>
    var static_path		= "<?php echo STATIC_PATH;?>";
	var app_path		= "<?php echo APPHOST;?>";
	var frist_file		= "<?php echo $_GET['filename'];?>";
	var codetheme 		= "<?php echo $this->config['codetheme'];?>";
	CodeMirror.modeURL	= "<?php echo STATIC_PATH;?>js/codemirror/mode/%N/%N.js";
  </script>
  <body>
	<div class="edit_main" style="height: 100%;">
		<div class="tools">
			<div class="left">
				<a class="save" href="#" title='保存'><i class="font-icon icon-save"></i></a>
				<a class="saveall" href="#" title='全部保存'><i class="font-icon icon-paste"></i></a>
				<span class="line"></span>
				<a class="pre" href="#" title="撤销"><i class="font-icon icon-undo"></i></a>
				<a class="next" href="#" title="反撤销"><i class="font-icon icon-repeat"></i></a>
				<span class="line"></span>
				<a class="find" href="#" title="查找"><i class="font-icon icon-search"></i></a>
				<a class="gotoline" href="#" title="定位到N行"><i class="font-icon icon-pushpin"></i></a>
				<span class="line"></span>
				<a class="font" href="#" title="字体大小">
					<i class="font-icon icon-font"></i>字体<i class="font-icon icon-caret-down"></i>
				</a>		
				<a class="codetheme" href="#" title="代码风格">
				<i class="font-icon icon-magic"></i>代码风格<i class="font-icon icon-caret-down"></i>
				</a>

				<span class="line"></span>
				<a class="wordbreak" href="#" title="自动换行"><i class="font-icon icon-level-down"></i></a>
				<a class="tabbeautify" href="#" title="tab对齐"><i class="font-icon icon-indent-left"></i></a>
				<span class="line"></span>
				<a class="about" href="#" title="ZendCoding使用帮助"><i class="font-icon icon-info"></i></a>
			</div>
			<div class="right">
				<a class="max" href="#" title="全屏"><i class="font-icon icon-fullscreen"></i></a>
				<a class="close" href="#" title="关闭"><i class="font-icon icon-remove"></i></a>
			</div>
			<div style="clear:both"></div>
		</div><!-- end tools -->
		<ul id="fontsize" class="dropbox">
			<li>12px</li><li>13px</li><li>14px</li><li>16px</li>
			<li>18px</li><li>24px</li><li>28px</li><li>32px</li>		
		</ul>
		<ul id="codetheme" class="dropbox">
		<?php 
			$tpl="<li class='{this} list' theme='{0}'>{0}</li>\n";
			echo getTplList('/','=',$this->config['codethemeall'],$tpl,$this->config['codetheme']);
		?>
		</ul>
		

		<!-- 主体部分 -->
		<div class="edit_tab">
			<div class="tabs">
				<a  href="javascript:Main.Editor.add()" class="add icon-plus"></a>
				<div style="clear:both"></div>
			</div>
		</div>
		<div class="edit_body">
			<div class="introduction">
				<div class="intro_left">
					<div class="tips blue">
						<h1> <span>丰富的功能</span> </h1>
						<p>多主题：选择你喜欢的编程风格</p>
						<p>自定义字体：适合种场景下使用</p>
						<p>zendcodeing支持,从此爱上在线编程</p>
						<p>代码块折叠、展开</p>
						<p>支持多标签，拖动切换顺序;</p>
						<p>维持多个文档、查找替换；历史记录</p>
						<p>自动补全[],{},(),"",''</p>
						<p>自动换行</p>
						<p>更多功能,等待你的发现……</p>
					</div>
					<div class="tips orange">
						<h1> <span>多种代码高亮</span> </h1>
						<p>前端：html,JavaScript,css,less,sass,scss</p>
						<p>web开发：php,perl,python,ruby,elang,go...</p>
						<p>传统语言：java,c,c++,c#,actionScript,VBScript...</p>
						<p>其他：markdown,shell,sql,lua,xml,yaml...</p>
					</div>
				</div>
				<div class="intro_right">
					<div class="tips green">
						<h1> <span>快捷键操作</span> </h1>
						<p>ctrl+s：保存</p>
						<p>ctrl+f/ctrl+G: 查找,替换</p>
						<p>ctrl+c/ctrl+x/ctrl+v:复制/剪切/粘贴</p>
						<p>ctrl+z/ctrl+y:撤消/反撤销操作</p>
						<p>ctrl+l: 选择当前行</p>				
						<p>ctrl+A: 选择到起始/结尾/全选</p>
						<p>home/end/移动光标到行首/行尾；
						<p>ctrl+home/ctrl+end:文档起始/结束部分</p>
						<p>shift+home/shift+end 从光标选择到行首/行尾
						<p>shift+ctrl+home: 从光标处选择到文档起始</p>
						<p>shift+ctrl+end: 从光标处选择到文档结尾</p>
						<p>tab: tab对齐</p>			
						<p>del：删除</p>
					</div></div>
				<div style="clear:both"></div>
			</div>
			<div class="tabs"></div>
		</div>
	</div>
	<script src="<?php echo STATIC_PATH;?>js/app/edit/edit.js"></script>
</body>
</html>
