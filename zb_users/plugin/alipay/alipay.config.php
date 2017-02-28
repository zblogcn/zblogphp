<?php
/* *
 * 配置文件
 */
require_once dirname(__FILE__) . '../../../../zb_system/function/c_system_base.php';

$zbp->Load();
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
$alipay_config['partner'] = $zbp->Config('alipay')->partner;

//卖家支付宝用户号
$alipay_config['seller_email'] = $zbp->Config('alipay')->alipayaccount;

//卖家支付宝账户名
$alipay_config['alipayname'] = $zbp->Config('alipay')->alipayname;

//安全检验码，以数字和字母组成的32位字符
$alipay_config['key'] = $zbp->Config('alipay')->key;

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//签名方式 不需修改
$alipay_config['sign_type'] = strtoupper('MD5');

//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset'] = strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert'] = getcwd() . '/lib/cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport'] = $zbp->Config('alipay')->transport;
