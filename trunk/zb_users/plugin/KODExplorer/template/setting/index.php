<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  menu="menubody">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="<?php echo STATIC_PATH;?>js/jquery-1.8.0.min.js"></script>	
	<script src="<?php echo STATIC_PATH;?>js/artDialog/jquery.artDialog.js"></script>
	<script src="<?php echo STATIC_PATH;?>js/common.js"></script>
	<link href="<?php echo STATIC_PATH;?>style/font-awesome/style.css" rel="stylesheet"/>

	
	<link href="<?php echo STATIC_PATH;?>style/skin/<?php echo $value['config']['theme'];?>/app_setting.css" rel="stylesheet" id='link_css_list'/>
	
</head>

<script type="text/javascript">
	var setting=location.hash.split("#", 2)[1];//首次进入定位。
	var favAddName="<?php echo $_GET['name'];?>";//添加收藏夹
	var favAddPath="<?php echo $_GET['path'];?>";
	var static_path = "<?php echo STATIC_PATH;?>";
</script>

<body>
	<div id="body">
		<div class="menu_left">	
			<h1>选项</h1>
			<ul class='setting'>
				<li id="editer"><i class="font-icon icon-edit"></i>编辑器设置</li>
				<li id="help"><i class="font-icon icon-question"></i>帮助</li>
				<li id="about"><i class="font-icon icon-info-sign"></i>关于</li>
			</ul>
		</div>		
		<div class='main'></div>
	</div>
</body>
<script src="<?php echo STATIC_PATH;?>js/app/setting.js"></script>
</html>