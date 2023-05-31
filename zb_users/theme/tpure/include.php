<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'searchstr.php';
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'sendmail.php';
#注册应用“tpure”
RegisterPlugin("tpure", "ActivePlugin_tpure");
//应用激活时执行的函数，在这个函数里挂接口
function ActivePlugin_tpure()
{
    global $zbp;
    //加载主题语言包
    $zbp->LoadLanguage('theme', 'tpure');
    //主题接口
    if ($zbp->Config('tpure')->SEOON == '1') {
        Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response', 'tpure_CategorySEO');
        Add_Filter_Plugin('Filter_Plugin_Tag_Edit_Response', 'tpure_TagSEO');
        Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'tpure_SingleSEO');
    }
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'tpure_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'tpure_Header');
    Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'tpure_Refresh');
    Add_Filter_Plugin('Filter_Plugin_ViewSearch_Template', 'tpure_SearchMain');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Ajax','tpure_CmdAjax');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Core', 'tpure_Exclude_Category');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'tpure_Edit_Response');
    Add_Filter_Plugin('Filter_Plugin_Member_Edit_Response', 'tpure_MemberEdit_Response');
    Add_Filter_Plugin('Filter_Plugin_PostModule_Succeed', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_DelComment_Succeed', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_CheckComment_Succeed', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Del', 'tpure_CreateModule');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_ArchiveAutoCache');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Del', 'tpure_ArchiveAutoCache');
    Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'tpure_ErrorCode');
    if($zbp->Config('tpure')->PostVIDEOON == '1'){
        Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'tpure_ZBvideoLoad');
    }
    if($zbp->Config('tpure')->PostZBAUDIOON == '1'){
        Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'tpure_ZBaudioLoad');
    }
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','tpure_CustomCode');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','tpure_SingleCode');
    Add_Filter_Plugin('Filter_Plugin_LargeData_Article','tpure_LargeDataArticle');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Template','tpure_DefaultTemplate');
    if($zbp->Config('tpure')->PostMAILON == '1'){
        Add_Filter_Plugin('Filter_Plugin_PostArticle_Core', 'tpure_ArticleCore');
        Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_ArticleSendmail');
        Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'tpure_CmtSendmail');
    }
    //Add_Filter_Plugin('Filter_Plugin_Mebmer_Avatar', 'tpure_MemberAvatar');
    if ($zbp->Config('tpure')->PostLOGINON == '1') {
        Add_Filter_Plugin('Filter_Plugin_Login_Header', 'tpure_LoginHeader');
    }
    if($zbp->Config('tpure')->PostVIEWALLON == '1'){
        Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'tpure_ArticleViewall');
    }

    if ($zbp->Config('tpure')->PostFANCYBOXON == '1') {
        Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','tpure_Fancybox');
        Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','tpure_FancyboxRegex');
    }
    if ($zbp->Config('tpure')->PostCATEPREVNEXTON == '1') {
        Add_Filter_Plugin('Filter_Plugin_Post_Prev', 'tpure_Post_Prev');
        Add_Filter_Plugin('Filter_Plugin_Post_Next', 'tpure_Post_Next');
    }
    if ($zbp->Config('tpure')->PostLAZYLOADON == '1') {
        Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'tpure_ListIMGLazyLoad');
        Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'tpure_ContentIMGLazyLoad');
    }
    //自定义侧栏模块名称
    $zbp->lang['msg']['theme_module'] = $zbp->lang['tpure']['thememodule'];
    $zbp->lang['msg']['sidebar'] = $zbp->lang['tpure']['index'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar2'] = $zbp->lang['tpure']['catalog'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar3'] = $zbp->lang['tpure']['article'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar4'] = $zbp->lang['tpure']['page'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar5'] = $zbp->lang['tpure']['search'].$zbp->lang['tpure']['page'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar6'] = $zbp->lang['tpure']['tagscloud'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar7'] = $zbp->lang['tpure']['archive'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar8'] = $zbp->lang['tpure']['member'].$zbp->lang['tpure']['sidebar'];
    $zbp->lang['msg']['sidebar9'] = $zbp->lang['tpure']['readers'].$zbp->lang['tpure']['sidebar'];
    $zbp->option['ZC_VERIFYCODE_STRING'] = $zbp->Config('tpure')->VerifyCode;
}

//花辰月夕ajax搜索
function tpure_CmdAjax($src)
{
    global $nice;
    if (strpos($src ,'search') === 0){
        $fun = tpure_ajaxSearch();
        if ($fun(array_values($param))){
            tpure_json(1);
        }else{
            tpure_json(0,13);
        }
    }
}

function tpure_ajaxSearch()
{
    global $zbp;
    $q = GetVars('q','POST');
    $w = array(
        array('search','log_Title','log_Content',$q),
        array('=','log_Status',0),
        );
    $articles = $zbp->GetArticleList('*',$w,array('log_PostTime'=>'DESC'),6);
    $res = array('post' => array());
    foreach($articles as $k => $article){
        if ($k == 5) break;
        $qc = '<mark>' . $q . '</mark>';
        $intro = preg_replace('/[\r\n\s]+/', '', trim(tpure_SubStrStartUTF8(TransferHTML($article->Intro, '[nohtml]'), $q, $zbp->Config('tpure')->PostINTRONUM)) . '...');
        $article->Intro = str_ireplace($q, $qc, $intro);
        $res['post'][] = array(
            'title' => $article->Title,
            'img'   => tpure_Thumb($article),
            'url'   => $article->Url,
            'intro' => $article->Intro
            );
    }
    $res['more'] = count($articles) > 5;
    exit(json_encode($res));
}

function tpure_json($code, $msg = '', $data = '')
{
    global $zbp;
    $a = array();
    if (is_numeric($msg) && isset($a[$msg])){
        $msg = $a[$msg];
    }
    if (is_array($code)){
        $json = $code;
    }else{
        $json = array(
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data,
        );
    }
    $rt = RunTime(false);
    $json['runtime'] = "time:{$rt['time']}ms query:{$rt['query']} memory:{$rt['memory']}kb error:{$rt['error']}";
    echo json_encode($json);
    exit;
}

//主题设置页导航
function tpure_SubMenu($id)
{
    global $zbp;
    $arySubMenu = array(
        0 => array($zbp->lang['tpure']['baseset'], 'base', 'left', false),
        1 => array($zbp->lang['tpure']['seoset'], 'seo', 'left', false),
        2 => array($zbp->lang['tpure']['colorset'], 'color', 'left', false),
        3 => array($zbp->lang['tpure']['sideset'], 'side', 'left', false),
        4 => array($zbp->lang['tpure']['slideset'], 'slide', 'left', false),
        5 => array($zbp->lang['tpure']['mailset'], 'mail', 'left', false),
        6 => array($zbp->lang['tpure']['configset'], 'config', 'left', false),
    );
    foreach ($arySubMenu as $k => $v) {
        echo '<li><a href="?act=' . $v[1] . '" ' . ($v[3] == true ? 'target="_blank"' : '') . ' class="' . ($id == $v[1] ? 'on' : '') . '">' . $v[0] . '</a></li>';
    }
}

//后台右上角添加主题设置入口；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'tpure_AddMenu');
function tpure_AddMenu(&$m)
{
    global $zbp;
    $m[] = MakeTopMenu("root", $zbp->lang['tpure']['themeset'], $zbp->host . "zb_users/theme/tpure/main.php?act=base", "", "topmenu_tpure", "icon-grid-1x2-fill");
}

//后台管理页面顶部的背景图片；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'tpure_Header');
function tpure_Header()
{
    global $zbp,$bloghost;
    if($zbp->Config('tpure')->PostAJAXPOSTON == '0'){$ajaxpost = 0;}else{$ajaxpost = 1;}
    echo '<style>.header{background:url(' . $bloghost . 'zb_users/theme/tpure/style/images/banner.jpg) no-repeat center center;background-size:cover;}</style>';
    echo '<script>window.theme = {ajaxpost:'.$ajaxpost.'}</script>';
}

//主题自带的登陆页面样式；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Login_Header', 'tpure_LoginHeader');
function tpure_LoginHeader()
{
    global $zbp;
    $logo = $zbp->Config('tpure')->PostLOGO && $zbp->Config('tpure')->PostLOGOON == 1 ? $zbp->Config('tpure')->PostLOGO : $zbp->name;
    echo <<<CSSJS
    <style>
        input:-webkit-autofill { -webkit-text-fill-color:#000 !important; background-color:transparent; background-image:none; transition:background-color 50000s ease-in-out 0s; }
        .bg { height:100%; background:url({$zbp->host}zb_users/theme/tpure/style/images/banner.jpg) no-repeat center top; background-size:cover; }
        .logo { width:100%; height:auto; margin:0; padding:20px 0 10px; text-align:center; border-bottom:1px solid #eee; }
        .logo img { width:auto; height:50px; margin:auto; background:none; display:block; }
        #wrapper { width:440px; min-height:400px; height:auto; border-radius:8px; background:#fff; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); }
        .login { width:auto; height:auto; padding:30px 40px 20px; }
        .login input[type="text"], .login input[type="password"] { width:100%; height:42px; float:none; padding:0 14px; font-size:16px; line-height:42px; border:1px solid #e4e8eb; outline:0; border-radius:3px; box-sizing:border-box; }
        .login input[type="password"] { font-size:24px; }
        .login input[type="text"]:focus, .login input[type="password"]:focus { color:#0188fb; background-color:#fff; border-color:#aab7c1; outline:0; box-shadow:0 0 0 0.2rem rgba(31,73,119,0.1); }
        .login dl { height:auto; }
        .login dd { margin-bottom:14px; }
        .login dd.submit, .login dd.password, .login dd.username { width:auto; float:none; overflow:visible; }
        .login dd.checkbox { width:170px; float:none; margin:0 0 10px; }
        .login dd.checkbox input[type="checkbox"] { width:16px; height:16px; margin-right:6px;; }
        .login label { width:auto; margin-bottom:5px; padding:0; font-size:16px; text-align:left; }
        .logintitle { padding:0 70px; font-size:24px; color:#0188fb; line-height:40px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; position:relative; display:block; }
        .logintitle:before,.logintitle:after { content:""; width:40px; height:0; border-top:1px solid #ddd; position:absolute; top:20px; right:30px; }
        .logintitle:before { right:auto; left:30px; }
        .button { width:100%; height:42px; float:none; font-size:16px; line-height:42px; border-radius:3px; outline:0; box-shadow:1px 3px 5px 0 rgba(72,108,255,0.3); background:#0188fb; }
        .button:hover { background:#0188fb; }
        @media only screen and (max-width: 768px){
            .login { padding:30px 30px 10px; }
            .login dd { float:left; margin-bottom:14px; padding:0; }
            .login dd.checkbox { width:auto; padding:0; }
            .login dd.submit { margin-right:0; }
        }
        @media only screen and (max-width: 520px){
            #wrapper { width:96%; margin:0 auto; }
        }
        </style>
        <script>
        $(function(){
        function check_is_img(url) {
            return (url.match(/\.(jpeg|jpg|gif|png|svg)$/) != null)
        }
        if(check_is_img("{$logo}")){
            $(".logo").find("img").replaceWith('<img src="{$logo}"/>').end().wrapInner("<a href='"+bloghost+"'/>");
        }else{
            $(".logo").find("img").replaceWith('<span class="logintitle">{$logo}<span>').end().wrapInner("<a href='"+bloghost+"'/>");
        }
        });
    </script>
CSSJS;
}

//开启“开发模式”后，修改主题源码保存将实时刷新，无需手动重建缓存；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'tpure_Refresh');
function tpure_Refresh()
{
    global $zbp;
    // if ($zbp->option['ZC_DEBUG_MODE']) {
    //     $zbp->BuildTemplate();
    // }
    if ($zbp->ismanage){
        return;
    }
    if (defined("ZBP_IN_CMD")) {
        return;
    }
    $zbp->lang['msg']['first_button'] = $zbp->lang['tpure']['index'];
    $zbp->lang['msg']['prev_button'] = $zbp->lang['tpure']['prevpage'];
    $zbp->lang['msg']['next_button'] = $zbp->lang['tpure']['nextpage'];
    $zbp->lang['msg']['last_button'] = $zbp->lang['tpure']['endpage'];
    $zbp->option['ZC_SEARCH_TYPE'] = 'list';
    //$zbp->option['ZC_PAGEBAR_COUNT'] = 3;
    //$zbp->option['ZC_SEARCH_COUNT'] = 10;
}

//分类列表页面包屑分类获取
function tpure_navcate($id)
{
   $html = '';
   $navcate = new Category;
   $navcate->LoadInfoByID($id);
   $html = ' &gt; <a href="' .$navcate->Url.'" title="查看' .$navcate->Name. '中的全部文章">' .$navcate->Name. '</a> '.$html;
   if(($navcate->ParentID)>0){tpure_navcate($navcate->ParentID);}
   echo $html;
}

//后台登陆过期时自动跳转到登录页，且网站关站时使用个性公告页面；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'tpure_ErrorCode');
function tpure_ErrorCode($errorCode)
{
    global $zbp;
    if($errorCode == 6){
        if($zbp->Config('tpure')->PostERRORTOPAGE){
            Redirect($zbp->Config('tpure')->PostERRORTOPAGE);
        }else{
            Redirect($zbp->host.'zb_system/login.php');
        }
        die();
    }elseif($errorCode == 82){
        echo tpure_CloseSite();
        die();
    }
}

//文章添加音频时在正文上方添加播放器
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'tpure_ZBaudioLoad');
function tpure_ZBaudioLoad(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    $zbaudio_player = '<link rel="stylesheet" href="'. $zbp->host .'zb_users/theme/tpure/plugin/zbaudio/style.css"><script src="'. $zbp->host .'zb_users/theme/tpure/plugin/zbaudio/audio.js"></script><p><span class="zbaudio"><span class="zbaudio_img"></span><span class="zbaudio_info"><strong></strong><em class="zbaudio_singer"></em><span class="zbaudio_area"><span class="zbaudio_item"><span class="zbaudio_progress"><span class="zbaudio_now"><span class="zbaudio_bar"></span></span><span class="zbaudio_cache"></span></span><span class="zbaudio_time"><em class="zbaudio_current">00:00</em><em class="zbaudio_total"></em></span></span><span class="zbaudio_play"><em data-action="play" data-on="play" data-off="pause"></em></span></span></span></span></p>';
    $zbaudio_config = '<script>var setConfig = {song : [{cover : "'. $article->Metas->audioimg .'",src : "'. $article->Metas->audio .'",title : "'. $article->Metas->audiotitle .'",singer : "'. $article->Metas->audiosinger .'"}],error : function(meg){console.log(meg);}};var zbaudio = audioPlay(setConfig);if(zbaudio){zbaudio.loadFile('. $article->Metas->audioautoplay .');}</script>';
    if(isset($article->Metas->audio)){
        $article->Content = $zbaudio_player ."\r\n". $zbaudio_config ."\r\n" . $article->Content;
    }
}
//文章添加视频时在正文上方添加播放器
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'tpure_ZBvideoLoad');
function tpure_ZBvideoLoad(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    $videostr = '';
    $videostr .= '<p id="zbvideo" class="videobox">';
    if(strpos($article->Metas->video,'.mp4') || strpos($article->Metas->video,'.m3u8') || strpos($article->Metas->video,'.flv')){
        $videostr .= '<script>tpure.zbvideo = "'.$article->Metas->video.'";tpure.videoautoplay = "'.$article->Metas->videoautoplay.'";tpure.videoloop = "'.$article->Metas->videoloop.'";</script>';
        $videostr .= '<video src="'.$article->Metas->video.'" data-type="mp4" controls="controls"'.($article->Metas->videoautoplay == '1' ? ' autoplay="autoplay"' : '').($article->Metas->videoloop == '1' ? ' loop="loop"' : '').'></video>';
    }elseif(strpos($article->Metas->video,'iframe') || strpos($article->Metas->video,'embed')){
        $videostr .= $article->Metas->video;
    }else{
        $videostr .= '<iframe src="'. $article->Metas->video .'" frameborder="0" allowfullscreen="true"></iframe>';
    }
    $videostr .= '</p>';
    if(isset($article->Metas->video)){
        $article->Content = $videostr ."\r\n" . $article->Content;
    }
}

//网站首尾添加通用代码
//挂接口：Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','tpure_CustomCode');
function tpure_CustomCode()
{
    global $zbp;
    $headercode = $zbp->Config('tpure')->PostHEADERCODE;
    $footercode = $zbp->Config('tpure')->PostFOOTERCODE;
    $zbp->header .= $headercode . "\r\n";
    $zbp->footer .= $footercode . "\r\n";
}

//文章顶部与底部添加通用代码
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','tpure_SingleCode');
function tpure_SingleCode(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    if($zbp->Config('tpure')->PostSINGLETOPCODE){
        $article->Content = $zbp->Config('tpure')->PostSINGLETOPCODE . $article->Content;
    }
    if($zbp->Config('tpure')->PostSINGLEBTMCODE){
        $article->Content .= $zbp->Config('tpure')->PostSINGLEBTMCODE;
    }
}

//网站关站页面
function tpure_CloseSite()
{
    global $zbp;
    $logo = $zbp->Config('tpure')->PostLOGO && $zbp->Config('tpure')->PostLOGOON == 1 ? "<img src=".$zbp->Config('tpure')->PostLOGO." />" : $zbp->name;
    $bg = $zbp->Config('tpure')->PostCLOSESITEBG ? ' style="background-image:url('.$zbp->Config('tpure')->PostCLOSESITEBG.');"' : '';
    $bgmask = $zbp->Config('tpure')->PostCLOSESITEBGMASKON ? ' bgmask' : '';
    $title = $zbp->Config('tpure')->PostCLOSESITETITLE ? $zbp->Config('tpure')->PostCLOSESITETITLE : $zbp->lang['tpure']['closesitetitle'];
    $content = $zbp->Config('tpure')->PostCLOSESITECON ? $zbp->Config('tpure')->PostCLOSESITECON : $zbp->lang['tpure']['closesitecon'];
    $skin = $zbp->Config('tpure')->PostCOLORON == 1 ? "<link rel=\"stylesheet\" href=\"".$zbp->host."zb_users/theme/tpure/include/skin.css \">" : '';
    $str = '';
    $str .= '<!DOCTYPE html><html xml:lang="zh-Hans" lang="zh-Hans"><head><meta charset="utf-8"><meta name="renderer" content="webkit"><meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1"><title>'.$title.'</title><link rel="stylesheet" href="'.$zbp->host.'zb_users/theme/tpure/style/style.css">'.$skin.'</head><body class="closepage">';
    if($bg){
        $str .= '<div class="closesitebg'.$bgmask.'"'.$bg.'></div>';
    }
    $str .='<div class="closesite"><div class="closelogo">'.$logo.'</div><h1>'.$title.'</h1><div class="closecon">'.$content.'</div></div></body></html>';
    return $str;
}


//文章页实现同一分类上下篇；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Post_Prev', 'tpure_Post_Prev');
function tpure_Post_Prev(&$thispage)
{
    global $zbp;
    $prev=$thispage;
    $articles = $zbp->GetPostList(
        array('*'),
        array(array('=', 'log_Type', 0), array('=', 'log_CateID', $prev->Category->ID),array('=', 'log_Status', 0), array('<', 'log_PostTime', $prev->PostTime)),
        array('log_PostTime' => 'DESC'),
        array(1),
        null
    );
    if (count($articles) == 1) {
        return $articles[0];
    }else{
        return null;
    }
}

//文章页实现同一分类上下篇；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Post_Next', 'tpure_Post_Next');
function tpure_Post_Next(&$thispage)
{
    global $zbp;
    $prev=$thispage;
    $articles = $zbp->GetPostList(
        array('*'),
        array(array('=', 'log_Type', 0), array('=', 'log_CateID', $prev->Category->ID),array('=', 'log_Status', 0), array('>', 'log_PostTime', $prev->PostTime)),
        array('log_PostTime' => 'ASC'),
        array(1),
        null
    );
    if (count($articles) == 1) {
        return $articles[0];
    }else{
        return null;
    }
}

//判断参数内日期是否是今天，示例：tpure_IsToday('2020-12-30')
function tpure_IsToday($date)
{
    $greyday = $date;
    $formatday = substr($greyday,0,10);
    $today = date('Y-m-d');
    if($formatday==$today){
        return true;
    }else{
        return false;
    }
}

//列表页按类型自动选择catalog模板并可设置其他模板；
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewList_Template','tpure_DefaultTemplate');
function tpure_DefaultTemplate(&$template)
{
    global $zbp;
    if($template->GetTags('type') == 'index' && $template->GetTags('page') != '1'){
        switch($zbp->Config('tpure')->PostINDEXSTYLE){
            case "1":
                $template->SetTemplate('forum');
                break;
            case "2":
                $template->SetTemplate('album');
                break;
            case "3":
                $template->SetTemplate('sticker');
                break;
            case "4":
                $template->SetTemplate('hotspot');
                break;
            default:
                $template->SetTemplate('catalog');
        }
    }
    if(($template->GetTags('type') == 'category' && $template->GetTags('category')->Template != 'forum' && $template->GetTags('category')->Template != 'album' && $template->GetTags('category')->Template != 'sticker' && $template->GetTags('category')->Template != 'hotspot') ||
        ($template->GetTags('type') == 'tag' && $template->GetTags('tag')->Template != 'forum' && $template->GetTags('tag')->Template != 'album' && $template->GetTags('tag')->Template != 'sticker' && $template->GetTags('tag')->Template != 'hotspot') || $template->GetTags('type') == 'date'){
        $template->SetTemplate('catalog');
    }
    if($template->GetTags('type') == 'author' && $template->GetTags('author')->Template != 'catalog' && $template->GetTags('author')->Template != 'forum' && $template->GetTags('author')->Template != 'album' && $template->GetTags('author')->Template != 'sticker' && $template->GetTags('author')->Template != 'hotspot'){
        $template->SetTemplate('author');
    }
}

//搜索功能函数；
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewSearch_Template', 'tpure_SearchMain');
function tpure_SearchMain(&$template)
{
    global $zbp;
    $articles = $template->GetTags('articles');
    $q = $template->GetTags('search');
    $qc = '<mark>' . $q . '</mark>';
    $introNum = $zbp->Config('tpure')->PostINTRONUM ? $zbp->Config('tpure')->PostINTRONUM : 110;
    foreach ($articles as $key => $article) {
        $a = $zbp->GetPostByID($article->ID);
        $intro = preg_replace('/[\r\n\s]+/', '', trim(tpure_SubStrStartUTF8(TransferHTML($a->Content, '[nohtml]'), $q, $introNum)) . '...');
        $article->Intro = str_ireplace($q, $qc, $intro);
        $article->Title = str_ireplace($q, $qc, $article->Title);
    }
}

//自定义模板样式名称
function tpure_JudgeListTemplate($listtype)
{
    global $zbp;
    $listtype = $zbp->Config('tpure')->PostSEARCHSTYLE;
    switch($listtype)
    {
        case 1:
            $template = 'forum';
            break;
        case 2:
            $template = 'album';
            break;
        case 3:
            $template = 'sticker';
            break;
        case 4:
            $template = 'hotspot';
            break;
        default:
            $template = '';
    }
    return $template;
}

//unicode字符转换实体
function tpure_CodeToString($str)
{
    $to = array(" ","  ","   ","    ","\"","<",">","&");
    $pre = array('&nbsp;','&nbsp;&nbsp;','&nbsp;&nbsp;&nbsp;','&nbsp;&nbsp;&nbsp;&nbsp;','&quot;','&lt','&gt','&amp');
    return str_replace($pre, $to, $str);
}

//首页过滤指定分类文章并重建分页；
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewList_Core', 'tpure_Exclude_Category');
function tpure_Exclude_Category($type, $page, $category, $author, $datetime, $tag, &$w, $pagebar)
{
    global $zbp;
    if ($type == 'index' && $filter = $zbp->Config('tpure')->PostFILTERCATEGORY) {
        $w[] = array('NOT IN', 'log_CateID', explode(',',$filter));
        $pagebar->Count = null;
    }
}

//主题设置页面获取分类的下拉控件
function tpure_Exclude_CategorySelect($default)
{
    global $zbp;
    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCategories'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }
    $s = '';
    $s .= '<option value="0">屏蔽多个分类ID</option>';
    foreach ($zbp->categoriesbyorder as $id => $cate) {
        $s .= '<option ' . ($default == $cate->ID ? 'selected="selected"' : '') . ' value="' . $cate->ID . '">' . $cate->SymbolName . '</option>';
    }
    return $s;
}

//个性化日期函数
function tpure_TimeAgo($ptime)
{
    global $zbp;
    if ($zbp->Config('tpure')->PostTIMESTYLE == '0') {
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if ($etime < 1) {
            return '刚刚';
        }
        $interval = array(
            12 * 30 * 24 * 60 * 60  => '年前<span class="datetime"> (' . date('Y-m-d', $ptime) . ')</span>',
            30 * 24 * 60 * 60       => '个月前<span class="datetime"> (' . date('m-d', $ptime) . ')</span>',
            7 * 24 * 60 * 60        => '周前<span class="datetime"> (' . date('m-d', $ptime) . ')</span>',
            24 * 60 * 60            => '天前',
            60 * 60                 => '小时前',
            60                      => '分钟前',
            1                       => '秒前',
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        }
    } else {
        $ptime = strtotime($ptime);
        if ($zbp->Config('tpure')->PostTIMEFORMAT == '5'){
            $etime = date('Y年m月d日 H:i:s', $ptime);
        }elseif ($zbp->Config('tpure')->PostTIMEFORMAT == '4'){
            $etime = date('Y年m月d日 H:i', $ptime);
        }elseif ($zbp->Config('tpure')->PostTIMEFORMAT == '3'){
            $etime = date('Y年m月d日', $ptime);
        }elseif ($zbp->Config('tpure')->PostTIMEFORMAT == '2'){
            $etime = date('Y-m-d H:i:s', $ptime);
        }elseif ($zbp->Config('tpure')->PostTIMEFORMAT == '1'){
            $etime = date('Y-m-d H:i', $ptime);
        }else{
            $etime = date('Y-m-d', $ptime);
        }
        return $etime;
    }
}

//自定义色彩设置，色彩设置保存时执行
function tpure_color()
{
    global $zbp;
    $skin = '';
    $color = $zbp->Config('tpure')->PostCOLOR;
    $skin .= "a, a:hover,.menu li a:hover,.menu li.on a,.menu li .subnav a:hover:after,.menu li .subnav a.on,.menu li.subcate:hover a,.menu li.subcate:hover .subnav a:hover,.menu li.subcate:hover .subnav a.on,.menu li.subcate:hover .subnav a.on:after,.sch-m input,.sch-m input:focus,.sch-m button:after,.schfixed input:focus,.schclose,.schform input,.schform button:after,.post h2 a:hover,.post h2 .istop:before,.post .user a:hover,.post .date a:hover,.post .cate a:hover,.post .views a:hover,.post .cmtnum a:hover,.post .edit a:hover,.post .del a:hover,.post .readmore:hover,.post .readmore:hover:after,.post .tags a:hover,.pages a:hover,a.backlist:hover,.cmtsfoot .reply:hover,.cmtsfoot .reply:hover:before,.cmtarea textarea:focus,.cmtform input:focus,.night .cmtform input:focus,.cmtsubmit button:hover,.cmtsubmit button:hover:before,.sidebox dd a:hover,#divTags ul li a:hover,#divCalendar td a,#divCalendar #today,#divContorPanel .cp-login a:hover,#divContorPanel .cp-vrs a:hover,#divContorPanel .cp-login a:hover:before,#divContorPanel .cp-vrs a:hover:before,.footer a:hover,.goback:hover,.goback:hover:after,.relateinfo h3 a:hover,.teles, .telesmore,.relate,.sitemap a:hover,.night .post h2 a:hover,.night .relateinfo h3 a:hover,.night #cancel-reply,.night #cancel-reply:before,.night .pages a:hover,.night .errorpage .errschtxt:focus,.errschtxt:focus,.night .errorpage .goback:hover,.night .relatelist a:hover,.night .sidebox dd .sidecmtarticle a:hover,.sidebox dd .sidelink a:hover,.night .sidebox dd .sidelink a:hover,.night .sidebox dd .sideitem .itemtitle:hover,.sidebox dd .noimg .sidelink a:hover,.archivedate.on,.archivelist h3 a:hover,.night .archivelist h3 a:hover,.copynoticetxt a:hover,.friendlink li a:hover,.sign span a,.signusermenu a:hover,.filternav li.active,.filternav li.active i::before,.night .filternav li.active,.filter li:hover,.post mark,.sidebox dd .sidecmtarticle a:hover,.night .sidebox dd .avatartitle:hover,.night .sidebox dd .sidecmtcon a:hover,.block.forum .item h2.istop a::before,.block.forum .item h2.istop a,.block.forum .item h2 a:hover,.block.forum .item:hover h2 a,.night .block.forum .item h2.istop a,.night .block.forum .item h2 a:hover,.block.album .albumcon a:hover,.night .block.album .albumcon a:hover,.block.sticker .post h2 .istop::before,.block.hotspot .post h2 .istop::before,.schitemcon em mark,.schwords a:hover { color:#{$color}; }.single p.ue-upload a,.zbaudio_play em.pause::before,.night .zbaudio_play em.pause::before { color:#{$color}!important; }@media screen and (max-width:1080px){.menu ul li.subcate.slidedown > a:after {color:#{$color}}}"; //color
    $skin .= ".menu li:before,.schfixed button,.post h2 em,.pagebar .now-page,.cmtpagebar .now-page,.pagebar a:hover,.cmtpagebar a:hover,a.backtotop:hover,.night .errorpage .errschbtn,.tagscloud li:hover,.night .tagscloud li:hover,.sign span a:hover,.night .signusermenu a:hover,.authinfo h1 span,.filternav li.active::after,.block.album .albumimg a em,.block.forum .item h2.istop em {background:#{$color};} .zbaudio_now,.zbaudio_bar::before {background:#{$color}!important;}"; //background
    $skin .= ".menuico span,.lazyline,a.setnight:hover,a.lang:hover,.night a.backtotop:hover,.swiper-pagination-bullet-active,.indexcon .swiper-button-prev:hover::after,.indexcon .swiper-button-next:hover::after {background-color:#{$color}}"; //background-color
    $skin .= ".menu li .subnav,.schfixed,.sideuserlink p a.wechat span {border-top-color:#{$color}}"; //border-top-color
    $skin .= ".menu li.subcate .subnav a {color:#333}";
    $skin .= ".menu li .subnav:before,.sch-m input,.schfixed:before,.schform input,.single h1:after,.single h2:after,.single h3:after,.single h4:after,.single h5:after,.single h6:after,.contitle h1,.contitle h2 {border-bottom-color:#{$color}}"; //border-bottom-color
    $skin .= ".post .readmore:hover,.post .tags a:hover,.telesmore i,.pagebar .now-page,.pagebar a:hover,a.backlist:hover,#divTags ul li a:hover,#divCalendar td a,#divContorPanel .cp-login a:hover,#divContorPanel .cp-vrs a:hover,.goback:hover,.night .post .readmore:hover,.night #divContorPanel .cp-login a:hover, .night #divContorPanel .cp-vrs a:hover,.night #cancel-reply,.night .pages a:hover,.cmtpagebar .now-page,.cmtpagebar a:hover,.cmtsubmit button:hover,.night .cmtsubmit button:hover,.night .errorpage .errschbtn,.night .errorpage .goback:hover,.closesite h1,.signuser.normal:hover,.sign span a,.searchnull a {border-color:#{$color}}"; //border-color
    $bgcolor = $zbp->Config('tpure')->PostBGCOLOR;
    $skin .= ".wrapper,.main,.indexcon,.closepage { background:#{$bgcolor}; }";
    $sidelayout = $zbp->Config('tpure')->PostSIDELAYOUT;
    if ($sidelayout == 'l') {
        $skin .= ".sidebar { float:left; } .content { float:right; }@media screen and (max-width:1200px){.content { float:none; margin:0; }}";
    }
    $font = $zbp->Config('tpure')->PostFONT;
    if ($font) {
        $skin .= "body,input,textarea {font-family:{$font}}";
    }
    if($zbp->Config('tpure')->PostBGIMGSTYLE == '2'){
        $bgimgstyle = "background-attachment:fixed; background-position:center top; background-size:cover;";
    }else{
        $bgimgstyle = "background-attachment:fixed; background-repeat:repeat;";
    }
    if($zbp->Config('tpure')->PostBGIMGON){
        $skin .= ".indexcon,.main { background-image:url(".$zbp->Config('tpure')->PostBGIMG.");".$bgimgstyle." }";
    }
    $headbgcolor = $zbp->Config('tpure')->PostHEADBGCOLOR;
    $footbgcolor = $zbp->Config('tpure')->PostFOOTBGCOLOR;
    $footfontcolor = $zbp->Config('tpure')->PostFOOTFONTCOLOR;
    if($headbgcolor){
        $skin .= ".header { background-color:#{$headbgcolor};}";
    }
    if($footbgcolor && $footfontcolor){
        $skin .= ".footer { color:#{$footfontcolor}; background-color:#{$footbgcolor}; } .footer a { color:#{$footfontcolor}; }";
    }
    $customcss = $zbp->Config('tpure')->PostCUSTOMCSS;
    $skin .= "{$customcss}";

    return $skin;
}

//文章或页面自动展示全文开关(开启)
//挂接口：Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'tpure_ArticleViewall');
function tpure_ArticleViewall()
{
    global $zbp,$article;
    echo '<div class="editmod">
            <label class="editinputname">自动展开全文</label>
            <input type="text" name="meta_viewall" value="'.$article->Metas->viewall.'" class="checkbox" />
        </div>';
}

//文章编辑页面插入自定义表单；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'tpure_Edit_Response');
function tpure_Edit_Response()
{
    global $zbp,$article;
    tpure_CustomMeta_Response($article);
}

//文章自定义Meta字段
function tpure_CustomMeta_Response(&$object)
{
    global $zbp; ?>
    <link rel="stylesheet" href="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/admin.css">
    <script src="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/custom.js"></script>
    <?php
        echo '<p><label for="proimg">自定义缩略图（列表缩略图片，未设置则调用文章首图）</label><span><input type="text" name="meta_proimg" id="proimg" placeholder="请点击上传按钮选择图片或手动输入图片地址..." value="' . htmlspecialchars($object->Metas->proimg) . '" class="metasrc"/></span><span><input type="button" class="uploadimg button" value="上传图片" /></span></p>';
        if($zbp->Config('tpure')->PostZBAUDIOON == '1'){
        if(htmlspecialchars($object->Metas->audioimg)){
            $audioimg = htmlspecialchars($object->Metas->audioimg);
        }else{
            $audioimg = $zbp->host.'zb_users/theme/tpure/style/images/audioimg.png';
        }
        echo '<div class="introbox"><div class="togglelabel">+++++ 音频设置 +++++</div>
<div class="media">
    <div class="mediacover"><p>
    <span>
    <input type="hidden" name="meta_audioimg" value="' . htmlspecialchars($object->Metas->audioimg) . '" class="thumbsrc"></span>
    <span><input type="button" value="" class="uploadimg uploadico">
    </span>
    <span><img src="' . $audioimg . '" class="thumbimg" /></span></p>

    </div>
    <div class="mediainfo">
        <div class="mediaform">
            <p>
                <span><input type="text" name="meta_audio" value="' . htmlspecialchars($object->Metas->audio) . '" placeholder="请点击上传按钮选择MP3文件或手动输入MP3地址..." class="metasrc"/></span>
                <span><input type="button" class="uploadfile button" value="上传MP3"></span>
            </p>
            <p>
            <input type="text" name="meta_audiotitle" value="' . htmlspecialchars($object->Metas->audiotitle) . '" placeholder="音频标题（选填）" class="metahalf"/>
            <input type="text" name="meta_audiosinger" value="' . htmlspecialchars($object->Metas->audiosinger) . '" placeholder="音频作者（选填）" class="metahalf"/>
            </p>
            <div class="mediaautoplay">自动播放：<input type="text" name="meta_audioautoplay" class="checkbox" value="' . $object->Metas->audioautoplay . '" /></div>
        </div>
    </div>
</div>
            </div>';
        }
        if($zbp->Config('tpure')->PostVIDEOON == '1'){
        echo '<div class="introbox"><div class="togglelabel">+++++ 视频设置 +++++</div>
<p class="videobox"><label for="video">自动播放：<input type="text" name="meta_videoautoplay" class="checkbox" value="' . $object->Metas->videoautoplay . '" />　|　循环播放：<input type="text" name="meta_videoloop" class="checkbox" value="' . $object->Metas->videoloop . '" /> （自动和循环播放仅支持MP4和m3u8流媒体视频）</label><span><input type="text" name="meta_video" id="video" placeholder="请点击右侧上传按钮选择MP4视频或手动输入站外视频通用地址..." value="' . htmlspecialchars($object->Metas->video) . '" class="metasrc"/></span><span><input type="button" class="uploadfile button" value="上传MP4" /></span></p>
            </div>';
        }
}

//文章页面自定义Meta字段；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'tpure_SingleSEO');
function tpure_SingleSEO()
{
    global $zbp,$article;
    $array = array('singletitle', 'singlekeywords', 'singledescription');
    $singletitle_intro = 'SEO标题';
    $singlekeywords_intro = 'SEO关键词';
    $singledescription_intro = 'SEO描述';
    if (is_array($array) == false) {
        return null;
    }
    if (count($array) == 0) {
        return null;
    }
    foreach ($array as $key => $value) {
        if ($key == 0) {
            $single_meta_intro = $singletitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ 文章页面SEO设置 +++++</div><p><label>' . $single_meta_intro . '</label><input type="text" name="meta_' . $value . '" placeholder="请输入' . $single_meta_intro . '..." value="' . htmlspecialchars($article->Metas->$value) . '" class="metasrc"/></p>';
        } elseif ($key == 1) {
            $single_meta_intro = $singlekeywords_intro;
            echo '<p><label>' . $single_meta_intro . '</label><input type="text" name="meta_' . $value . '" placeholder="请输入' . $single_meta_intro . '..." value="' . htmlspecialchars($article->Metas->$value) . '" class="metasrc"/></p>';
        } else {
            $single_meta_intro = $singledescription_intro;
            echo '<p><span class="title">' . $single_meta_intro . '</span><br /><textarea cols="3" rows="3" name="meta_' . $value . '" placeholder="请输入' . $single_meta_intro . '..." class="metaintro">' . htmlspecialchars($article->Metas->$value) . '</textarea></p></div>';
        }
    }
}

//分类自定义Meta字段；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response', 'tpure_CategorySEO');
function tpure_CategorySEO()
{
    global $zbp,$cate; ?>
    <link rel="stylesheet" href="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/admin.css">
    <script src="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/custom.js"></script>
    <?php
    if ($zbp->CheckPlugin('UEditor')) {
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    }
    $array = array('catetitle', 'catekeywords', 'catedescription');
    $catetitle_intro = '分类SEO标题';
    $catekeywords_intro = '分类SEO关键词';
    $catedescription_intro = '分类SEO描述';
    if (is_array($array) == false) {
        return null;
    }
    if (count($array) == 0) {
        return null;
    }
    foreach ($array as $key => $value) {
        if ($key == 0) {
            $cate_meta_intro = $catetitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ 分类列表SEO设置 +++++</div><p><span class="title">' . $cate_meta_intro . '</span><br /><input type="text" name="meta_' . $value . '" value="' . htmlspecialchars($cate->Metas->$value) . '" class="metasrc"/></p>';
        } elseif ($key == 1) {
            $cate_meta_intro = $catekeywords_intro;
            echo '<p><span class="title">' . $cate_meta_intro . '</span><br /><input type="text" name="meta_' . $value . '" value="' . htmlspecialchars($cate->Metas->$value) . '" class="metasrc"/></p>';
        } else {
            $cate_meta_intro = $catedescription_intro;
            echo '<p><span class="title">' . $cate_meta_intro . '</span><br /><textarea cols="3" rows="3" id="edtIntro" name="meta_' . $value . '" class="metaintro">' . htmlspecialchars($cate->Metas->$value) . '</textarea></p></div>';
        }
    }
}

//标签自定义Meta字段；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Tag_Edit_Response', 'tpure_TagSEO');
function tpure_TagSEO()
{
    global $zbp,$tag; ?>
    <link rel="stylesheet" href="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/admin.css">
    <script src="<?php echo $zbp->host; ?>zb_users/theme/tpure/script/custom.js"></script>
    <?php
    if ($zbp->CheckPlugin('UEditor')) {
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    }
    $array = array('tagtitle', 'tagkeywords', 'tagdescription');
    $tagtitle_intro = '标签SEO标题';
    $tagkeywords_intro = '标签SEO关键词';
    $tagdescription_intro = '标签SEO描述';
    if (is_array($array) == false) {
        return null;
    }
    if (count($array) == 0) {
        return null;
    }
    foreach ($array as $key => $value) {
        if ($key == 0) {
            $tag_meta_intro = $tagtitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ TAGS列表SEO设置 +++++</div><p><span class="title">' . $tag_meta_intro . '</span><br /><input type="text" name="meta_' . $value . '" value="' . htmlspecialchars($tag->Metas->$value) . '" class="metasrc"/></p>';
        } elseif ($key == 1) {
            $tag_meta_intro = $tagkeywords_intro;
            echo '<p><span class="title">' . $tag_meta_intro . '</span><br /><input type="text" name="meta_' . $value . '" value="' . htmlspecialchars($tag->Metas->$value) . '" class="metasrc"/></p>';
        } else {
            $tag_meta_intro = $tagdescription_intro;
            echo '<p><span class="title">' . $tag_meta_intro . '</span><br /><textarea cols="3" rows="3" id="edtIntro" name="meta_' . $value . '" class="metaintro">' . htmlspecialchars($tag->Metas->$value) . '</textarea></p></div>';
        }
    }
}

//用户上传头像及性别
//挂接口：Add_Filter_Plugin('Filter_Plugin_Member_Edit_Response', 'tpure_MemberEdit_Response');
function tpure_MemberEdit_Response()
{
    global $zbp,$member;
    ?>
    <link rel="stylesheet" href="<?php echo $zbp->host;?>zb_users/theme/tpure/script/admin.css">
    <script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/custom.js"></script>
    <?php
    if ($zbp->CheckPlugin('UEditor')) {
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
        echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    }
    $array = array('memberimg','membersex');
    $memberimg_intro = '头像';
    $membersex_intro = '性别';
    if (is_array($array) == false) {
        return null;
    }
    if (count($array) == 0) {
        return null;
    }
    foreach ($array as $key => $value) {
        if ($key == 0) {
            $member_meta_intro = $memberimg_intro;
            echo '<p style="width:600px;"><span class="title">' . $member_meta_intro . '</span><br /><span><input type="text" name="meta_' . $value . '" value="' . htmlspecialchars($member->Metas->$value) . '" class="metasrc"/></span><span><input type="button" class="uploadimg button" value="上传头像"></span></p>';
        }elseif ($key == 1) {
            $member_meta_intro = $membersex_intro;
            echo '<p><span class="title">' . $member_meta_intro . '</span><br /><select class="edit" size="1" name="meta_' . $value . '">
                    <option value="1" '.($member->Metas->$value == '1' ? 'selected="selected"' : '').'>男</option><option value="2" '.($member->Metas->$value == '2' ? 'selected="selected"' : '').'>女</option></select></p>';
        }
    }
}

//文章图片灯箱引入样式及js；
//挂接口：Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','tpure_Fancybox');
function tpure_Fancybox()
{
    global $zbp;
    $zbp->header .= '    <link href="'.$zbp->host.'zb_users/theme/'.$zbp->theme.'/plugin/fancybox/fancybox.css" rel="stylesheet" type="text/css" />' . "\r\n";
    $zbp->header .= '    <script src="'.$zbp->host.'zb_users/theme/'.$zbp->theme.'/plugin/fancybox/fancybox.js"></script>' . "\r\n";
    $zbp->header .= '    <script>$(document).ready(function() {$(".fancybox").fancybox();});</script>' . "\r\n";
}

//匹配文章中class值为ue-image的图片执行灯箱查看；
//挂接口：Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','tpure_FancyboxRegex');
function tpure_FancyboxRegex(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    if($zbp->CheckPlugin('UEditor') && version_compare($zbp->LoadApp('plugin', 'UEditor')->version, '1.6.5') >= 0){
        $pattern = "/<img([^>]*ue-image[^>]*)src=('|\")([^'\">]*).(bmp|gif|jpeg|jpg|png|swf|svg)('|\")(.*?)>/i";
    }else{
        $pattern = "/<img(.*?)src=('|\")([^'\">]*).(bmp|gif|jpeg|jpg|png|swf|svg)('|\")(.*?)>/i";
    }
    if(!empty($article)){
        $replacement = '<a href=$2$3.$4$5 data-fancybox="images"><img class="ue-image" src=$2$3.$4$5$6></a>';
        $content = preg_replace($pattern, $replacement, $article->Content);
        $article->Content = $content;
        $template->SetTags('article', $article);
    }
}

//主题自带模块(热门阅读/热评文章/最新文章/推荐阅读/最新评论/站长简介)
//编辑模块成功时执行接口：Add_Filter_Plugin('Filter_Plugin_PostModule_Succeed', 'tpure_CreateModule');
//评论成功时执行接口：Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'tpure_CreateModule');
//评论删除成功时执行接口：Add_Filter_Plugin('Filter_Plugin_DelComment_Succeed', 'tpure_CreateModule');
//审核评论成功时执行接口：Add_Filter_Plugin('Filter_Plugin_CheckComment_Succeed', 'tpure_CreateModule');
//文章提交成功时执行接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_CreateModule');
//删除文章时执行接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Del', 'tpure_CreateModule');
function tpure_CreateModule()
{
    global $zbp;
    //刷新浏览总量
    $all_views = ($zbp->option['ZC_LARGE_DATA'] == true || $zbp->option['ZC_VIEWNUMS_TURNOFF'] == true) ? 0 : GetValueInArrayByCurrent($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Post']), 'num');
    $zbp->cache->all_view_nums = $all_views;
    $zbp->SaveCache();

    $module_list = array(
        array("tpure_hotviewarticle", "tpure_HotViewArticle", "ul", "热门阅读","0"),
        array("tpure_hotcmtarticle", "tpure_HotCmtArticle", "ul", "热评文章","0"),
        array("tpure_newarticle", "tpure_NewArticle", "ul", "最新文章","0"),
        array("tpure_recarticle", "tpure_RecArticle", "ul", "推荐阅读","0"),
        array("tpure_avatarcomment", "tpure_AvatarComment", "ul", "最近评论","0"),
        array("tpure_newcomment", "tpure_NewComment", "ul", "最新评论","0"),
        array("tpure_user", "tpure_User", "div", "站长简介","1"),
        array("tpure_readers", "tpure_Readers", "ul", "读者墙","0"),
    );
    $module_filenames = array();
    foreach ($module_list as $item) {
        array_push($module_filenames, $item[0]);
    }
    $modules = $zbp->GetModuleList(array("*"), array(
        array("IN", "mod_FileName", $module_filenames),
    ));
    $has_modules = array();
    foreach ($modules as $item) {
        $item->Content = tpure_SideContent($item);
        $item->Save();
        //$zbp->AddBuildModule($item->FileName);
        array_push($has_modules, $item->FileName);
    }
    foreach ($module_filenames as $k => $item) {
        if (!array_search($item, $has_modules)) {
            $module = $module_list[$k];
            $t = new Module();
            $t->Name = $module[3];
            $t->IsHideTitle = $module[4];
            $t->FileName = $module[0];
            $t->Source = "theme_tpure";
            $t->SidebarID = 0;
            $t->Content = tpure_SideContent($t);
            $t->HtmlID = $module[1];
            $t->Type = $module[2];
            $t->Save();
        }
    }
}

//卸载主题时判断是否删除自定义模块
function tpure_DelModule()
{
    global $zbp;
    $modules = array('tpure_hotviewarticle', 'tpure_hotcmtarticle', 'tpure_newarticle', 'tpure_recarticle', 'tpure_avatarcomment', 'tpure_newcomment', 'tpure_user', 'tpure_readers');
    $w = array();
    $w[] = array('IN', 'mod_FileName', $modules);
    $modules = $zbp->GetModuleList(array('*'),$w);
    foreach ($modules as $item) {
        $item->Del();
    }
}

//模块内容
function tpure_SideContent(&$module)
{
    global $zbp;
    $str = "";
    if($zbp->Config('tpure')->PostBLANKSTYLE == 2){
        $blankstyle = ' target="_blank"';
    }else{
        $blankstyle = '';
    }
    switch ($module->FileName) {
        case 'tpure_hotviewarticle':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 5;
            $hotArtList = tpure_GetHotArticleList($num);
            foreach ($hotArtList as $item) {
                $str .= '<li class="sideitem">';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){
                    $str .= '<div class="sideimg">
                        <a href="'.$item->Url.'"'. $blankstyle .'>
                            <img src="'.tpure_Thumb($item).'" alt="'.$item->Title.'">
                        </a>
                    </div>';
                }
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '<div class="hasimg">'; }
                $str .= '<a href="'.$item->Url.'"'. $blankstyle .' title="'.$item->Title.'" class="itemtitle">'.$item->Title.'</a>';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '</div>'; }
                $str .= '<p class="sideinfo"><span class="view">'.$item->ViewNums.' '.$zbp->lang['tpure']['viewnum'].'</span>'.$item->Category->Name.'</p>
            </li>';
            }
        break;
        case 'tpure_hotcmtarticle':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 5;
            $hotArtList = tpure_GetHotArticleList($num, "cmt");
            foreach ($hotArtList as $item) {
                $str .= '<li class="sideitem">';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){
                    $str .= '<div class="sideimg">
                        <a href="'.$item->Url.'"'. $blankstyle .'>
                            <img src="'.tpure_Thumb($item).'" alt="'.$item->Title.'">
                        </a>
                    </div>';
                }
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '<div class="hasimg">'; }
                $str .= '<a href="'.$item->Url.'"'. $blankstyle .' title="'.$item->Title.'" class="itemtitle">'.$item->Title.'</a>';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '</div>'; }
                $str .= '<p class="sideinfo"><span class="view">'.$item->CommNums.' '.$zbp->lang['tpure']['cmtnum'].'</span>'.$item->Category->Name.'</p>
            </li>';
            }
        break;
        case 'tpure_newarticle':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 5;
            $hotArtList = GetList($num);
            foreach ($hotArtList as $item) {
                $str .= '<li class="sideitem">';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){
                    $str .= '<div class="sideimg">
                        <a href="'.$item->Url.'"'. $blankstyle .'>
                            <img src="'.tpure_Thumb($item).'" alt="'.$item->Title.'">
                        </a>
                    </div>';
                }
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '<div class="hasimg">'; }
                $str .= '<a href="'.$item->Url.'"'. $blankstyle .' title="'.$item->Title.'" class="itemtitle">'.$item->Title.'</a>';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){ $str .= '</div>'; }
                $str .= '<p class="sideinfo"><em class="view">'.tpure_TimeAgo($item->Time()).'</em></p>
            </li>';
            }
        break;
        case 'tpure_recarticle':
            $tuiArtList = tpure_GetRecArticle();
            foreach ($tuiArtList as $item) {
                $image = tpure_Thumb($item);
                $str .= '<li class="sideitem';if(tpure_Thumb($item) == '' || $zbp->Config('tpure')->PostSIDEIMGON == '0'){$str .= ' noimg';}$str .= '">';
                if(tpure_Thumb($item) != '' && $zbp->Config('tpure')->PostSIDEIMGON == '1'){
                    if($zbp->Config('tpure')->PostRANDTHUMBON == '1'){$IsThumb='2';}else{$IsThumb='1';}
                $str .= '<div class="sideitemimg">
                    <a href="'.$item->Url.'"'. $blankstyle .'>
                        <img src="'.tpure_Thumb($item,$IsThumb).'" alt="'.$item->Title.'">
                    </a>
                </div>';
                }
                $str .= '<div class="sidelink">
                    <a href="'.$item->Url.'"'. $blankstyle .' title="'.$item->Title.'">'.$item->Title.'</a>
                    <p class="sideinfo"><em class="date">'.tpure_TimeAgo($item->Time()).'</em>'.$item->Category->Name.'</p>
                </div>
            </li>';
            }
        break;
        case 'tpure_avatarcomment':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 5;
            $newCmtList = tpure_GetNewComment($num);
            foreach ($newCmtList as $item) {
                $str .= '<li class="sideitem">
                    <div class="avatarcmt">
                        <div class="avatarimg"><span><img src="'.tpure_MemberAvatar($item->Author,$item->Author->Email).'"/></span></div>
                        <div class="avatarinfo">
                            <p><em class="avatarname">'.$item->Author->StaticName.'</em><span>评论文章：</span></p>
                            <p><a href="'.$item->Post->Url.'"'. $blankstyle .' title="'.$item->Post->Title.'" class="avatartitle">'.$item->Post->Title.'</a></p>
                        </div>
                        <div class="avatarcon"><i>'.$item->Content.'</i></div>
                    </div>
                </li>';
            }
        break;
        case 'tpure_newcomment':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 5;
            $newCmtList = tpure_GetNewComment($num);
            foreach ($newCmtList as $item) {
                $str .= '<li class="sideitem">
                <div class="sidecmtinfo"><em>'.$item->Author->StaticName.'</em>'.tpure_TimeAgo($item->Time()).'</div>
                <div class="sidecmtcon"><a href="'.$item->Post->Url.'#cmt'.$item->ID.'"'. $blankstyle .' title="'.$item->Content.'">'.$item->Content.'</a></div>
                <div class="sidecmtarticle"><a href="'.$item->Post->Url.'"'. $blankstyle .' title="'.$item->Post->Title.'">'.$item->Post->Title.'</a></div>
            </li>';
            }
        break;
        case 'tpure_user':
            if($zbp->Config('tpure')->PostSIDEUSERBG){$sideuserbg = $zbp->Config('tpure')->PostSIDEUSERBG;}else{$sideuserbg = $zbp->host.'zb_users/theme/tpure/style/images/banner.jpg';}
            if($zbp->Config('tpure')->PostSIDEUSERIMG){$sideuserimg = $zbp->Config('tpure')->PostSIDEUSERIMG;}else{$sideuserimg = $zbp->host.'zb_users/avatar/0.png';}
            if($zbp->Config('tpure')->PostSIDEUSERNAME){$sideusername = $zbp->Config('tpure')->PostSIDEUSERNAME;}else{$sideusername = $zbp->name;}
            if($zbp->Config('tpure')->PostSIDEUSERINTRO){$sideuserintro = $zbp->Config('tpure')->PostSIDEUSERINTRO;}else{$sideuserintro = $zbp->name;}
            if($zbp->Config('tpure')->PostSIDEUSERQQ){$sideuserqq = '<p><a href="https://wpa.qq.com/msgrd?v=3&uin='.$zbp->Config('tpure')->PostSIDEUSERQQ.'&site=qq&menu=yes" target="_blank" title="'.$zbp->lang['tpure']['sideqq'].'" class="qq"></a></p>';}else{$sideuserqq = '';}
            if($zbp->Config('tpure')->PostSIDEUSERWECHAT){$sideuserwechat = '<p><a href="javascript:;" title="'.$zbp->lang['tpure']['sidewechat'].'" class="wechat"><span><img src="'.$zbp->Config('tpure')->PostSIDEUSERWECHAT.'" alt="'.$zbp->lang['tpure']['sidewechat'].'"></span></a></p>';}else{$sideuserwechat = '';}
            if($zbp->Config('tpure')->PostSIDEUSEREMAIL){$sideuseremail = '<p><a href="mailto:'.$zbp->Config('tpure')->PostSIDEUSEREMAIL.'" target="_blank" title="'.$zbp->lang['tpure']['sideemail'].'" class="email"></a></p>';}else{$sideuseremail = '';}
            if($zbp->Config('tpure')->PostSIDEUSERWEIBO){$sideuserweibo = '<p><a href="'.$zbp->Config('tpure')->PostSIDEUSERWEIBO.'" target="_blank" title="'.$zbp->lang['tpure']['sideweibo'].'" class="weibo"></a></p>';}else{$sideuserweibo = '';}
            if($zbp->Config('tpure')->PostSIDEUSERGROUP){$sideusergroup = '<p><a href="'.$zbp->Config('tpure')->PostSIDEUSERGROUP.'" target="_blank" title="'.$zbp->lang['tpure']['sidegroup'].'" class="group"></a></p>';}else{$sideusergroup = '';}
            $str .= '<div class="sideuser">
            <div class="sideuserhead" style="background-image:url('.$sideuserbg.');"></div>
            <div class="sideusercon">
            <div class="avatar"><img src="'.$sideuserimg.'" alt="'.$sideusername.'"></div>
            <h4>'.$sideusername.'</h4>
            <p>'.$sideuserintro.'</p>
            <div class="sideuserlink">
                '.$sideuserqq.$sideuserwechat.$sideuseremail.$sideuserweibo.$sideusergroup.'
            </div>';
            if($zbp->Config('tpure')->PostSIDEUSERCOUNT == '1'){
            $str .= '<div class="sideuserfoot">
                <p><strong>'.$zbp->cache->all_article_nums.'</strong><span>'.$zbp->lang['tpure']['sidearticle'].'</span></p>
                <p><strong>'.$zbp->cache->all_comment_nums.'</strong><span>'.$zbp->lang['tpure']['sidecmt'].'</span></p>
                <p><strong>'.$zbp->cache->all_view_nums.'</strong><span>'.$zbp->lang['tpure']['sideview'].'</span></p>
            </div>';
            }
            $str .= '</div>
            </div>';
        break;
        case 'tpure_readers':
            $num = $module->MaxLi > 0 ? $module->MaxLi : 8;
            $Readers = tpure_Readers($num);
            foreach ($Readers as $item) {
                $str .= '<li class="readeritem">';
                if($item['url']){ $str .= '<a href="'. $item['url'] .'" target="_blank" rel="nofollow">';}
                $str .= '<span class="readerimg"><img src="'. tpure_MemberAvatar($item['member'],$item['email']) .'" alt="'. $item['name'] .'"></span>';
                $str .= '<span class="readername">'. $item['name'] .'</span><span class="readernum">'. $item['count'] .'</span>';
                if($item['url']){ $str .= '</a>';}
            $str .= '</li>';
            }
        break;
    }
    return $str;
}

//获取热门文章列表
function tpure_GetHotArticleList($num = 5, $type = "view")
{
    global $zbp;
    if ($type == "cmt") {
        $time = $zbp->Config('tpure')->PostSIDECMTDAY;
    } else {
        $time = $zbp->Config('tpure')->PostSIDEVIEWDAY;
    }
    if (empty($time)) {
        $time = 90 * 10;
    }
    $time = time() - $time * 24 * 60 * 60;
    $w = array();
    $w[] = array("=", "log_Type", "0");
    $w[] = array("=", "log_Status", "0");
    $w[] = array(">", "log_PostTime", $time);
    if ($type == "view") {
        $order = array("log_ViewNums" => "DESC");
    } else {
        $order = array("log_CommNums" => "DESC");
    }
    $articles = $zbp->GetArticleList(array('*'), $w, $order, array($num));
    return $articles;
}

//推荐阅读模块
function tpure_GetRecArticle()
{
    global $zbp;
    $w = array();
    $w[] = array("=", "log_Type", "0");
    $w[] = array("=", "log_Status", "0");
    $ids = $zbp->Config('tpure')->PostSIDERECID;
    $ids = explode(",", $ids);
    $w[] = array("IN", "log_ID", $ids);
    $list = $zbp->GetArticleList(array('*'), $w);
    $articles = array();
    foreach ($ids as $item) {
        $articles[] = $zbp->GetPostByID($item);
    }
    return $articles;
}

//获取最新评论
function tpure_GetNewComment($num = 5)
{
    global $zbp;
    $w = array();
    $w[] = array("=", "comm_IsChecking", "0");
    $comments = $zbp->GetCommentList(array('*'), $w, array("comm_PostTime" => "DESC"), array($num));
    foreach ($comments as &$comment) {
        if ($comment->ParentID > 0) {
            $comment->Parent = $zbp->GetCommentByID($comment->ParentID);
            $comment->Parent->Content = TransferHTML($comment->Parent->Content, '[nohtml]');
        }
        $comment->Content = TransferHTML($comment->Content, '[nohtml]');
    }
    return $comments;
}

//获取站内所有标签(标签云)
function tpure_GetTagCloudList()
{
	global $zbp;
	$filterNum = 0;
	$result = $zbp->GetTagList(array('*'), array(array('>', 'tag_Count', $filterNum),), array('tag_Count' => 'DESC'));
	$str = '<ul class="tagscloud">';
        foreach($result as $tag){
            $str .= "<li><a href=\"{$tag->Url}\" title=\"{$tag->Name}\">{$tag->Name}</a><span>({$tag->Count})</span></li>";
        }
	$str .= '</ul>';
    $tagsnull = '<div class="tagsnull">'.$zbp->lang['tpure']['tagsnull'].'</div>';
	return count($result) ? $str : $tagsnull;
}

//获取文章归档列表(文章过多时不建议开启)
function tpure_GetArchiveList()
{
    global $zbp;
    $result = $zbp->GetArticleList(array('*'), array(
        array('=', 'log_Status', '0'),
    ), array('log_PostTime' => 'ASC'), array(1));
    if (count($result) == 0) {
        return false;
    }
    $months = array();
    $beignYear = (int) $result[0]->Time('Y');
    $beignMonth = (int) $result[0]->Time('m');
    $nowYear = (int) date('Y');
    $nowMonth = (int) date('m');
    $n = (int) date('Y') - $beignYear + 1;
    for ($i = 0; $i < $n; $i++) {
        $key = $beignYear + $i;
        $j_start = 1;
        if ($key == $beignYear) {
            $j_start = $beignMonth;
        }
        $z = 13;
        if ($key == $nowYear) {
            $z = $nowMonth + 1;
        }
        for ($j = $j_start; $j < $z; $j++) {
            $key = $key . $j;
            $months[$key] = mktime(0, 0, 0, $j, 1, $beignYear + $i);
        }
    }
    $list = array();
    foreach ($months as $k => $v) {
        $start = (int) $v - 1;
        $end = (int) strtotime('+1 month', $v) + 1;
        $result2 = $zbp->GetArticleList(array('*'), array(
            array('=', 'log_Status', '0'),
            array('>', 'log_PostTime', $start),
            array('<', 'log_PostTime', $end),
        ), array('log_PostTime' => 'DESC'));
        if (count($result2) > 0) {
            $list[$k] = array(
                'timestamp' => $v,
                'articles'  => $result2,
            );
        }
    }
    return $list;
}

//创建文章归档HTML
function tpure_CreateArchiveHTML()
{
    global $zbp;
    $archives = tpure_GetArchiveList();
    $zbp->Config('tpure')->PostBLANKSTYLE == 2?$target = ' target="_blank"':$target = '';
    $str = null;
    if ($archives) {
        if ($zbp->Config('tpure')->PostARCHIVEDATESORT == 'DESC') {
            $archives = array_reverse($archives);
        }
        foreach ($archives as $monthItem) {
            $str .= '<div class="archiveitem">';
            $str .= '<div class="archivedate">'.date('Y年m月', $monthItem['timestamp']).'</div>';
            $str .= '<ul class="archivelist">';
            foreach ($monthItem['articles'] as $item) {
                $str .= '<li>';
                if($zbp->Config('tpure')->PostARCHIVEDATEON == '1'){
                    $str .= '<span class="archivetime">';
                    if($zbp->Config('tpure')->PostARCHIVEDATETYPE == '0'){
                        $str .= '['.$item->Time('m/d').']';
                    }else{
                        $str .= '['.$item->Time('m月d日').']';
                    }
                }
                $str .= '</span>';
                $str .= ' <h3><a href="'.$item->Url.'"'.$target.' title="'.$item->Title.'">'.$item->Title.'</a></h3></li>';
            }
            $str .= '</ul>';
            $str .= '</div>';
        }
    } else {
        $str = '<div class="archivenull">'.$zbp->lang['tpure']['archivenull'].'</div>';
    }
    return $str;
}

//创建文章归档缓存文件(缓存路径：zb_users/theme/tpure/archive.html)
function tpure_CreateArchiveCache($str = false)
{
    global $zbp;
    $path = $zbp->usersdir . 'cache/theme/tpure/';
    if (!file_exists($path)) {
        @mkdir($path, 0755, true);
    }
    if (!file_exists($path)) {
        return false;
    }
    if (!$str) {
        $str = tpure_CreateArchiveHTML();
    }
    $filePath = $path . 'archive.html';
    $file = fopen($filePath, "w");
    fwrite($file, $str);
    fclose($file);
    if (!file_exists($filePath)) {
        return false;
    }
    return true;
}

//获取文章归档缓存文件
function tpure_GetArchives()
{
    global $zbp;
    $filePath = $zbp->usersdir . 'cache/theme/tpure/archive.html';
    if (!file_exists($filePath)) {
        $str = tpure_CreateArchiveHTML();
        $status = tpure_CreateArchiveCache($str);
        if (!$status) {
            return $str;
        } else {
            $str = file_get_contents($filePath);
            return $str;
        }
    } else {
        $str = file_get_contents($filePath);
        return $str;
    }
}

//自动更新文章归档缓存
//文章提交成功时更新归档缓存；挂接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_ArchiveAutoCache');
//文章删除成功时更新归档缓存；挂接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Del', 'tpure_ArchiveAutoCache');
function tpure_ArchiveAutoCache()
{
    global $zbp;
    if ($zbp->Config('tpure')->PostAUTOARCHIVEON) {
        tpure_CreateArchiveCache();
    }
}

//删除文章归档缓存
function tpure_delArchive()
{
    global $zbp;
    $dir = $zbp->usersdir . 'cache/theme/' . $zbp->theme . '/';
    if (file_exists($dir)) {
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."archive.html";
                if(!is_dir($fullpath)) {
                    @unlink($fullpath);
                }else{
                    @deldir($fullpath);
                }
            }
        }
        closedir($dh);
    }
}

//列表排序(可风)
//挂接口：Add_Filter_Plugin('Filter_Plugin_LargeData_Article','tpure_LargeDataArticle');
function tpure_LargeDataArticle($select, $w, &$order, $limit, $option, $type='')
{
    global $zbp;
    switch($type){
        case 'category':
            $pagebar = $option['pagebar'];
            $sort = GetVars('sort','GET') ? 'ASC' : 'DESC';
            switch($o = GetVars('order','GET')){
                case 'viewlist':
                    $order = array('log_ViewNums' => $sort);
                    break;
                case 'cmtlist':
                    $order = array('log_CommNums' => $sort);
                    break;
                case 'newlist':
                default:
                    $order = array('log_PostTime' => $sort);
                    $sort == 'DESC' && $o = null;
                    break;
            }
            if ($o){
                $pagebar->UrlRule->__construct($zbp->option['ZC_CATEGORY_REGEX'] .($zbp->Config('system')->ZC_STATIC_MODE != 'REWRITE' ? '&' : '?'). 'order={%order%}&sort={%sort%}');
                $pagebar->UrlRule->Rules['{%order%}'] = $o;
                $pagebar->UrlRule->Rules['{%sort%}'] = (int)GetVars('sort','GET');
            }
            break;
    }
}

//读者墙(可风)
function tpure_readers($limit = 100)
{
    global $zbp;
    $list = array();
    if($zbp->Config('tpure')->PostREADERSURLON == '1'){
        $where = array(array('<>','comm_IsChecking','1'),array('<>','comm_HomePage',''));
    }else{
        $where = array('<>','comm_IsChecking','1');
    }
    $sql = $zbp->db->sql->get()->select($zbp->table['Comment'])->column('comm_Name,comm_Email,comm_AuthorID,comm_HomePage,count(*)')->where($where)->groupby('comm_Name,comm_Email,comm_AuthorID,comm_HomePage')->orderBy(array('count(*)' => 'DESC'))->limit($limit)->sql;
    foreach($zbp->db->Query($sql) as $value){
        $value = array_values($value);
        $list[] = array(
            'name'   => $value[0],
            'email'  => $value[1],
            'member' => $zbp->GetMemberByID($value[2]),
            'url'  => str_ireplace('{#ZC_BLOG_HOST#}',$zbp->host,$value[3]),
            'count'  => $value[4],
        );
    }
    return $list;
}

//新文章判断
//挂接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Core', 'tpure_ArticleCore');
function tpure_ArticleCore($article)
{
    if($article->ID > 0){
        $GLOBALS['is_new_article'] = false;
    }else{
        $GLOBALS['is_new_article'] = true;
    }
}

//新文章通知
//挂接口：Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'tpure_ArticleSendmail');
function tpure_ArticleSendmail($article)
{
    global $zbp;
    $mailto = $zbp->Config('tpure')->MAIL_TO;
    if($zbp->Config('tpure')->PostLOGOON){
        $logo = '<img src="'.$zbp->Config('tpure')->PostLOGO.'" style="height:40px;line-height:0;border:none;display:block;" />';
    }else{
        $logo = '<span style="font-size:22px; color:#666;">'.$zbp->name.'</span>';
    }
    if($zbp->Config('tpure')->PostINTRONUM){
        $intro = preg_replace('/[\r\n\s]+/', ' ', trim(SubStrUTF8(TransferHTML($article->Intro,'[nohtml]'),$zbp->Config('tpure')->PostINTRONUM)).'...');
    }else{
        $intro = $article->Intro;
    }
    if($zbp->user->StaticName == $article->Author->StaticName){
        $staticname = 'Ta';
    }else{
        $staticname = $article->Author->StaticName;
    }
    if($GLOBALS['is_new_article'] === true){
        if($zbp->Config('tpure')->PostNEWARTICLEMAILSENDON){
            if($mailto){
                $subject = $zbp->user->StaticName . '发布了一篇新文章 《'.$article->Title.'》';
                $content = '<table width="700" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; border:1px solid rgb(230,230,230);"><tbody><tr><td><table cellpadding="0" cellspacing="0" border="0"><tbody><tr><td width="30"></td><td width="640" style="padding:20px 0 10px;"><a href="'.$zbp->host.'" target="_blank" style="text-decoration:none; display:inline-block; vertical-align:top;">'.$logo.'</a></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table><tbody><tr><td width="30"></td><td width="640"><p style="margin:0; padding:30px 0 0px; font-size:14px; color:#151515; font-family:microsoft yahei; font-weight:bold; border-top:1px solid #eee;">管理员，你好！</p><p style="font-size:14px; color:#151515; font-family:microsoft yahei;">'.$zbp->user->StaticName.'在 [ '.$zbp->name.' ] 发布了'.$staticname.'的新文章<em style="font-weight:bold;font-style:normal; margin: 0 5px;">《'.$article->Title.'》</em>：</p></td><td width="30"></td></tr><tr><td width="30"></td><td width="640"><p style="margin:0 0 20px; padding:15px 20px; font-size:16px; color:#7d8795; font-family:microsoft yahei; line-height:22px; border:1px solid #e6e6e6; background-color:#f5f5f5;">'.$intro.'</p><p style="margin:0 0 30px; text-align:center;"><a href="'.$article->Url.'" target="_blank" style="margin:0 auto; padding:12px 25px; font-size:14px; color:#fff; font-family:microsoft yahei; font-weight:bold; text-decoration:none; text-transform:capitalize; border:0; border-radius:50px; cursor:pointer; box-shadow:0 1px 2px rgba(0, 0, 0, 0.1); background-color:#206ffd; background-image:linear-gradient(to top, #206dfd 0%, #2992ff 100%); display: inline-block;">查看文章的完整内容</a></p></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table align="center" cellspacing="0" class="dao-content-footer" style=" background-color:rgb(245,245,245); line-height: 28px; padding: 13px 23px; color: #7d8795; font-weight:500; border-top:1px solid #e6e6e6;" width="100%" bgcolor="#e6e6e6"><tbody><tr><td style="font-family:microsoft yahei; font-size:14px; vertical-align:top; text-align:center;" valign="top">'.$zbp->name.' - '.$zbp->subname.'</td></tr></tbody></table></td></tr></tbody></table>';
                tpure_SendEmail($mailto,$subject,$content);
            }
        }
    }else{
        if($zbp->Config('tpure')->PostEDITARTICLEMAILSENDON){
            if($mailto){
                $subject = $zbp->user->StaticName . '编辑了文章 《'.$article->Title.'》';
                $content = '<table width="700" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; border:1px solid rgb(230,230,230);"><tbody><tr><td><table cellpadding="0" cellspacing="0" border="0"><tbody><tr><td width="30"></td><td width="640" style="padding:20px 0 10px;"><a href="'.$zbp->host.'" target="_blank" style="text-decoration:none; display:inline-block; vertical-align:top;">'.$logo.'</a></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table><tbody><tr><td width="30"></td><td width="640"><p style="margin:0; padding:30px 0 0px; font-size:14px; color:#151515; font-family:microsoft yahei; font-weight:bold; border-top:1px solid #eee;">管理员，你好！</p><p style="font-size:14px; color:#151515; font-family:microsoft yahei;">'.$zbp->user->StaticName.'在 [ '.$zbp->name.' ] 编辑了'.$staticname.'的文章<em style="font-weight:bold;font-style:normal; margin: 0 5px;">《'.$article->Title.'》</em>：</p></td><td width="30"></td></tr><tr><td width="30"></td><td width="640"><p style="margin:0 0 20px; padding:15px 20px; font-size:16px; color:#7d8795; font-family:microsoft yahei; line-height:22px; border:1px solid #e6e6e6; background-color:#f5f5f5;">'.$intro.'</p><p style="margin:0 0 30px; text-align:center;"><a href="'.$article->Url.'" target="_blank" style="margin:0 auto; padding:12px 25px; font-size:14px; color:#fff; font-family:microsoft yahei; font-weight:bold; text-decoration:none; text-transform:capitalize; border:0; border-radius:50px; cursor:pointer; box-shadow:0 1px 2px rgba(0, 0, 0, 0.1); background-color:#206ffd; background-image:linear-gradient(to top, #206dfd 0%, #2992ff 100%); display: inline-block;">查看文章的完整内容</a></p></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table align="center" cellspacing="0" class="dao-content-footer" style=" background-color:rgb(245,245,245); line-height: 28px; padding: 13px 23px; color: #7d8795; font-weight:500; border-top:1px solid #e6e6e6;" width="100%" bgcolor="#e6e6e6"><tbody><tr><td style="font-family:microsoft yahei; font-size:14px; vertical-align:top; text-align:center;" valign="top">'.$zbp->name.' - '.$zbp->subname.'</td></tr></tbody></table></td></tr></tbody></table>';
                tpure_SendEmail($mailto,$subject,$content);
            }
        }
    }
}

//新评论邮件通知
//挂接口：Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'tpure_CmtSendmail');
function tpure_CmtSendmail($cmt)
{
    global $zbp;
    $logid=$cmt->LogID;
    $log=new Post();
    $log->LoadinfoByID($logid);
    $log_author = $zbp->GetPostByID($logid)->Author;
    if($zbp->Config('tpure')->PostLOGOON){
        $logo = '<img src="'.$zbp->Config('tpure')->PostLOGO.'" style="height:40px;line-height:0;border:none;display:block;" />';
    }else{
        $logo = '<span style="font-size:22px; color:#666;">'.$zbp->name.'</span>';
    }
    if($zbp->Config('tpure')->PostCMTMAILSENDON){
        if($log_author->Email && $log_author->Email != 'null@null.com'){
            $subject = '日志《'.$log->Title.'》收到了新的评论';
            $content = '<table width="700" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; border:1px solid rgb(230,230,230);"><tbody><tr><td><table cellpadding="0" cellspacing="0" border="0"><tbody><tr><td width="30"></td><td width="640" style="padding:20px 0 10px;"><a href="'.$zbp->host.'" target="_blank" style="text-decoration:none; display:inline-block; vertical-align:top;">'.$logo.'</a></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table><tbody><tr><td width="30"></td><td width="640"><p style="margin:0; padding:30px 0 0px; font-size:14px; color:#151515; font-family:microsoft yahei; font-weight:bold; border-top:1px solid #eee;">'.$log_author->StaticName.'，您好！</p><p style="font-size:14px; color:#151515; font-family:microsoft yahei;">您在 [ '.$zbp->name.' ] 的文章<em style="font-weight:bold;font-style:normal; margin: 0 5px;">《'.$log->Title.'》</em>收到了新评论：</p></td><td width="30"></td></tr><tr><td width="30"></td><td width="640"><p style="margin:0 0 20px; padding:15px 20px; font-size:16px; color:#7d8795; font-family:microsoft yahei; line-height:22px; border:1px solid #e6e6e6; background-color:#f5f5f5;">'.$cmt->Content.'</p><p style="margin:0 0 30px; text-align:center;"><a href="'.$log->Url.'" target="_blank" style="margin:0 auto; padding:12px 25px; font-size:14px; color:#fff; font-family:microsoft yahei; font-weight:bold; text-decoration:none; text-transform:capitalize; border:0; border-radius:50px; cursor:pointer; box-shadow:0 1px 2px rgba(0, 0, 0, 0.1); background-color:#206ffd; background-image:linear-gradient(to top, #206dfd 0%, #2992ff 100%); display: inline-block;">查看评论的完整内容</a></p></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table align="center" cellspacing="0" class="dao-content-footer" style=" background-color:rgb(245,245,245); line-height: 28px; padding: 13px 23px; color: #7d8795; font-weight:500; border-top:1px solid #e6e6e6;" width="100%" bgcolor="#e6e6e6"><tbody><tr><td style="font-family:microsoft yahei; font-size:14px; vertical-align:top; text-align:center;" valign="top">'.$zbp->name.' - '.$zbp->subname.'</td></tr></tbody></table></td></tr></tbody></table>';
            tpure_SendEmail($log_author->Email,$subject,$content);
        }
    }
    if($zbp->Config('tpure')->PostREPLYMAILSENDON && $cmt->ParentID>0){
        $parentcmt = $zbp->GetCommentByID($cmt->ParentID);
        $mailto=$parentcmt->Email;
        if($mailto && $mailto != 'null@null.com'){
            $subject = "您在 【".$zbp->name."】 上的留言有回复啦！";
            $content='<table width="700" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; border:1px solid rgb(230,230,230);"><tbody><tr><td><table cellpadding="0" cellspacing="0" border="0"><tbody><tr><td width="30"></td><td width="640" style="padding:20px 0 10px;"><a href="'.$zbp->host.'" target="_blank" style="text-decoration:none; display:inline-block; vertical-align:top;">'.$logo.'</a></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table><tbody><tr><td width="30"></td><td width="640"><p style="margin:0; padding:30px 0 0px; font-size:14px; color:#151515; font-family:microsoft yahei; font-weight:bold; border-top:1px solid #eee;">'.$parentcmt->Name.'，您好！</p><p style="font-size:14px; color:#151515; font-family:microsoft yahei;">您在 [ '.$zbp->name.' ] 的文章<em style="font-weight:bold;font-style:normal; margin: 0 5px;">《'.$log->Title.'》</em>上发表评论：</p></td><td width="30"></td></tr><tr><td width="30"></td><td width="640"><p style="margin:0 0 20px; padding:15px 20px; font-size:16px; color:#7d8795; font-family:microsoft yahei; line-height:22px; border:1px solid #e6e6e6; background-color:#f5f5f5;">'.$parentcmt->Content.'</p><p style="font-size:14px; color:#151515; font-family:microsoft yahei;">用户<em style="font-weight:bold;font-style:normal; margin: 0 5px;">'.$cmt->Name.'</em>给您回复：</p><p style="margin:0 0 20px; padding:15px 20px; font-size:16px; color:#7d8795; font-family:microsoft yahei; line-height:22px; border:1px solid #e6e6e6; background-color:#f5f5f5;">'.$cmt->Content.'</p><p style="margin:0 0 30px; text-align:center;"><a href="'.$log->Url.'" target="_blank" style="margin:0 auto; padding:12px 25px; font-size:14px; color:#fff; font-family:microsoft yahei; font-weight:bold; text-decoration:none; text-transform:capitalize; border:0; border-radius:50px; cursor:pointer; box-shadow:0 1px 2px rgba(0, 0, 0, 0.1); background-color:#206ffd; background-image:linear-gradient(to top, #206dfd 0%, #2992ff 100%); display: inline-block;">查看回复的完整内容</a></p></td><td width="30"></td></tr></tbody></table></td></tr><tr><td><table align="center" cellspacing="0" class="dao-content-footer" style=" background-color:rgb(245,245,245); line-height: 28px; padding: 13px 23px; color: #7d8795; font-weight:500; border-top:1px solid #e6e6e6;" width="100%" bgcolor="#e6e6e6"><tbody><tr><td style="font-family:microsoft yahei; font-size:14px; vertical-align:top; text-align:center;" valign="top">'.$zbp->name.' - '.$zbp->subname.'</td></tr></tbody></table></td></tr></tbody></table>';
            tpure_SendEmail($mailto,$subject,$content);
        }
    }
}

//头像优先级
function tpure_MemberAvatar($member,$email=null)
{
    global $zbp;
    $avatar = '';
    //自定义头像优先级最高
    if($member->Metas->memberimg){
        $avatar = $member->Metas->memberimg;
    }elseif(isset($email)){
        preg_match_all('/((\d)*)@qq.com/', $email, $vai);
        if (empty($vai['1']['0'])) {
            if($zbp->CheckPlugin('Gravatar')){
                $avatar = str_replace("{%emailmd5%}",md5($email),$zbp->Config('Gravatar')->default_url);
            }else{
                $avatar = $member->Avatar;
            }
        }else{
            $avatar = 'https://q2.qlogo.cn/headimg_dl?dst_uin='.$vai['1']['0'].'&spec=100';
        }
    }elseif($member->Email && $member->Email != 'null@null.com'){
        preg_match_all('/((\d)*)@qq.com/', $member->Email, $vai);
        if (empty($vai['1']['0'])) {
            $avatar = $member->Avatar;
        }else{
            $avatar = 'https://q2.qlogo.cn/headimg_dl?dst_uin='.$vai['1']['0'].'&spec=100';
        }
    }elseif(is_file($zbp->usersdir . 'avatar/' . $member->ID . '.png')){
        $avatar = $zbp->host . 'zb_users/avatar/' . $member->ID . '.png';
    }else{
        $avatar = $zbp->host . 'zb_users/avatar/0.png';
    }
    return $avatar;
}

//判断移动端
function tpure_isMobile()
{
    global $zbp;
    if(isset($_GET['must_use_mobile']))
    {
        return true;
    }
    $is_mobile = false;
    $regex = '/android|adr|iphone|ipad|linux|windows\sphone|kindle|gt\-p|gt\-n|rim\stablet|opera|meego|Mobile|Silk|BlackBerry|opera\smini/i';
    if (preg_match($regex, GetVars('HTTP_USER_AGENT', 'SERVER')))
    {
        $is_mobile = true;
    }
    return $is_mobile;
}

//主题缩略图
function tpure_Thumb($Source, $IsThumb = '0')
{
    global $zbp;
    if (ZC_VERSION_COMMIT >= 2800 && $zbp->Config('tpure')->PostTHUMBNEWON == '1') {
        return tpure_Thumb_new($Source, $IsThumb);
    }
    $ThumbSrc = $temp = '';
    $randnum = mt_rand(1, 10);
    $pattern = "/<img[^>]+src=\"(?<url>[^\"]+)\"[^>]*>/";
    $content = $Source->Content;
    preg_match_all($pattern, $content, $matchContent);
    if ($zbp->Config('tpure')->PostIMGON == '1'){
        if (isset($Source->Metas->proimg)) {
            $temp = $Source->Metas->proimg;
        }elseif(isset($matchContent[1][0])){
            $temp = $matchContent[1][0];
        }else{
            if($zbp->Config('tpure')->PostTHUMBON == '1'){
                $temp = $zbp->Config('tpure')->PostTHUMB;
            }elseif($zbp->Config('tpure')->PostRANDTHUMBON == '1'){
                $temp = $zbp->host."zb_users/theme/".$zbp->theme."/include/thumb/".$randnum.".jpg";
            }elseif($IsThumb == '1'){
                $temp = $zbp->Config('tpure')->PostTHUMB;
            }else{
                $temp = '';
            }
        }
    }else{
        $temp = '';
    }
    $ThumbSrc = $temp;
    return $ThumbSrc;
}

//Z-Blog1.7版本缩略图
function tpure_Thumb_new($Source, $IsThumb)
{
    global $zbp;
    $ThumbSrc = '';
    $randnum = mt_rand(1, 10);
    if ($zbp->Config('tpure')->PostIMGON == '1'){
        if (isset($Source->Metas->proimg)) {
            $temp = $Source->Metas->proimg;
        }elseif($Source->ImageCount >= 1 && (count($thumbs = $Source->Thumbs(210, 147, 1)) > 0)){
            $temp = $thumbs[0];
        }else{
            if($zbp->Config('tpure')->PostTHUMBON == '1'){
                $temp = $zbp->Config('tpure')->PostTHUMB;
            }elseif($zbp->Config('tpure')->PostRANDTHUMBON == '1'){
                $temp = $zbp->host."zb_users/theme/".$zbp->theme."/include/thumb/".$randnum.".jpg";
            }elseif($IsThumb == '1'){
                $temp = $zbp->Config('tpure')->PostTHUMB;
            }else{
                $temp = '';
            }
        }
    }else{
        $temp = '';
    }
    $ThumbSrc = $temp;
    return $ThumbSrc;
}

//列表缩略图延迟加载
function tpure_ListIMGLazyLoad(&$template)
{
    global $zbp;
    $templateArr = explode(',', 'post-multi,post-istop,post-albummulti,post-albumistop,post-hotspotmulti,post-hotspotistop');
    $templateArr = array_unique($templateArr);
    foreach ($templateArr as $key => $value) {
        if (isset($template[$value])) {
            $template[$value] = tpure_LazyLoadReplace($template[$value]);
        }
    }
}

//匹配图片更新内容
function tpure_LazyLoadReplace($content)
{
    global $zbp;
    $placeIMG = $zbp->host . "zb_users/theme/tpure/style/images/lazyload.png";
    $pattern = "/<img([^>]+)src=[\"']([^\"']+)[\"']([^>]+)>/";
    $content = preg_replace($pattern, "<img$1src=\"{$placeIMG}\" data-original=\"$2\" class=\"lazyload\"$3>", $content);
    return $content;
}

//文章正文替换匹配后的图片延迟加载格式
function tpure_ContentIMGLazyLoad(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    $article->Content = tpure_LazyLoadReplace($article->Content);
}

function tpure_IP($ip)
{
    $ch = curl_init();
     $url = 'https://whois.pconline.com.cn/ipJson.jsp?ip='.$ip;
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     $location = curl_exec($ch);
     curl_close($ch);
     $location = mb_convert_encoding($location, 'utf-8','GB2312');
     $location = substr($location, strlen('({')+strpos($location, '({'),(strlen($location) - strpos($location, '})'))*(-1));
     $location = str_replace('"',"",str_replace(":","=",str_replace(",","&",$location)));
     parse_str($location,$ip_location);
     return $ip_location['pro'];
}

function tpure_Config()
{
    global $zbp;
    $array = array(
        'PostLOGO' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/logo.svg',
        'PostNIGHTLOGO' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/nightlogo.svg',
        'PostLOGOON' => '1',
        'PostLOGOHOVERON' => '1',
        'PostFAVICON' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/favicon.ico',
        'PostFAVICONON' => '1',
        'PostTHUMB' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/thumb.png',
        'PostTHUMBON' => '0',
        'PostRANDTHUMBON' => '0',
        'PostIMGON' => '1',
        'PostSIDEIMGON' => '1',
        'PostTHUMBNEWON' => '0',
        'PostBANNERON' => '1',
        'PostBANNER' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/banner.jpg',
        'PostBANNERDISPLAYON' => '1',
        'PostBANNERALLON' => '0',
        'PostBANNERFONT' => $zbp->subname,
        'PostBANNERPCHEIGHT' => '360',
        'PostBANNERMHEIGHT' => '200',
        'PostBANNERSEARCHWORDS' => '热搜词1|热搜词2|热搜词3',
        'PostBANNERSEARCHLABEL' => '大家都在搜：',
        'PostBANNERSEARCHON' => '1',
        'PostSEARCHON' => '1',
        'PostSCHTXT' => '搜索...',
        'PostVIEWALLON' => '1',
        'PostVIEWALLHEIGHT' => '1000',
        'PostVIEWALLSTYLE' => '1',
        'PostVIEWALLSINGLEON' => '1',
        'PostVIEWALLPAGEON' => '1',
        'PostLISTINFO' => '{"user":"1","date":"1","cate":"0","view":"1","cmt":"0","edit":"1","del":"1"}',
        'PostARTICLEINFO' => '{"user":"1","date":"1","cate":"1","view":"1","cmt":"0","edit":"1","del":"1"}',
        'PostPAGEINFO' => '{"user":"1","date":"0","view":"0","cmt":"0","edit":"1","del":"1"}',
        'PostSINGLEKEY' => '1',
        'PostPAGEKEY' => '1',
        'PostRELATEON' => '1',
        'PostRELATECATE' => '1',
        'PostRELATENUM' => '6',
        'PostRELATETITLE' => '1',
        'PostRELATESTYLE' => '0',
        'PostRELATEDIALLEL' => '1',
        'PostAJAXON' => '1',
        'PostLOADPAGENUM' => '3',
        'PostARTICLECMTON' => '1',
        'PostPAGECMTON' => '1',
        'PostCMTMAILON' => '1',
        'PostCMTSITEON' => '1',
        'PostCMTLOGINON' =>  '0',
        'PostCMTIPON' => '1',
        'VerifyCode' =>  'ABCDEFGHKMNPRSTUVWXYZ123456789',
        'PostINDEXSTYLE' =>  '0',
        'PostSEARCHSTYLE' =>  '0',
        'PostFILTERCATEGORY' => '',
        'PostISTOPSIMPLEON' => '0',
        'PostISTOPINDEXON' => '0',
        'PostGREYON' => '0',
        'PostGREYSTATE' => '0',
        'PostGREYDAY' => '',
        'PostSETNIGHTON' => '1',
        'PostSETNIGHTAUTOON' => '0',
        'PostSETNIGHTSTART' => '22',
        'PostSETNIGHTOVER' => '6',
        'PostTIMESTYLE' => '0',
        'PostTIMEFORMAT' => '0',
        'PostCOPYNOTICEON' => '1',
        'PostCOPYNOTICEMOBILEON' => '0',
        'PostCOPYURLON' => '1',
        'PostQRON' => '1',
        'PostQRSIZE' => '70',
        'PostCOPYNOTICE' => '<p>扫描二维码推送至手机访问。</p><p>版权声明：本文由<strong>'.$zbp->name.'</strong>发布，如需转载请注明出处。</p>',
        'PostSHAREARTICLEON' => '1',
        'PostSHAREPAGEON' => '1',
        'PostSHARE' => '<a href="#" class="share-icon icon-weibo">微博</a>
<a href="#" class="share-icon icon-qq">QQ</a>
<a href="#" class="share-icon icon-wechat">微信</a>
<a href="#" class="share-icon icon-douban">豆瓣</a>
<a href="#" class="share-icon icon-qzone">QQ空间</a>
<a href="#" class="share-icon icon-linkedin">领英</a>',
        'PostARCHIVEINFOON' => '1',
        'PostARCHIVEFOLDON' => '0',
        'PostAUTOARCHIVEON' => '0',
        'PostARCHIVEDATEON' => '1',
        'PostARCHIVEDATETYPE' => '0',
        'PostARCHIVEDATESORT' => 'DESC',
        'PostFRIENDLINKON' => '1',
        'PostFRIENDLINKMON' => '0',
        'PostERRORTOPAGE' => '',
        'PostCLOSESITEBG' => '',
        'PostCLOSESITEBGMASKON' => '1',
        'PostCLOSESITETITLE' => '网站维护中，暂时无法访问！',
        'PostCLOSESITECON' => '<p>感谢您一直以来对我们的支持与信赖。</p><p>为了提供更好的服务质量，我们正在进行网站的技术升级及系统维护工作。</p><p>期间将暂停访问。对您造成的不便我们深感歉意。</p><p>我们期待更完善的内容与您相遇，为您提供更优质的体验与服务。</p>',
        'PostSIGNON' => '1',
        'PostSIGNBTNTEXT' => '登录/注册',
        'PostSIGNBTNURL' => '{#ZC_BLOG_HOST#}zb_system/login.php',
        'PostSIGNUSERSTYLE' => '0',
        'PostSIGNUSERURL' => '{#ZC_BLOG_HOST#}zb_system/login.php',
        'PostSIGNUSERMENU' => '<a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=ArticleEdt" target="_blank">新建文章</a>
<a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=ArticleMng" target="_blank">文章管理</a>
<a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=CategoryMng" target="_blank">分类管理</a>
<a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=CommentMng" target="_blank">评论管理</a>
<a href="{#ZC_BLOG_HOST#}zb_system/cmd.php?act=ModuleMng" target="_blank">模块管理</a>
<a href="{#ZC_BLOG_HOST#}zb_users/theme/tpure/main.php?act=base" target="_blank">主题设置</a>',
        'PostSITEMAPON' => '1',
        'PostSITEMAPSTYLE' => '0',
        'PostSITEMAPTXT' => '首页',
        'PostZBAUDIOON' => '1',
        'PostVIDEOON' => '1',
        'PostMEDIAICONON' => '1',
        'PostMEDIAICONSTYLE' => '0',
        'PostREADERSNUM' => '100',
        'PostREADERSURLON' => '0',
        'PostINTROSOURCE' => '0',
        'PostINTRONUM' => '150',
        'PostBACKTOTOPON' => '1',
        'PostBACKTOTOPVALUE' => '500',
        'PostBLANKSTYLE' => '2',
        'PostLOGINON' => '1',
        'PostFILTERON' => '1',
        'PostMOREBTNON' => '0',
        'PostBIGPOSTIMGON' => '0',
        'PostFIXMENUON' => '1',
        'PostFANCYBOXON' => '1',
        'PostLAZYLOADON' => '1',
        'PostLAZYLINEON' => '0',
        'PostLAZYNUMON' => '1',
        'PostINDENTON' => '0',
        'PostTAGSON' => '1',
        'PostPREVNEXTON' => '1',
        'PostCATEPREVNEXTON' => '1',
        'PostTFONTSIZEON' => '1',
        'PostREMOVEPON' => '1',
        'PostSELECTON' => '0',
        'PostCHECKDPION' => '0',
        'PostLANGON' => '1',

        'SEOON' => '0',
        'SEODIVIDE' => ' - ',
        'SEOTITLE' => $zbp->name . ' - ' . $zbp->title,
        'SEOKEYWORDS' => '关键词1,关键词2,关键词3',
        'SEODESCRIPTION' => '此处为网站描述内容',
        'SEOCATALOGINFO' => '{"catalog":"1","title":"1","subtitle":"0"}',
        'SEOARTICLEINFO' => '{"article":"1","catalog":"1","title":"1","subtitle":"0"}',
        'SEOPAGEINFO' => '{"page":"1","title":"1","subtitle":"0"}',
        'SEOTAGINFO' => '{"tag":"1","title":"1","subtitle":"0"}',
        'SEOUSERINFO' => '{"user":"1","title":"1","subtitle":"0"}',
        'SEODATEINFO' => '{"date":"1","title":"1","subtitle":"0"}',
        'SEOSEARCHINFO' => '{"search":"1","title":"1","subtitle":"0"}',
        'SEOOTHERINFO' => '{"other":"1","title":"1","subtitle":"0"}',
        'SEOTITLENOCODEON' => '1',
        'SEORETITLEON' => '0',
        'SEODESCRIPTIONDATA' => '0',
        'SEODESCRIPTIONNUM' => '200',
        'PostHEADERCODE' => '',
        'PostFOOTERCODE' => '',
        'PostSINGLETOPCODE' => '',
        'PostSINGLEBTMCODE' => '',

        'PostCOLORON' => '0',
        'PostFONT' => 'Penrose, "PingFang SC", "Hiragino Sans GB", Tahoma, Arial, "Lantinghei SC", "Microsoft YaHei", "simsun", sans-serif',
        'PostCOLOR' => '0188fb',
        'PostSIDELAYOUT' => 'r',
        'PostBGCOLOR' => 'f1f1f1',
        'PostBGIMG' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/background.jpg',
        'PostBGIMGON' => '0',
        'PostBGIMGSTYLE' => '2',
        'PostHEADBGCOLOR' => 'ffffff',
        'PostFOOTBGCOLOR' => 'e4e8eb',
        'PostFOOTFONTCOLOR' => '999999',
        'PostCUSTOMCSS'  => '',

        'PostFIXSIDEBARON' => '1',
        'PostFIXSIDEBARSTYLE' => '0',
        'PostSIDEMOBLIEON' => '0',
        'PostSIDECMTDAY' => '365',
        'PostSIDEVIEWDAY' => '365',
        'PostSIDERECID' => '',
        'PostSIDEUSERBG' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/banner.jpg',
        'PostSIDEUSERIMG' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/sethead.png',
        'PostSIDEUSERNAME' => $zbp->name,
        'PostSIDEUSERINTRO' => $zbp->title,
        'PostSIDEUSERQQ' => '#',
        'PostSIDEUSERWECHAT' => '{#ZC_BLOG_HOST#}zb_users/theme/tpure/style/images/qr.png',
        'PostSIDEUSEREMAIL' => '#',
        'PostSIDEUSERWEIBO' => '#',
        'PostSIDEUSERGROUP' => '#',
        'PostSIDEUSERCOUNT' => '1',

        'PostSLIDEON' => '1',
        'PostSLIDEPLACE' => '0',
        'PostSLIDETITLEON' => '1',
        'PostSLIDETIME' => '2500',
        'PostSLIDEDISPLAY' => '1',
        'PostSLIDEPAGEON' => '1',
        'PostSLIDEPAGETYPE' => '1',
        'PostSLIDEBTNON' => '1',
        'PostSLIDEEFFECTON' => '0',
        'PostSLIDEDATA' => '[{"order":"1","img":"'.$zbp->host.'zb_users/theme/tpure/style/images/slide01.png","title":"幻灯标题","url":"'.$zbp->host.'","isused":"1","color":"ffffff"},{"order":"2","img":"'.$zbp->host.'zb_users/theme/tpure/style/images/slide02.png","title":"幻灯标题","url":"'.$zbp->host.'","isused":"1","color":"ffffff"}]',

        'PostMAILON' => '0',
        'SMTP_SSL' => '0',
        'SMTP_HOST' => 'smtp.163.com',
        'SMTP_PORT' => '25',
        'FROM_EMAIL' => '',
        'SMTP_PASS' => '',
        'FROM_NAME' => '',
        'MAIL_TO' => '',
        'PostNEWARTICLEMAILSENDON' => '0',
        'PostEDITARTICLEMAILSENDON' => '0',
        'PostCMTMAILSENDON' => '0',
        'PostREPLYMAILSENDON' => '0',

        'PostAJAXPOSTON' => '1',
        'PostSAVECONFIG' => '1',
    );
    foreach ($array as $value => $intro) {
        $zbp->Config('tpure')->$value = $intro;
    }
}

//主题启用时的默认配置项
function InstallPlugin_tpure()
{
    global $zbp;
    if (!$zbp->Config('tpure')->HasKey('Version')) {
        tpure_Config();
    }
    $zbp->Config('tpure')->Version = '5.0.2';
    $zbp->SaveConfig('tpure');
    tpure_CreateModule();
}

//应用升级时执行
function UpdatePlugin_tpure()
{
    global $zbp;
    $version = $zbp->Config('tpure')->Version;
    if($version !== '5.0.2'){
        $zbp->Config('tpure')->Version = '5.0.2';
        $zbp->SaveConfig('tpure');
    }
    if(!$zbp->Config('tpure')->Haskey("Version")){
        $zbp->Config('tpure')->Version = '5.0.2';
        $zbp->SaveConfig('tpure');
    }
}

//旧版兼容
function tpure_Updated()
{
    UpdatePlugin_tpure();
}

//卸载主题时判断是否删除已保存的配置信息
function UninstallPlugin_tpure()
{
    global $zbp;
    if ($zbp->Config('tpure')->PostSAVECONFIG == 0) {
        $zbp->DelConfig('tpure');
    }
    //删除主题在模块管理中创建的模块
    tpure_DelModule();
}