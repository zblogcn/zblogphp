<?php
/*
 * @Name     : Zit Include
 * @Author   : 吉光片羽
 * @Support  : jgpy.cn
 * @Create   : 2019-12-25 20:10:23
 * @Update   : 2020-02-19 13:57:48
 */

RegisterPlugin('Zit', 'ActivePlugin_Zit');

function ActivePlugin_Zit()
{
    global $zbp;
    $name = 'Zit';
    $zbp->LoadLanguage('theme', $name);

    Add_Filter_Plugin('Filter_Plugin_Post_Get', $name . '_PostGet');
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', $name . '_TopMenu');
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', $name . '_AdminHeader');
    Add_Filter_Plugin('Filter_Plugin_Login_Header', $name . '_LoginHeader');
    Add_Filter_Plugin('Filter_Plugin_Zbp_BuildModule', $name . '_BuildModule');
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', $name . '_MakeTemplatetags');
}

function InstallPlugin_Zit()
{
    global $zbp;
    $name = 'Zit';
    $cfg = $zbp->Config($name);
    $def = Zit_Defaults(!$cfg->Custom);
    foreach ($def as $k=>$v) {
        $cfg->$k = $v;
    }
    $cfg->Save();
}

function Zit_Defaults($init = false)
{
    global $bloghost;

    $cfg = new stdClass();

    if ($init) {
        $cfg->Logo = 'ZBlogIt';
        $cfg->Motto = 'Nice to meet you, too!';
        $cfg->Cover = $bloghost . 'zb_users/theme/Zit/style/bg.jpg';
        $cfg->Profile = 1;
        $cfg->ListTags = 0;
        $cfg->MobileSide = 0;
        $cfg->HideIntro = 0;
        $cfg->SideMods = '';

        $cfg->CmtIds = '';
        $cfg->GbookID = 2;

        $cfg->Description = '';
        $cfg->Keywords = '';
        $cfg->RelatedTitle = '少长咸集';
        $cfg->CommentTitle = '群贤毕至';

        $cfg->Custom = false;
        $cfg->ColorChange = 0;
    }

    return (array) $cfg;
}

function UninstallPlugin_Zit()
{
}

function Zit_TopMenu(&$m)
{
    global $bloghost,$lang;

    array_unshift($m, MakeTopMenu("root", $lang['Zit']['setting'], $bloghost . 'zb_users/theme/Zit/main.php', '', 'topmenu_Zit'));
}

function Zit_PostGet(&$post, $name)
{
    global $zbp;

    switch ($name) {
    case 'Cover':
      preg_match_all('/<img[^>]*?\s+src="([^\s"]{5,})"(\/?>|\s[^<]*?>)/i', $post->Intro . $post->Content, $imgs);
      $post->$name = isset($imgs[1][0]) ? $imgs[1][0] : $zbp->Config('Zit')->Cover;
    break;
    case 'TimeUrl':
      $url = new UrlRule($zbp->option['ZC_DATE_REGEX']);
      $url->Rules['{%date%}'] = $post->Time('Y-m-d');
      $url->Rules['{%year%}'] = $post->Time('Y');
      $url->Rules['{%month%}'] = $post->Time('m');
      $url->Rules['{%day%}'] = $post->Time('d');
      $post->$name = $url->Make();
    break;
  }
}

function Zit_LoginHeader()
{
    global $zbp;

    $logo = $zbp->Config('Zit')->Logo ? $zbp->Config('Zit')->Logo : $zbp->name;

    echo <<<CSSJS
  <style>
    .bg{background-image:url({$zbp->host}zb_users/theme/Zit/style/bg.jpg);background-size:cover;animation:slide 600s infinite linear;}
    .zit{color:#18a;background:#fff;padding:.3em;line-height:1;position:absolute;z-index:2;min-width:2em;display:inline-block;min-height:1em;font-size:2em;border-radius:.2em;margin:0 auto;box-shadow:.5em .5em .5em -.3em rgba(0,0,0,.3);font-family:verdana}
    .zit::after{content:"Z";position:absolute;left:.5em;bottom:-.5em;transform:rotate(30deg);display:inline-block;margin:0 .2em 0 0;z-index:-1;color:#fff;font-weight:bold;}
    @keyframes slide{
      0% {background-position:center;}
      25% {background-position:0 0;}
      75% {background-position:100% 100%;}
      100% {background-position:center;}
    }
  </style>
  <script>$(function(){
    $(".logo").find("img").replaceWith('<b class="zit">{$logo}</b>').end().wrapInner("<a href='"+bloghost+"'/>");
  })</script>
CSSJS;
}

function Zit_AdminHeader()
{
    global $zbp;

    echo '<link href="' . $zbp->host . 'zb_users/theme/Zit/admin.css" rel="stylesheet">';
    $logo = $zbp->Config('Zit')->Logo ? $zbp->Config('Zit')->Logo : $zbp->name;
    $username = $zbp->user->Name;

    echo <<<JS
  <script>$(function(){
    //logo
    $(".logo").find("img").replaceWith('<b class="zit">{$logo}</b>');
    //pane
    var \$main=$("#divMain"),\$main2=$("#divMain2"),\$mtable=\$main.find("table"),pane="<div class='pane'/>",url=document.URL;
    if($(".content-box")[0]){
      $(".content-box").wrap(pane);
    }else if(url.indexOf('/plugin/')>-1&&url.indexOf('/plugin/Kandy')<0){
      \$main2.wrapInner(pane);
    }else if(\$mtable[0]){
      \$mtable[$("a.hilite")[0]?"wrapAll":"wrap"](pane);
    }else if(!\$main.find(".pane")[0]){
      \$main2.wrapInner(pane);
    }
    $("#frmTheme").unwrap(pane);
    //menu
    var \$menu=$("#leftmenu").attr("dir","ltr").wrap('<div id="menuwrap" dir="rtl"/>');
    $("#menuwrap").css("margin-left",function(){
      return -$(this).width()+\$menu.width();
    });
    $("#topmenu1").find("a").text(function(i,s){
      return s.replace(/\shome/i,"");
    }).wrapInner("<span/>").end().prependTo(\$menu.prepend('<li class="split"/>'));
    $("#topmenu2").find("a").wrapInner("<span/>").end().appendTo(\$menu.append('<li class="split"/>'));
    \$menu.find("span").css("background-image",function(i,s){
      if($(this).parents("li")[0].id==="topmenu1") return s.replace("window_1","home_2");
      return s.replace("1.","2.");
    });
    $(".username").text("{$username}");
  })</script>
JS;
}

function Zit_BuildModule()
{
    global $zbp;

    $cfg = $zbp->Config('Zit');

    if (trim($cfg->CmtIds)) {
        return;
    }

    if (isset($zbp->modulesbyfilename['zit_cmtids'])) {
        $mod = $zbp->modulesbyfilename['zit_cmtids'];
    } else {
        $mod = new Module();
        $mod->FileName = 'zit_cmtids';
        $mod->Name = 'Comment ID Cache';
        $mod->Source = 'theme_Zit';
    }

    $where = array(
        array('=', 'comm_IsChecking', 0),
    );
    $ids = array();
    $cmts = $zbp->GetCommentList('', $where, array('comm_PostTime' => 'DESC'));
    foreach ($cmts as $cmt) {
        $ids[] = $cmt->ID;
    }
    $mod->Content = json_encode($ids);
    $mod->Save();
}

function Zit_MakeTemplatetags(&$templateTags)
{
    global $zbp;
    $templateTags['msg'] = (object) $zbp->lang['Zit'];

    $templateTags['cfg'] = $zbp->Config('Zit');

    $templateTags['sideMods'] = $templateTags['cfg']->MobileSide ? array() : explode(' ', trim($templateTags['cfg']->SideMods));
}
