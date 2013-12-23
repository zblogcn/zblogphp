<?php

#注册插件
RegisterPlugin("CloudStorage","ActivePlugin_CloudStorage");

function ActivePlugin_CloudStorage() {
	Add_Filter_Plugin('Filter_Plugin_Upload_Url','CS_Return_Url');
	Add_Filter_Plugin('Filter_Plugin_Upload_SaveFile','CloudStorage');
	Add_Filter_Plugin('Filter_Plugin_Upload_DelFile','CloudStorage_Del');
	// Add_Filter_Plugin('Filter_Plugin_Upload_SaveBase64File','CloudStorage');
}

function CloudStorage($tmp, &$upload){
	global $zbp;
	$bucket = $zbp->Config('CloudStorage')->CS_Bucket;//云文件夹
	$filename = date("Ymd", time()).mt_rand(1000, 9999).'_'.mt_rand(0, 1000) .'.'.GetFileExt($upload->SourceName);
	$object = $zbp->Config('CloudStorage')->CS_Dir . date("Y/m/", time()).$filename;	//构造云文件名
	$file_path = $zbp->usersdir . 'upload/tmp.data';//本地临时文件地址

	@move_uploaded_file($tmp, $file_path);//先上传到本地
	$upload->Name = $filename;

	switch ( $zbp->Config('CloudStorage')->CS_Storage ) {
		case '1':
			define('OSS_ACCESS_ID', $zbp->Config('CloudStorage')->CS_Ali_KeyID);	//ACCESS_ID
			define('OSS_ACCESS_KEY', $zbp->Config('CloudStorage')->CS_Ali_KeySecret);//ACCESS_KEY
			require_once dirname(__FILE__).'/api/oss/sdk.class.php';
			$oss_sdk_service = new ALIOSS();
			$oss_sdk_service->set_debug_mode(FALSE);
			//$oss_sdk_service->set_enable_domain_style(TRUE);//二级域名
			$response = $oss_sdk_service->upload_file_by_file($bucket,$object,$file_path);//将本地文件上传到云
			$upload->Metas->CS_URL = $response->header['_info']['url'];
			break;
		case '2':
			break;
		case '3':
			define ( 'BCS_AK', $zbp->Config('CloudStorage')->CS_Baidu_KeyID);//AK 公钥
			define ( 'BCS_SK', $zbp->Config('CloudStorage')->CS_Baidu_KeySecret);//SK 私钥
			require_once dirname(__FILE__).'/api/bcs/bcs.class.php';
			$baidu_bcs = new BaiduBCS();
			$object = "/".$object;
			$response = $baidu_bcs->create_object ( $bucket, $object, $file_path );

			break;
		default:
			break;
	}

	$upload->Metas->CS_Tpye = $zbp->Config('CloudStorage')->CS_Storage;
	unlink($file_path);//删除本地文件
	$GLOBALS['Filter_Plugin_Upload_SaveFile']['CloudStorage'] = PLUGIN_EXITSIGNAL_RETURN;
}

function CloudStorage_Del(&$upload){
	global $zbp;
	switch ( $upload->Metas->CS_Tpye ) {
		case '1':
			define('OSS_ACCESS_ID', $zbp->Config('CloudStorage')->CS_Ali_KeyID);	//ACCESS_ID
			define('OSS_ACCESS_KEY', $zbp->Config('CloudStorage')->CS_Ali_KeySecret);//ACCESS_KEY
			require_once dirname(__FILE__).'/api/oss/sdk.class.php';
			$oss_sdk_service = new ALIOSS();
			$oss_sdk_service->set_debug_mode(FALSE);
			$bucket = $zbp->Config('CloudStorage')->CS_Bucket;
			$object = str_replace("http://".$bucket.".oss.aliyuncs.com/", '', $upload->Metas->CS_URL);
			$oss_sdk_service->delete_object($bucket,$object);
			break;
		case '2':
			break;
		case '3':
			define ( 'BCS_AK', $zbp->Config('CloudStorage')->CS_Baidu_KeyID);//AK 公钥
			define ( 'BCS_SK', $zbp->Config('CloudStorage')->CS_Baidu_KeySecret);//SK 私钥
			require_once dirname(__FILE__).'/api/bcs/bcs.class.php';
			$baidu_bcs = new BaiduBCS();


			$bucket = $zbp->Config('CloudStorage')->CS_Bucket;
			$object = str_replace("http://".$bucket.".oss.aliyuncs.com/", '', $upload->Metas->CS_URL);
			$oss_sdk_service->delete_object($bucket,$object);
			break;
		default:
			break;
	}
	$GLOBALS['Filter_Plugin_Upload_DelFile']['CloudStorage_Del'] = PLUGIN_EXITSIGNAL_RETURN;
}

function CS_Return_Url($upload){
	global $zbp;
	$file = new Upload;
	$file = $zbp->GetUploadByID($upload->ID);
	return $file->Metas->CS_URL;
}

function InstallPlugin_CloudStorage() {
	global $zbp;
	if(!$zbp->Config('CloudStorage')->HasKey('Version')){
		$zbp->Config('CloudStorage')->Version = '1.0';
		$zbp->Config('CloudStorage')->CS_Storage = '';
		$zbp->Config('CloudStorage')->CS_Bucket = 'imzhou';
		$zbp->Config('CloudStorage')->CS_Dir = '';
		$zbp->Config('CloudStorage')->CS_Ali_KeyID = '';
		$zbp->Config('CloudStorage')->CS_Ali_KeySecret = '';
		$zbp->Config('CloudStorage')->CS_QNiu_KeyID = '';
		$zbp->Config('CloudStorage')->CS_QNiu_KeySecret = '';
		$zbp->Config('CloudStorage')->CS_Baidu_KeyID = '';
		$zbp->Config('CloudStorage')->CS_Baidu_KeySecret = '';
		$zbp->SaveConfig('CloudStorage');
	}
}
function UninstallPlugin_CloudStorage() {}

?>