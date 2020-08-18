<?php

//注册插件
RegisterPlugin("AdminColor", "ActivePlugin_AdminColor");

DefinePluginFilter('Filter_Plugin_AdminColor_CSS_Pre');

function ActivePlugin_AdminColor()
{
    global $zbp;
    if (method_exists($zbp, 'IsExclusive')) {
        if ($zbp->IsExclusive('backend-ui') != false) {
            return;
        }
        $zbp->SetExclusive('backend-ui', 'AdminColor');
    }
    if ($zbp->theme == 'Zit' && $zbp->Config('Zit')->DefaultAdmin == false) {
        return;
    }
    Add_Filter_Plugin('Filter_Plugin_Login_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Other_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'AdminColor_Css');
    Add_Filter_Plugin('Filter_Plugin_Admin_Js_Add', 'AdminColor_AddJS');
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
        $zbp->Config('AdminColor')->HeaderPath = (string) 'images/banner.jpg';
        $zbp->Config('AdminColor')->SlidingButton = (bool) 1;
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

    if (stripos($zbp->currenturl, 'phpinfo') !== false) {
        return;
    }
    $app = $zbp->LoadApp('plugin', 'AdminColor');
    echo '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_users/plugin/AdminColor/css.php?id=' . $zbp->Config('AdminColor')->ColorID . '&hast=' . crc32($zbp->Config('AdminColor')) . '&v=' . $app->modified . '"/>' . "\r\n";

    if ($zbp->Config('AdminColor')->ColorID != 10) {
        echo '<script type="text/javascript">var lang_admincolor_closemenu2 = "' . $zbp->lang['AdminColor']['closemenu'] . '";var lang_admincolor_closemenu = "<i class=icon-caret-left-fill></i>' . $zbp->lang['AdminColor']['closemenu'] . '";var lang_admincolor_expandmenu2 = "' . $zbp->lang['AdminColor']['expandmenu'] . '";var lang_admincolor_expandmenu = "<i class=icon-caret-right-fill></i>' . $zbp->lang['AdminColor']['expandmenu'] . '"</script>' . "\r\n";
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button');
        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#ededed;}.left #leftmenu span{margin-left:10px;padding-left:22px;}.left #leftmenu span i {margin-right:18px;}div.main,section.main{padding-left:46px;}</style>';
        }
    } else {
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button2');
        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#333333;}.left #leftmenu span{margin-left:10px;padding-left:22px;}.left #leftmenu span i {margin-right:18px;}div.main,section.main{padding-left:46px;}body{background-position:-125px center;}.left #leftmenu #nav_admincolor2 span{margin-left:10px;padding-left:20px ;background-position:0px 12px;}</style>';
        }
    }
}

function AdminColor_Add_Button(&$leftmenus)
{
    global $zbp;
    if ($GLOBALS['blogversion'] < 172360 || $zbp->Config('AdminColor')->SlidingButton == false) {
        return;
    }
    $hm = GetVars('admincolor_hm', 'COOKIE');
    if ($hm == '1') {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['expandmenu'], "javascript:admincolor_showMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/images/arror2.png", 'icon-caret-right-fill');
    } else {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['closemenu'], "javascript:admincolor_hideMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/images/arror.png", 'icon-caret-left-fill');
    }
}

function AdminColor_Add_Button2(&$leftmenus)
{
    global $zbp;
    if ($GLOBALS['blogversion'] < 172360 || $zbp->Config('AdminColor')->SlidingButton == false) {
        return;
    }
    $hm = GetVars('admincolor_hm', 'COOKIE');
    if ($hm == '1') {
        array_push($leftmenus, MakeLeftMenu(5, '', "javascript:admincolor_showMenu();", "nav_admincolor2", "aAdminColor2", $zbp->host . "zb_users/plugin/AdminColor/images/arror2.png", 'icon-caret-right-fill'));
    } else {
        array_push($leftmenus, MakeLeftMenu(5, '', "javascript:admincolor_hideMenu();", "nav_admincolor2", "aAdminColor2", $zbp->host . "zb_users/plugin/AdminColor/images/arror.png", 'icon-caret-left-fill'));
    }
}

function AdminColor_ColorButton()
{
    global $zbp;

    $s = '';

    for ($i = 0; $i < 9; $i++) {
        if ($i == 7) {
            continue;
        }
        $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/main.php?setcolor=" . $i . "'><span style='height:16px;width:16px;background:" . $GLOBALS['AdminColor_NormalColor'][$i] . "'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' height='16' alt='' /></span></a>&nbsp;&nbsp;";
    }

    $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/main.php?setcolor=9' title='星空云'><span style='height:16px;width:16px;background:#8db3e2;'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

    $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/main.php?setcolor=10' title='深度云'><span style='height:16px;width:16px;background:#333;'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' alt=''/></span></a>&nbsp;&nbsp;";

    echo "<div id='admin_color'>" . $s . "</div>";
}

function AdminColor_AddJS()
{
    global $zbp;
    $js1 = <<<EOD
function admincolor_hideMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_expandmenu);
 $("#aAdminColor").attr('title',lang_admincolor_expandmenu2);
 $("div.left,aside.left").css({"background-color":"#ededed"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"10px","padding-left":"22px"}); 
 $("#leftmenu span i").animate({"margin-right":"8px","margin-right":"18px"}); 

 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();

}

function admincolor_showMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_closemenu);
 $("#aAdminColor").attr('title',lang_admincolor_closemenu2);
 $("div.left,aside.left").css({"background-color":"transparent"});
 $("div.left,aside.left").animate({"width":"140px"});
 $("div.main,section.main").animate({"padding-left":"150px"});
 $("#leftmenu span").animate({"margin-left":"25px","padding-left":"22px"});
 $("#leftmenu span i").animate({"margin-right":"18px","margin-right":"8px"}); 

 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
 //$("#leftmenu a").tooltip( "destroy" );
}

function admincolor_tooptip(){
    $("#leftmenu a").tooltip({
      disabled:false,
      position: {
        my: "left+50 top-33",
       //my: "left+50 top-33",
       at: "left bottom",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow_leftmenu" )
            .appendTo( this );
        }
      }
    });
}

$(document).ready(function(){
  if(GetCookie('admincolor_hm')=='1') {admincolor_tooptip();}
});
EOD;

    $js2 = <<<EOD
function admincolor_hideMenu(){
 $("#aAdminColor2").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor2 span i").attr('class','icon-caret-right-fill');
 $("div.left,aside.left").css({"background-color":"#333333"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"10px","padding-left":"22px"}); 
 $("#leftmenu span i").animate({"margin-right":"8px","margin-right":"18px"}); 

 $("#leftmenu #nav_admincolor2 span").animate({"margin-left":"10px","padding-left":"20px","background-positionX":"0px"}); 
 $("body").animate({"background-positionX":"-125px"}); 
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();
}

function admincolor_showMenu(){
 $("#aAdminColor2").attr('href','javascript:admincolor_hideMenu()');
 $("#aAdminColor2 span i").attr('class','icon-caret-left-fill');
 $("div.left,aside.left").css({"background-color":"#333333"});
 $("div.left,aside.left").animate({"width":"160px"});
 $("div.main,section.main").animate({"padding-left":"170px"});
 $("#leftmenu span").animate({"margin-left":"25px","padding-left":"29px"});
 $("#leftmenu span i").animate({"margin-right":"18px","margin-right":"8px"}); 

 $("#leftmenu #nav_admincolor2 span").animate({"padding-left":"60px","background-positionX":"40px"}); 
 $("body").animate({"background-positionX":"+0px"}); 
 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
 //$("#leftmenu a").tooltip( "destroy" );
}

function admincolor_tooptip(){
    $("#leftmenu a").tooltip({
      disabled:false,
      position: {
        my: "left+50 top-33",
       //my: "left+50 top-33",
       at: "left bottom",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow_leftmenu" )
            .appendTo( this );
        }
      }
    });
}

$(document).ready(function(){
  if(GetCookie('admincolor_hm')=='1') {admincolor_tooptip();}
});
EOD;

    $js3 = <<<EOD
function admincolor_hideMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_expandmenu);
 $("#aAdminColor").attr('title',lang_admincolor_expandmenu2);
 $("div.left,aside.left").css({"background-color":"#e3eaf3"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"10px","padding-left":"22px"}); 
 $("#leftmenu span i").animate({"margin-right":"8px","margin-right":"18px"});  
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();
}

function admincolor_showMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_closemenu);
 $("#aAdminColor").attr('title',lang_admincolor_closemenu2);
 $("div.left,aside.left").css({"background-color":"Transparent"});
 $("div.left,aside.left").animate({"width":"140px"});
 $("div.main,section.main").animate({"padding-left":"150px"});
 $("#leftmenu span").animate({"margin-left":"25px","padding-left":"22px"});
 $("#leftmenu span i").animate({"margin-right":"18px","margin-right":"8px"}); 
 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
 //$("#leftmenu a").tooltip( "destroy" );
}

function admincolor_tooptip(){
    $("#leftmenu a").tooltip({
      disabled:false,
      position: {
        my: "left+50 top-33",
       //my: "left+50 top-33",
       at: "left bottom",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow_leftmenu" )
            .appendTo( this );
        }
      }
    });
}

$(document).ready(function(){
  if(GetCookie('admincolor_hm')=='1') {admincolor_tooptip();}
});
EOD;

    if ($zbp->Config('AdminColor')->ColorID == 10) {
        echo $js2;
    } elseif ($zbp->Config('AdminColor')->ColorID == 9) {
        echo $js3;
    } else {
        echo $js1;
    }
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

$AdminColor_BlodColor[8] = '#a60138';
$AdminColor_NormalColor[8] = '#ff6699';
$AdminColor_LightColor[8] = '#f993b5';
$AdminColor_HighColor[8] = '#df4679';
$AdminColor_AntiColor[8] = '#df1ce6';

$AdminColor_BlodColor[9] = '#17365d';
$AdminColor_NormalColor[9] = '#366092';
$AdminColor_LightColor[9] = '#b8cce4';
$AdminColor_HighColor[9] = '#8db3e2';
$AdminColor_AntiColor[9] = '#e36c09';

$AdminColor_BlodColor[10] = '#262f3e';
$AdminColor_NormalColor[10] = '#0070c0';
$AdminColor_LightColor[10] = '#D9DFE5';
$AdminColor_HighColor[10] = '#3f3f3f';
$AdminColor_AntiColor[10] = '#c0504d';
