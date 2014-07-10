<?php
include_once dirname(__FILE__) . "/qiniu/rs.php";
include_once dirname(__FILE__) . "/qiniu/io.php";
include_once dirname(__FILE__) . "/qiniu.class.php";
include_once dirname(__FILE__) . "/function.php";
//define('QINIU_WATER_URL', 'http://su.bdimg.com/static/superplus/img/logo_white.png');
define('QINIU_WATER_URL', $bloghost . 'zb_users/plugin/qiniuyun/water.png');

$qiniu = new QINIU();

RegisterPlugin("qiniuyun","ActivePlugin_qiniuyun");
function init_qiniu()
{
	global $qiniu;
	$qiniu->initialize();
}

function ActivePlugin_qiniuyun()
{
	Add_Filter_Plugin('Filter_Plugin_Upload_Url','qiniuyun_upload_url');
	Add_Filter_Plugin('Filter_Plugin_Upload_SaveFile','qiniuyun_upload_savefile');
	Add_Filter_Plugin('Filter_Plugin_Upload_DelFile','qiniuyun_upload_delfile');
	Add_Filter_Plugin('Filter_Plugin_Upload_SaveBase64File','qiniuyun_upload_savefile');
}

function qiniuyun_upload_url(&$upload)
{
	init_qiniu();
	global $zbp; global $qiniu;
	$file = $zbp->GetUploadByID($upload->ID);
	$url = $qiniu->get_url($file->Metas->qiniu_key, ($qiniu->water_enable && !$qiniu->water_overwrite));
	return $url;
}

function qiniuyun_upload_savefile($tmp, &$upload)
{
	init_qiniu();
	global $zbp; global $qiniu;
	$file_path = $zbp->usersdir . 'upload/zbp.qiniutmp.' .time();
	$file_name = date("Ymd", time()) . mt_rand(1000, 9999) . '_' . mt_rand(0, 10000) . '.' . GetFileExt($upload->SourceName);
	$upload_water = ($qiniu->water_enable && $qiniu->water_overwrite);
	
	if (is_file($tmp))
		@move_uploaded_file($tmp, $file_path);//先上传到本地
	else
		@file_put_contents($file_path, base64_decode($tmp));
	
	$upload->Name = $file_name;	
	
	$cloud_path = $qiniu->cloudpath . date("Y/m/", time()) . $file_name;	//构造云文件名

	$ret = $qiniu->upload($cloud_path, $file_path, $upload_water);
	$upload->Metas->qiniu_key = $ret['key'];
	
	unlink($file_path);
	$GLOBALS['Filter_Plugin_Upload_SaveFile']['qiniuyun_upload_savefile'] = PLUGIN_EXITSIGNAL_RETURN;
	$GLOBALS['Filter_Plugin_Upload_SaveBase64File']['qiniuyun_upload_savefile'] = PLUGIN_EXITSIGNAL_RETURN;
	return true;
}

function qiniuyun_upload_savebase64file($str64, &$upload)
{
	$s = base64_decode($str64);
	
}

function qiniuyun_upload_delfile(&$upload)
{
	init_qiniu();
	global $zbp; global $qiniu;
	$qiniu->delete($upload->Metas->qiniu_key);
	$GLOBALS['Filter_Plugin_Upload_DelFile']['qiniuyun_upload_delfile'] = PLUGIN_EXITSIGNAL_RETURN;
	return true;
}

function InstallPlugin_qiniuyun()
{
}

function UninstallPlugin_qiniuyun()
{
}