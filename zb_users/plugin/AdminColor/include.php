<?php

//注册插件
RegisterPlugin("AdminColor", "ActivePlugin_AdminColor");

DefinePluginFilter('Filter_Plugin_AdminColor_CSS_Pre');

function ActivePlugin_AdminColor()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_Login_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Other_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Admin_Footer', 'AdminColor_Css2');
    $zbp->LoadLanguage('plugin', 'AdminColor');
}

function InstallPlugin_AdminColor()
{
    global $zbp;
    if ($zbp->HasConfig('AdminColor') == false) {
        $zbp->Config('AdminColor')->ColorID = 0;
        $zbp->Config('AdminColor')->BlodColor = (string) $GLOBALS['AdminColor_BlodColor'][0];
        $zbp->Config('AdminColor')->NormalColor = (string) $GLOBALS['AdminColor_NormalColor'][0];
        $zbp->Config('AdminColor')->LightColor = (string) $GLOBALS['AdminColor_LightColor'][0];
        $zbp->Config('AdminColor')->HighColor = (string) $GLOBALS['AdminColor_HighColor'][0];
        $zbp->Config('AdminColor')->AntiColor = (string) $GLOBALS['AdminColor_AntiColor'][0];
        $zbp->SaveConfig('AdminColor');
    }
}

function UninstallPlugin_AdminColor()
{
    global $zbp;
    $zbp->DelConfig('AdminColor');
}

function AdminColor_Css()
{
    global $zbp;

    $aly = '';
    $f = get_included_files();
    if (strpos(array_pop($f), 'admin_header.php') !== false) {
        $aly = '?aly';
    }

    echo '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_users/plugin/AdminColor/css.php' . $aly . '"/>' . "\r\n";

    if ($zbp->Config('AdminColor')->ColorID != 10) {
        echo '<script type="text/javascript">var lang_admincolor_closemenu = "' . $zbp->lang['AdminColor']['closemenu'] . '";var lang_admincolor_expandmenu = "' . $zbp->lang['AdminColor']['expandmenu'] . '"</script>' . "\r\n";
        echo '<script src="' . $zbp->host . 'zb_users/plugin/AdminColor/menu.js" type="text/javascript"></script>' . "\r\n";
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button');
        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#ededed;}.left #leftmenu span{margin-left:10px;padding-left:100px;}div.main,section.main{padding-left:46px;}</style>';
        }
    } else {
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button2');

        echo '<script src="' . $zbp->host . 'zb_users/plugin/AdminColor/menu2.js" type="text/javascript"></script>' . "\r\n";

        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#22282e;}.left #leftmenu span{margin-left:10px;padding-left:100px;}div.main,section.main{padding-left:46px;}body{background-position:-125px center;}.left #leftmenu #nav_admincolor2 span{margin-left:10px;padding-left:20px ;background-position:0px 12px;}</style>';
        }
    }
}

function AdminColor_Css2()
{
    global $zbp;
    if ($zbp->Config('AdminColor')->ColorID == 10) {
        echo '
<script type="text/javascript">
$("#leftmenu li span").each(function(k){
  if(k>0){
  var i=$(this).css("background-image");
  var j=i.replace("1.","2.");
  $(this).css("cssText","background-image:-webkit-cross-fade("+j+","+i+",30%)!important");
  }
});
</script>';
    }
}

function AdminColor_Add_Button(&$leftmenus)
{
    global $zbp;

    $hm = GetVars('admincolor_hm', 'COOKIE');
    if ($hm == '1') {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['expandmenu'], "javascript:admincolor_showMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/arror2.png");
    } else {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['closemenu'], "javascript:admincolor_hideMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/arror.png");
    }
}

function AdminColor_Add_Button2(&$leftmenus)
{
    global $zbp;

    $hm = GetVars('admincolor_hm', 'COOKIE');
    if ($hm == '1') {
        array_unshift($leftmenus, MakeLeftMenu(5, '', "javascript:admincolor_showMenu();", "nav_admincolor2", "aAdminColor2", $zbp->host . "zb_users/plugin/AdminColor/arroraly2.png"));
    } else {
        array_unshift($leftmenus, MakeLeftMenu(5, '', "javascript:admincolor_hideMenu();", "nav_admincolor2", "aAdminColor2", $zbp->host . "zb_users/plugin/AdminColor/arroraly.png"));
    }
}

function AdminColor_ColorButton()
{
    global $zbp;

    $s = '';

    for ($i = 0; $i < 9; $i++) {
        $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/css.php?setcolor=" . $i . "'><span style='height:16px;width:16px;background:" . $GLOBALS['AdminColor_NormalColor'][$i] . "'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' height='16' alt='' /></span></a>&nbsp;&nbsp;";
    }

    $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/css.php?setcolor=9' title='文艺范'><span style='height:16px;width:16px;background:#eee;'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

    $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/css.php?setcolor=10' title='阿里云'><span style='height:16px;width:16px;background:#0099CD;'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

    echo "<div id='admin_color'>" . $s . "</div>";
    //echo "<script type='text/javascript'>$('.divHeader').append(\"<div id='admin_color'>" . $s . "</div>\");</script>";
}

$AdminColor_BlodColor[0] = '#1d4c7d';
$AdminColor_NormalColor[0] = '#3a6ea5';
$AdminColor_LightColor[0] = '#b0cdee';
$AdminColor_HighColor[0] = '#3399cc';
$AdminColor_AntiColor[0] = '#d60000';

$AdminColor_BlodColor[1] = '#143c1f';
$AdminColor_NormalColor[1] = '#5b992e';
$AdminColor_LightColor[1] = '#bee3a3';
$AdminColor_HighColor[1] = '#6ac726';
$AdminColor_AntiColor[1] = '#d60000';

$AdminColor_BlodColor[2] = '#06282b';
$AdminColor_NormalColor[2] = '#2db1bd';
$AdminColor_LightColor[2] = '#87e6ef';
$AdminColor_HighColor[2] = '#119ba7';
$AdminColor_AntiColor[2] = '#d60000';

$AdminColor_BlodColor[3] = '#3e1165';
$AdminColor_NormalColor[3] = '#5c2c84';
$AdminColor_LightColor[3] = '#a777d0';
$AdminColor_HighColor[3] = '#8627d7';
$AdminColor_AntiColor[3] = '#08a200';

$AdminColor_BlodColor[4] = '#3f280d';
$AdminColor_NormalColor[4] = '#b26e1e';
$AdminColor_LightColor[4] = '#e3b987';
$AdminColor_HighColor[4] = '#d88625';
$AdminColor_AntiColor[4] = '#d60000';

$AdminColor_BlodColor[5] = '#0a4f3e';
$AdminColor_NormalColor[5] = '#267662';
$AdminColor_LightColor[5] = '#68cdb4';
$AdminColor_HighColor[5] = '#25bb96';
$AdminColor_AntiColor[5] = '#d60000';

$AdminColor_BlodColor[6] = '#3a0b19';
$AdminColor_NormalColor[6] = '#7c243f';
$AdminColor_LightColor[6] = '#d57c98';
$AdminColor_HighColor[6] = '#d31b54';
$AdminColor_AntiColor[6] = '#2039b7';

$AdminColor_BlodColor[7] = '#2d2606';
$AdminColor_NormalColor[7] = '#d9b611';
$AdminColor_LightColor[7] = '#ebd87d';
$AdminColor_HighColor[7] = '#c4a927';
$AdminColor_AntiColor[7] = '#d60000';

$AdminColor_BlodColor[8] = '#3f0100';
$AdminColor_NormalColor[8] = '#e5535f';
$AdminColor_LightColor[8] = '#ffb3a7';
$AdminColor_HighColor[8] = '#da4b4a';
$AdminColor_AntiColor[8] = '#ff000c';

$AdminColor_BlodColor[9] = '#4a380b';
$AdminColor_NormalColor[9] = '#896a1c';
$AdminColor_LightColor[9] = '#caa855';
$AdminColor_HighColor[9] = '#b08313';
$AdminColor_AntiColor[9] = '#2227e0';

$AdminColor_BlodColor[10] = '#0087B5';
$AdminColor_NormalColor[10] = '#0099CD';
$AdminColor_LightColor[10] = '#D9DFE5';
$AdminColor_HighColor[10] = '#3399cc';
$AdminColor_AntiColor[10] = '#d60000';
