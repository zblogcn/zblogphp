<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('metro')) {
    $zbp->ShowError(48);
    die();
}

echo GetVars('bodybg5', 'POST');
//检查过滤

//Dim strb,strc,strh,strl
    $sbg = $shg = "";

    if (!empty($_POST['bodybg2'])) {
        $sbg = implode(" ", $_POST['bodybg2']);
    }
    if (!empty($_POST['hdbg2'])) {
        $shg = implode(" ", $_POST['hdbg2']);
    }

    if (empty($_POST['bodybg5'])) {
        $_POST['bodybg5'] = "";
    }
    if (empty($_POST['bodybg0'])) {
        $_POST['bodybg0'] = "";
    }

    if (empty($_POST['hdbg0'])) {
        $_POST['hdbg0'] = "";
    }
    if (empty($_POST['hdbg6'])) {
        $_POST['hdbg6'] = "";
    }

    $strl = $_POST["layout"];
    $strb = $_POST["bodybg0"] . "|" . $_POST["bodybg1"] . "|" . $sbg . "|" . $_POST["bodybg3"] . "|" . $_POST["bodybg4"] . "|" . $_POST["bodybg5"];
    $strh = $_POST["hdbg0"] . "|" . $_POST["hdbg1"] . "|" . $shg . "|" . $_POST["hdbg3"] . "|" . $_POST["hdbg4"] . "|" . $_POST["hdbg5"] . "|" . $_POST["hdbg6"];
    $strc = implode("|", $_POST["color"]);

    $zbp->Config('metro')->custom_layout = $strl;
    $zbp->Config('metro')->custom_bodybg = $strb;
    $zbp->Config('metro')->custom_hdbg = $strh;
    $zbp->Config('metro')->custom_color = $strc;
    $zbp->SaveConfig('metro');

    metro_savetofile("style.css");

    $zbp->SetHint('good');
    Redirect('./editor.php');
