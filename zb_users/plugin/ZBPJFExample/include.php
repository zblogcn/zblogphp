<?php

//注册插件
RegisterPlugin("ZBPJFExample", "ActivePlugin_ZBPJFExample");

function ActivePlugin_ZBPJFExample()
{
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'ZBPJFExample_AddCode');
}

function ZBPJFExample_AddCode()
{
    echo file_get_contents(dirname(__FILE__) . '/zbpjfexample.js');
}
function InstallPlugin_ZBPJFExample()
{
}
function UninstallPlugin_ZBPJFExample()
{
}
