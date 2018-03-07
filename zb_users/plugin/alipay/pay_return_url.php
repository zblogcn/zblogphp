<?php
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once 'alipay.config.php';
require_once 'lib/alipay_notify.class.php';
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
    //验证成功
    //商户订单号
    $out_trade_no = $_GET['out_trade_no'];

    //支付宝交易号
    $trade_no = $_GET['trade_no'];

    //交易状态
    $trade_status = $_GET['trade_status'];

    if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayPayReturn_Succeed'] as $fpname => &$fpsignal) {
            $fpname($_GET);
        }
    } else {
        foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayPayReturn_Failed'] as $fpname => &$fpsignal) {
            $fpname($_GET);
        }
    }
} else {
    //验证失败

    foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayPayReturn_Failed'] as $fpname => &$fpsignal) {
        $fpname($_GET);
    }
}
