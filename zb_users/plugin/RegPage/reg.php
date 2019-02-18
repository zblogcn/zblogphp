<?php

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'RespondError', PLUGIN_EXITSIGNAL_RETURN);

if (!$zbp->CheckPlugin('RegPage')) {
    $zbp->ShowError(48);
    die();
}

Add_Filter_Plugin('Filter_Plugin_Zbp_CheckValidCode', 'RegPage_CheckValidCode');

function RegPage_CheckValidCode($vaidcode, $id = '')
{
    global $zbp;
    $ua_md5 = GetVars('REMOTE_ADDR', 'SERVER') . GetVars('hash', 'POST');
    $vaidcode = strtolower($vaidcode);
    $original = GetVars('captcha_' . crc32($zbp->guid . $id), 'COOKIE');
    setcookie('captcha_' . crc32($zbp->guid . $id), '', time() - 3600, $zbp->cookiespath);

    return md5($zbp->guid . date("Ymdh") . strtolower($vaidcode) . $ua_md5) == $original
            ||
            md5($zbp->guid . date("Ymdh", time() - (3600 * 1)) . strtolower($vaidcode) . $ua_md5) == $original;
}

$name = trim($_POST['name']);
$password = trim($_POST['password']);
$repassword = trim($_POST['repassword']);
$email = trim($_POST['email']);

$invitecode = trim($_POST['invitecode']);

$homepage = '';

if ($zbp->Config('RegPage')->disable_website != true) {
    $homepage = trim($_POST['homepage']);
}

if ($zbp->Config('RegPage')->disable_validcode != true) {
    $verifycode = trim($_POST['verifycode']);
    if (!$zbp->CheckValidCode($verifycode, 'RegPage')) {
        $zbp->ShowError('验证码错误，请重新输入.');
        die();
    }
}

$member = new Member();

if ($zbp->Config('RegPage')->only_one_ip) {
    $sql = $zbp->db->sql->Select($RegPage_Table, '*', array(array('=', 'reg_IP', GetVars('REMOTE_ADDR', 'SERVER')), array('>', 'reg_Time', time() - 23 * 3600)), null, null, null);
    $array = $zbp->GetListCustom($RegPage_Table, $RegPage_DataInfo, $sql);
    $num = count($array);
    if ($num > 0) {
        $zbp->ShowError('今天已注册过了请明天再来注册.');
        die();
    }
}

$sql = $zbp->db->sql->Select($RegPage_Table, '*', array(array('=', 'reg_InviteCode', $invitecode), array('=', 'reg_AuthorID', 0)), null, null, null);
$array = $zbp->GetListCustom($RegPage_Table, $RegPage_DataInfo, $sql);
$num = count($array);
if ($num == 0) {
    $zbp->ShowError('邀请码不存在或已被使用.');
    die();
}
$reg = $array[0];

$member->Guid = $invitecode;
$member->Level = $reg->Level;

if (strlen($name) < $zbp->option['ZC_USERNAME_MIN'] || strlen($name) > $zbp->option['ZC_USERNAME_MAX']) {
    $zbp->ShowError('用户名不能过长或过短.');
    die();
}

if (!CheckRegExp($name, '[username]')) {
    $zbp->ShowError('用户名只能包含字母数字._和中文.');
    die();
}

if ($zbp->GetMemberByName($name)->ID > 0) {
    $zbp->ShowError('用户名已存在');
    die();
}

$member->Name = $name;

if (strlen($password) < $zbp->option['ZC_PASSWORD_MIN'] || strlen($password) > $zbp->option['ZC_PASSWORD_MAX']) {
    $zbp->ShowError('密码必须在' . $zbp->option['ZC_PASSWORD_MIN'] . '位-' . $zbp->option['ZC_PASSWORD_MAX'] . '位间.');
    die();
}

if ($password != $repassword) {
    $zbp->ShowError('请核对密码.');
    die();
}

$member->Password = Member::GetPassWordByGuid($password, $invitecode);

$member->PostTime = time();

$member->IP = GetGuestIP();

if (strlen($email) < 5 || strlen($email) > $zbp->option['ZC_EMAIL_MAX']) {
    $zbp->ShowError('邮箱不能过长或过短.');
    die();
}

if (CheckRegExp($email, '[email]')) {
    $member->Email = $email;
} else {
    $zbp->ShowError('邮箱格式不正确.');
    die();
}

if (RegPage_CheckEmail($member->Email) == true) {
    $zbp->ShowError('该邮箱已被注册使用.');
    die();
}

if (strlen($homepage) > $zbp->option['ZC_HOMEPAGE_MAX']) {
    $zbp->ShowError('网址不能过长.');
    die();
}

if (CheckRegExp($homepage, '[homepage]')) {
    $member->HomePage = $homepage;
}

$member->Save();

foreach ($GLOBALS['hooks']['Filter_Plugin_RegPage_RegSucceed'] as $fpname => &$fpsignal) {
    $fpname($member);
}

$keyvalue = array();
$keyvalue['reg_AuthorID'] = $member->ID;
$keyvalue['reg_IP'] = GetVars('REMOTE_ADDR', 'SERVER');
$keyvalue['reg_Time'] = time();

$sql = $zbp->db->sql->Update($RegPage_Table, $keyvalue, array(array('=', 'reg_ID', $reg->ID)));
$zbp->db->Update($sql);

//var_dump($member);

echo '恭喜您注册成功,请在登录页面登录.';
