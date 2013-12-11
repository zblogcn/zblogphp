<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cloud Explorer——云。在线资源管理</title>
	<script src="<?php echo STATIC_PATH;?>js/jquery-1.8.0.min.js"></script>	
	<script src="<?php echo STATIC_PATH;?>js/artDialog/jquery.artDialog.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/contextMenu/jquery.ui.position.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/contextMenu/jquery.contextMenu.js"></script>

	<script src="<?php echo STATIC_PATH;?>js/common.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/cmp4/cmp.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/ztree/js/jquery.ztree.all-3.5.min.js"></script>

	<script src="<?php echo STATIC_PATH;?>js/picasa/picasa.js"></script>
	<link href="<?php echo STATIC_PATH;?>js/picasa/style/style.css" rel="stylesheet"/>
	<link href="<?php echo STATIC_PATH;?>style/font-awesome/style.css" rel="stylesheet"/>

	
	<link href="<?php echo STATIC_PATH;?>style/skin/<?php echo $value['config']['theme'];?>/app_explorer.css" rel="stylesheet" id='link_css_list'/>
	
</head>

<script>
var HOME    = '<?php echo HOME;?>';//$dir为初次进入或者刷新浏览器后的当前目录。
var web_host= '<?php echo HOST;?>';// localhost 访问根目录
var this_path='<?php echo urlencode($value["dir"]);?>';//D:/wwwroot/www/explorer/0000/当前绝对路径
var WEB_ROOT='<?php echo WEB_ROOT;?>';//D:/wwwroot/ 服务器路径 用于api更新列表情况下保证web_path的正确性.
var web_path='<?php echo urlencode(str_replace(WEB_ROOT,'',$value["dir"]));?>';// 当前url目录,从根目录开始到当前 用于文件打开 

var json_data		= '';			//用于存储每次获取列表后的json数据值。
var app_path		= '<?php echo urlencode(APPHOST);?>';	///www/explorer/  程序路径，用于静态资源调用
var list_type		= '<?php echo $value['config']['list'];?>';		//文件列表显示方式 list/icon
var list_theme		= '<?php echo $value['config']['theme'];?>';	//文件列表主题
var json_sort_field = '<?php echo $value['config']['list_sort_field'];?>'; //列表排序依照的字段  
var json_sort_order = '<?php echo $value['config']['list_sort_order'];?>';	//列表排序升序or降序
var static_path		= '<?php echo STATIC_PATH;?>';
var musictheme		= '<?php echo $value["config"]["musictheme"];?>';
var movietheme		= '<?php echo $value["config"]["movietheme"];?>';
</script>

<body onselectstart="return false" style="overflow:hidden;">
	<?php include(TEMPLATE.'common/navbar/index.html');?>
	<div class="frame-header">
		<div class="header-content">
			<div class="header-left">
				<a href="#" class="nouse button left" id='history_back' title='后退'>
					<i class="font-icon icon-arrow-left"></i>
				</a>
				<a href="#" class="button middle" id='history_next' title='前进'>
					<i class="font-icon icon-arrow-right"></i>
				</a>
				<a href="#" class="button right" id='refresh' title='强刷数据'>
					<i class="font-icon icon-refresh"></i>
				</a>
			</div><!-- /header left -->
			
			<div class='header-middle'>
				<a href="#" id='home' class="home button left"  title='根目录'>
					<i class="font-icon icon-home"></i>
				</a>				
				<div id='yarnball' title="点击进入编辑状态"></div>
				<div id='yarnball_input'><input type="text" name="path" value="" class="path" onkeydown="Main.UI.header.keydown(event);" id="path"/></div>
				<a href="#" id='go' class="left button menuTree"  title='走着!'>
					<i class="font-icon icon-circle-arrow-right"></i>
				</a>
				<a href="#" id='up' class="right button"  title='上一层'>
					<i class="font-icon icon-circle-arrow-up"></i>
				</a>
			</div><!-- /header-middle end-->

		</div>
	</div><!-- / header end -->


	<div class="frame-main">
		<div class='frame-right'>
			<div class="frame-right-main">
				<div class="messageBox"><div class="content"></div></div>				
				<div class="tools">
					<div class="tools-left">
				        <a id='newfolder' href="#" class="button left"><i class="font-icon icon-folder-close-alt"></i>新建文件夹</a>
				        <a id='newfile' href="#" class="button middle"><i class="font-icon icon-file-alt"></i>新建文件</a>
				        <a id='upload' href="#" class="button right"><i class="font-icon icon-cloud-upload"></i>上传</a>
						<span class='msg'>载入中...</span>
					</div>
					<div class="tools-right">
						<a id='set_icon' href="#" class="button left" title="显示图标模式"><i class="font-icon icon-th"></i></a>
						<a id='set_list' href="#" class="button middle" title="显示列表模式"><i class="font-icon icon-list"></i></a>
					</div>
					<div style="clear:both"></div>
				</div><!-- end tools -->

				<ul id="theme_list" class="theme_list">
					<?php 
						$tpl="<li class='list {this}' theme='{0}'>{1}</li>\n";
						echo getTplList('/','=',$value['config']['themeall'],$tpl,$value['config']['theme'],'this');
					?>
				</ul>

				<div id='list_type_list'></div><!-- list type 列表排序方式 -->
				<div class='bodymain html5_drag_upload_box'>
					<div class="fileContiner">
						<div class="loading " style="text-align:center;padding:20px;"><img src="./static/images/loading_content.gif"/></div>
					</div>
				</div><!-- html5拖拽上传list -->
			</div>
		</div><!-- / frame-right end-->
	</div><!-- / frame-main end-->
<script src="<?php echo STATIC_PATH;?>js/app/common/taskTap.js"></script>
<script src="<?php echo STATIC_PATH;?>js/app/common/CMPlayer.js"></script>
<script src="<?php echo STATIC_PATH;?>js/app/common/common.js"></script>

<script src="<?php echo STATIC_PATH;?>js/app/explorer/main.js"></script>
<script src="<?php echo STATIC_PATH;?>js/app/explorer/rightMenu.js"></script>
<script src="<?php echo STATIC_PATH;?>js/app/explorer/pathOperate.js"></script>
<script src="<?php echo STATIC_PATH;?>js/app/explorer/fileSelect.js"></script>
</body>
</html>
