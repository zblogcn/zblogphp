<?php

//注册插件
RegisterPlugin("metro", "ActivePlugin_metro");

function ActivePlugin_metro()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'metro_AddMenu');
}

function metro_AddMenu(&$m)
{
    global $zbp;
    $m[] = MakeTopMenu("root", 'Metro主题配置', $zbp->host . "zb_users/theme/metro/editor.php", "", "topmenu_metro");
}

function InstallPlugin_metro()
{
    global $zbp;
    //配置初始化
    if (!$zbp->Config('metro')->HasKey('version')) {
        $zbp->Config('metro')->version = '1.0';
        $zbp->Config('metro')->custom_layout = "r";
        $zbp->Config('metro')->custom_bodybg = "#EEEEEE|" . $zbp->host . "zb_users/theme/metro/style/images/bg.jpg|repeat|2|top|";
        $zbp->Config('metro')->custom_hdbg = "|" . $zbp->host . "zb_users/theme/metro/style/images/headbg.jpg|repeat  fixed|1|top|120|";
        $zbp->Config('metro')->custom_color = "#5EAAE4| #A3D0F2| #222222| #333333| #FFFFFF";
        $zbp->SaveConfig('metro');
    }
}

function UninstallPlugin_metro()
{
    global $zbp;
    //$zbp->DelConfig('metro');
}

//******************************************************************************************
// 保存css文件
//******************************************************************************************
function metro_savetofile($stylefile)
{
    global $zbp;

    if ($zbp->Config('metro')->HasKey('version')) {
        $strlayout = $zbp->Config('metro')->custom_layout;
        $strBodyBg = $zbp->Config('metro')->custom_bodybg;
        $strHdBg = $zbp->Config('metro')->custom_hdbg;
        $strColor = $zbp->Config('metro')->custom_color;
        $aryBodyBg = explode("|", $strBodyBg);
        $aryHdBg = explode("|", $strHdBg);
        $aryColor = explode("|", $strColor);
    }

    $p = array("", "left", "center", "right");
    $aryBodyBg[3] = $p[(int) $aryBodyBg[3]];
    $aryHdBg[3] = $p[(int) $aryHdBg[3]];
    $strBodyBgsize = "auto";
    $strHdBgsize = "auto";

    if (stripos($aryBodyBg[2], "repeat") > -1) {
        $aryBodyBg[2] = "no-repeat " . $aryBodyBg[2];
    }
    if (stripos($aryHdBg[2], "repeat") > -1) {
        $aryHdBg[2] = "no-repeat " . $aryHdBg[2];
    }
    if (stripos($aryBodyBg[2], "cover") > -1) {
        $aryBodyBg[2] = str_replace("cover", "", $aryBodyBg[2]);
        $strBodyBgsize = "cover";
    }
    if (stripos($aryHdBg[2], "cover") > -1) {
        $aryHdBg[2] = str_replace("cover", "", $aryHdBg[2]);
        $strHdBgsize = "cover";
    }

    if ($aryBodyBg[5] == "True") {
        $strBodyBg = $aryBodyBg[0] . " " . "url('" . $blogpath . $aryBodyBg[1] . "') " . " " . $aryBodyBg[2] . " " . $aryBodyBg[3] . " " . $aryBodyBg[4];
    } else {
        $strBodyBg = $aryBodyBg[0];
    }

    if ($aryHdBg[0] != "transparent") {
        $aryHdBg[0] = $aryColor[0];
    }
    if ($aryHdBg[6] == "True") {
        $strHdBg = $aryHdBg[0] . " " . "url('" . $blogpath . $aryHdBg[1] . "') " . $aryHdBg[2] . " " . $aryHdBg[3] . " " . $aryHdBg[4];
    } else {
        $strHdBg = $aryHdBg[0];
    }

    //$sLeft,$sRight
    $sLeft = $p[1];
    $sRight = $p[3];
    if ($strlayout == "l") {
        $sLeft = $p[3];
        $sRight = $p[1];
    }

    //替换模版标签
    //strContent=LoadFromFile(BlogPath  .  "zb_users\theme\metro\plugin\style.css.html" ,"utf-8");
    $strContent = @file_get_contents($zbp->usersdir . 'theme/metro/source/style.css.html');

    $strContent = str_replace("{%strlayoutl%}", $sLeft, $strContent);
    $strContent = str_replace("{%strlayoutr%}", $sRight, $strContent);

    $strContent = str_replace("{%strBodyBg%}", $strBodyBg, $strContent);
    $strContent = str_replace("{%strHdBg%}", $strHdBg, $strContent);

    $strContent = str_replace("{%strHdBgsize%}", $strHdBgsize, $strContent);
    $strContent = str_replace("{%strBodyBgsize%}", $strBodyBgsize, $strContent);

    for ($i = 1; $i < count($aryBodyBg); $i++) {
        $strContent = str_replace("{%aryBodyBg(" . $i . ")%}", $aryBodyBg[$i], $strContent);
    }
    for ($i = 1; $i < count($aryHdBg); $i++) {
        $strContent = str_replace("{%aryHdBg(" . $i . ")%}", $aryHdBg[$i], $strContent);
    }
    for ($i = 0; $i < count($aryColor); $i++) {
        $strContent = str_replace("{%aryColor(" . $i . ")%}", $aryColor[$i], $strContent);
    }

    @file_put_contents($zbp->usersdir . 'theme/metro/style/style.css', $strContent);
}
