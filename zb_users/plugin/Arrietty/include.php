<?php
#注册插件
RegisterPlugin("Arrietty", "ActivePlugin_Arrietty");

defined('ZC_POST_STATUS_PRIVATE') || define('ZC_POST_STATUS_PRIVATE', 4);
defined('ZC_POST_STATUS_PASSWORD') || define('ZC_POST_STATUS_PASSWORD', 8);

#借物少女的工具箱
function ActivePlugin_Arrietty() {
    global $zbp;
    if (ExistsPluginFilter('Filter_Plugin_OutputOptionItemsOfPostStatus')) {
        Add_Filter_Plugin('Filter_Plugin_OutputOptionItemsOfPostStatus', 'Arrietty_OutputOptionItemsOfPostStatus');
    } else {
        Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'Arrietty_Edit_Response3');
    }
    Add_Filter_Plugin('Filter_Plugin_ViewList_Core', 'Arrietty_ViewList_Core');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Core', 'Arrietty_ViewPost_Core');
    Add_Filter_Plugin('Filter_Plugin_ViewList_Template', 'Arrietty_ViewList_Template');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'Arrietty_ViewPost_Template');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Core', 'Arrietty_PostArticle_Core');
    Add_Filter_Plugin('Filter_Plugin_PostPage_Core', 'Arrietty_PostArticle_Core');
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'Arrietty_Html_Js_Add');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Ajax', 'Arrietty_Cmd_Ajax');
    if (!isset($zbp->lang['post_status_name']['4'])) {
        $zbp->lang['post_status_name']['4'] = '私人';
    }
    if (!isset($zbp->lang['post_status_name']['8'])) {
        $zbp->lang['post_status_name']['8'] = '加密';
    }
}

function Arrietty_OutputOptionItemsOfPostStatus($default, &$tz){
    global $zbp;
    if (!$zbp->CheckRights('ArticlePub') && $default == 2) {
        return ;
    } elseif (!$zbp->CheckRights('ArticleAll') && $default == 2) {
        return ;
    }
    $tz[4] = $zbp->lang['post_status_name']['4'];
    $tz[8] = $zbp->lang['post_status_name']['8'];
}

function Arrietty_Edit_Response3(){
    global $zbp;
    global $article;
    if (!$zbp->CheckRights('ArticlePub') && $default == 2) {
        return ;
    } elseif (!$zbp->CheckRights('ArticleAll') && $default == 2) {
        return ;
    }
    $script=<<<script
<script>
var selDom = $("#cmbPostStatus");
selDom.append("<option value='4'>{$zbp->lang['post_status_name']['4']}</option>");
selDom.append("<option value='8'>{$zbp->lang['post_status_name']['8']}</option>");
selDom.val("{$article->Status}");
</script>
script;
    echo $script;
}

function Arrietty_ViewList_Core($type, $page, $category, $author, $datetime, $tag, &$w, $pagebar, $template){
    global $zbp;
    if ($zbp->user->ID != null) {
        foreach ($w as $key => $value) {
            if(count($value) == 3 && $value[1] == 'log_Status'){
                if ($zbp->version < 162300) {
                    $w[$key] = "log_Status = '0' OR log_Status = '8' OR (log_Status = '4' AND log_AuthorID = '{$zbp->user->ID}')";
                } else {
                    $w[$key] = array('OR',array('=','log_Status',0), array('=','log_Status',8), array('log_Status = 4 AND log_AuthorID = \'' . $zbp->user->ID . '\''));
                }
            }
        }
    } else {
        foreach ($w as $key => $value) {
            if(count($value) == 3 && $value[1]=='log_Status'){
                $w[$key] = array('OR',array('=','log_Status',0), array('=','log_Status',8));
            }
        }
    }
}

function Arrietty_ViewPost_Core($select, &$w, $order, $limit, $option){
    global $zbp;
    Arrietty_ViewList_Core(null, null, null, null, null, null, $w, null, null);
}


function Arrietty_ViewList_Template(&$template){
    global $zbp;
    $articles = $zbp->template->GetTags('articles');
    foreach ($articles as $key => $value) {
        if ($value->Status == ZC_POST_STATUS_PRIVATE && $zbp->user->ID != $value->AuthorID) {
            $value->Intro = $zbp->lang['error']['9'];
            $value->Content = $zbp->lang['error']['9'];
        }
        if ($value->Status == ZC_POST_STATUS_PRIVATE) {
            //$value->Title = '' . $value->Title;
        }
        if ($value->Status == ZC_POST_STATUS_PASSWORD) {
            Arrietty_GetLocked($value);
        }
    }
}


function Arrietty_ViewPost_Template(&$template){
    global $zbp;
    $value = $zbp->template->GetTags('article');
    if ($value->Status == ZC_POST_STATUS_PRIVATE && $zbp->user->ID != $value->AuthorID) {
        $value->Intro = $zbp->lang['error']['9'];
        $value->Content = $zbp->lang['error']['9'];
    }
    if ($value->Status == ZC_POST_STATUS_PASSWORD) {
        Arrietty_GetLocked($value);
    }
}


function Arrietty_GetLocked(&$article){
    global $zbp;
    $vi = $article->Intro;
    //if($article->Type == ZC_POST_TYPE_PAGE){
        //$vi = '';
    //}
    $vc = $article->Content;

    $article->Intro = '';
    if ($zbp->user->ID == $article->AuthorID || $zbp->CheckRights('ArticleAll')) {
        $article->Intro .= '<div class="arrietty-setpassword"><input id="arrietty_password_'.$article->ID.'" type="text" value="'.$article->Metas->Arrietty_PW.'" />&nbsp;<button onclick="arrietty_getrandpw('.$article->ID.')">生成随机分享码</button>&nbsp;<button onclick="arrietty_setpassword('.$article->ID.')">保存分享码</button></div>';
    } else {
        $article->Intro .= '<div class="arrietty-password"><input type="text"  id="arrietty_password_'.$article->ID.'" />&nbsp;<button onclick="arrietty_submitpw('.$article->ID.')">输入分享码</button></div>';
    }
    if ($zbp->user->ID == $article->AuthorID || $zbp->CheckRights('ArticleAll')) {
        $article->Content = $article->Intro . $vc;
    }else{
        $article->Content = $article->Intro . '<div class="arrietty-intro">' . $vi . '</div>';
    }
    $article->Intro .= '<div class="arrietty-intro">' . $vi . '</div>';

    if (GetVars('arrietty_post_' . $article->ID, 'COOKIE') == $article->Metas->Arrietty_PW) {
        $article->Content = $vc; 
        $article->Intro = $vi;
    }
}

function Arrietty_GetRandPW(){
    $s = 'ABCDEFGHJKLMNPQRSTUVWXYZ1234567890abcdefghijkmnopqrstuvwxyz';
    $t = '';
    for ($i = 0; $i < 6 ; $i++) { 
        $j = rand(0, strlen($s) - 1);
        $t .= substr($s, $j, 1);
    }
    return $t;
}

function Arrietty_PostArticle_Core(&$article){
    if ($article->Status == ZC_POST_STATUS_PASSWORD) {
        if ($article->Metas->Arrietty_PW == null){
            $article->Metas->Arrietty_PW = Arrietty_GetRandPW();
        }
    }
}

function Arrietty_Cmd_Ajax($src){
    global $zbp;
    if($src != 'Arrietty')return ;
    $subact = GetVars('subact', 'GET');
    $postid = GetVars('postid', 'GET');
    $pw = GetVars('password', 'GET');
    if( $subact == 'getrandpw' && $zbp->user->ID != null){
        echo Arrietty_GetRandPW();
        return;
    }
    if( $subact == 'setpassword' && $zbp->user->ID != null ){
        $article = $zbp->GetPostByID($postid);
        if ($article->ID > 0) {
            if ($zbp->user->ID == $article->AuthorID || $zbp->CheckRights('ArticleAll')) {
                $pw = str_replace(array(' ','　'), '', trim($pw));
                $article->Metas->Arrietty_PW = $pw;
                $article->Save();
                echo $zbp->langs->msg->operation_succeed;
                return;
            }
        }
    }
    if( $subact == 'submitpw' ){
        $article = $zbp->GetPostByID((int) $postid);
        if ($article->ID > 0) {
            if (trim($pw) === $article->Metas->Arrietty_PW) {
                setcookie("arrietty_post_" . $article->ID, $article->Metas->Arrietty_PW, 0, $zbp->cookiespath);
                echo json_encode(array('err'=>0, 'content'=>$article->Url));
                return;
            }
        }
        echo json_encode(array('err'=>1, 'content'=>'分享码不正确'));
        return;
    }
}

function Arrietty_Html_Js_Add(){
    $script=<<<script
function arrietty_getrandpw(id){

$.get(ajaxurl + "Arrietty", { subact: "getrandpw", postid: id  },
  function(data){
    $("#arrietty_password_" + id).val(data);
  });

}

function arrietty_setpassword(id){

var pw = $("#arrietty_password_" + id).val();
$.get(ajaxurl + "Arrietty", { subact: "setpassword", postid: id, password: pw },
  function(data){
    alert(data);
  });
}

function arrietty_submitpw(id){

var pw = $("#arrietty_password_" + id).val();
$.get(ajaxurl + "Arrietty", { subact: "submitpw", postid: id, password: pw },
  function(data){
    var data = $.parseJSON(data);
    if(data.err == 0){
      location.href = data.content;
    }else{
      alert(data.content);
    }
  });
}
script;
    echo $script;
}