<?php

RegisterPlugin("default", "ActivePlugin_default");

function ActivePlugin_default()
{
    global $zbp;
    $zbp->LoadLanguage('theme', 'default');
    $zbp->lang['msg']['first_button'] = '&lt;&lt;';
    $zbp->lang['msg']['prev_button'] = '&lt;';
    $zbp->lang['msg']['next_button'] = '&gt;';
    $zbp->lang['msg']['last_button'] = '&gt;&gt;';
    Add_Filter_Plugin('Filter_Plugin_ViewSearch_Begin', 'default_ViewSearch_Begin');
}

function default_ViewSearch_Begin()
{
    global $zbp;
    $zbp->option['ZC_SEARCH_TYPE'] = 'list';
}
