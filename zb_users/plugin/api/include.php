<?php

//注册插件
RegisterPlugin("api", "ActivePlugin_api");

function ActivePlugin_api()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_API_Begin', 'api_main');
}

function api_main()
{
    global $zbp;
    echo 'api';
}

function InstallPlugin_api()
{
    global $zbp;
}

function UninstallPlugin_api()
{
    global $zbp;
}
