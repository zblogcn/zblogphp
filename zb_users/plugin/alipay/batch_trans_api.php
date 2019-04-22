<?php

//构造要请求的参数数组，无需改动
// $trans_detail = array(
// array('流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1')
// );

function AlipayAPI_Start($trans_detail, $parameter = array())
{
    global $zbp;
    require_once 'alipay.config.php';
    require_once 'lib/alipay_submit.class.php';

    //公共$parameter

    $parameter['service'] = 'batch_trans_notify';
    $parameter['partner'] = trim($alipay_config['partner']);
    $parameter['email'] = trim($alipay_config['seller_email']);
    $parameter['account_name'] = trim($alipay_config['alipayname']);
    $parameter['_input_charset'] = trim(strtolower($alipay_config['input_charset']));
    $parameter['notify_url'] = (isset($parameter['notify_url'])) ? $parameter['notify_url'] : ($zbp->host . 'zb_users/plugin/alipay/batch_trans_notify_url.php');
    $parameter['pay_date'] = date('Ymd');
    $parameter['batch_no'] = (isset($parameter['batch_no'])) ? $parameter['batch_no'] : (date('Ymd') . time());
    $parameter['batch_num'] = count($trans_detail);
    $batch_fee = 0;
    $detail_data = '';
    foreach ($trans_detail as $key => $value) {
        //流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1
        $detail_data = '|' . $value[0] . $value[1] . $value[2] . $value[3] . $value[4];
        $batch_fee += $value[3];
    }

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
    echo $html_text;
}
