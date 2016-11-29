<?php
DefinePluginFilter('Filter_Plugin_AlipayLogin_Succeed');
DefinePluginFilter('Filter_Plugin_AlipayLogin_Failed');
DefinePluginFilter('Filter_Plugin_AlipayPayReturn_Succeed');
DefinePluginFilter('Filter_Plugin_AlipayPayReturn_Failed');
DefinePluginFilter('Filter_Plugin_AlipayPayNotice_Succeed');
#注册插件
RegisterPlugin("alipay","ActivePlugin_alipay");

function ActivePlugin_alipay() {

}
?>