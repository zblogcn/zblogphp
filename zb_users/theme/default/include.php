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
    $zbp->option['ZC_SEARCH_TYPE'] = 'list';
    $zbp->option['ZC_SEARCH_COUNT'] = 10;
}
