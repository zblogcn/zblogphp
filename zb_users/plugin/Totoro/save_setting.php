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
Totoro_init();
$blogtitle = 'Totoro反垃圾评论';

if (function_exists('CheckIsRefererValid')) {
    CheckIsRefererValid();
}

foreach ($Totoro->config_array as $type_name => &$type_value) {
    foreach ($type_value as $name => &$value) {
        $config_name = $type_name . '_' . $name;
        $value = GetVars('TOTORO_' . $config_name, 'POST');
        if ($type_name == 'BLACK_LIST') {
            $value = urldecode($value);
        }

        $zbp->Config('Totoro')->$config_name = $value;
        echo $config_name . '<br/>' . $value;
    }
}

$zbp->SaveConfig('Totoro');
$zbp->SetHint('good');
Redirect('main.php');
