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
	"allowFiles" => $zbp->option['ZC_UPLOAD_FILETYPE']
);

	
foreach ($_FILES as $key => $value) {
	if($_FILES[$key]['error'] == 0){
		if (is_uploaded_file($_FILES[$key]['tmp_name'])) {

			$upload = new Upload;
			$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . GetFileExt($_FILES[$key]['name']);
			$upload->SourceName = $_FILES[$key]['name'];
			$upload->MimeType = $_FILES[$key]['type'];
			$upload->Size =$_FILES[$key]['size'];
			$upload->AuthorID = $zbp->user->ID;

			if(!$upload->CheckExtName())
			{
				$result['state'] = $lang['error'][26];
				exit_output();
			}
			
			if(!$upload->CheckSize())
			{
				$result['state'] = $lang['error'][27];
				exit_output();
			}

			$upload->SaveFile($_FILES[$key]['tmp_name']);
			$upload->Save();
			
			$result["url"] = $upload->Url;
			$result["type"] = '.' . GetFileExt($_FILES[$key]['name']) ;	
			$result["original"] = $upload->SourceName ;
			$result["state"] = 'SUCCESS' ;
		
			exit_output();		
		}
	}
}


function exit_output()
{
	global $result;
	echo json_encode($result);
	exit();
}