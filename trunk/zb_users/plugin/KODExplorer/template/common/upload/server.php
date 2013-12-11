<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_PATH;?>js/swfUpload/style.css">
<script src="<?php echo STATIC_PATH;?>js/jquery-1.8.0.min.js"></script>	
<script src="<?php echo STATIC_PATH;?>js/common.js"></script>	
</head>

<script language="Javascript">	
	function download() {
		var urls = '';		
		$('.list input').each(function(i){
			if ($(this).val() != '') {
				urls += $(this).val() + ',';
			}
		});
		urls = urls.substr(0,urls.length-1);
		FrameCall.father('Main.Common.download',"'<?php echo $value["save_path"];?>*=*"+urls+"'");
	}
</script>
<body>
<div id="content">
	<div class='top_nav'>
		<a href='?upload&save_path=<?php echo $value["save_path"];?>' class="menu">本地上传</a>
		<a href='#' class="menu this">远程下载</a>
		<div style="clear:both"></div>
	</div>
	<form id="form" action="?upload/serverPost" method="post">
		<div class='list'>下载地址1: <input type="text" name="url1" value="" /></div>
		<div class='list'>下载地址2: <input type="text" name="url2" value="" /></div>
		<div class='list'>下载地址3: <input type="text" name="url3" value="" /></div>
		<div class='list'>下载地址4: <input type="text" name="url4" value="" /></div>
		<div class='list'>下载地址5: <input type="text" name="url5" value="" /></div>
		<div class='submit'><input type="button" name="" value="提交" class="" onclick="download()" /></div>
	</form>
</div>
</body>
</html>
