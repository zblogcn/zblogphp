<?php
require '../../../../zb_system/function/c_system_base.php';
$zbp->Load();
$action = 'root';

///////////////////////////////////
$appid='tpure';
///////////////////////////////////

if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin($appid)) {$zbp->ShowError(48);die();}

foreach($_FILES as $key => $value){
	if(!strpos($key, "_php")){
		if(is_uploaded_file($_FILES[$key]['tmp_name'])){
			$tmp_name = $_FILES[$key]['tmp_name'];
			$name = $_FILES[$key]['name'];
			$configfile = file_get_contents($_FILES[$key]['tmp_name']);
			$configfile = base64_decode($configfile);
			$configs=json_decode($configfile,true);
			foreach($configs as $key => $value){
				$zbp->Config($appid)->$key = $value;
			}
			$zbp->SaveConfig($appid);
		}
	}
}
$zbp->SetHint('good',$zbp->lang['tpure']['import_config_success']);
Redirect('../main.php?act=config');