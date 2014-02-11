<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require 'function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$type=$_GET['type'];

$id=$_GET['id'];

$app=new App;

if($app->LoadInfoByXml($type,$id)==false)die;

ob_clean();

header('Content-Type: application/octet-stream');

header('Content-Disposition:attachment;filename='. $id .'.zba');

echo $app->Pack();