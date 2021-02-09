<?php

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->CheckPlugin('RegPage')) {
    $zbp->ShowError(48);
    die();
}

header('Content-Type: text/html; Charset=utf-8');

if (!$zbp->Config('RegPage')->open_reg) {
    echo '<p>本网站不开放会员注册.</p>';
    die();
}

$sql = $zbp->db->sql->Select($RegPage_Table, '*', array(array('=', 'reg_AuthorID', 0)), null, array(100), null);
$array = $zbp->GetListCustom($RegPage_Table, $RegPage_DataInfo, $sql);
$num = count($array);
if ($num == 0) {
    echo '<p>邀请码派发完了.</p>';
} else {
    echo '<p>邀请码: </p><p>' . $array[mt_rand(0, $num - 1)]->InviteCode . '</p><p>请选中邀请码并复制后点OK按钮.</p>';
}

die();
