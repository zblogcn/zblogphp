<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'cmt';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('Totoro')) {$zbp->ShowError(48);die();}


ob_clean();

header('Content-Type: application/x-javascript; charset=utf-8');

echo 'var totoro_token=$("#inpId").parents("form").attr("action");';

echo '$("#inpId").parents("form").attr("action",totoro_token+ "&totoro_token=" + "' . Totoro_GetTokenbyID(GetVars('id')) . '");';

//echo 'alert($("#inpId").parents("form").attr("action"));';

die();