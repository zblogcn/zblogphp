<?php

//注册插件
RegisterPlugin("WhitePage", "ActivePlugin_WhitePage");

function ActivePlugin_WhitePage()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'WhitePage_AddMenu');
    $s = '';
    if ($zbp->Config('WhitePage')->HasKey("custom_bgcolor")) {
        $zbp->Config('WhitePage')->custom_bgcolor = str_replace('#', '', $zbp->Config('WhitePage')->custom_bgcolor);
        $s .= "body{background-color:#" . $zbp->Config('WhitePage')->custom_bgcolor . ";}";
    }
    if ($zbp->Config('WhitePage')->HasKey("custom_headtitle")) {
        $s .= "#BlogTitle,#BlogSubTitle,#BlogCopyRight,#BlogPowerBy{text-align:" . $zbp->Config('WhitePage')->custom_headtitle . ";}";
    }
    if ($zbp->Config('WhitePage')->HasKey("custom_pagewidth")) {
        if ($zbp->Config('WhitePage')->custom_pagewidth == 1000) {
            $s .= "#divAll{width:1000px;}#divMiddle{width:940px;padding:0 30px;}#divSidebar{width:240px;padding:0 0 0 20px;}#divMain{width:670px;padding:0 0 20px 0;}#divTop{padding-top:30px;}body{font-size:15px;}";
        }
    }
    if ($zbp->Config('WhitePage')->HasKey("text_indent")) {
        $s .= "div.post-body p{text-indent:" . (int) $zbp->Config('WhitePage')->text_indent . "em;}";
    }
    if ($zbp->Config('WhitePage')->HasKey("custom_pagetype")) {
        if ($zbp->Config('WhitePage')->custom_pagetype == 1) {
            if ($zbp->Config('WhitePage')->custom_pagewidth == 1000) {
                $s .= "#divAll{background:url('" . $zbp->host . "zb_users/theme/WhitePage/style/default/bg1000-1.png') no-repeat 50% top;}#divPage{background:url('" . $zbp->host . "zb_users/theme/WhitePage/style/default/bg1000-2.png') no-repeat 50% bottom;}#divMiddle{background:url('" . $zbp->host . "zb_users/theme/WhitePage/style/default/bg1000-3.png') repeat-y 50% 50%;}";
            }
        }
        if ($zbp->Config('WhitePage')->custom_pagetype == 2) {
            $s .= "#divAll{box-shadow: 0 0 5px #666;background-color:white;border-radius: 0px;}";
            $s .= "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
        }
        if ($zbp->Config('WhitePage')->custom_pagetype == 3) {
            $s .= "#divAll{box-shadow: 0 0 5px #666;background-color:white;border-radius: 7px;}";
            $s .= "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
        }
        if ($zbp->Config('WhitePage')->custom_pagetype == 4) {
            $s .= "#divAll{box-shadow:none;background-color:white;border-radius: 0;}";
            $s .= "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
            $s .= "#divTop{padding-top:30px;}";
        }
        if ($zbp->Config('WhitePage')->custom_pagetype == 5) {
            $s .= "#divAll{box-shadow:none;background-color:white;border-radius: 0;border-radius: 7px;}";
            $s .= "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
            $s .= "#divTop{padding-top:30px;}";
        }
    }

    $Pad_List = '/touch|arm;|android|iphone|ipad|windows\sphone|kindle|gt\-p|gt\-n|rim\stablet|meego/i';
    $UA = GetGuestAgent();
    if (CheckRegExp($UA, $Pad_List) == true) {
        $s .= "body{font-size:4vw;}#divMain{width:100%;}#divSidebar{width:100%;}.function{width:50%;font-size:3vw;}";
        $s .= "div.post .post-title{width:100%;}div.post .post-date{width:100%;}#divBottom{font-size:2vw;}#divAll{background:white;width:99%;margin:0.3em auto;}#divPage{background:none;}#divMiddle{background:white;width:98%;padding:0;}#divCommentPost input{width:70%;}#divCommentPost input.button{width:50%;}";
    }

    $zbp->header .= '	<style type="text/css">' . $s . '</style>' . "\r\n";
}

function WhitePage_AddMenu(&$m)
{
    global $zbp;
    $m[] = MakeTopMenu("root", 'WhitePage主题配置', $zbp->host . "zb_users/theme/WhitePage/main.php", "", "topmenu_WhitePage");
}

function InstallPlugin_WhitePage()
{
    global $zbp;
}

function UninstallPlugin_WhitePage()
{
    global $zbp;
}
