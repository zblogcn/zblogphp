<?php
/*
 * @Name     : Zit Include
 * @Author   : 吉光片羽
 * @Support  : jgpy.cn
 * @Create   : 2019-12-25 20:10:23
 * @Update   : 2020-03-12 23:19:28
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

    $cfg = $zbp->Config($name);
    $css = '';
    if ((int) $cfg->Hue) {
        $css .= 'input,button,textarea,select,th,td,a,.zit,.hue,#navim,#backdrop,#topic h5,#rel .log,.cmt small{filter:hue-rotate(' . $cfg->Hue . 'deg)}';
        $css .= '.more span{filter:none}';
        $css .= '#backdrop{animation:none;}';
        $css .= 'a img{filter:hue-rotate(-' . $cfg->Hue . 'deg)}';
        $css .= 'td a,#rel .log .zit,#topic h5 a,.cmt small a,.cmt small input{filter:none}';
    }
    if ($cfg->Backdrop) {
        $css .= '#backdrop{background-image:url(' . $cfg->Backdrop . ');filter:none;}';
    }

    if ($css) {
        $zbp->header .= '  <style type="text/css">' . $css . '</style>' . PHP_EOL;
    }
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
        $cfg->Cover = $bloghost . 'zb_users/theme/Zit/style/bg.jpg';
        $cfg->Backdrop = '';
        $cfg->DefaultAdmin = 0;
        //$cfg->Profile = 1;
        $cfg->ListTags = 0;
        $cfg->MobileSide = 0;
        $cfg->HideIntro = 0;
        $cfg->SideMods = '';
        $cfg->Hue = 0;
        $cfg->StaticName = 1;

        $cfg->Motto = 'Nice to meet you, too!';
        $cfg->MottoUrl = '';
        $cfg->MottoSize = '';

        #TODO add in 1.2, fit main.php and languages
        //$cfg->CmtLink = 1;
        $cfg->CmtIds = '';
        $cfg->GbookID = 2;

        $cfg->Description = '';
        $cfg->Keywords = '';
        $cfg->RelatedTitle = '少长咸集';
        $cfg->CommentTitle = '群贤毕至';

        $cfg->Custom = 0;
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

    $cfg = $zbp->Config('Zit');
    if ($cfg->DefaultAdmin) {
        return;
    }
    $logo = $cfg->Logo ? $cfg->Logo : $zbp->name;
    $bg = $cfg->Backdrop ? $cfg->Backdrop : $zbp->host . 'zb_users/theme/Zit/style/bg.jpg';
    $bghue = $cfg->Backdrop ? 'none' : $cfg->Hue;
    $hue = $cfg->Backdrop ? $cfg->Hue : 'none';

    echo <<<CSSJS
  <style>
    .bg{background:url({$bg}) center;background-size:cover;filter:hue-rotate({$bghue}deg);}
    .zit{color:#fff;background:#18a;padding:.5em;line-height:1;position:relative;min-width:2em;display:inline-block;min-height:1em;font-size:2em;margin:0 1em 0 0;box-shadow:.5em .5em .5em -.3em rgba(0,0,0,.3);border-radius:0.1em;}
    .zit::after{content:"Z";position:absolute;left:.5em;bottom:-.5em;transform:rotate(30deg);display:inline-block;margin:0 .2em 0 0;z-index:-1;color:#18a;font-weight:bold;}
    #wrapper{filter:hue-rotate({$hue}deg);position:relative;max-width:600px;padding-top:300px;}
    .logo{position:absolute;bottom:125px;left:0;height:auto;width:auto;margin:0;word-break:break-all;}
    @media only screen and (max-width:768px) {
      #wrapper{padding-top:200px;}
      .logo{left:2em}
    }
  </style>
  <script>
    $(function(){
      $(".logo").find("img").replaceWith('<b class="zit">{$logo}</b>').end().wrapInner("<a href='"+bloghost+"'/>");
    })
  </script>
CSSJS;
}

function Zit_AdminHeader()
{
    global $zbp;

    $cfg = $zbp->Config('Zit');
    if ($cfg->DefaultAdmin) {
        return;
    }

    echo '<link href="' . $zbp->host . 'zb_users/theme/Zit/admin.css" rel="stylesheet">';
    $logo = $cfg->Logo ? $cfg->Logo : $zbp->name;
    $username = $zbp->user->Name;

    echo <<<CSS
  <style>
    input.button,
    input[type="submit"],
    input[type="button"],
    .btn,
    .zit,
    .left,
    .pagebar span,
    .theme-now,
    #topmenu a,
    .SubMenu{filter:hue-rotate({$cfg->Hue}deg);}
    .theme-now input,
    #kandyApps a{filter:none;}
    .theme-now img{filter:hue-rotate(-{$cfg->Hue}deg);}
  </style>
CSS;

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

    $cfg = $zbp->Config('Zit');

    $templateTags['msg'] = (object) $zbp->lang['Zit'];

    $templateTags['cfg'] = $cfg;

    $templateTags['sideMods'] = $cfg->MobileSide ? array() : explode(' ', trim($cfg->SideMods));

    $motto=$cfg->Motto?$cfg->Motto:$zbp->subname;
    
    if($cfg->MottoSize) $motto='<span style="display:block;font-size:'.$cfg->MottoSize.'">'.$motto.'</span>';
    if($cfg->MottoUrl) $motto='<a href="'.trim(str_replace('~',$zbp->host,$cfg->MottoUrl)).'" targe="_blank">'.$motto.'</a>';

    $templateTags['motto']=$motto;
}
