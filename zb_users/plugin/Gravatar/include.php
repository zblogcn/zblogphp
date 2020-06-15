<?php

//注册插件
RegisterPlugin("Gravatar", "ActivePlugin_Gravatar");

function ActivePlugin_Gravatar()
{
    Add_Filter_Plugin('Filter_Plugin_Member_Avatar', 'Gravatar_Url');
}

function InstallPlugin_Gravatar()
{
    global $zbp;
    //$zbp->Config('Gravatar')->default_url='http://cn.gravatar.com/avatar/{%emailmd5%}?s=40&d={%source%}';
    $zbp->Config('Gravatar')->default_url = '//dn-qiniu-avatar.qbox.me/avatar/{%emailmd5%}.png?s=60&d=mm&r=G';
    $zbp->Config('Gravatar')->source = '{%host%}zb_users/avatar/0.png';
    $zbp->Config('Gravatar')->local_priority = 0;
    $zbp->SaveConfig('Gravatar');
}

function UninstallPlugin_Gravatar()
{
    global $zbp;
    $zbp->DelConfig('Gravatar');
}

function Gravatar_Url(&$member)
{
    global $zbp;
    $default_url = $zbp->Config('Gravatar')->default_url;
    $source = $zbp->Config('Gravatar')->source;
    $source = str_replace('{%host%}', $zbp->host, $source);

    if ($zbp->Config('Gravatar')->local_priority && $member->ID > 0) {
        if (file_exists($zbp->usersdir . 'avatar/' . $member->ID . '.png')) {
            //$GLOBALS['Filter_Plugin_Member_Avatar']['Gravatar_Url']=PLUGIN_EXITSIGNAL_BREAK;
            //return null;
            $GLOBALS['Filter_Plugin_Member_Avatar']['Gravatar_Url'] = PLUGIN_EXITSIGNAL_RETURN;
            $s = $zbp->host . 'zb_users/avatar/' . $member->ID . '.png';

            return $s;
        }
    }

    if ($member->Email !== '') {
        $GLOBALS['Filter_Plugin_Member_Avatar']['Gravatar_Url'] = PLUGIN_EXITSIGNAL_RETURN;
        $s = $default_url;
        $s = str_replace('{%source%}', urlencode($source), $s);
        $s = str_replace('{%emailmd5%}', md5($member->Email), $s);

        return htmlspecialchars($s);
    } else {
        return $source;
    }
}
