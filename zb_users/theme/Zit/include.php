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
    Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response', $name . '_CategoryEdit');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Template', $name . '_ViewList');

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
        $cfg->MenuAnimate = 1;
        $cfg->DefaultAdmin = 0;
        //$cfg->Profile = 1;
        $cfg->ListTags = 0;
        $cfg->ListAlbum = 0;
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
        $cfg->RandLog = 0;
        $cfg->HideRand = 0;

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

    array_unshift($m, MakeTopMenu("root", $lang['Zit']['setting'], $bloghost . 'zb_users/theme/Zit/main.php', '', 'topmenu_Zit', 'icon-gear-fill'));
}

function Zit_PostGet(&$post, $name)
{
  global $zbp;

  switch ($name) {
    case 'Cover':
      $post->$name=$post->ImageCount>0?$post->Thumbs(400,300)[0]:'';
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
        $mod->Source = 'themeinclude_Zit';
    }

    $where = array(
        array('=', 'comm_IsChecking', 0),
        array('custom','length(`comm_Content`)>20'),
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
}

function Zit_ViewList(&$tpl)
{
    global $zbp;

    $cfg=$zbp->Config('Zit');

    foreach($tpl->GetTags('articles') as $v){
      if($cfg->ListAlbum&&count($v->AllImages)>3){
        $v->Intro='';
      }else{
        $v->Intro=preg_replace('/<img[^>]*?\s+src="([^\s"]{5,})"(\/?>|\s[^<]*?>)/i','',$v->Intro);
        $v->Intro=preg_replace('/<p>(<br\/?>)?<\/p>/i','',$v->Intro);
        if($cfg->HideIntro) $v->Intro='<div class="hidem">'.$v->Intro.'</div>';
      }
    }

}

function Zit_Motto($category,$cfg)
{
  global $zbp;

  $motto=$cfg->Motto?$cfg->Motto:$zbp->subname;

  $mottoSize=$cfg->MottoSize;
  $mottoUrl=$cfg->MottoUrl;

  if($category){
    if($category->Metas->Motto) $motto=$category->Metas->Motto;
    if($category->Metas->MottoSize) $mottoSize=$category->Metas->MottoSize;
    if($category->Metas->MottoUrl) $mottoUrl=$category->Metas->MottoUrl;
  }

  if($mottoSize) $motto='<span style="display:block;font-size:'.$mottoSize.'">'.$motto.'</span>';
  if($mottoUrl) $motto='<a href="'.trim(str_replace('~',$zbp->host,$mottoUrl)).'" targe="_blank">'.$motto.'</a>';

  echo $motto;
}

function Zit_CategoryEdit()
{
  global $bloghost,$lang,$cate;

  $msg = (object) $lang['Zit'];

  echo <<<STR
  <h3 class="zit">{$msg->otherset}</h3>
  <p><dfn>{$msg->backdrop}</dfn> <input size="60" type="text" name="meta_Backdrop" placeholder="{$msg->backdrop_place}" value="{$cate->Metas->Backdrop}" class="pic"> <small>{$msg->backdrop_tip}</small></p>
  <p><dfn>{$msg->mottotxt}</dfn> <input size="60" type="text" name="meta_Motto" value="{$cate->Metas->Motto}"> <small>{$msg->mottotxt_tip}</small></p>
  <p><dfn>{$msg->mottourl}</dfn> <input size="60" type="text" name="meta_MottoUrl" value="{$cate->Metas->MottoUrl}" placeholder="//"> <small>{$msg->mottourl_tip}{$bloghost}</small></p>
  <p><dfn>{$msg->mottosize}</dfn> <input size="10" type="text" name="meta_MottoSize" value="{$cate->Metas->MottoSize}"> <small>{$msg->mottosize_tip}<var>px</var> <var>%</var> <var>em</var></small></p>
  <style>
  .pane .zit{top:-1em;left:1em;}
  .pane dfn{font:bold 1em arial;display:block;}
  .pane dfn::after{content:":";font-weight:bold;}
  .pane small{color:#789;margin-left:.5em}
  .pic{transition:text-indent .2s;}
  .picable{background-size:3em 100%;background-repeat:no-repeat;text-indent:3em;}
  #response{position:relative;border-top:4em solid #f4f5f6;margin:2em -2em 0;padding:2em;}
  #response input{padding:5px 10px;height:33px;}
  </style>
  <script>
  $("#edit").prepend("<h3 class='zit'>{$msg->basicset}</h3>");
  $("input.pic").focus(function(){
    $(this).removeClass("picable").css("background-image","none");
  }).blur(function(){
    if(this.value) $(this).addClass("picable").css("background-image","url("+this.value+")");
  }).blur();
  </script>
STR;
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
    .logo{position:absolute;left:0;height:auto;width:auto;margin:-6em 0 0;word-break:break-all;}
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

    $animate=$cfg->MenuAnimate?'':'.left{animation:none}';

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
    .content-box-tabs,
    #topmenu a,
    .SubMenu{filter:hue-rotate({$cfg->Hue}deg);}
    .theme-now input,
    #kandyApps a{filter:none;}
    .theme-now img,
    .m-warn{filter:hue-rotate(-{$cfg->Hue}deg);}
    {$animate}
  </style>
CSS;

    echo <<<JS
  <script>$(function(){
    $("html").addClass("kandyZit");
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
    $("#topmenu").find("a[href$='?act=admin']").wrapInner("<span/>").parent().attr("id","nav_admin").prependTo(\$menu.prepend('<li class="split"/>'));
    $("#topmenu").find("a[href$='?act=SettingMng']").wrapInner("<span/>").parent().attr("id","nav_settings").appendTo(\$menu);
    $("#nav_settings").before('<li class="split"/>');
    $("li.split").next(".split").remove();
    \$menu.find("li:last").filter(".split").remove();
    /* \$menu.find("span").css("background-image",function(i,s){
      if($(this).parents("li")[0].id==="topmenu1") return s.replace("window_1","home_2");
      return s.replace("1.","2.");
    }); */
    $(".username").text("{$username}");
    //btn
    $("button").addClass("btn");
  })</script>
JS;
}
