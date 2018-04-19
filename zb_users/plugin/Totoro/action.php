<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('Totoro')) {
    $zbp->ShowError(48);
    die();
}
if (!$zbp->ValidToken(GetVars('token', 'GET'))) {
    $zbp->ShowError(5, __FILE__, __LINE__);
    die();
}
Totoro_init();
$act = GetVars('act', 'GET');
$functionName = "Totoro_Action_" . ucfirst($act);
if (function_exists($functionName)) {
    $functionName();
} else {
    $zbp->ShowError(5, __FILE__, __LINE__);
}
$zbp->SetHint('good');
Redirect('../../../zb_system/admin/index.php?act=CommentMng');

function Totoro_Action_Blockip()
{
    global $zbp;
    global $Totoro;
    $id = GetVars('id', 'GET');
    $id = (int) $id;
    if ($id <= 0) {
        return;
    }
    $comment = $zbp->GetCommentByID($id);
    if ($comment->ID <= 0) {
        return;
    }
    $Totoro->filter_ip($comment->IP, true);
}
RunTime();
