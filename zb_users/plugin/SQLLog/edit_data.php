<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('SQLLog')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'SQLLog';
$filename = GetVars('filename', 'GET');
$handle = opendir(SQLLOG_LOGPATH);

switch (GetVars('act', 'GET')) {
    case 'delete_all':
        while (($filename_in_while = readdir($handle))) {
            if (!is_dir(SQLLOG_LOGPATH . $filename_in_while) && preg_match("/\d+_zbp_.+?\.php/", $filename_in_while)) {
                unlink(SQLLOG_LOGPATH . $filename_in_while);
            }
        }

        $zbp->SetHint('good');
        Redirect('main.php');
        break;
    case 'delete':
        unlink(SQLLOG_LOGPATH . $filename);
        $zbp->SetHint('good');
        Redirect('main.php');
        break;
    case 'download':
        ob_clean();
        header('Content-Type: application/octet-stream');
        header('Content-Disposition:attachment;filename=' . str_replace('.php', '.txt', $filename));
        $file = fopen(SQLLOG_LOGPATH . $filename, 'r');
        echo fread($file, filesize(SQLLOG_LOGPATH . $filename));
        fclose($file);
        break;
}
