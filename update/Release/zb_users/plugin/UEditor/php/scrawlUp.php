<?php

require '../../../../zb_system/function/c_system_base.php';

$zbp->Load();

$result = array(
	'url' => '',
	'title' => '',
	'original' => '',
	'state' => ''
);

if(!$zbp->CheckRights('UploadPst')){
	$result['state'] = $lang['error'][6];
	exit_output();
}


//上传配置
$config = array(
	"savePath" => $zbp->usersdir . 'upload/',
	"maxSize" => $zbp->option['ZC_UPLOAD_FILESIZE'],
	"allowFiles" => "gif|png|jpg|jpeg|bmp"
);

//临时文件目录
$tmpPath = "tmp/";

//获取当前上传的类型
$action = GetVars("action","GET");

if ( $action == "tmpImg" ) { // 背景上传
	$config[ "savePath" ] = $tmpPath;


	 
	foreach ($_FILES as $key => $value) {
		if($_FILES[$key]['error'] == 0){
			if (is_uploaded_file($_FILES[$key]['tmp_name'])) {

				$upload = new Upload;
				$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . GetFileExt($_FILES[$key]['name']);
				$upload->SourceName = $_FILES[$key]['name'];
				$upload->MimeType = $_FILES[$key]['type'];
				$upload->Size =$_FILES[$key]['size'];
				$upload->AuthorID = $zbp->user->ID;
		
				$upload->SaveFile($_FILES[$key]['tmp_name']);
				$upload->Save();
				
				$result["url"] = $upload->Url;
				$result["state"] = 'SUCCESS';
		
				echo "<script>parent.ue_callback('" . $info[ "url" ] . "','" . $info[ "state" ] . "')</script>";
		
					}
				}
			}
	 
	 
	
}

else
{
		
	$upload = new Upload;
	$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.png';
	$upload->SourceName = date("YmdHis") . '_scraw' . '.png';
	$upload->MimeType = 'image/png';
	//$upload->Size =$_FILES[$key]['size'];
	$upload->AuthorID = $zbp->user->ID;
		
	$upload->SaveBase64File($_POST['content']);
	$upload->Save();
			
	$result["url"] = $upload->Url;
	$result["state"] = 'SUCCESS';
		
	exit_output();	
			
}
		


function exit_output()
{
	global $result;
	echo json_encode($result);
	exit();
}
