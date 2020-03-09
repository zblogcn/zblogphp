<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'searchstr.php';

RegisterPlugin("tpure", "ActivePlugin_tpure");
function ActivePlugin_tpure(){
    global $zbp;
    $zbp->LoadLanguage('theme', 'tpure');
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu','tpure_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_Admin_Header','tpure_Header');
    Add_Filter_Plugin('Filter_Plugin_Zbp_Load','tpure_Refresh');
    Add_Filter_Plugin('Filter_Plugin_Search_Begin','tpure_SearchMain');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Core','tpure_Exclude_Category');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response5','tpure_Edit_Response');
    if($zbp->Config('tpure')->SEOON == '1'){
        Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response','tpure_CategorySEO');
        Add_Filter_Plugin('Filter_Plugin_Tag_Edit_Response','tpure_TagSEO');
        Add_Filter_Plugin('Filter_Plugin_Edit_Response5','tpure_SingleSEO');
    }
}

function tpure_SubMenu($id){
    global $zbp;
    $arySubMenu = array(
        0 => array('基本设置', 'base', 'left', false),
        1 => array('SEO设置', 'seo', 'left', false),
        2 => array('色彩设置', 'color', 'left', false),
    );
    foreach($arySubMenu as $k => $v){
        echo '<li><a href="?act='.$v[1].'" '.($v[3]==true?'target="_blank"':'').' class="'.($id==$v[1]?'on':'').'">'.$v[0].'</a></li>';
    }
}

function tpure_AddMenu(&$m){
    global $zbp;
    $m[]=MakeTopMenu("root",'主题设置',$zbp->host . "zb_users/theme/tpure/main.php?act=base","","topmenu_tpure");
}

function tpure_Header(){
    global $bloghost;
    echo '<style>.header{background:url('.$bloghost.'zb_users/theme/tpure/style/images/banner.jpg) no-repeat center center;background-size:cover;}</style>';
}

function tpure_Refresh(){
    global $zbp;
    if ($zbp->option['ZC_DEBUG_MODE']) {
        $zbp->BuildTemplate();
    }
}

function tpure_SearchMain() {
    global $zbp;
    foreach ($GLOBALS['Filter_Plugin_ViewSearch_Begin'] as $fpname => &$fpsignal){
        $fpreturn = $fpname();
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN){
            $fpsignal=PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }
    if(!$zbp->CheckRights($GLOBALS['action'])){Redirect('./');}
    $q = trim(htmlspecialchars(GetVars('q','GET')));
    $qc = '<span class=\'schwords\'>' . $q . '</span>';
    $articles = array();
    $category = new Metas;
    $author = new Metas;
    $tag = new Metas;
    $type = 'tpure-search';
    $zbp->title = '搜索 [ ' . $q . ' ] ';
    $template = $zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
    if($zbp->template->hasTemplate('search')){
        $template = 'search';
    }
    $w=array();
    if(!$zbp->Config('tpure')->PostGLOBALSEARCHON == '1'){
        $w[]=array('=','log_Type','0');
    }
    if($q){
        $w[]=array('search','log_Content','log_Intro','log_Title',$q);
    }else{
        Redirect('./');
    }
    if(!($zbp->CheckRights('ArticleAll')&&$zbp->CheckRights('PageAll'))){
        $w[]=array('=','log_Status',0);
    }

    if($zbp->Config('tpure')->PostGLOBALSEARCHON == '1'){
        $articles = $zbp->GetPostList(
            '*',
            $w,
            array('log_PostTime' => 'DESC'),
            array($zbp->searchcount),
            null
        );
    }else{
        $articles = $zbp->GetArticleList(
            '*',
            $w,
            array('log_PostTime' => 'DESC'),
            array($zbp->searchcount),
            null
        );
    }

    foreach($articles as $article){
        $intro = preg_replace('/[\r\n\s]+/', '', trim(tpure_SubStrStartUTF8(TransferHTML($article->Content,'[nohtml]'),$q,$zbp->Config('tpure')->PostINTRONUM)) . '...');
        $article->Intro = str_ireplace($q,$qc,$intro);
        $article->Title = str_ireplace($q,$qc,$article->Title);
    }

    $zbp->header .= '<meta name="robots" content="noindex,follow" />' . "\r\n";
    $zbp->template->SetTags('title', $zbp->title);
    $zbp->template->SetTags('articles',$articles);
    $zbp->template->SetTags('type',$type);
    $zbp->template->SetTags('page',1);
    $zbp->template->SetTags('pagebar',null);
    if($zbp->template->hasTemplate('search')){
        $zbp->template->SetTemplate($template);
    } else {
        $zbp->template->SetTemplate('catalog');
    }
    foreach ($GLOBALS['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
        $fpreturn=$fpname($zbp->template);
    }
    $zbp->template->Display();
    RunTime();
    die();
}

function tpure_Exclude_Category(&$type,&$page,&$category,&$author,&$datetime,&$tag,&$w,&$pagebar){
    global $zbp;    
    if($type == 'index' && !isset($zbp->Config('tpure')->PostFILTERCATEGORY)){
        $w[]=array('NOT IN' ,'log_CateID' ,$zbp->Config('tpure')->PostFILTERCATEGORY);
        //以下是为了重建分页，过滤了分类，数量会发生变化
        $pagebar = new Pagebar($zbp->option['ZC_INDEX_REGEX']);
        $pagebar->PageCount = $zbp->displaycount;
        $pagebar->PageNow = $page;
        $pagebar->PageBarCount = $zbp->pagebarcount;
    }   
}

function tpure_Exclude_CategorySelect($default) {
    global $zbp;
    foreach ($GLOBALS['hooks']['Filter_Plugin_OutputOptionItemsOfCategories'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($default);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN){
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

function tpure_TimeAgo($ptime){
    global $zbp;
    if($zbp->Config('tpure')->PostTIMEAGOON == '1'){
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if($etime < 1) return '刚刚';
        $interval = array(
            12 * 30 * 24 * 60 * 60  =>  '年前<time class="datetime"> ('.date('Y-m-d', $ptime).')</time>',
            30 * 24 * 60 * 60   =>  '个月前<time class="datetime"> ('.date('m-d', $ptime).')</time>',
            7 * 24 * 60 * 60        =>  '周前<time class="datetime"> ('.date('m-d', $ptime).')</time>',
            24 * 60 * 60        =>  '天前',
            60 * 60         =>  '小时前',
            60          =>  '分钟前',
            1           =>  '秒前'
        );
        foreach ($interval as $secs => $str){
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        };
    }else{
        $ptime = strtotime($ptime);
        $etime = date('Y-m-d', $ptime)/* .' <time class="datetime">'. date('H:i:s', $ptime).'</time>'*/;
        return $etime;
    }
}

function tpure_color(){
    global $zbp;
    $skin = '';
    $color = $zbp->Config('tpure')->PostCOLOR;
    $skin .= "a, a:hover,.menu li a:hover,.menu li.on a,.menu li .subnav a:hover:after,.menu li .subnav a.on,.menu li.subcate:hover a,.menu li.subcate:hover .subnav a:hover,.menu li.subcate:hover .subnav a.on,.menu li.subcate:hover .subnav a.on:after,.sch-m input,.sch-m button:after,.schfixed input,.schclose,.schform input,.schform button:after,.post h2 a:hover,.post h2 .istop:before,.post .user a:hover,.post .date a:hover,.post .cate a:hover,.post .views a:hover,.post .cmtnum a:hover,.post .readmore:hover,.post .readmore:hover:after,.post .tags a:hover,.pages a:hover,a.backlist:hover,.cmtsfoot .reply:hover,.cmtsfoot .reply:hover:before,.cmtsubmit button:hover,.cmtsubmit button:hover:before,.sidebox dd a:hover,#divTags ul li a:hover,#divCalendar td a,#divCalendar #today,#divContorPanel .cp-login a:hover,#divContorPanel .cp-vrs a:hover,#divContorPanel .cp-login a:hover:before,#divContorPanel .cp-vrs a:hover:before,.footer a:hover,.goback:hover,.goback:hover:after,.relateinfo h3 a:hover { color:#{$color}; }@media screen and (max-width:1080px){.menu ul li.subcate.slidedown > a:after {color:#{$color}}}";//color
    $skin .= ".menu li:before,.schfixed button,.pagebar .now-page,.cmtpagebar .now-page,.pagebar a:hover,.cmtpagebar a:hover,a.backtotop {background:#{$color}}";//background
    $skin .= ".menuico span {background-color:#{$color}}";//background-color
    $skin .= ".menu li .subnav,.schfixed {border-top-color:#{$color}}";//border-top-color
    $skin .= ".menu li.subcate .subnav a {color:#333}";
    $skin .= ".menu li .subnav:before,.sch-m input,.schfixed:before,.schform input,.single h1:after,.single h2:after,.single h3:after,.single h4:after,.single h5:after,.single h6:after,.contitle h1,.contitle h2 {border-bottom-color:#{$color}}";//border-bottom-color
    $skin .= ".post .readmore:hover,.post .tags a:hover,.pagebar .now-page,.cmtpagebar .now-page,.pagebar a:hover,.cmtpagebar a:hover,a.backlist:hover,.cmtsubmit button:hover,#divTags ul li a:hover,#divCalendar td a,#divContorPanel .cp-login a:hover,#divContorPanel .cp-vrs a:hover,.goback:hover {border-color:#{$color}}";//border-color
    $bgcolor = $zbp->Config('tpure')->PostBGCOLOR;
    $skin .= ".wrapper { background:#{$bgcolor}; }";
    $sidelayout = $zbp->Config('tpure')->PostSIDELAYOUT;
    if($sidelayout == 'l'){
        $skin .=".sidebar { float:left; } .content { float:right; }@media screen and (max-width:1080px){.content { float:none; margin:0; }}";
    }else{
        $skin .="";
    }
    $customcss = $zbp->Config('tpure')->PostCUSTOMCSS;
        $skin .= "{$customcss}";
    return $skin;
}

function tpure_Edit_Response(){
    global $zbp,$article;
    tpure_CustomMeta_Response($article);
}

function tpure_CustomMeta_Response(&$object){
    global $zbp;
    ?>
    <link rel="stylesheet" href="<?php echo $zbp->host;?>zb_users/theme/tpure/script/admin.css">
    <script src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/custom.js" type="text/javascript"></script>
    <?php
    $array=array('proimg');
    $proimg_intro = '自定义缩略图';
    if(is_array($array)==false)return null;
    if(count($array)==0)return null;
    foreach ($array as $key => $value) {
        $single_meta_intro = $proimg_intro;
        echo '<p><label for="'. $value .'">'. $single_meta_intro .'（列表缩略图片，未设置则调用文章首图）</label><span><input type="text" name="meta_' . $value . '" placeholder="请点击上传按钮选择图片或手动输入图片地址..." value="'.htmlspecialchars($object->Metas->$value).'" class="metasrc"/></span><span><input type="button" class="uploadimg button" value="上传图片" /></span></p>';
    }
}

function tpure_CategorySEO(){
    global $zbp,$cate;
    ?>
    <link rel="stylesheet" href="<?php echo $zbp->host;?>zb_users/theme/tpure/script/admin.css">
    <script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/custom.js"></script>
    <script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/jscolor.js"></script>
    <?php
    if ($zbp->CheckPlugin('UEditor')) {
        echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.config.php"></script>';
        echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    }
    $array=array('catetitle','catekeywords','catedescription');
    $catetitle_intro = '分类SEO标题';
    $catekeywords_intro = '分类SEO关键词';
    $catedescription_intro = '分类SEO描述';
    if(is_array($array)==false)return null;
    if(count($array)==0)return null;
    foreach ($array as $key => $value){
        if($key==0){
            $cate_meta_intro = $catetitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ 分类列表SEO设置 +++++</div><p><span class="title">'. $cate_meta_intro .'</span><br /><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($cate->Metas->$value).'" class="metasrc"/></p>';
        }elseif($key==1){
            $cate_meta_intro = $catekeywords_intro;
            echo '<p><span class="title">'. $cate_meta_intro .'</span><br /><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($cate->Metas->$value).'" class="metasrc"/></p>';
        }else{
            $cate_meta_intro = $catedescription_intro;
            echo '<p><span class="title">'. $cate_meta_intro .'</span><br /><textarea cols="3" rows="6" id="edtIntro" name="meta_' . $value . '" class="metaintro">'.htmlspecialchars($cate->Metas->$value).'</textarea></p></div>';
        }
    }
}

function tpure_TagSEO(){
    global $zbp,$tag;
    ?>
    <link rel="stylesheet" href="<?php echo $zbp->host;?>zb_users/theme/tpure/script/admin.css">
    <script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/custom.js"></script>
    <script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/theme/tpure/script/jscolor.js"></script>
    <?php
    if ($zbp->CheckPlugin('UEditor')) {
        echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.config.php"></script>';
        echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
    }
    $array=array('tagtitle','tagkeywords','tagdescription');
    $tagtitle_intro = '标签SEO标题';
    $tagkeywords_intro = '标签SEO关键词';
    $tagdescription_intro = '标签SEO描述';
    if(is_array($array)==false)return null;
    if(count($array)==0)return null;
    foreach ($array as $key => $value){
        if($key==0){
            $tag_meta_intro = $tagtitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ TAGS列表SEO设置 +++++</div><p><span class="title">'. $tag_meta_intro .'</span><br /><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($tag->Metas->$value).'" class="metasrc"/></p>';
        }elseif($key==1){
            $tag_meta_intro = $tagkeywords_intro;
            echo '<p><span class="title">'. $tag_meta_intro .'</span><br /><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($tag->Metas->$value).'" class="metasrc"/></p>';
        }else{
            $tag_meta_intro = $tagdescription_intro;
            echo '<p><span class="title">'. $tag_meta_intro .'</span><br /><textarea cols="3" rows="6" id="edtIntro" name="meta_' . $value . '" class="metaintro">'.htmlspecialchars($tag->Metas->$value).'</textarea></p></div>';
        }
    }
}

function tpure_SingleSEO(){
    global $zbp,$article;
    $array=array('singletitle','singlekeywords','singledescription');
    $singletitle_intro = 'SEO标题';
    $singlekeywords_intro = 'SEO关键词';
    $singledescription_intro = 'SEO描述';
    if(is_array($array)==false)return null;
    if(count($array)==0)return null;
    foreach ($array as $key => $value){
        if($key==0){
            $single_meta_intro = $singletitle_intro;
            echo '<div class="introbox"><div class="togglelabel">+++++ 文章页面SEO设置 +++++</div><p><label>'. $single_meta_intro .'</label><input type="text" name="meta_' . $value . '" placeholder="请输入'. $single_meta_intro .'..." value="'.htmlspecialchars($article->Metas->$value).'" class="metasrc"/></p>';
        }elseif($key==1){
            $single_meta_intro = $singlekeywords_intro;
            echo '<p><label>'. $single_meta_intro .'</label><input type="text" name="meta_' . $value . '" placeholder="请输入'. $single_meta_intro .'..." value="'.htmlspecialchars($article->Metas->$value).'" class="metasrc"/></p>';
        }else{
            $single_meta_intro = $singledescription_intro;
            echo '<p><span class="title">'. $single_meta_intro .'</span><br /><textarea cols="3" rows="6" name="meta_' . $value . '" placeholder="请输入'. $single_meta_intro .'..." class="metaintro">'.htmlspecialchars($article->Metas->$value).'</textarea></p></div>';
        }
    }
}

function tpure_isMobile(){
    global $zbp;
    if (isset($_GET['must_use_mobile'])) {
        return true;
    }
    $is_mobile = false;
    $regex = '/android|adr|iphone|ipad|linux|windows\sphone|kindle|gt\-p|gt\-n|rim\stablet|opera|meego|Mobile|Silk|BlackBerry|Opera\Mini/i';
    if (preg_match($regex, GetVars('HTTP_USER_AGENT', 'SERVER'))) $is_mobile = true;
    return $is_mobile;
}

function tpure_Thumb($Source,$IsThumb='0'){
    global $zbp;
    $ThumbSrc = '';
    $randnum = mt_rand(1,20);
    $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/i";
    $content = $Source->Content;
    preg_match_all($pattern,$content,$matchContent);
    if($zbp->Config('tpure')->PostIMGON == '1'){
    if(isset($Source->Metas->proimg)){
        $temp = $Source->Metas->proimg;
    }elseif(isset($matchContent[1][0])){
        $temp = $matchContent[1][0];
    }else{
        if($zbp->Config('tpure')->PostTHUMBON == '1'){
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

function InstallPlugin_tpure() {
    global $zbp;
    if(!$zbp->Config('tpure')->HasKey('Version')){
        $array = array(
            'PostLOGO' => $zbp->host.'zb_users/theme/tpure/style/images/logo.png',
            'PostLOGOON' => '0',
            'PostFAVICON' => $zbp->host.'zb_users/theme/tpure/style/images/favicon.ico',
            'PostFAVICONON' => '1',
            'PostTHUMB' => $zbp->host.'zb_users/theme/tpure/style/images/thumb.png',
            'PostTHUMBON' => '0',
            'PostBANNER' => $zbp->host.'zb_users/theme/tpure/style/images/banner.jpg',
            'PostIMGON' => '1',
            'PostSEARCHON' => '1',
            'PostSCHTXT' => '搜索...',
            'PostVIEWALLON' => '1',
            'PostVIEWALLHEIGHT' => '1000',
            'PostVIEWALLSTYLE' => '1',
            'PostLISTINFO' => '[{"l_user":"1","l_date":"1","l_cate":"0","l_view":"1","l_cmt":"0"}]',
            'PostARTICLEINFO' => '[{"a_user":"1","a_date":"1","a_cate":"0","a_view":"1","a_cmt":"0"}]',
            'PostPAGEINFO' => '[{"p_user":"1","p_date":"1","p_view":"0","p_cmt":"0"}]',
            'PostSINGLEKEY' => '1',
            'PostPAGEKEY' => '1',
            'PostRELATEON' => '1',
            'PostRELATENUM' => '6',
            'PostINTRONUM' => '100',
            'PostFILTERCATEGORY' => '',
            'PostSHARE' => '<div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a></div><script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>',
            'PostLOADPAGENUM' => '5',
            'PostAJAXON' => '1',
            'PostMOREBTNON' => '1',
            'PostARTICLECMTON' => '1',
            'PostPAGECMTON' => '1',
            'PostFIXMENUON' => '1',
            'PostLOGOHOVERON' => '1',
            'PostBANNERDISPLAYON' => '1',
            'PostBLANKON' => '0',
            'PostGREYON' => '0',
            'PostLAZYLOADON' => '0',
            'PostREMOVEPON' => '1',
            'PostSELECTON' => '0',
            'PostGLOBALSEARCHON' => '0',
            'PostTIMEAGOON' => '1',
            'PostBACKTOTOPON' => '1',
            'PostSAVECONFIG' => '1',

            'PostCOLORON' => '0',
            'PostCOLOR' => '0188fb',
            'PostBGCOLOR' => 'f6f8f9',
            'PostSIDELAYOUT' => 'r',
            'PostCUSTOMCSS' => '',

            'SEOON' => '0',
            'SEOTITLE' => $zbp->name.' - '.$zbp->title,
            'SEOKEYWORDS' => '关键词1,关键词2,关键词3',
            'SEODESCRIPTION' => '此处为网站描述内容',
        );
        foreach($array as $value => $intro){
            $zbp->Config('tpure')->$value = $intro;
        }
        $zbp->SaveConfig('tpure');
    }
}

function UninstallPlugin_tpure() {
    global $zbp;
    if($zbp->Config('tpure')->PostSAVECONFIG==0){
        $zbp->DelConfig('tpure');
    }
}