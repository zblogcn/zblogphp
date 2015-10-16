<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require dirname(__FILE__) . '/function.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$type = $_GET['type'];

$id = $_GET['id'];

$app = new App;

if (!$app->LoadInfoByXml($type, $id)) {
	exit;
}

ob_clean();

if (
	function_exists('gzencode') &&
	$zbp->Config('AppCentre')->enablegzipapp &&
	$app->adapted > 140614// 1.3和之前版本不打包为gzba
) {
	header('Content-Disposition:attachment;filename=' . $id . '.gzba');
	echo AppCentre_Pack($app, true);
} else {
	header('Content-Disposition:attachment;filename=' . $id . '.zba');
	echo AppCentre_Pack($app, false);
}