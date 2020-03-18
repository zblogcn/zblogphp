<?php

RegisterPlugin("default", "ActivePlugin_default");

function ActivePlugin_default()
{
    global $zbp;
    $zbp->LoadLanguage('theme', 'default');
    $zbp->lang['msg']['first_button'] = '⏮️';
    $zbp->lang['msg']['prev_button'] = '◀️';
    $zbp->lang['msg']['next_button'] = '▶️';
    $zbp->lang['msg']['last_button'] = '⏭️';
    $zbp->option['ZC_SEARCH_TYPE'] = 'list';
    $zbp->option['ZC_SEARCH_COUNT'] = 10;
}
