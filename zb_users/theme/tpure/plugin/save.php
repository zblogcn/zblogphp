<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
$appid = 'tpure';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin($appid)) {$zbp->ShowError(48);die();}

if($_GET['type'] == 'delarchive' ){
	global $zbp;
    tpure_delArchive();
	$zbp->SetHint('good','文章归档缓存清除成功！');
	Redirect('../main.php?act=base');
}

?>