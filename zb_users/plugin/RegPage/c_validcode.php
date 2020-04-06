<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 * @version
 */
require '../../../zb_system/function/c_system_base.php';
require './validatecode.php';

$zbp->Load();
ob_clean();

Add_Filter_Plugin('Filter_Plugin_Zbp_ShowValidCode', 'RegPage_ShowValidCode');

$zbp->option['ZC_VERIFYCODE_STRING'] = 'ABCDEFGHJKMNPQRSTUVWXYZ1234567890';

$zbp->ShowValidCode(GetVars('id', 'GET'));

function RegPage_ShowValidCode($id = '')
{
    global $zbp;
    $ua_md5 = GetVars('REMOTE_ADDR', 'SERVER') . GetVars('hash', 'GET');
    $_vc = new RegPage_ValidateCode();
    $_vc->GetImg();
    setcookie('captcha_' . crc32($zbp->guid . $id), md5($zbp->guid . date("Ymdh") . $_vc->GetCode() . $ua_md5), null, $zbp->cookiespath);
    die;
}
