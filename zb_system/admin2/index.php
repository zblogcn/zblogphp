<?php
require '../function/c_system_base.php';
require '../function/c_system_admin_function.php';
require 'function/admin2_function.php';

$zbp->Load();
$zbp->action = GetVars('act', 'GET');
$zbp->action = ($zbp->action == '') ? 'admin' : $zbp->action;

$zbp->CheckTemplateAdmin();

if (!$zbp->CheckRights($zbp->action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

HookFilterPlugin('Filter_Plugin_Admin_Begin');

list($zbp->title, $ActionInfo) = zbp_admin2_GetActionInfo($zbp->action);

$zbp->template_admin->SetTags("title", $zbp->title);
$zbp->template_admin->SetTags("action", $zbp->action);
$zbp->template_admin->SetTags("main", $ActionInfo);
$zbp->template_admin->Display("index");

HookFilterPlugin('Filter_Plugin_Admin_End');

RunTime();
