<?php
/* *
 * 功能：支付宝服务器异步通知页面

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

    foreach ($GLOBALS['hooks']['Filter_Plugin_AlipayBatchTrans_Notice'] as $fpname => &$fpsignal) {
        $fpname($_POST);
    }

    //批量付款数据中转账成功的详细信息

    //$success_details = $_POST['success_details'];

    //批量付款数据中转账失败的详细信息
    //$fail_details = $_POST['fail_details'];

    //判断是否在商户网站中已经做过了这次通知返回的处理
    //如果没有做过处理，那么执行商户的业务程序
    //如果有做过处理，那么不执行商户的业务程序

    echo 'success';        //请不要修改或删除
} else {
    //验证失败
    echo 'fail';
}
