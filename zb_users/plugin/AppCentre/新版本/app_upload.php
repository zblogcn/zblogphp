<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require dirname(__FILE__) . '/function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

foreach ($_FILES as $key => $value) {
	if($_FILES[$key]['error']==0){
		if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
			$tmp_name = $_FILES[$key]['tmp_name'];
			$name = $_FILES[$key]['name'];

			$xml=file_get_contents($tmp_name);
			if(App::UnPack($xml)){
				$zbp->SetHint('good','上传APP并解压成功!');
				Redirect($_SERVER["HTTP_REFERER"]);
			}else{
				$zbp->SetHint('bad',$zbp->lang['error']['64']);
				Redirect($_SERVER["HTTP_REFERER"]);
			};
		}
	}

}

Redirect($_SERVER["HTTP_REFERER"]);