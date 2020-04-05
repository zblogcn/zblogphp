<?php

include 'x2013.php';
RegisterPlugin("x2013", "ActivePlugin_x2013");

function ActivePlugin_x2013()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'x2013_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Template', 'x2013_tags_set');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'x2013_tags_set');
}

function x2013_AddMenu(&$m)
{
    global $zbp;
    array_unshift($m, MakeTopMenu("root", 'X2013主题设置', $zbp->host . "zb_users/theme/x2013/main.php", "", "topmenu_x2013"));
}

function x2013_tags_set(&$template)
{
    global $zbp;
    $weibo = $qqmail = '';
    //weibo
    if (($zbp->Config('x2013')->SetWeiboSina) != '') {
        $weibo .= "<li><a class=\"btn btn-mini\" target=\"_blank\" href=\"{$zbp->Config('x2013')->SetWeiboSina}\">新浪微博</a></li>";
    }
    if (($zbp->Config('x2013')->SetWeiboQQ) != '') {
        $weibo .= "<li><a class=\"btn btn-mini\" target=\"_blank\" href=\"{$zbp->Config('x2013')->SetWeiboQQ}\">腾讯微博</a></li>";
    }
    if ($weibo != '') {
        $weibo = '<ul class="popup-follow-weibo">' . $weibo . '</ul>';
    }
    //qqmailfeed
    if (($zbp->Config('x2013')->DisplayFeed)) {
        $qqmail = '<div class="popup-follow-mail"><h4>邮件订阅：</h4><form action="https://list.qq.com/cgi-bin/qf_compose_send" target="_blank" method="post"><input type="hidden" name="t" value="qf_booked_feedback" /><input type="hidden" name="id" value="' . $zbp->Config('x2013')->SetMailKey . '" /><input id="to" placeholder="输入邮箱 订阅本站" name="to" type="text" class="ipt" /><input class="btn btn-primary" type="submit" value="邮件订阅" /></form></div>';
    }

    $showlink = $zbp->Config('x2013')->NavBar;
    $showlink = str_replace('{$host}', $zbp->host, $showlink);

    $template->SetTags('zc_tm_setweibo', $weibo);
    $template->SetTags('zc_tm_setfeedtomail', $qqmail);
    $template->SetTags('x2013_adheader', $zbp->Config('x2013')->PostAdHeader);
    $template->SetTags('x2013_adfooter', $zbp->Config('x2013')->PostAdFooter);
    $template->SetTags('x2013_showlink', $showlink);
}

function InstallPlugin_x2013()
{
    global $zbp;
    if (!$zbp->Config('x2013')->HasKey('Version')) {
        $zbp->Config('x2013')->Version = '2.3';
        $zbp->Config('x2013')->SetWeiboSina = 'http://weibo.com/810888188';
        $zbp->Config('x2013')->SetWeiboQQ = 'http://t.qq.com/involvements';
        $zbp->Config('x2013')->DisplayFeed = '1';
        $zbp->Config('x2013')->SetMailKey = '4e54e0008863773ff0f44e54eb9c1805cf165e63a0601789';
        $zbp->Config('x2013')->PostAdHeader = '<embed src="//music.163.com/style/swf/widget.swf?sid=27090774&type=2&auto=1&width=320&height=66" width="340" height="86"  allowNetworking="all"></embed>';
        $zbp->Config('x2013')->PostAdFooter = '<img src="http://www.baidu.com/img/shouye_b5486898c692066bd2cbaeda86d74448.gif">';
        $zbp->Config('x2013')->FirstInstall = '1';
        $zbp->Config('x2013')->NavBar = '<li class="menu-item" style="position: relative;"><a href="{$host}">{$name}</a></li>';
        $zbp->SaveConfig('x2013');
    }
    if (!$zbp->Config('x2013')->HasKey('Css')) {
        $zbp->Config('x2013')->Css = '#38A3DB';
        $zbp->SaveConfig('x2013');
    }
    $zbp->Config('x2013')->Version = '2.3';
    $zbp->SaveConfig('x2013');
    //Call SetBlogHint_Custom("<span style='color:#ff0000'>x2013主题</span>已经激活，点击<a href='" +BlogHost+"zb_users/theme/x2013/plugin/main.asp'>[主题设置]</a>去配置主题")
}

function UninstallPlugin_x2013()
{
}
