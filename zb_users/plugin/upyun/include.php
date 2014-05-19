<?php
include_once dirname(__FILE__)."/function.php";
#注册插件
RegisterPlugin("upyun","ActivePlugin_upyun");

function ActivePlugin_upyun() {
	Add_Filter_Plugin('Filter_Plugin_Upload_Url','upyun_Return_Url');
	Add_Filter_Plugin('Filter_Plugin_Upload_SaveFile','upyun');
	Add_Filter_Plugin('Filter_Plugin_Upload_DelFile','upyun_Del');
	// Add_Filter_Plugin('Filter_Plugin_Upload_SaveBase64File','upyun');
}

function upyun($tmp, &$upload){
	global $zbp;
	$bucket = $zbp->Config('upyun')->upyun_bucket;//云文件夹
	$filename = date("Ymd", time()).mt_rand(1000, 9999).'_'.mt_rand(0, 10000) .'.'.GetFileExt($upload->SourceName);
	$object = $zbp->Config('upyun')->upyun_dir . date("Y/m/", time()).$filename;	//构造云文件名
	$file_path = $zbp->usersdir . 'upload/tmp.data';//本地临时文件地址
	@move_uploaded_file($tmp, $file_path);//先上传到本地
	$upload->Name = $filename;

	$operator_name = $zbp->Config('upyun')->upyun_operator_name;
	$operator_password = $zbp->Config('upyun')->upyun_operator_password;

	require_once dirname(__FILE__).'/api/upyun.class.php';
	$upyun = new UpYun($bucket, $operator_name, $operator_password);
	$fh = fopen($file_path, 'r');
    $rsp = $upyun->writeFile($object, $fh, True);
    fclose($fh);

    $upload->Metas->upyun_url = $object;
	$upload->Metas->upyun_bucket = $bucket;
	unlink($file_path);//删除本地文件
	$GLOBALS['Filter_Plugin_Upload_SaveFile']['upyun'] = PLUGIN_EXITSIGNAL_RETURN;
}

/**
 * @param $upload
 * @return null
 */
function upyun_Return_Url($upload){
	global $zbp;
	$file = new Upload;
	$file = $zbp->GetUploadByID($upload->ID);
	if ($zbp->Config('upyun')->upyun_storagetype == 1) {
		if ($zbp->Config('upyun')->upyun_enable_domain) {
			return $zbp->Config('upyun')->upyun_domain.$file->Metas->upyun_url;
		}else{
			return 'http://'.$file->Metas->upyun_bucket.'.b0.upaiyun.com'.$file->Metas->upyun_url;
		}
	} else {
		$filename = $file->Metas->upyun_url;

		if ($zbp->Config('upyun')->upyun_enable_domain) {
			$filename = $zbp->Config('upyun')->upyun_domain.$filename;
		}else{
			$filename = 'http://'.$file->Metas->upyun_bucket.'.b0.upaiyun.com'.$filename;
		}

		if ($zbp->Config('upyun')->upyun_enable_Thumbnail) {
			$filename = $filename . $zbp->Config('upyun')->upyun_cutname . $zbp->Config('upyun')->upyun_ver_name;
		}
		return $filename;
	}

}
/**
 * @param $upload
 * @return null
 */
function upyun_Del(&$upload){
	global $zbp;
	$bucket = $zbp->Config('upyun')->upyun_bucket;//云文件夹

	$operator_name = $zbp->Config('upyun')->upyun_operator_name;
	$operator_password = $zbp->Config('upyun')->upyun_operator_password;

	require_once dirname(__FILE__).'/api/upyun.class.php';
	$upyun = new UpYun($bucket, $operator_name, $operator_password);
	$upyun->deleteFile($upload->Metas->upyun_url);
	$GLOBALS['Filter_Plugin_Upload_DelFile']['upyun_Del'] = PLUGIN_EXITSIGNAL_RETURN;
}

function InstallPlugin_upyun() {
	global $zbp;
	if(!$zbp->Config('upyun')->HasKey('Version')){
		$zbp->Config('upyun')->Version = '1.0';
		$zbp->Config('upyun')->upyun_storagetype = '1';
		$zbp->Config('upyun')->upyun_bucket = 'zblogphp';
		$zbp->Config('upyun')->upyun_dir = '';
		$zbp->Config('upyun')->upyun_enable_domain = 0;
		$zbp->Config('upyun')->upyun_domain = 'http://images.rainbowsoft.org';
		$zbp->Config('upyun')->upyun_operator_name = '';
		$zbp->Config('upyun')->upyun_operator_password = '';
		$zbp->Config('upyun')->upyun_enable_Thumbnail = 0;
		$zbp->Config('upyun')->upyun_cutname = '';
		$zbp->Config('upyun')->upyun_ver_name = '';
		$zbp->SaveConfig('upyun');
	}
}
function UninstallPlugin_upyun() {}