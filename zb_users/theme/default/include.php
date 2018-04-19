<?php

RegisterPlugin("default", "ActivePlugin_default");

function ActivePlugin_default()
{
    global $zbp;
    $zbp->LoadLanguage('theme', 'default');
}
