<?php

//注册插件
RegisterPlugin("OnePage", "ActivePlugin_OnePage");

function ActivePlugin_OnePage()
{
    global $zbp;
    $zbp->LoadLanguage('theme', 'OnePage');

    Add_Filter_Plugin('Filter_Plugin_Cmd_Begin', 'OnePage_CMT');
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'OnePage_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'OnePage_AddJS');

    if($zbp->HasConfig('OnePage') && isset($zbp->html_js_hash)){
        $zbp->html_js_hash .= crc32((string)$zbp->Config('OnePage'));
    }

    $s = '';
    if ($zbp->Config('OnePage')->HasKey("custom_bgcolor")) {
        $s .= "body{background-color:#" . $zbp->Config('OnePage')->custom_bgcolor . ";}div.post-body>p>code,div.post-body>pre.prism-highlight,div.post-body blockquote{background-color:#" . $zbp->Config('OnePage')->custom_bgcolor . ";}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_pagecolor")) {
        $s .= "textarea,input[type=text],input[type=password],#divNavBar li ul{background-color:#" . $zbp->Config('OnePage')->custom_pagecolor . "}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_fontcolor")) {
        $s .= "body{color:#" . $zbp->Config('OnePage')->custom_fontcolor . ";}textarea,input[type=text],input[type=password]{color:#" . $zbp->Config('OnePage')->custom_fontcolor . ";}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_acolor")) {
        $s .= "a,span.now-page,#BlogTitle a{color:#" . $zbp->Config('OnePage')->custom_acolor . ";}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_ahovercolor")) {
        $s .= "a:hover,a:hover span.page,#BlogTitle a:hover,#divNavBar a:hover{color:#" . $zbp->Config('OnePage')->custom_ahovercolor . ";}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_blogtitlecolor")) {
        $s .= "#BlogTitle a{color:#" . $zbp->Config('OnePage')->custom_blogtitlecolor . ";}";
    }
    if ($zbp->Config('OnePage')->HasKey("text_indent")) {
        $s .= "div.post-body p{text-indent:" . (int) $zbp->Config('OnePage')->text_indent . "em;}";
    }
    if ($zbp->Config('OnePage')->HasKey("custom_pagetype")) {
        if ($zbp->Config('OnePage')->custom_pagetype == 2) {
            $s .= "#divAll{box-shadow: 0 0 0.6em #333;border-radius: 0;}";
            $s .= "#divAll{background:#" . $zbp->Config('OnePage')->custom_pagecolor . ";}#divMiddle{background:none;}";
        } elseif ($zbp->Config('OnePage')->custom_pagetype == 3) {
            $s .= "#divAll{box-shadow: 0 0 0.6em #333;border-radius: 1em;}";
            $s .= "#divAll{background:#" . $zbp->Config('OnePage')->custom_pagecolor . ";}#divMiddle{background:none;}";
        } elseif ($zbp->Config('OnePage')->custom_pagetype == 4) {
            $s .= "#divAll{box-shadow: none;border-radius: 0;}";
            $s .= "#divAll{background:#" . $zbp->Config('OnePage')->custom_pagecolor . ";}#divMiddle{background:none;}";
        } elseif ($zbp->Config('OnePage')->custom_pagetype == 5) {
            $s .= "#divAll{box-shadow: none;border-radius: 1em;}";
            $s .= "#divAll{background:#" . $zbp->Config('OnePage')->custom_pagecolor . ";}#divMiddle{background:none;}";
        } else {
            $s .= "#divAll{background:none;}#divMiddle{background:none;}";
        }
    }

    $zbp->header .= '    <style>' . $s . '</style>' . "\r\n";
    $zbp->lang['msg']['first_button'] = '&lt;&lt;';
    $zbp->lang['msg']['prev_button'] = '&lt;';
    $zbp->lang['msg']['next_button'] = '&gt;';
    $zbp->lang['msg']['last_button'] = '&gt;&gt;';
    Add_Filter_Plugin('Filter_Plugin_ViewSearch_Begin', 'OnePage_ViewSearch_Begin');
}

function OnePage_ViewSearch_Begin()
{
    global $zbp;
    $zbp->option['ZC_SEARCH_TYPE'] = 'list';
}

function InstallPlugin_OnePage()
{
    global $zbp;
}

function UninstallPlugin_OnePage()
{
    global $zbp;
}

function OnePage_CMT()
{
    global $zbp, $action;

    if ($action == 'cmt') {
        if (empty($zbp->user->ID) == false) {
            $_POST['email'] = $zbp->user->Email;
            $_POST['homepage'] = $zbp->user->HomePage;
            $_POST['name'] = $zbp->user->Name;
        }
    }
}

function OnePage_AddMenu(&$m)
{
    global $zbp;
    $m[] = MakeTopMenu("root", $zbp->lang['OnePage']['theme_config'], $zbp->host . "zb_users/theme/OnePage/main.php", "", "topmenu_OnePage","icon-nut-fill");
}

function OnePage_AddJS($echo = true)
{
    $s = <<<JS
$(function() {
    var s = document.location;
    $("#divNavBar a").each(function() {
        if (this.href == s.toString().split("#")[0]) {
            $(this).addClass("on");
            return false;
        }
    });
});

zbp.plugin.unbind("comment.reply.start", "system");
/*重写了common.js里的同名函数*/
zbp.plugin.on("comment.reply.start", "OnePage", function(id) {
    var i = id;
    $("#inpRevID").val(i);
    var frm = $('#divCommentPost'),
        cancel = $("#cancel-reply"),
        temp = $('#temp-frm');


    var div = document.createElement('div');
    div.id = 'temp-frm';
    div.style.display = 'none';
    frm.before(div);

    $('#AjaxComment' + i).before(frm);

    frm.addClass("reply-frm");

    cancel.show();
    cancel.click(function() {
        $("#inpRevID").val(0);
        var temp = $('#temp-frm'),
            frm = $('#divCommentPost');
        if (!temp.length || !frm.length) return;
        temp.before(frm);
        temp.remove();
        $(this).hide();
        frm.removeClass("reply-frm");
        return false;
    });
    try {
        $('#txaArticle').focus();
    } catch (e) {}
    return false;
});

/*重写GetComments，防止评论框消失*/
zbp.plugin.on("comment.get", "OnePage", function (logid, page) {
    $('span.commentspage').html("Waiting...");
    $.get(bloghost + "zb_system/cmd.php?act=getcmt&postid=" + logid + "&page=" + page, function(data) {
        $('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove();
        $('#AjaxCommentEnd').before(data);
        $("#cancel-reply").click();
    });
});


zbp.plugin.on("comment.post.success", "OnePage", function () {
    $("#cancel-reply").click();
});
JS;

    if ($echo) {
        echo $s;
    }

    return $s;
}

function OnePage_SuperFast_Pre()
{

}

function OnePage_SuperFast()
{

}
