<?php

//注册插件
RegisterPlugin("AdminColor", "ActivePlugin_AdminColor");

DefinePluginFilter('Filter_Plugin_AdminColor_CSS_Pre');
DefinePluginFilter('Filter_Plugin_AdminColor_CSS');
DefinePluginFilter('Filter_Plugin_AdminColor_AddJS');

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
    if($zbp->HasConfig('AdminColor') && isset($zbp->admin_js_hash)){
        $zbp->admin_js_hash .= hash('crc32b', (string)$zbp->Config('AdminColor'));
    }
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
        $zbp->Config('AdminColor')->FontSize = (int) 14;
        $zbp->Config('AdminColor')->LeftWidth = (int) 140;
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

    //if (stripos($zbp->currenturl, 'phpinfo') !== false) {
    //    return;
    //}
    if(0 == (int)$zbp->Config('AdminColor')->FontSize)
        $zbp->Config('AdminColor')->FontSize = 14;

    $app = $zbp->LoadApp('plugin', 'AdminColor');
    echo '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_users/plugin/AdminColor/css.php?id=' . $zbp->Config('AdminColor')->ColorID . '&hash=' . hash('crc32b', $zbp->Config('AdminColor')) . '&v=' . $app->modified . '"/>' . "\r\n";

    list($fontsize, $leftwidth, $rightwidth, $background_positionX, $background_color) = AdminColor_GetValue();

    echo '<script type="text/javascript">var lang_admincolor_closemenu2 = "' . $zbp->lang['AdminColor']['closemenu'] . '";var lang_admincolor_closemenu = "<i class=icon-caret-left-fill></i>' . $zbp->lang['AdminColor']['closemenu'] . '";var lang_admincolor_expandmenu2 = "' . $zbp->lang['AdminColor']['expandmenu'] . '";var lang_admincolor_expandmenu = "<i class=icon-caret-right-fill></i>' . $zbp->lang['AdminColor']['expandmenu'] . '"</script>' . "\r\n";

    if ($zbp->Config('AdminColor')->ColorID < 10) {
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button');
        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#ededed;}.left #leftmenu span{margin-left:11px;font-size:0;}.left #leftmenu span i{font-size:'.$fontsize.'px;}div.main,section.main{padding-left:46px;}.left #leftmenu li,.left #leftmenu a{width:'.$leftwidth.'px}';
            if ($zbp->Config('AdminColor')->ColorID == 9)
                echo '.left{background-color:rgb(227, 234, 243);}';
            echo '</style>';
        }else{
            echo '<style type="text/css">.left{width:'.$leftwidth.'px}.main{padding-left:'.$rightwidth.'px}.left #leftmenu li,.left #leftmenu a{width:'.$leftwidth.'px}</style>';
        }
    } elseif ($zbp->Config('AdminColor')->ColorID == 10) {
        Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AdminColor_Add_Button');
        $hm = GetVars('admincolor_hm', 'COOKIE');
        if ($hm == '1') {
            echo '<style type="text/css">.left{width:36px;background-color:#333333;}.left #leftmenu span{margin-left:11px;font-size:0;}.left #leftmenu span i{font-size:'.$fontsize.'px;}div.main,section.main{padding-left:46px;}body{background-position:-964px top;}.left #leftmenu #nav_admincolor2 span{margin-left:11px;}.left #leftmenu li,.left #leftmenu a{width:'.$leftwidth.'px}</style>';
        }else{
            echo '<style type="text/css">.left{width:'.$leftwidth.'px}.main{padding-left:'.$rightwidth.'px}body{background-position:'.$background_positionX.'px top;}.left #leftmenu li,.left #leftmenu a{width:'.$leftwidth.'px}</style>';
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_AdminColor_CSS'] as $fpname => &$fpsignal) {
        $fpname();
    }

}

function AdminColor_Add_Button(&$leftmenus)
{
    global $zbp;
    if ($zbp->Config('AdminColor')->SlidingButton == false) {
        return;
    }
    $hm = GetVars('admincolor_hm', 'COOKIE');
    if ($hm == '1') {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['expandmenu'], "javascript:admincolor_showMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/images/arror2.png", 'icon-caret-right-fill');
    } else {
        $leftmenus['nav_admincolor'] = MakeLeftMenu(5, $zbp->lang['AdminColor']['closemenu'], "javascript:admincolor_hideMenu();", "nav_admincolor", "aAdminColor", $zbp->host . "zb_users/plugin/AdminColor/images/arror.png", 'icon-caret-left-fill');
    }
}

function AdminColor_ColorButton()
{
    global $zbp;

    $s = '';

    for ($i = 0; $i < count($GLOBALS['AdminColor_NormalColor']); $i++) {
        $s .= "&nbsp;&nbsp;<a href='" . $zbp->host . "zb_users/plugin/AdminColor/main.php?setcolor=" . $i . "' title='" . $GLOBALS['AdminColor_Title'][$i] . "'><span style='height:16px;width:16px;background:" . $GLOBALS['AdminColor_Square'][$i] . "'><img src='" . $zbp->host . "zb_system/image/admin/none.gif' width='16' height='16' alt='' /></span></a>&nbsp;&nbsp;";
    }

    echo "<div id='admin_color'>" . $s . "</div>";
}

function AdminColor_GetValue() {
    global $zbp;
    $fontsize = (int)$zbp->Config('AdminColor')->FontSize;
    if(0 == $fontsize)$fontsize = 14;
    $leftwidth = $zbp->Config('AdminColor')->LeftWidth;
    if(0 == $leftwidth)$leftwidth = 140;
    $background_positionX = 0;
    $background_color = '';

    if ($zbp->Config('AdminColor')->ColorID < 9) {
        $background_color = '#ededed';
    } elseif ($zbp->Config('AdminColor')->ColorID == 9) {
        $background_color = '#e3eaf3';
    } elseif ($zbp->Config('AdminColor')->ColorID == 10) {
        $background_color = '#333333';
    }

    if($fontsize == 12){
        $leftwidth = $leftwidth -8 -8;
        $rightwidth = $leftwidth +10;
        $background_positionX = 0 -1000 +$leftwidth;
    }
    if($fontsize == 13){
        $leftwidth = $leftwidth -8;
        $rightwidth = $leftwidth +10;
        $background_positionX = 0 -1000 +$leftwidth;
    }
    if($fontsize == 14){
        $leftwidth = $leftwidth;
        $rightwidth = $leftwidth +10;
        $background_positionX = 0 -1000 +$leftwidth -0;
    }
    if($zbp->Config('AdminColor')->ColorID == 10){
        $leftwidth = $leftwidth +20;
        $rightwidth = $leftwidth +10;
        $background_positionX += 20;
    }
    return array($fontsize, $leftwidth, $rightwidth, $background_positionX, $background_color);
}

function AdminColor_AddJS()
{
    global $zbp;

    list($fontsize, $leftwidth, $rightwidth, $background_positionX, $background_color) = AdminColor_GetValue();

    $js1 = <<<EOD
function admincolor_hideMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_expandmenu);
 $("#aAdminColor").attr('title',lang_admincolor_expandmenu2);
 if(zbp.blogversion<170000)$("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/images/arror2.png)");
 $("div.left,aside.left").css({"background-color":"{$background_color}"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"11px"});
 $("#leftmenu span").css({"font-size":"0"});
 $("#leftmenu span i").css({"font-size": $("body").css("font-size")});
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();

}

function admincolor_showMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
 $("#aAdminColor").find('span').html(lang_admincolor_closemenu);
 $("#aAdminColor").attr('title',lang_admincolor_closemenu2);
 if(zbp.blogversion<170000)$("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/images/arror.png)");
 $("div.left,aside.left").css({"background-color":"transparent"});
 $("div.left,aside.left").animate({"width":"{$leftwidth}px"});
 $("div.main,section.main").animate({"padding-left":"{$rightwidth}px"});
 $("#leftmenu span").animate({"margin-left":"24px"});
 $("#leftmenu span").css({"font-size": $("body").css("font-size")});
 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
}
EOD;


    $js2 = <<<EOD
function admincolor_hideMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_showMenu()');
 $("#aAdminColor span i").attr('class','icon-caret-right-fill');
 if(zbp.blogversion<170000)$("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/images/arror2.png)");
 $("div.left,aside.left").css({"background-color":"{$background_color}"});
 $("div.left,aside.left").animate({"width":"36px"});
 $("div.main,section.main").animate({"padding-left":"46px"});
 $("#leftmenu span").animate({"margin-left":"11px"}); 
 $("#leftmenu span").css({"font-size":"0"});
 $("#leftmenu span i").css({"font-size":"{$fontsize}px"});
 $("#leftmenu #nav_admincolor2 span").animate({"margin-left":"11px","padding-left1":"20px"}); 
 $("body").animate({"background-positionX":"-964px"}); 
 SetCookie('admincolor_hm','1',365);
 admincolor_tooptip();
}

function admincolor_showMenu(){
 $("#aAdminColor").attr('href','javascript:admincolor_hideMenu()');
 $("#aAdminColor span i").attr('class','icon-caret-left-fill');
 if(zbp.blogversion<170000)$("#aAdminColor>span").css("background-image","url("+bloghost + "zb_users/plugin/AdminColor/images/arror.png)");
 $("div.left,aside.left").css({"background-color":"{$background_color}"});
 $("div.left,aside.left").animate({"width":"{$leftwidth}px"});
 $("div.main,section.main").animate({"padding-left":"{$rightwidth}px"});
 $("#leftmenu span").animate({"margin-left":"32px"});
 $("#leftmenu span").css({"font-size":"{$fontsize}px"});
 $("body").animate({"background-positionX":"{$background_positionX}px"}); 
 SetCookie('admincolor_hm','',-1); 
 $("#leftmenu a").tooltip({disabled: true});
}
EOD;

    $js_common = <<<EOD
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

    if ($zbp->Config('AdminColor')->ColorID < 10) {
        echo $js1;
    } elseif ($zbp->Config('AdminColor')->ColorID == 10) {
        echo $js2;
    }

    echo $js_common;

    foreach ($GLOBALS['hooks']['Filter_Plugin_AdminColor_AddJS'] as $fpname => &$fpsignal) {
        $fpname();
    }


}

//深色 标准色 浅色 高光色 反色 标题 方块颜色
$AdminColor_BlodColor[0] = '#1d4c7d';
$AdminColor_NormalColor[0] = '#3a6ea5';
$AdminColor_LightColor[0] = '#b0cdee';
$AdminColor_HighColor[0] = '#3399cc';
$AdminColor_AntiColor[0] = '#d60000';
$AdminColor_Title[0] = 'ZB蓝';
$AdminColor_Square[0] = $AdminColor_NormalColor[0];

$AdminColor_BlodColor[1] = '#143c1f';
$AdminColor_NormalColor[1] = '#5b992e';
$AdminColor_LightColor[1] = '#bee3a3';
$AdminColor_HighColor[1] = '#6ac726';
$AdminColor_AntiColor[1] = '#d60000';
$AdminColor_Title[1] = '';
$AdminColor_Square[1] = $AdminColor_NormalColor[1];

$AdminColor_BlodColor[2] = '#06282b';
$AdminColor_NormalColor[2] = '#2db1bd';
$AdminColor_LightColor[2] = '#87e6ef';
$AdminColor_HighColor[2] = '#119ba7';
$AdminColor_AntiColor[2] = '#d60000';
$AdminColor_Title[2] = '';
$AdminColor_Square[2] = $AdminColor_NormalColor[2];

$AdminColor_BlodColor[3] = '#3e1165';
$AdminColor_NormalColor[3] = '#5c2c84';
$AdminColor_LightColor[3] = '#a777d0';
$AdminColor_HighColor[3] = '#8627d7';
$AdminColor_AntiColor[3] = '#08a200';
$AdminColor_Title[3] = '';
$AdminColor_Square[3] = $AdminColor_NormalColor[3];

$AdminColor_BlodColor[4] = '#3f280d';
$AdminColor_NormalColor[4] = '#b26e1e';
$AdminColor_LightColor[4] = '#e3b987';
$AdminColor_HighColor[4] = '#d88625';
$AdminColor_AntiColor[4] = '#d60000';
$AdminColor_Title[4] = '';
$AdminColor_Square[4] = $AdminColor_NormalColor[4];

$AdminColor_BlodColor[5] = '#0a4f3e';
$AdminColor_NormalColor[5] = '#267662';
$AdminColor_LightColor[5] = '#68cdb4';
$AdminColor_HighColor[5] = '#25bb96';
$AdminColor_AntiColor[5] = '#d60000';
$AdminColor_Title[5] = '';
$AdminColor_Square[5] = $AdminColor_NormalColor[5];

$AdminColor_BlodColor[6] = '#3a0b19';
$AdminColor_NormalColor[6] = '#7c243f';
$AdminColor_LightColor[6] = '#d57c98';
$AdminColor_HighColor[6] = '#d31b54';
$AdminColor_AntiColor[6] = '#2039b7';
$AdminColor_Title[6] = '';
$AdminColor_Square[6] = $AdminColor_NormalColor[6];

$AdminColor_BlodColor[7] = '#2d2606';
$AdminColor_NormalColor[7] = '#d4a30e';
$AdminColor_LightColor[7] = '#fcd251';
$AdminColor_HighColor[7] = '#e9b20a';
$AdminColor_AntiColor[7] = '#d60000';
$AdminColor_Title[7] = '';
$AdminColor_Square[7] = $AdminColor_NormalColor[7];

$AdminColor_BlodColor[8] = '#a60138';
$AdminColor_NormalColor[8] = '#ff6699';
$AdminColor_LightColor[8] = '#f993b5';
$AdminColor_HighColor[8] = '#df4679';
$AdminColor_AntiColor[8] = '#df1ce6';
$AdminColor_Title[8] = '';
$AdminColor_Square[8] = $AdminColor_NormalColor[8];

$AdminColor_BlodColor[9] = '#17365d';
$AdminColor_NormalColor[9] = '#366092';
$AdminColor_LightColor[9] = '#b8cce4';
$AdminColor_HighColor[9] = '#8db3e2';
$AdminColor_AntiColor[9] = '#e36c09';
$AdminColor_Title[9] = '星空云';
$AdminColor_Square[9] = $AdminColor_BlodColor[9];

$AdminColor_BlodColor[10] = '#262f3e';
$AdminColor_NormalColor[10] = '#0070c0';
$AdminColor_LightColor[10] = '#D9DFE5';
$AdminColor_HighColor[10] = '#3f3f3f';
$AdminColor_AntiColor[10] = '#c0504d';
$AdminColor_Title[10] = '深度云';
$AdminColor_Square[10] = $AdminColor_BlodColor[10];
