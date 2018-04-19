<?php

header('Content-type: application/json');

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('linkmanage')) {
    $zbp->ShowError(48);
    die();
}

switch (GetVars('type', 'GET')) {
    case 'save_menu':
        linkmanage_saveMenu();
        break;
    case 'del_link':
        linkmanage_deleteLink(GetVars('id', 'POST'), GetVars('menuid', 'POST'));
        break;
    case 'save_link':
        //linkmanage_saveLink();
        linkmanage_saveLink_s(GetVars('menuid', 'POST'));
        break;
    case 'save_sort':
        //linkmanage_saveLink();
        linkmanage_saveLink_s_sort(GetVars('menuid', 'POST'));
        break;
    case 'save_config':
        linkmanage_saveConfig();
        break;
    default:
        // code...
        break;
}
