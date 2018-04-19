<?php

DefinePluginFilter('Filter_Plugin_AlipayLogin_Succeed');
DefinePluginFilter('Filter_Plugin_AlipayLogin_Failed');
DefinePluginFilter('Filter_Plugin_AlipayPayReturn_Succeed');
DefinePluginFilter('Filter_Plugin_AlipayPayReturn_Failed');
DefinePluginFilter('Filter_Plugin_AlipayPayNotice_Succeed');
DefinePluginFilter('Filter_Plugin_AlipayBatchTrans_Notice');
//注册插件
RegisterPlugin('alipay', 'ActivePlugin_alipay');

function ActivePlugin_alipay()
{
}

function InstallPlugin_alipay()
{
    global $zbp;
    if (!$zbp->Config('alipay')->HasKey('ver')) {
        $zbp->Config('alipay')->ver = '12';
        $zbp->Config('alipay')->partner = '';
        $zbp->Config('alipay')->key = '';
        $zbp->Config('alipay')->alipayaccount = '';
        $zbp->Config('alipay')->alipayname = '';
        $zbp->Config('alipay')->transport = '';
        $zbp->Config('alipay')->savelogs = 0;
        $zbp->SaveConfig('alipay');
    }
    if ($zbp->Config('alipay')->HasKey('payforname')) {
        $zbp->Config('alipay')->DelKey('payforname');
    }
    if ($zbp->Config('alipay')->HasKey('notify_add')) {
        $zbp->Config('alipay')->DelKey('notify_add');
    }
    $zbp->Config('alipay')->Version = '12';
    $zbp->SaveConfig('alipay');
}
