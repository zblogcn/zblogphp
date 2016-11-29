<?php
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 */
require_once 'alipay.aconfig.php';
require_once 'lib/alipay_notify.class.php';
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
    //验证成功
    //print_r($_GET);
    foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayLogin_Succeed'] as $fpname => &$fpsignal) {
        $fpname($_GET);
    }
} else {
    //验证失败
    foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayLogin_Failed'] as $fpname => &$fpsignal) {
        $fpname($_GET);
    }
}
