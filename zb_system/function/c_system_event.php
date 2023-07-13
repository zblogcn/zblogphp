<?php

/**
 * 事件操作相关函数.
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

//###############################################################################################################

/**
 * 验证登录.
 *
 * @param bool $throwException
 *
 * @throws Exception
 *
 * @return bool
 */
function VerifyLogin($throwException = true, $ignoreValidCode = true, $ignoreCsrfCheck = true)
{
    global $zbp;

    if ($zbp->option['ZC_LOGIN_CSRFCHECK_ENABLE'] && $ignoreCsrfCheck == false) {
        $zbp->csrfExpirationMinute = 5;
        if ($zbp->VerifyCSRFToken(GetVars('csrfToken' , 'POST'), 'login', 'minute') == false) {
            $zbp->ShowError(5, __FILE__, __LINE__);
        }
    }

    if ($zbp->option['ZC_LOGIN_VERIFY_ENABLE'] && $ignoreValidCode == false) {
        $zbp->verifyCodeExpirationMinute = 5;
        if ($zbp->CheckValidCode(GetVars('verify' , 'POST'), 'login', 'minute') == false) {
            $zbp->ShowError(38, __FILE__, __LINE__);
        }
    }

    /* @var Member $m */
    $m = null;
    if ($zbp->Verify_MD5(trim(GetVars('username', 'POST')), trim(GetVars('password', 'POST')), $m)) {
        $zbp->user = $m;
        $zbp->islogin = true;
        $sd = (float) GetVars('savedate');
        $sd = ($sd < 1) ? 1 : $sd; // must >= 1 day
        $sdt = (time() + 3600 * 24 * $sd);
        SetLoginCookie($m, (int) $sdt);

        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }

        return true;
    } else {
        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Failed'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname();
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        if ($throwException) {
            $zbp->ShowError(8, __FILE__, __LINE__);
        }
    }

    return false;
}

/**
 * 设置登录Cookie，直接登录该用户.
 *
 * @param Member $user
 * @param int    $cookieTime
 *
 * @return bool
 */
function SetLoginCookie($user, $cookieTime)
{
    global $zbp;
    $addinfo = array();
    $addinfo['chkadmin'] = (int) $zbp->CheckRights('admin');
    $addinfo['chkarticle'] = (int) $zbp->CheckRights('ArticleEdt');
    $addinfo['levelname'] = $user->LevelName;
    $addinfo['userid'] = $user->ID;
    $addinfo['useralias'] = $user->StaticName;
    $token = $zbp->GenerateUserToken($user, $cookieTime);
    $secure = HTTP_SCHEME == 'https://';
    setcookie('username_' . hash("crc32b", $zbp->guid), $user->Name, $cookieTime, $zbp->cookiespath, $zbp->cookie_domain, $secure, $zbp->cookie_httponly);
    setcookie('token_' . hash("crc32b", $zbp->guid), $token, $cookieTime, $zbp->cookiespath, $zbp->cookie_domain, $secure, $zbp->cookie_httponly);
    setcookie('addinfo' . str_replace('/', '', $zbp->cookiespath), json_encode($addinfo), $cookieTime, $zbp->cookiespath, $zbp->cookie_domain, $secure, false);

    return true;
}

/**
 * 注销登录.
 */
function Logout()
{
    global $zbp;

    setcookie('username_' . hash("crc32b", $zbp->guid), '', (time() - 3600), $zbp->cookiespath);
    setcookie('token_' . hash("crc32b", $zbp->guid), '', (time() - 3600), $zbp->cookiespath);
    setcookie('password', '', (time() - 3600), $zbp->cookiespath);
    setcookie("addinfo" . str_replace('/', '', $zbp->cookiespath), '', (time() - 3600), $zbp->cookiespath);

    foreach ($GLOBALS['hooks']['Filter_Plugin_Logout_Succeed'] as $fpname => &$fpsignal) {
        $fpname();
    }
}

//###############################################################################################################

function Redirect_to_search()
{
    Redirect_cmd_to_search(0);
}

function Redirect_cmd_to_search($post_type = 0)
{
    global $zbp, $action;
    //$q = rawurlencode(trim(strip_tags(GetVars('q', 'POST'))));
    //Redirect302($zbp->searchurl . '?q=' . $q);

    $route = $zbp->GetPostType_Sub($post_type, 'routes', 'post_article_search');
    if (!empty($route)) {
        $r = new UrlRule($zbp->GetRoute($route));
    } else {
        $urlrule = $zbp->GetPostType($post_type, 'search_urlrule');
        $r = new UrlRule($urlrule);
    }

    $q = rawurlencode(trim(strip_tags(GetVars('q', 'POST'))));
    $r->Rules['{%page%}'] = '';
    $r->Rules['{%q%}'] = $q;
    $r->Rules['{%search%}'] = $q;
    $r->Rules['{%posttype%}'] = $post_type;

    $url = $r->Make();

    Redirect_cmd_end($url);
}

function Redirect_to_inside($url)
{
    Redirect_cmd_from_args_with_loggedin($url);
}

/**
 * 检查已登录后才跳转到内部页面的CMD页面跳转函数
 */
function Redirect_cmd_from_args_with_loggedin($url)
{
    global $zbp;
    if (empty($zbp->user->ID)) {
        return;
    }
    if (empty($url)) {
        return;
    }
    $a = parse_url($url);
    $b = parse_url($zbp->host);
    if (isset($a['host']) && isset($b['host']) && strcasecmp($a['host'], $b['host']) == 0) {
        Redirect_cmd_end($url);
    }
}

/**
 * CMD页面结束前的跳转函数.
 *
 * @api Filter_Plugin_Cmd_Redirect
 */
function Redirect_cmd_end($url)
{
    global $zbp, $action;

    foreach ($GLOBALS['hooks']['Filter_Plugin_Cmd_Redirect'] as $fpname => &$fpsignal) {
        $fpname($url, $action);
    }

    Redirect302($url);
}

/**
 * CMD页面结束前的跳转函数Script版本.
 *
 * @api Filter_Plugin_Cmd_Redirect
 */
function Redirect_cmd_end_by_script($url)
{
    global $zbp, $action;

    foreach ($GLOBALS['hooks']['Filter_Plugin_Cmd_Redirect'] as $fpname => &$fpsignal) {
        $fpname($url, $action);
    }

    RedirectByScript($url);
}

//###############################################################################################################

/**
 * 通用的提交Post表对象数据.
 *
 * @api Filter_Plugin_PostPost_Core
 * @api Filter_Plugin_PostPost_Succeed
 *
 * @throws Exception
 *
 * @return Post|false
 */
function PostPost()
{
    global $zbp;
    if (!isset($_POST['ID'])) {
        return false;
    }

    if (isset($_POST['Type'])) {
        $_POST['Type'] = (int) $_POST['Type'];
    } else {
        $_POST['Type'] = 0;
    }

    if (isset($_POST['PostTime'])) {
        $_POST['PostTime'] = strtotime($_POST['PostTime']);
    }

    if (!isset($_POST['AuthorID'])) {
        $_POST['AuthorID'] = $zbp->user->ID;
    } else {
        $actions = $zbp->GetPostType($_POST['Type'], 'actions');
        if (($_POST['AuthorID'] != $zbp->user->ID) && (!$zbp->CheckRights($actions['all']))) {
            $_POST['AuthorID'] = $zbp->user->ID;
        }
        unset($actions);
    }

    if (isset($_POST['Alias'])) {
        $_POST['Alias'] = FormatString($_POST['Alias'], '[noscript]');
    }

    if (isset($_POST['Tag'])) {
        $_POST['Tag'] = FormatString($_POST['Tag'], '[noscript]');
        $_POST['Tag'] = PostArticle_CheckTagAndConvertIDtoString($_POST['Tag'], $_POST['Type']);
    }

    $post = new Post();
    $post->Type = $_POST['Type'];

    if (GetVars('ID', 'POST') == 0) {
        $i = 0;
        if (!$zbp->CheckRights($post->TypeActions['new'])) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights($post->TypeActions['public'])) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
    } else {
        $post = $zbp->GetPostByID(GetVars('ID', 'POST'));
        if ($post->ID == 0) {
            $zbp->ShowError(9, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights($post->TypeActions['edit'])) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if ((!$zbp->CheckRights($post->TypeActions['public'])) && ($post->Status == ZC_POST_STATUS_AUDITING)) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
        if (($post->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights($post->TypeActions['all']))) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    }

    foreach ($zbp->datainfo['Post'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $post->$key = GetVars($key, 'POST');
        }
    }

    FilterMeta($post);
    $post->UpdateTime = time();

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostPost_Core'] as $fpname => &$fpsignal) {
        $fpname($post);
    }

    FilterPost($post);

    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        //CountPostArray(array($post->ID));
        CountPost($post, null);
    }

    $post->Save();

    $zbp->AddBuildModule('comments');

    if (GetVars('AddNavbar', 'POST') === '0') {
        $zbp->DelItemToNavbar('page', $post->ID);
    }

    if (GetVars('AddNavbar', 'POST') === '1') {
        $zbp->AddItemToNavbar('page', $post->ID, $post->Title, $post->Url);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostPost_Succeed'] as $fpname => &$fpsignal) {
        $fpname($post);
    }

    return $post;
}

/**
 * 通用的删除Post表对象数据.
 *
 * @api Filter_Plugin_DelPost_Core
 * @api Filter_Plugin_DelPost_Succeed
 * @throws Exception
 *
 * @return bool
 */
function DelPost()
{
    global $zbp;

    $id = (int) GetVars('id');

    $post = $zbp->GetPostByID($id);

    if ($post->ID > 0) {
        if (!$zbp->CheckRights($post->TypeActions['del'])) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        if (!$zbp->CheckRights($post->TypeActions['all']) && $post->AuthorID != $zbp->user->ID) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelPost_Core'] as $fpname => &$fpsignal) {
            $fpname($post);
        }

        $post->Del();

        DelArticle_Comments($post->ID);

        $zbp->DelItemToNavbar($zbp->GetPostType($post->Type, 'name'), $post->ID);

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelPost_Succeed'] as $fpname => &$fpsignal) {
            $fpname($post);
        }
    }

    return true;
}

/**
 * 提交文章数据.
 *
 * @api Filter_Plugin_PostArticle_Core
 * @api Filter_Plugin_PostArticle_Succeed
 *
 * @throws Exception
 *
 * @return Post|false
 */
function PostArticle()
{
    global $zbp;
    if (!isset($_POST['ID'])) {
        return false;
    }

    if (isset($_COOKIE['timezone'])) {
        $tz = GetVars('timezone', 'COOKIE');
        if (is_numeric($tz)) {
            date_default_timezone_set('Etc/GMT' . sprintf('%+d', -$tz));
        }
        unset($tz);
    }

    if (isset($_POST['Tag'])) {
        $_POST['Tag'] = FormatString($_POST['Tag'], '[noscript]');
        $_POST['Tag'] = PostArticle_CheckTagAndConvertIDtoString($_POST['Tag'], 0);
    }
    if (isset($_POST['Content'])) {
        $_POST['Content'] = preg_replace("/<hr class=\"more\"\s*\/>/i", '<!--more-->', $_POST['Content']);

        if (isset($_POST['Intro'])) {
            if (stripos($_POST['Content'], '<!--more-->') !== false) {
                $_POST['Intro'] = GetValueInArray(explode('<!--more-->', $_POST['Content']), 0);
            }
            if (trim($_POST['Intro']) == '' || (stripos($_POST['Intro'], '<!--autointro-->') !== false)) {
                if ($zbp->option['ZC_ARTICLE_INTRO_WITH_TEXT'] == true) {
                    //改纯HTML摘要
                    $i = (int) $zbp->option['ZC_ARTICLE_EXCERPT_MAX'];
                    $_POST['Intro'] = FormatString($_POST['Content'], "[nohtml]");
                    $_POST['Intro'] = SubStrUTF8_Html($_POST['Intro'], $i);
                } else {
                    $i = (int) $zbp->option['ZC_ARTICLE_EXCERPT_MAX'];
                    if (Zbp_StrLen($_POST['Content']) > $i) {
                        $i = (int) Zbp_Strpos($_POST['Content'], '>', $i);
                    }
                    if ($i == 0) {
                        $i = (int) Zbp_StrLen($_POST['Content']);
                    }
                    if ($i < $zbp->option['ZC_ARTICLE_EXCERPT_MAX']) {
                        $i = (int) $zbp->option['ZC_ARTICLE_EXCERPT_MAX'];
                    }
                    $_POST['Intro'] = SubStrUTF8_Html($_POST['Content'], $i);
                    $_POST['Intro'] = CloseTags($_POST['Intro']);
                }

                $_POST['Intro'] .= '<!--autointro-->';
            } else {
                if ($zbp->option['ZC_ARTICLE_INTRO_WITH_TEXT'] == true) {
                    //改纯HTML摘要
                    $_POST['Intro'] = FormatString($_POST['Intro'], "[nohtml]");
                }
                $_POST['Intro'] = CloseTags($_POST['Intro']);
            }
        }
    }

    if (!isset($_POST['AuthorID'])) {
        $_POST['AuthorID'] = $zbp->user->ID;
    } else {
        if (($_POST['AuthorID'] != $zbp->user->ID) && (!$zbp->CheckRights('ArticleAll'))) {
            $_POST['AuthorID'] = $zbp->user->ID;
        }
        if (empty($_POST['AuthorID'])) {
            $_POST['AuthorID'] = $zbp->user->ID;
        }
    }

    if (isset($_POST['Alias'])) {
        $_POST['Alias'] = FormatString($_POST['Alias'], '[noscript]');
    }

    if (isset($_POST['PostTime'])) {
        $_POST['PostTime'] = strtotime($_POST['PostTime']);
    }

    if (!$zbp->CheckRights('ArticleAll')) {
        unset($_POST['IsTop']);
    }

    $article = new Post();
    $pre_author = null;
    $pre_tag = null;
    $pre_category = null;
    $pre_istop = null;
    $pre_status = null;
    $orig_id = 0;

    if (GetVars('ID', 'POST') == 0) {
        if (!$zbp->CheckRights('ArticleNew')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights('ArticlePub')) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
    } else {
        $article = $zbp->GetPostByID(GetVars('ID', 'POST'));
        if ($article->ID == 0) {
            $zbp->ShowError(9, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights('ArticleEdt')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('ArticleAll'))) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if ((!$zbp->CheckRights('ArticlePub')) && ($article->Status == ZC_POST_STATUS_AUDITING)) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
        $orig_id = $article->ID;
        $pre_author = $article->AuthorID;
        $pre_tag = $article->Tag;
        $pre_category = $article->CateID;
        $pre_istop = $article->IsTop;
        $pre_status = $article->Status;
    }

    foreach ($zbp->datainfo['Post'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $article->$key = GetVars($key, 'POST');
        }
    }

    $article->Type = ZC_POST_TYPE_ARTICLE;

    $article->UpdateTime = time();

    FilterMeta($article);

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostArticle_Core'] as $fpname => &$fpsignal) {
        $fpname($article);
    }

    FilterPost($article);

    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        //CountPostArray(array($post->ID));
        CountPost($article, null);
    }

    $article->Save();
    $zbp->AddCache($article);

    //更新统计信息
    $pre_arrayTag = $zbp->LoadTagsByIDString($pre_tag);
    $now_arrayTag = $zbp->LoadTagsByIDString($article->Tag);
    $pre_array = $now_array = array();
    foreach ($pre_arrayTag as $tag) {
        $pre_array[] = $tag->ID;
    }
    foreach ($now_arrayTag as $tag) {
        $now_array[] = $tag->ID;
    }
    $del_array = array_diff($pre_array, $now_array);
    $add_array = array_diff($now_array, $pre_array);
    $del_string = $zbp->ConvertTagIDtoString($del_array);
    $add_string = $zbp->ConvertTagIDtoString($add_array);
    if ($del_string) {
        CountTagArrayString($del_string, -1, $article->ID);
    }
    if ($add_string) {
        CountTagArrayString($add_string, +1, $article->ID);
    }
    if ($pre_author != $article->AuthorID) {
        if ($pre_author > 0) {
            CountMemberArray(array($pre_author), array(-1, 0, 0, 0));
        }

        CountMemberArray(array($article->AuthorID), array(+1, 0, 0, 0));
    }
    if ($pre_category != $article->CateID) {
        if ($pre_category > 0) {
            CountCategoryArray(array($pre_category), -1);
        }

        CountCategoryArray(array($article->CateID), +1);
    }
    if ($orig_id == 0 && $article->IsTop == 0 && $article->Status == ZC_POST_STATUS_PUBLIC) {
        CountNormalArticleNums(+1);
    } elseif ($orig_id > 0) {
        if (($pre_istop == 0 && $pre_status == 0) && ($article->IsTop != 0 || $article->Status != 0)) {
            CountNormalArticleNums(-1);
        }
        if (($pre_istop != 0 || $pre_status != 0) && ($article->IsTop == 0 && $article->Status == 0)) {
            CountNormalArticleNums(+1);
        }
    }
    if ($article->IsTop == true && $article->Status == ZC_POST_STATUS_PUBLIC) {
        CountTopPost($article->Type, $article->ID, null);
    } else {
        CountTopPost($article->Type, null, $article->ID);
    }

    $zbp->AddBuildModule('previous');
    $zbp->AddBuildModule('calendar');
    $zbp->AddBuildModule('comments');
    $zbp->AddBuildModule('tags');
    $zbp->AddBuildModule('authors');
    $zbp->AddBuildModule('archives');

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostArticle_Succeed'] as $fpname => &$fpsignal) {
        $fpname($article);
    }

    return $article;
}

/**
 * 删除文章.
 *
 * @throws Exception
 *
 * @return bool
 */
function DelArticle()
{
    global $zbp;

    $id = (int) GetVars('id');

    $article = $zbp->GetPostByID($id);
    if ($article->ID > 0) {
        if (!$zbp->CheckRights('ArticleDel')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        if (!$zbp->CheckRights('ArticleAll') && $article->AuthorID != $zbp->user->ID) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        $pre_author = $article->AuthorID;
        $pre_tag = $article->Tag;
        $pre_category = $article->CateID;
        $pre_istop = $article->IsTop;
        $pre_status = $article->Status;

        $article->Del();

        DelArticle_Comments($article->ID);

        CountTagArrayString($pre_tag, -1, $article->ID);
        CountMemberArray(array($pre_author), array(-1, 0, 0, 0));
        CountCategoryArray(array($pre_category), -1);
        if (($pre_istop == 0 && $pre_status == 0)) {
            CountNormalArticleNums(-1);
        }
        if ($article->IsTop == true) {
            CountTopPost($article->Type, null, $article->ID);
        }

        $zbp->AddBuildModule('previous');
        $zbp->AddBuildModule('calendar');
        $zbp->AddBuildModule('comments');
        $zbp->AddBuildModule('tags');
        $zbp->AddBuildModule('authors');
        $zbp->AddBuildModule('archives');

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelArticle_Succeed'] as $fpname => &$fpsignal) {
            $fpname($article);
        }

        return true;
    }

    return false;
}

/**
 * 提交文章数据时检查tag数据，并将新tags转为标准格式返回.
 *
 * @param string $tagnamestring 提交的文章tag数据，可以:,，、等符号分隔
 *
 * @return string 返回如'{1}{2}{3}{4}'的字符串
 */
function PostArticle_CheckTagAndConvertIDtoString($tagnamestring, $post_type = 0)
{
    global $zbp;
    $s = '';
    $tagnamestring = str_replace(array(';', '，', '、'), ',', $tagnamestring);
    $tagnamestring = strip_tags($tagnamestring);
    $tagnamestring = trim($tagnamestring);
    if ($tagnamestring == '') {
        return '';
    }

    if ($tagnamestring == ',') {
        return '';
    }

    $a = explode(',', $tagnamestring);
    $b = array();
    foreach ($a as $value) {
        $v = trim($value);
        if ($v) {
            $b[] = $v;
        }
    }
    $b = array_unique($b);
    $b = array_slice($b, 0, 20);
    $c = array();

    $t = $zbp->LoadTagsByNameString($tagnamestring, $post_type);
    foreach ($t as $key => $value) {
        $c[] = $key;
    }

    $d = array_diff($b, $c);
    if ($zbp->CheckRights('TagNew') && $zbp->CheckRights('TagPst')) {
        foreach ($d as $key) {
            $tag = new Tag();
            $tag->Name = $key;
            $tag->Type = $post_type;

            foreach ($GLOBALS['hooks']['Filter_Plugin_PostTag_Core'] as $fpname => &$fpsignal) {
                $fpname($tag);
            }

            FilterTag($tag);
            $tag->Save();
            $zbp->AddCache($tag);
            $zbp->AddBuildModule('tags');

            foreach ($GLOBALS['hooks']['Filter_Plugin_PostTag_Succeed'] as $fpname => &$fpsignal) {
                $fpname($tag);
            }
        }
    }

    foreach ($b as $key) {
        if (!isset($zbp->tagsbyname_type[$post_type][$key])) {
            continue;
        }

        $s .= '{' . $zbp->tagsbyname_type[$post_type][$key]->ID . '}';
    }

    return $s;
}

/**
 * 删除文章下所有评论.
 *
 * @param int $id 文章ID
 */
function DelArticle_Comments($id)
{
    global $zbp;

    $sql = $zbp->db->sql->Delete($zbp->table['Comment'], array(array('=', 'comm_LogID', $id)));
    $zbp->db->Delete($sql);
}

//###############################################################################################################

/**
 * 提交页面数据.
 *
 * @throws Exception
 *
 * @return Post|false
 */
function PostPage()
{
    global $zbp;
    if (!isset($_POST['ID'])) {
        return false;
    }

    if (isset($_POST['PostTime'])) {
        $_POST['PostTime'] = strtotime($_POST['PostTime']);
    }

    if (!isset($_POST['AuthorID'])) {
        $_POST['AuthorID'] = $zbp->user->ID;
    } else {
        if (($_POST['AuthorID'] != $zbp->user->ID) && (!$zbp->CheckRights('PageAll'))) {
            $_POST['AuthorID'] = $zbp->user->ID;
        }
    }

    if (isset($_POST['Alias'])) {
        $_POST['Alias'] = FormatString($_POST['Alias'], '[noscript]');
    }

    $article = new Post();
    $pre_author = null;
    $orig_id = 0;
    if (GetVars('ID', 'POST') == 0) {
        if (!$zbp->CheckRights('PageNew')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights('PagePub')) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
    } else {
        $article = $zbp->GetPostByID(GetVars('ID', 'POST'));
        if ($article->ID == 0) {
            $zbp->ShowError(9, __FILE__, __LINE__);
        }
        if (!$zbp->CheckRights('PageEdt')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        if ((!$zbp->CheckRights('PagePub')) && ($article->Status == ZC_POST_STATUS_AUDITING)) {
            $_POST['Status'] = ZC_POST_STATUS_AUDITING;
        }
        if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('PageAll'))) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        $pre_author = $article->AuthorID;
        $orig_id = $article->ID;
    }

    foreach ($zbp->datainfo['Post'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $article->$key = GetVars($key, 'POST');
        }
    }

    $article->Type = ZC_POST_TYPE_PAGE;

    FilterMeta($article);

    $article->UpdateTime = time();

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostPage_Core'] as $fpname => &$fpsignal) {
        $fpname($article);
    }

    FilterPost($article);

    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        //CountPostArray(array($post->ID));
        CountPost($article, null);
    }

    $article->Save();

    if ($pre_author != $article->AuthorID) {
        if ($pre_author > 0) {
            CountMemberArray(array($pre_author), array(0, -1, 0, 0));
        }

        CountMemberArray(array($article->AuthorID), array(0, +1, 0, 0));
    }

    $zbp->AddBuildModule('comments');

    if (GetVars('AddNavbar', 'POST') === '0') {
        $zbp->DelItemToNavbar('page', $article->ID);
    }

    if (GetVars('AddNavbar', 'POST') === '1') {
        $zbp->AddItemToNavbar('page', $article->ID, $article->Title, $article->Url);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostPage_Succeed'] as $fpname => &$fpsignal) {
        $fpname($article);
    }

    return $article;
}

/**
 * 删除页面.
 *
 * @throws Exception
 *
 * @return bool
 */
function DelPage()
{
    global $zbp;

    $id = (int) GetVars('id');

    $article = $zbp->GetPostByID($id);
    if ($article->ID > 0) {
        if (!$zbp->CheckRights('PageDel')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        if (!$zbp->CheckRights('PageAll') && $article->AuthorID != $zbp->user->ID) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }

        $pre_author = $article->AuthorID;

        $article->Del();

        DelArticle_Comments($article->ID);

        CountMemberArray(array($pre_author), array(0, -1, 0, 0));

        $zbp->AddBuildModule('comments');

        $zbp->DelItemToNavbar('page', $article->ID);

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelPage_Succeed'] as $fpname => &$fpsignal) {
            $fpname($article);
        }
    }

    return true;
}

/**
 * 批量删除Post.
 *
 * @param $type
 */
function BatchPost($type)
{
    foreach ($GLOBALS['hooks']['Filter_Plugin_BatchPost'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($type);
    }
}

//###############################################################################################################

/**
 * 提交评论.
 *
 * @throws Exception
 *
 * @return Comment|false
 */
function PostComment()
{
    global $zbp;

    $isAjax = GetVars('isajax', 'POST');
    $returnJson = GetVars('format', 'POST') == 'json';
    $returnCommentWhiteList = array(
        'ID'       => null,
        'Content'  => null,
        'LogId'    => null,
        'Name'     => null,
        'ParentID' => null,
        'PostTime' => null,
        'HomePage' => null,
        'Email'    => null,
        'AuthorID' => null,
    );

    if (isset($_GET['postid'])) {
        $_POST['LogID'] = $_GET['postid'];
    } elseif (isset($_POST['postid'])) {
        $_POST['LogID'] = $_POST['postid'];
    } elseif (!isset($_POST['LogID'])) {
        $_POST['LogID'] = 0;
    }

    if ($zbp->option['ZC_COMMENT_VALIDCMTKEY_ENABLE']) {
        if ($zbp->ValidCmtKey($_POST['LogID'], $_GET['key']) == false) {
            $zbp->ShowError(43, __FILE__, __LINE__);
        }
    }

    if ($zbp->option['ZC_COMMENT_VERIFY_ENABLE']) {
        if (!$zbp->CheckRights('NoValidCode')) {
            if ($zbp->CheckValidCode($_POST['verify'], 'cmt') == false) {
                $zbp->ShowError(38, __FILE__, __LINE__);
            }
        }
    }

    $post_name = isset($_POST['name']) ? GetVars('name', 'POST') : GetVars('Name', 'POST');
    $post_replyid = isset($_POST['replyid']) ? GetVars('replyid', 'POST') : GetVars('ReplyID', 'POST');
    $post_email = isset($_POST['email']) ? GetVars('email', 'POST') : GetVars('Email', 'POST');
    $post_homepage = isset($_POST['homepage']) ? GetVars('homepage', 'POST') : GetVars('HomePage', 'POST');
    $post_content = isset($_POST['content']) ? GetVars('content', 'POST') : GetVars('Content', 'POST');

    //判断是不是有同名的用户
    $m = $zbp->GetMemberByName($post_name);
    if ($m->ID > 0) {
        if ($m->ID != $zbp->user->ID) {
            $zbp->ShowError(31, __FILE__, __LINE__);
        }
    }

    $replyid = (int) $post_replyid;

    if ($replyid == 0) {
        $_POST['RootID'] = 0;
        $_POST['ParentID'] = 0;
    } else {
        $_POST['ParentID'] = $replyid;
        $c = $zbp->GetCommentByID($replyid);
        if ($c->Level > ($zbp->comment_recursion_level - 2)) {
            $zbp->ShowError(52, __FILE__, __LINE__);
        }
        $_POST['RootID'] = Comment::GetRootID($c->ID);
    }

    $_POST['AuthorID'] = $zbp->user->ID;
    $_POST['Name'] = $post_name;
    $_POST['Email'] = $post_email;
    $_POST['HomePage'] = $post_homepage;
    $_POST['Content'] = $post_content;
    $_POST['PostTime'] = time();
    $_POST['IP'] = GetGuestIP();
    $_POST['Agent'] = GetGuestAgent();

    if ($zbp->user->ID > 0) {
        $_POST['Name'] = $zbp->user->Name;
    }

    $cmt = new Comment();

    foreach ($zbp->datainfo['Comment'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if ($key == 'IsChecking') {
            continue;
        }

        if (isset($_POST[$key])) {
            $cmt->$key = GetVars($key, 'POST');
        }
    }

    if ($zbp->option['ZC_COMMENT_AUDIT'] && !$zbp->CheckRights('root')) {
        $cmt->IsChecking = true;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostComment_Core'] as $fpname => &$fpsignal) {
        $fpname($cmt);
    }

    FilterComment($cmt);

    if ($cmt->IsThrow) {
        $zbp->ShowError(14, __FILE__, __LINE__);

        return false;
    }

    $cmt->Save();
    $zbp->AddCache($cmt);

    if ($cmt->IsChecking) {
        CountCommentNums(0, +1);
        $zbp->ShowError(53, __FILE__, __LINE__);

        return false;
    }

    CountPostArray(array($cmt->LogID), +1);
    CountCommentNums(+1, 0);
    if ($zbp->user->ID > 0) {
        CountMember($zbp->user, array(0, 0, 1, 0));
        $zbp->user->Save();
    }

    $zbp->AddBuildModule('comments');

    if ($isAjax) {
        ViewComment($cmt->ID);
    } elseif ($returnJson) {
        ob_clean();
        ViewComment($cmt->ID);
        $commentHtml = ob_get_clean();
        JsonReturn(
            array_merge_recursive(
                array(
                    "html" => $commentHtml,
                ),
                array_intersect_key($cmt->GetData(), $returnCommentWhiteList)
            )
        );
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostComment_Succeed'] as $fpname => &$fpsignal) {
        $fpname($cmt);
    }

    return $cmt;
}

/**
 * 删除评论.
 *
 * @return bool
 */
function DelComment()
{
    global $zbp;

    $id = (int) GetVars('id', 'GET');
    $cmt = $zbp->GetCommentByID($id);

    if ($zbp->CheckRights('CommentAll') == false) {
        if ($cmt->AuthorID != $zbp->user->ID && $cmt->Post->AuthorID != $zbp->user->ID) {
            return false;
        }
    }

    if ($cmt->ID > 0) {
        $comments = $zbp->GetCommentList('*', array(array('=', 'comm_LogID', $cmt->LogID)), null, null, null);

        DelComment_Children($cmt->ID);

        if ($cmt->IsChecking == false) {
            CountCommentNums(-1, 0);
        } else {
            CountCommentNums(-1, -1);
        }
        $cmt->Del();

        if ($cmt->IsChecking == false) {
            CountPostArray(array($cmt->LogID), -1);
            if ($cmt->AuthorID > 0) {
                CountMember($cmt->Author, array(0, 0, -1, 0));
                $cmt->Author->Save();
            }
        }

        $zbp->AddBuildModule('comments');

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelComment_Succeed'] as $fpname => &$fpsignal) {
            $fpname($cmt);
        }
    }

    return true;
}

/**
 * 删除评论下的子评论.
 *
 * @param int $id 父评论ID
 */
function DelComment_Children($id)
{
    global $zbp;

    $cmt = $zbp->GetCommentByID($id);

    foreach ($cmt->Comments as $comment) {
        if (count($comment->Comments) > 0) {
            DelComment_Children($comment->ID);
        }
        if ($comment->IsChecking == false) {
            CountCommentNums(-1, 0);
        } else {
            CountCommentNums(-1, -1);
        }
        $comment->Del();
    }
}

/**
 * 只历遍并保留评论id进array,不进行删除.
 *
 * @param int       $id    父评论ID
 * @param Comment[] $array 将子评论ID存入新数组
 */
function GetSubComments($id, &$array)
{
    global $zbp;

    /* @var Comment $cmt */
    $cmt = $zbp->GetCommentByID($id);

    foreach ($cmt->Comments as $comment) {
        $array[] = $comment->ID;
        if (count($comment->Comments) > 0) {
            GetSubComments($comment->ID, $array);
        }
    }
}

/**
 *检查评论数据并保存、更新计数、更新“最新评论”模块.
 */
function CheckComment()
{
    global $zbp;

    $id = (int) GetVars('id');
    $ischecking = (bool) GetVars('ischecking');

    $cmt = $zbp->GetCommentByID($id);
    if ($cmt->ID == 0) {
        return $cmt;
    }
    if ($zbp->CheckRights('CommentAll') == false) {
        if ($cmt->AuthorID != $zbp->user->ID && $cmt->Post->AuthorID != $zbp->user->ID) {
            return $cmt;
        }
    }

    $orig_check = (bool) $cmt->IsChecking;
    $cmt->IsChecking = $ischecking;

    foreach ($GLOBALS['hooks']['Filter_Plugin_CheckComment_Core'] as $fpname => &$fpsignal) {
        $fpname($cmt);
    }

    $cmt->Save();

    foreach ($GLOBALS['hooks']['Filter_Plugin_CheckComment_Succeed'] as $fpname => &$fpsignal) {
        $fpname($cmt);
    }

    if (($orig_check) && (!$ischecking)) {
        CountPostArray(array($cmt->LogID), +1);
        CountCommentNums(0, -1);
        if ($cmt->AuthorID > 0) {
            CountMember($cmt->Author, array(0, 0, +1, 0));
            $cmt->Author->Save();
        }
    } elseif ((!$orig_check) && ($ischecking)) {
        CountPostArray(array($cmt->LogID), -1);
        CountCommentNums(0, +1);
        if ($cmt->AuthorID > 0) {
            CountMember($cmt->Author, array(0, 0, -1, 0));
            $cmt->Author->Save();
        }
    }

    $zbp->AddBuildModule('comments');

    return $cmt;
}

/**
 * 评论批量处理（删除、通过审核、加入审核）.
 */
function BatchComment()
{
    global $zbp;
    if (isset($_POST['all_del'])) {
        $type = 'all_del';
    } elseif (isset($_POST['all_pass'])) {
        $type = 'all_pass';
    } elseif (isset($_POST['all_audit'])) {
        $type = 'all_audit';
    } else {
        return;
    }
    if (!isset($_POST['id'])) {
        return;
    }
    $array = $_POST['id'];
    if (is_array($array)) {
        $array = array_unique($array);
    } else {
        $array = array($array);
    }

    $childArray = $zbp->GetCommentByArray($array);

    // Search Child Comments
    /* @var Comment[] $childArray */
    //$childArray = array();
    //foreach ($array as $i => $id) {
    //    $cmt = $zbp->GetCommentByID($id);
    //    if ($cmt->ID == 0) {
    //        continue;
    //    }
    //    $childArray[] = $cmt;
    //    GetSubComments($cmt->ID, $childArray);
    //}

    // Unique child array
    //$childArray = array_unique($childArray);
    //foreach ($childArray as $key => $value) {
    //    if (is_int($value)) {
    //        $childArray[$key] = $zbp->GetCommentByID($value);
    //    }
    //    if (is_subclass_of($childArray[$key], 'Base') == false || $childArray[$key]->ID == 0) {
    //        unset($childArray[$key]);
    //    }
    //}

    if ($type == 'all_del') {
        foreach ($childArray as $i => $cmt) {
            if ($zbp->CheckRights('CommentAll') == false) {
                if ($cmt->AuthorID != $zbp->user->ID && $cmt->Post->AuthorID != $zbp->user->ID) {
                    continue;
                }
            }

            $cmt->Del();
            if (!$cmt->IsChecking) {
                CountPostArray(array($cmt->LogID), -1);
                CountCommentNums(-1, 0);
                if ($cmt->AuthorID > 0) {
                    CountMember($cmt->Author, array(0, 0, -1, 0));
                    $cmt->Author->Save();
                }
            } else {
                CountCommentNums(-1, -1);
            }
        }
    } elseif ($type == 'all_pass') {
        foreach ($childArray as $i => $cmt) {
            if (!$cmt->IsChecking) {
                continue;
            }
            if ($zbp->CheckRights('CommentAll') == false) {
                if ($cmt->AuthorID != $zbp->user->ID && $cmt->Post->AuthorID != $zbp->user->ID) {
                    continue;
                }
            }

            $cmt->IsChecking = false;
            $cmt->Save();
            CountPostArray(array($cmt->LogID), +1);
            CountCommentNums(0, -1);
            if ($cmt->AuthorID > 0) {
                CountMember($cmt->Author, array(0, 0, 1, 0));
                $cmt->Author->Save();
            }
        }
    } elseif ($type == 'all_audit') {
        foreach ($childArray as $i => $cmt) {
            if ($cmt->IsChecking) {
                continue;
            }
            if ($zbp->CheckRights('CommentAll') == false) {
                if ($cmt->AuthorID != $zbp->user->ID && $cmt->Post->AuthorID != $zbp->user->ID) {
                    continue;
                }
            }

            $cmt->IsChecking = true;
            $cmt->Save();
            CountPostArray(array($cmt->LogID), -1);
            CountCommentNums(0, +1);
            if ($cmt->AuthorID > 0) {
                CountMember($cmt->Author, array(0, 0, -1, 0));
                $cmt->Author->Save();
            }
        }
    }

    $zbp->AddBuildModule('comments');
}

//###############################################################################################################

/**
 * 提交分类数据.
 *
 * @return Category|false
 */
function PostCategory()
{
    global $zbp;
    if (!$zbp->CheckRights('CategoryPst')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    if (!isset($_POST['ID'])) {
        return false;
    }

    if (isset($_POST['Alias'])) {
        $_POST['Alias'] = FormatString($_POST['Alias'], '[noscript]');
    }

    $parentid = (int) GetVars('ParentID', 'POST');
    if ($parentid > 0) {
        if (isset($zbp->categories_all[$parentid]) && $zbp->categories_all[$parentid]->Level > ($zbp->category_recursion_level - 2)) {
            $_POST['ParentID'] = '0';
        }
    }

    $cate = new Category();
    $cate_id = (int) GetVars('ID', 'POST');
    if ($cate_id == 0) {
        $i = 0;
        if (!$zbp->CheckRights('CategoryNew')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    } else {
        $cate = $zbp->GetCategoryByID($cate_id);
        if (!$zbp->CheckRights('CategoryEdt')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    }

    foreach ($zbp->datainfo['Category'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $cate->$key = GetVars($key, 'POST');
        }
    }

    FilterMeta($cate);

    $cate->UpdateTime = time();

    //刷新RootID
    $cate->Level;

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostCategory_Core'] as $fpname => &$fpsignal) {
        $fpname($cate);
    }

    FilterCategory($cate);

    // 此处用作刷新分类内文章数据使用，不作更改
    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        if ($cate->ID > 0) {
            CountCategory($cate, null, $cate->Type);
        }
    }

    $cate->Save();
    $zbp->AddCache($cate);

    $zbp->AddBuildModule('catalog');

    if (GetVars('AddNavbar', 'POST') === '0') {
        $zbp->DelItemToNavbar('category', $cate->ID);
    }

    if (GetVars('AddNavbar', 'POST') === '1') {
        $zbp->AddItemToNavbar('category', $cate->ID, $cate->Name, $cate->Url);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostCategory_Succeed'] as $fpname => &$fpsignal) {
        $fpname($cate);
    }

    return $cate;
}

/**
 * 删除分类.
 *
 * @throws Exception
 *
 * @return bool
 */
function DelCategory()
{
    global $zbp;
    if (!$zbp->CheckRights('CategoryDel')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $id = (int) GetVars('id');
    $cate = $zbp->GetCategoryByID($id);
    if ($cate->ID > 0) {
        if (count($cate->SubCategories) > 0) {
            $zbp->ShowError(49, __FILE__, __LINE__);

            return false;
        }

        DelCategory_Articles($cate->ID);
        $cate->Del();

        $zbp->LoadCategories();
        $zbp->AddBuildModule('catalog');
        $zbp->DelItemToNavbar('category', $cate->ID);

        foreach ($GLOBALS['hooks']['Filter_Plugin_DelCategory_Succeed'] as $fpname => &$fpsignal) {
            $fpname($cate);
        }

        return true;
    }

    return false;
}

/**
 * 删除分类下所有文章.
 *
 * @param int $id 分类ID
 */
function DelCategory_Articles($id)
{
    global $zbp;

    $sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_CateID' => 0), array(array('=', 'log_CateID', $id)));
    $zbp->db->Update($sql);
}

//###############################################################################################################

/**
 * 提交标签数据.
 *
 * @return Tag|false
 */
function PostTag()
{
    global $zbp;
    if (!$zbp->CheckRights('TagPst')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    if (!isset($_POST['ID'])) {
        return false;
    }

    if (isset($_POST['Alias'])) {
        $_POST['Alias'] = FormatString($_POST['Alias'], '[noscript]');
    }

    $tag = new Tag();
    if (GetVars('ID', 'POST') == 0) {
        $i = 0;
        if (!$zbp->CheckRights('TagNew')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    } else {
        $tag = $zbp->GetTagByID(GetVars('ID', 'POST'));
        if (!$zbp->CheckRights('TagEdt')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    }

    foreach ($zbp->datainfo['Tag'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $tag->$key = GetVars($key, 'POST');
        }
    }

    FilterMeta($tag);

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostTag_Core'] as $fpname => &$fpsignal) {
        $fpname($tag);
    }

    FilterTag($tag);

    $tag->UpdateTime = time();

    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        CountTag($tag);
    }

    //检查Name重名(用GetTagList不用GetTagByName)
    $array = $zbp->GetTagList('*', array(array('=', 'tag_Name', $tag->Name), array('=', 'tag_Type', $tag->Type)), '', 1, '');
    $checkTag = new Tag();
    if (count($array) > 0) {
        $checkTag = $array[0];
    }
    if (($tag->ID == 0 && $checkTag->ID > 0) || ($tag->ID > 0 && $checkTag->ID > 0 && $checkTag->ID != $tag->ID)) {
        $zbp->ShowError(98, __FILE__, __LINE__);
    }

    $tag->Save();
    $zbp->AddCache($tag);

    if (GetVars('AddNavbar', 'POST') === '0') {
        $zbp->DelItemToNavbar('tag', $tag->ID);
    }

    if (GetVars('AddNavbar', 'POST') === '1') {
        $zbp->AddItemToNavbar('tag', $tag->ID, $tag->Name, $tag->Url);
    }

    $zbp->AddBuildModule('tags');

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostTag_Succeed'] as $fpname => &$fpsignal) {
        $fpname($tag);
    }

    return $tag;
}

/**
 * 删除标签.
 *
 * @return bool
 */
function DelTag()
{
    global $zbp;
    if (!$zbp->CheckRights('TagDel')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $tagid = (int) GetVars('id', 'GET');
    $tag = $zbp->GetTagByID($tagid);
    if ($tag->ID > 0) {
        $tag->Del();
        $zbp->DelItemToNavbar('tag', $tag->ID);
        $zbp->AddBuildModule('tags');
        foreach ($GLOBALS['hooks']['Filter_Plugin_DelTag_Succeed'] as $fpname => &$fpsignal) {
            $fpname($tag);
        }
    }

    return true;
}

//###############################################################################################################

/**
 * 提交用户数据.
 *
 * @throws Exception
 *
 * @return Member|false
 */
function PostMember()
{
    global $zbp;
    if (!$zbp->CheckRights('MemberPst')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $mem = new Member();

    $data = array();

    if (!isset($_POST['ID'])) {
        return false;
    }

    //检测密码
    if (trim($_POST["Password"]) == '' || trim($_POST["PasswordRe"]) == '' || $_POST["Password"] != $_POST["PasswordRe"]) {
        unset($_POST["Password"]);
        unset($_POST["PasswordRe"]);
    }

    $data['ID'] = $_POST['ID'];
    $editableField = array('Password', 'Email', 'HomePage', 'Alias', 'Intro', 'Template');
    // 如果是管理员，则再允许改动别的字段
    if ($zbp->CheckRights('MemberAll')) {
        array_push($editableField, 'Level', 'Status', 'Name', 'IP');
    }
    // 复制一个新数组
    foreach ($editableField as $value) {
        if (isset($_POST[$value])) {
            $data[$value] = GetVars($value, 'POST');
        }
    }

    if (isset($data['Name'])) {
        // 检测同名
        $m = $zbp->GetMemberByName($data['Name']);
        if ($m->ID > 0 && $m->ID != $data['ID']) {
            $zbp->ShowError(62, __FILE__, __LINE__);
        }
    }

    if (isset($data['Alias'])) {
        $data['Alias'] = FormatString($data['Alias'], '[noscript]');
    }

    if ($data['ID'] == 0) {
        //新建用户必须提供密码
        if (!isset($data['Password']) || $data['Password'] == '') {
            $zbp->ShowError(73, __FILE__, __LINE__);
        }
        $data['IP'] = GetGuestIP();
        if ($mem->Guid == '') {
            $mem->Guid = GetGuid();
        }
        //检查新建用户权限
        if (!$zbp->CheckRights('MemberNew')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    } else {
        $mem = $zbp->GetMemberByID($data['ID']);
        if ($mem->ID == 0) {
            $zbp->ShowError(69, __FILE__, __LINE__);
        }
        //检查编辑用户权限
        if (!$zbp->CheckRights('MemberEdt')) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
        //检查编辑他人(MemberAll)
        if ($mem->ID <> $zbp->user->ID) {
            if (!$zbp->CheckRights('MemberAll')) {
                $zbp->ShowError(6, __FILE__, __LINE__);
            }
        }
    }

    if ($mem->IsGod == true) {
        if ($zbp->user->IsGod == false) {
            unset($data['Password']);
            unset($data['Name']);
            unset($data['Email']);
            unset($data['Alias']);
            unset($data['Status']);
            unset($data['Intro']);
            unset($data['HomePage']);
        }
        unset($data['Level']);
    }

    foreach ($zbp->datainfo['Member'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($data[$key])) {
            $mem->$key = $data[$key];
        }
    }

    // 然后，读入密码
    // 密码需要单独处理，因为拿不到用户Guid
    if (isset($data['Password'])) {
        $data['Password'] = trim($data['Password']);
        if ($data['Password'] != '') {
            if (strlen($data['Password']) < $zbp->option['ZC_PASSWORD_MIN'] || strlen($data['Password']) > $zbp->option['ZC_PASSWORD_MAX']) {
                $zbp->ShowError(54, __FILE__, __LINE__);
            }
            if (!CheckRegExp($data['Password'], '[password]')) {
                $zbp->ShowError(54, __FILE__, __LINE__);
            }
            $mem->Password = Member::GetPassWordByGuid($data['Password'], $mem->Guid);
        }
    }

    FilterMeta($mem);

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostMember_Core'] as $fpname => &$fpsignal) {
        $fpname($mem);
    }

    FilterMember($mem);

    $mem->UpdateTime = time();

    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        CountMember($mem, array(null, null, null, null));
    }

    // 查询同名
    if (isset($data['Name'])) {
        if ($data['ID'] == 0) {
            if ($zbp->CheckMemberNameExist($data['Name'])) {
                $zbp->ShowError(62, __FILE__, __LINE__);
            }
        }
    }

    $mem->Save();
    $zbp->AddCache($mem);

    $zbp->AddBuildModule('authors');

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostMember_Succeed'] as $fpname => &$fpsignal) {
        $fpname($mem);
    }

    return $mem;
}

/**
 * 删除用户.
 *
 * @return bool
 */
function DelMember()
{
    global $zbp;
    if (!$zbp->CheckRights('MemberDel')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $id = (int) GetVars('id', 'GET');
    $mem = $zbp->GetMemberByID($id);
    if ($mem->ID > 0 && $mem->ID != $zbp->user->ID) {
        if ($mem->IsGod !== true) {
            DelMember_AllData($id);
            $mem->Del();
            foreach ($GLOBALS['hooks']['Filter_Plugin_DelMember_Succeed'] as $fpname => &$fpsignal) {
                $fpname($mem);
            }
        }
    } else {
        return false;
    }

    return true;
}

/**
 * 删除用户下所有数据（包括文章、评论、附件）.
 *
 * @param int $id 用户ID
 */
function DelMember_AllData($id)
{
    global $zbp;

    $w = array();
    $w[] = array('=', 'log_AuthorID', $id);

    /* @var Post[] $articles */
    $articles = $zbp->GetPostList('*', $w);
    foreach ($articles as $a) {
        if ($zbp->option['ZC_DELMEMBER_WITH_ALLDATA'] == true) {
            $a->Del();
        } else {
            $a->AuthorID = 0;
            $a->Save();
        }
    }

    $w = array();
    $w[] = array('=', 'comm_AuthorID', $id);
    /* @var Comment[] $comments */
    $comments = $zbp->GetCommentList('*', $w);
    foreach ($comments as $c) {
        if ($zbp->option['ZC_DELMEMBER_WITH_ALLDATA'] == true) {
            $c->Del();
        } else {
            $c->AuthorID = 0;
            $c->Save();
        }
    }

    $w = array();
    $w[] = array('=', 'ul_AuthorID', $id);
    /* @var Upload[] $uploads */
    $uploads = $zbp->GetUploadList('*', $w);
    foreach ($uploads as $u) {
        $u->Del();
        $u->DelFile();
    }
}

//###############################################################################################################

/**
 * 提交模块数据.
 *
 * @return Module|false
 */
function PostModule()
{
    global $zbp;
    if (!$zbp->CheckRights('ModulePst')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    if (isset($_POST['catalog_style'])) {
        $zbp->option['ZC_MODULE_CATALOG_STYLE'] = $_POST['catalog_style'];
        $zbp->SaveOption();
    }

    if ($_POST['FileName'] == 'archives') {
        if (isset($_POST['archives_style'])) {
            $zbp->option['ZC_MODULE_ARCHIVES_STYLE'] = 1;
        } else {
            $zbp->option['ZC_MODULE_ARCHIVES_STYLE'] = 0;
        }
        $zbp->SaveOption();
    }

    if (!isset($_POST['ID'])) {
        return false;
    }

    if (!GetVars('FileName', 'POST')) {
        $_POST['FileName'] = 'mod' . rand(1000, 9999);
    } else {
        $_POST['FileName'] = strtolower($_POST['FileName']);
    }
    if (!GetVars('HtmlID', 'POST')) {
        $_POST['HtmlID'] = $_POST['FileName'];
    }
    if (isset($_POST['MaxLi'])) {
        $_POST['MaxLi'] = (int) $_POST['MaxLi'];
    }
    if (isset($_POST['IsHideTitle'])) {
        $_POST['IsHideTitle'] = (int) $_POST['IsHideTitle'];
    }
    if (!isset($_POST['Type'])) {
        $_POST['Type'] = 'div';
    }
    // if (isset($_POST['Content'])) {
    //     if ($_POST['Type'] != 'div') {
    //         // div不再过滤\r和\n//$_POST['Content'] = str_replace(array("\r", "\n"), array('', ''), $_POST['Content']);
    //     }
    // }

    /* @var Module $mod */
    $mod = $zbp->GetModuleByID(GetVars('ID', 'POST'));

    foreach ($zbp->datainfo['Module'] as $key => $value) {
        if ($key == 'ID' || $key == 'Meta') {
            continue;
        }
        if (isset($_POST[$key])) {
            $mod->$key = GetVars($key, 'POST');
        }
    }

    if (isset($_POST['NoRefresh'])) {
        $mod->NoRefresh = (bool) $_POST['NoRefresh'];
    }

    FilterMeta($mod);

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostModule_Core'] as $fpname => &$fpsignal) {
        $fpname($mod);
    }

    FilterModule($mod);

    //不能新存themeinclude
    if ($mod->SourceType == 'themeinclude') {
        $f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $mod->FileName . '.php';
        if (!file_exists($f)) {
            return false;
        }
    }

    $mod->Save();
    $zbp->AddCache($mod);

    if ((int) GetVars('ID', 'POST') > 0) {
        $zbp->AddBuildModule($mod->FileName);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_PostModule_Succeed'] as $fpname => &$fpsignal) {
        $fpname($mod);
    }

    return $mod;
}

/**
 * 删除模块.
 *
 * @return bool
 */
function DelModule()
{
    global $zbp;
    if (!$zbp->CheckRights('ModuleDel')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    if (isset($_GET['id'])) {
        $id = (int) GetVars('id', 'GET');
        $mod = $zbp->GetModuleByID($id);
        if ($mod->Source != 'system' && $mod->ID != 0) {
            $mod->Del();
            foreach ($GLOBALS['hooks']['Filter_Plugin_DelModule_Succeed'] as $fpname => &$fpsignal) {
                $fpname($mod);
            }
            unset($mod);
            return true;
        }
    } elseif (GetVars('source', 'GET') == 'theme') {
        $fn = GetVars('filename', 'GET');
        if ($fn) {
            $mod = $zbp->GetModuleByFileName($fn);
            if ($mod->FileName == $fn && $mod->ID != 0) {
                $mod->Del();
                foreach ($GLOBALS['hooks']['Filter_Plugin_DelModule_Succeed'] as $fpname => &$fpsignal) {
                    $fpname($mod);
                }
                unset($mod);
                return true;
            }
        }
    }

    return false;
}

//###############################################################################################################

/**
 * 附件上传.
 *
 * @throws Exception
 * @return Upload|false
 */
function PostUpload()
{
    global $zbp;
    if (!$zbp->CheckRights('UploadPst')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    foreach ($_FILES as $key => $value) {
        if ($_FILES[$key]['error'] == 0) {
            if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
                $upload = new Upload();
                $upload->Name = $_FILES[$key]['name'];
                if (GetVars('auto_rename', 'POST') == 'on' || GetVars('auto_rename', 'POST') == true) {
                    $temp_arr = explode(".", $upload->Name);
                    $file_ext = strtolower(trim(array_pop($temp_arr)));
                    $upload->Name = date("YmdHis") . time() . rand(10000, 99999) . '.' . $file_ext;
                }
                $upload->SourceName = $_FILES[$key]['name'];
                $upload->MimeType = $_FILES[$key]['type'];
                $upload->Size = $_FILES[$key]['size'];
                $upload->AuthorID = $zbp->user->ID;

                //检查同月重名
                $d1 = date('Y-m-01', time());
                $d2 = date('Y-m-d', strtotime(date('Y-m-01', time()) . ' +1 month -1 day'));
                $d1 = strtotime($d1);
                $d2 = strtotime($d2);
                $w = array();
                $w[] = array('=', 'ul_Name', $upload->Name);
                $w[] = array('>=', 'ul_PostTime', $d1);
                $w[] = array('<=', 'ul_PostTime', $d2);
                $uploads = $zbp->GetUploadList('*', $w);
                if (count($uploads) > 0) {
                    $zbp->ShowError(28, __FILE__, __LINE__);
                }

                if (!$upload->CheckExtName()) {
                    $zbp->ShowError(26, __FILE__, __LINE__);
                }

                if (!$upload->CheckSize()) {
                    $zbp->ShowError(27, __FILE__, __LINE__);
                }

                $upload->SaveFile($_FILES[$key]['tmp_name']);
                $upload->Save();
                $zbp->AddCache($upload);
            }
        }
    }
    if (isset($upload)) {
        CountMemberArray(array($upload->AuthorID), array(0, 0, 0, +1));

        foreach ($GLOBALS['hooks']['Filter_Plugin_PostUpload_Succeed'] as $fpname => &$fpsignal) {
            $fpname($upload);
        }

        return $upload;
    }

    return false;
}

/**
 * 删除附件.
 *
 * @return bool
 */
function DelUpload()
{
    global $zbp;
    if (!$zbp->CheckRights('UploadDel')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $id = (int) GetVars('id', 'GET');
    $u = $zbp->GetUploadByID($id);
    if ($zbp->CheckRights('UploadAll') || (!$zbp->CheckRights('UploadAll') && $u->AuthorID == $zbp->user->ID)) {
        $u->Del();
        CountMemberArray(array($u->AuthorID), array(0, 0, 0, -1));
        $u->DelFile();
    } else {
        return false;
    }

    return true;
}

//###############################################################################################################

/**
 * 启用插件.
 *
 * @param string $name 插件ID
 *
 * @throws Exception
 *
 * @return string 返回插件ID
 */
function EnablePlugin($name)
{
    global $zbp;

    $app = $zbp->LoadApp('plugin', $name);
    if (($result = $app->CheckCompatibility_Global('Enable')) !== true) {
        $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
    }
    if (($result = $app->CheckCompatibility()) !== true) {
        $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
    }

    $zbp->option['ZC_USING_PLUGIN_LIST'] = AddNameInString($zbp->option['ZC_USING_PLUGIN_LIST'], $name);

    $array = explode('|', $zbp->option['ZC_USING_PLUGIN_LIST']);
    $arrayhas = array();
    foreach ($array as $p) {
        if (is_readable($zbp->usersdir . 'plugin/' . $p . '/plugin.xml')) {
            $arrayhas[] = $p;
        }
    }

    $zbp->option['ZC_USING_PLUGIN_LIST'] = trim(implode('|', $arrayhas), '|');

    $zbp->SaveOption();

    return $name;
}

/**
 * 禁用插件.
 *
 * @param string $name 插件ID
 *
 * @return App|bool
 */
function DisablePlugin($name)
{
    global $zbp;

    $app = $zbp->LoadApp('plugin', $name);
    if (($result = $app->CheckCompatibility_Global('Disable')) !== true) {
        $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
    }

    UninstallPlugin($name);
    $zbp->option['ZC_USING_PLUGIN_LIST'] = DelNameInString($zbp->option['ZC_USING_PLUGIN_LIST'], $name);

    $array = explode('|', $zbp->option['ZC_USING_PLUGIN_LIST']);
    $arrayhas = array();
    foreach ($array as $p) {
        if (is_readable($zbp->usersdir . 'plugin/' . $p . '/plugin.xml')) {
            $arrayhas[] = $p;
        }
    }

    $zbp->option['ZC_USING_PLUGIN_LIST'] = trim(implode('|', $arrayhas), '|');

    $zbp->SaveOption();

    return true;
}

/**
 * 设置当前主题样式.
 *
 * @param string $theme 主题ID
 * @param string $style 样式名
 *
 * @throws Exception
 *
 * @return string 返回主题ID
 */
function SetTheme($theme, $style)
{
    global $zbp;

    $app = $zbp->LoadApp('theme', $theme);
    if (($result = $app->CheckCompatibility_Global('Enable')) !== true) {
        $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
    }

    if (($result = $app->CheckCompatibility()) !== true) {
        $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
    }

    $oldTheme = $zbp->option['ZC_BLOG_THEME'];
    $old = $zbp->LoadApp('theme', $oldTheme);
    if ($theme != $oldTheme) {
        if (($result = $old->CheckCompatibility_Global('Disable')) !== true) {
            $zbp->ShowError($result->getMessage(), __FILE__, __LINE__);
        }
    }

    if ($theme != $oldTheme && $old->isloaded == true) {
        $old->SaveSideBars();
    }

    $zbp->option['ZC_BLOG_THEME'] = $theme;
    if ($style == '') {
        $stylefiles = GetFilesInDir($zbp->usersdir . 'theme/' . $theme . '/style', 'css');
        if (is_array($stylefiles) && count($stylefiles) > 0) {
            $style = key($stylefiles);
        } else {
            $style = 'style';
        }
    }
    $zbp->option['ZC_BLOG_CSS'] = $style;
    if ($theme != $oldTheme) {
        $app->LoadSideBars();
    } else {
        $app->SaveSideBars();
    }

    $zbp->SaveOption();

    //del oldtheme SideBars cache
    $aa = array();
    foreach ($zbp->cache as $key => $value) {
        if (stripos($key, 'sidebars_') !== false) {
            $aa[] = substr($key, 9);
        }
    }
    foreach ($aa as $key => $value) {
        $a = $zbp->LoadApp('theme', $value);
        if ($a->isloaded == false) {
            $zbp->cache->DelKey('sidebars_' . $a->id);
        }
    }

    $zbp->cache->templates_md5_array = '';
    $zbp->cache->templates_md5 = '';
    $zbp->SaveCache();

    if ($oldTheme != $theme) {
        UninstallPlugin($oldTheme);
    }

    return $theme;
}

/**
 * 设置侧栏.
 */
function SetSidebar()
{
    global $zbp;
    for ($i = 1; $i <= 9; $i++) {
        $optionName = $i === 1 ? 'ZC_SIDEBAR_ORDER' : "ZC_SIDEBAR{$i}_ORDER";
        $formName = $i === 1 ? 'sidebar' : "sidebar{$i}";
        if (isset($_POST[$formName])) {
            $zbp->option[$optionName] = trim(GetVars($formName, 'POST'), '|');
        }
    }
    $zbp->SaveOption();
    return true;
}

/**
 * 保存网站设置选项.
 *
 * @throws Exception
 */
function SaveSetting()
{
    global $zbp;

    foreach ($_POST as $key => $value) {
        if (substr($key, 0, 2) !== 'ZC') {
            continue;
        }

        if ($key == 'ZC_PERMANENT_DOMAIN_ENABLE'
            || $key == 'ZC_COMMENT_TURNOFF'
            || $key == 'ZC_COMMENT_REVERSE_ORDER'
            || $key == 'ZC_COMMENT_AUDIT'
            || $key == 'ZC_DISPLAY_SUBCATEGORYS'
            || $key == 'ZC_SYNTAXHIGHLIGHTER_ENABLE'
            || $key == 'ZC_COMMENT_VERIFY_ENABLE'
            || $key == 'ZC_CLOSE_SITE'
            || $key == 'ZC_ADDITIONAL_SECURITY'
            || $key == 'ZC_ARTICLE_THUMB_SWITCH'
            || $key == 'ZC_API_THROTTLE_ENABLE'
            || $key == 'ZC_API_ENABLE'
            || $key == 'ZC_LOGIN_VERIFY_ENABLE'
        ) {
            $zbp->option[$key] = (bool) $value;
            continue;
        }
        if ($key == 'ZC_RSS2_COUNT'
            || $key == 'ZC_UPLOAD_FILESIZE'
            || $key == 'ZC_DISPLAY_COUNT'
            || $key == 'ZC_SEARCH_COUNT'
            || $key == 'ZC_PAGEBAR_COUNT'
            || $key == 'ZC_COMMENTS_DISPLAY_COUNT'
            || $key == 'ZC_MANAGE_COUNT'
            || $key == 'ZC_ARTICLE_THUMB_TYPE'
            || $key == 'ZC_ARTICLE_THUMB_WIDTH'
            || $key == 'ZC_ARTICLE_THUMB_HEIGHT'
            || $key == 'ZC_API_DISPLAY_COUNT'
            || $key == 'ZC_API_THROTTLE_MAX_REQS_PER_MIN'
        ) {
            $zbp->option[$key] = (int) $value;
            continue;
        }
        if ($key == 'ZC_UPLOAD_FILETYPE') {
            $value = strtolower($value);
            $value = str_replace(array(' ','　'), '', $value);
            $value = DelNameInString($value, 'php');
            $value = DelNameInString($value, 'asp');
        }
        //$zbp->option[$key] = trim(str_replace(array("\r", "\n"), array("", ""), $value));
        //这里不拿掉\r和\n了
        $zbp->option[$key] = $value;
    }
    $zbp->option['ZC_DEBUG_MODE'] = (bool) $zbp->option['ZC_DEBUG_MODE'];

    if ($zbp->option['ZC_DEBUG_MODE']) {
        $zbp->option['ZC_DEBUG_MODE'] = true;
        $zbp->option['ZC_DEBUG_MODE_STRICT'] = true;
        $zbp->option['ZC_DEBUG_MODE_WARNING'] = true;
        $zbp->option['ZC_DEBUG_LOG_ERROR'] = true;
    } else {
        $zbp->option['ZC_DEBUG_MODE'] = false;
        $zbp->option['ZC_DEBUG_MODE_STRICT'] = false;
        $zbp->option['ZC_DEBUG_LOG_ERROR'] = false;
    }

    $zbp->option['ZC_BLOG_HOST'] = trim($zbp->option['ZC_BLOG_HOST']);
    $zbp->option['ZC_BLOG_HOST'] = trim($zbp->option['ZC_BLOG_HOST'], '/') . '/';
    if ($zbp->option['ZC_BLOG_HOST'] == '/') {
        $zbp->option['ZC_BLOG_HOST'] = $zbp->host;
    }
    $usePC = false;
    for ($i = 0; $i < (strlen($zbp->option['ZC_BLOG_HOST']) - 1); $i++) {
        $l = substr($zbp->option['ZC_BLOG_HOST'], $i, 1);
        if (ord($l) >= 192) {
            $usePC = true;
        }
    }
    if ($usePC && function_exists('mb_strtolower')) {
        $Punycode = new Punycode();
        $zbp->option['ZC_BLOG_HOST'] = $Punycode->encode($zbp->option['ZC_BLOG_HOST']);
    }
    $lang = include $zbp->usersdir . 'language/' . $zbp->option['ZC_BLOG_LANGUAGEPACK'] . '.php';
    $zbp->option['ZC_BLOG_LANGUAGE'] = $lang['lang'];
    $zbp->option['ZC_BLOG_PRODUCT'] = 'Z-BlogPHP';
    $zbp->SaveOption();

    return true;
}
