<?php

//注册插件
RegisterPlugin("Howl", "ActivePlugin_Howl");

function ActivePlugin_Howl()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_Zbp_CheckRights', 'Howl_CheckRights');
    $zbp->LoadLanguage('plugin', 'Howl');
}

function InstallPlugin_Howl()
{
    global $zbp;
    $zbp->Config('Howl')->version = '1.0';
    $zbp->SaveConfig('Howl');
}

function UninstallPlugin_Howl()
{
    global $zbp;
    //$zbp->DelConfig('Howl');
}

function Howl_GetRightName($key)
{
    global $zbp;
    if (isset($zbp->lang['actions'][$key])) {
        return $zbp->lang['actions'][$key];
    } else {
        return $zbp->lang['Howl'][''];
    }
}

function Howl_CheckRights(&$action)
{
    global $zbp;

    $group_nums = count($zbp->lang['user_level_name']);
    $group_key = array();
    foreach ($zbp->lang['user_level_name'] as $key => $value) {
        $group_key[] = $key;
    }

    $a = array();
    foreach ($group_key as $key) {
        $a[$key] = array();
    }

    $g = $zbp->user->Level;
    foreach ($group_key as $key) {
        $name = 'Group' . $key;
        if ($zbp->Config('Howl')->HasKey($name)) {
            $a[$key] = $zbp->Config('Howl')->$name;
        }
    }

    $userid = 'User' . $zbp->user->ID;
    if ($zbp->Config('Howl')->HasKey($userid)) {
        $useractions = $zbp->Config('Howl')->$userid;
        if (array_key_exists($action, $useractions)) {
            $GLOBALS['Filter_Plugin_Zbp_CheckRights']['Howl_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;

            return (bool) $useractions[$action];
        }
    }

    if (array_key_exists($action, $a[$g])) {
        $GLOBALS['Filter_Plugin_Zbp_CheckRights']['Howl_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;

        return (bool) $a[$g][$action];
    }
}
