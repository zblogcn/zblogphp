<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_PATH;?>js/swfUpload/style.css">
<script src="<?php echo STATIC_PATH;?>js/jquery-1.8.0.min.js"></script>	
<script src="<?php echo STATIC_PATH;?>js/common.js"></script>	
<script src="<?php echo STATIC_PATH;?>js/swfUpload/swfupload.js"></script>
<script src="<?php echo STATIC_PATH;?>js/swfUpload/swfupload.queue.js"></script>
<script src="<?php echo STATIC_PATH;?>js/swfUpload/fileprogress.js"></script>
<script src="<?php echo STATIC_PATH;?>js/swfUpload/handlers.js"></script>

<script type="text/javascript">
	var upload;
	window.onload = function() {
		upload = new SWFUpload({
			// Backend Settings
			upload_url: '?upload/swfUpload&session_id=<?php echo session_id();?>&save_path=<?php echo $value["save_path"];?>',
			post_params: {"sid" : ""},
			// File Upload Settings
			file_size_limit : "100000000",	// 1000MB
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit :200,
			file_queue_limit : 0,
			// Event Handler Settings (all my handlers are in the Handler.js file)
			swfupload_preload_handler : preLoad,
			swfupload_load_failed_handler : loadFailed,
			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : function(){},//fileDialogComplete,文件不自动上传

			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			// Button Settings
			button_image_url : "static/js/swfUpload/images/button.png",
			button_placeholder_id : "spanButtonPlaceholder",
			button_width: 61,
			button_height: 25,			
			// Flash Settings
			flash_url : "static/js/swfUpload/swfupload.swf",
			flash9_url : "static/js/swfUpload/swfupload_fp9.swf",		

			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},			
			debug: false
		});
 }
</script>
</head>


<body>
<div id="content">
	<div class='top_nav'>
		<a href='#' class="menu this">本地上传</a>
		<a href='?upload/server&save_path=<?php echo $value["save_path"];?>' class="menu">远程下载</a>
		<div style="clear:both"></div>
	</div>
	<form id="form" action="?upload/swfUpload" method="post" enctype="multipart/form-data">
			<div>
				<div style="padding-left: 5px;">
					<span id="spanButtonPlaceholder" title='上传'></span><i>可选择多个文件上传</i>
					<!-- <a href="#" class="left button" onclick="upload.startUpload();">开始上传</a>
					<a href="#" class="right button" onclick="cancelQueue(upload);" id="btnCancel">取消上传</a> -->
				</div>
				<div class="filelist" id="fsUploadProgress"></div>
			</div>
	</form>
</div>
</body>
</html>
