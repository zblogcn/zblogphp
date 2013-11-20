<?php

require '../../../../zb_system/function/c_system_base.php';

$zbp->Load();

if(!$zbp->CheckRights('UploadPst')){
	echo "{'url':'','state':'" . $lang['error'][6] . "'}";
	die();
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
	//背景保存在临时目录中
	$config[ "savePath" ] = $tmpPath;

	/**
	 * 返回数据，调用父页面的ue_callback回调
	 */
	 
	foreach ($_FILES as $key => $value) {
		if($_FILES[$key]['error']==0){
			if (is_uploaded_file($_FILES[$key]['tmp_name'])) {

		$upload = new Upload;
		$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . GetFileExt($_FILES[$key]['name']);
		$upload->SourceName = $_FILES[$key]['name'];
		$upload->MimeType = $_FILES[$key]['type'];
		$upload->Size =$_FILES[$key]['size'];
		$upload->AuthorID = $zbp->user->ID;

		$upload->SaveFile($_FILES[$key]['tmp_name']);
		$upload->Save();
		
		$info=array();
		$info["url"]=$upload->Name;
		$info["state"]='SUCCESS';

		echo "<script>parent.ue_callback('" . $info[ "url" ] . "','" . $info[ "state" ] . "')</script>";

			}
		}
	}
	 
	 
	
} else {

	$upload = new Upload;
	$upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.png';
	$upload->SourceName = date("YmdHis") . '_scraw' . '.png';
	$upload->MimeType = 'image/png';
	//$upload->Size =$_FILES[$key]['size'];
	$upload->AuthorID = $zbp->user->ID;

	$upload->SaveBase64File($_POST['content']);
	$upload->Save();
	
	$info=array();
	$info["url"]=$upload->Name;
	$info["state"]='SUCCESS';

	echo "{'url':'" . $info[ "url" ] . "',state:'" . $info[ "state" ] . "'}";
}


