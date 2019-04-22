<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('clinic')) {
    $zbp->ShowError(48);
    die();
}

require 'clinic.php';
$module = GetVars('module', 'GET');
$module = (isset($clinic->modules[$module]) ? $clinic->modules[$module] : null);
if (!$module) {
    exit(json_encode(array("err" => "no this module")));
}

$func = GetVars('function', 'POST');
$param = GetVars('param', 'POST');
$class = $clinic->load_module($module['id']);
$class->$func($param);
ob_clean();
echo '[';
echo implode(',', $class->output_json);
echo ']';
