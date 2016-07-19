<?php

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
    case 'sort':
        linkmanage_saveNav();
        break;
    case 'menu':
        linkmanage_creatLink();
        break;
    case 'del_link':
        linkmanage_deleteLink(GetVars('id', 'POST'),GetVars('menuid', 'POST'));
        break;

    default:
        # code...
        break;
}


if (GetVars('type', 'GET') == 'save_link') {
    $menu = json_decode($zbp->Config('linkmanage')->Menu, true);
    $new_menu = array();
    foreach ($_POST as $key => $value) {
        $new_menu[$key] = $value;
    }
    $new_menu['type'] = $menu['ID'.$new_menu['id']]['type'];
    $menu['ID'.$new_menu['id']] = $new_menu;
    // $menu[] = array(
    // 	"id" => "123456",
    // 	"title" => "导航栏",
    // 	"url" => "",
    // 	"newtable" => "true",
    // 	"img" => "",
    // 	"type" => "",
    // );
    $zbp->Config('linkmanage')->Menu = json_encode($menu);
    $zbp->SaveConfig('linkmanage');

    echo json_encode($_POST);
    die();
}
