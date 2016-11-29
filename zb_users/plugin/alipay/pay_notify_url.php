<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23

 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once 'alipay.config.php';
require_once 'lib/alipay_notify.class.php';

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if ($verify_result) {
    //验证成功
    //商户订单号
    $out_trade_no = $_POST['out_trade_no'];

    //支付宝交易号
    $trade_no = $_POST['trade_no'];

    //交易状态
    $trade_status = $_POST['trade_status'];

    if ($_POST['trade_status'] == 'TRADE_FINISHED') {
        foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayPayNotice_Succeed'] as $fpname => &$fpsignal) {
            $fpname($_POST);
        }

        //注意：
        //该种交易状态只在两种情况下出现
        //1、开通了普通即时到账，买家付款成功后。
        //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
    } elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayPayNotice_Succeed'] as $fpname => &$fpsignal) {
            $fpname($_POST);
        }

        //注意：
        //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
    }
    echo 'success';
} else {
    //验证失败
    echo 'fail';
}
