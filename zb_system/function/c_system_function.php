<?php
/**
 * 功能型的函数.
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 获取文章.
 *
 * @param mixed $idorname    文章id 或 名称、别名 (1.7支持复杂的array参数,$count为array时后面的参数可以不设)
 * @param array $option |null
 *
 * @return Post
 */
function GetPost($idorname, $option = null)
{
    //新版本的使用说明请看
    //https://wiki.zblogcn.com/doku.php?id=zblogphp:development:functions:getpost
    global $zbp;
    $post = null;
    $id = null;
    $title = null;
    $alias = null;
    $titleoralias = null;

    if (is_array($idorname)) {
        $args = $idorname;
        if (array_key_exists('idorname', $args)) {
            $idorname = $args['idorname'];
        } else {
            $idorname = null;
        }
        if (array_key_exists('id', $args)) {
            $id = $args['id'];
            unset($args['id']);
        }
        if (array_key_exists('title', $args)) {
            $title = $args['title'];
            unset($args['title']);
        }
        if (array_key_exists('alias', $args)) {
            $alias = $args['alias'];
            unset($args['alias']);
        }
        if (array_key_exists('titleoralias', $args)) {
            $titleoralias = $args['titleoralias'];
            unset($args['titleoralias']);
        }
        if (array_key_exists('option', $args)) {
            $option = $args['option'];
            unset($args['option']);
        }
        if (!is_array($option)) {
            $option = array();
        }
        $option = array_merge($args, $option);
        unset($args);
    }

    if (!is_array($option)) {
        $option = array();
    }
    if (!array_key_exists('post_type', $option)) {
        $option['post_type'] = null;
    }
    if (!array_key_exists('post_status', $option)) {
        $option['post_status'] = null;
    }
    if (!array_key_exists('only_article', $option)) {
        $option['only_article'] = false;
    }
    if (!array_key_exists('only_page', $option)) {
        $option['only_page'] = false;
    }
    if (!array_key_exists('where_custom', $option)) {
        $option['where_custom'] = array();
    }
    if (!array_key_exists('order_custom', $option)) {
        $option['order_custom'] = array();
    }

    $w = array();
    if ($option['post_type'] !== null) {
        $w[] = array('=', 'log_Type', (int) $option['post_type']);
    } elseif ($option['only_article'] == true) {
        $w[] = array('=', 'log_Type', 0);
    } elseif ($option['only_page'] == true) {
        $w[] = array('=', 'log_Type', 1);
    }

    if ($option['post_status'] !== null) {
        $w[] = array('=', 'log_Status', (int) $option['post_status']);
    }

    $option2 = $option;
    unset($option2['post_type'], $option2['post_status'], $option2['only_article'], $option2['only_page']);
    unset($option2['order_custom'], $option2['where_custom']);

    if (is_null($id) === false) {
        $w[] = array('=', 'log_ID', (int) $id);
    } elseif (is_null($title) === false) {
        $w[] = array('=', 'log_Title', $title);
    } elseif (is_null($alias) === false) {
        $w[] = array('=', 'log_Alias', $alias);
    } elseif (is_null($titleoralias) === false) {
        $w[] = array('array', array(array('log_Alias', $titleoralias), array('log_Title', $titleoralias)));
    } elseif (is_string($idorname)) {
        $w[] = array('array', array(array('log_Alias', $idorname), array('log_Title', $idorname)));
    } elseif (is_int($idorname)) {
        $w[] = array('=', 'log_ID', (int) $idorname);
    } else {
        $w[] = array('=', 'log_ID', '');
    }

    $select = '';
    $count = 1;

    if (!empty($option['where_custom']) && is_array($option['where_custom'])) {
        foreach ($option['where_custom'] as $key => $value) {
            $w[] = $value;
        }
    }

    $order = array();
    if (!empty($option['order_custom']) && is_array($option['order_custom'])) {
        foreach ($option['order_custom'] as $key => $value) {
            $order[$key] = $value;
        }
    }

    $articles = $zbp->GetPostList($select, $w, $order, $count, $option2);

    if (count($articles) == 0) {
        $post = new Post();
    } else {
        $post = $articles[0];
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_GetPost_Result'] as $fpname => &$fpsignal) {
        $fpreturn = call_user_func($fpname, $post);
    }

    return $post;
}

/**
 * 获取文章列表.
 *
 * @param int  $count  数量 (1.7支持复杂的array参数,$count为array时后面的参数可以不设)
 * @param null $cate   分类ID
 * @param null $auth   用户ID
 * @param null $date   日期
 * @param null $tags   标签
 * @param null $search 搜索关键词
 * @param null $option
 *
 * @return array|mixed
 */
function GetList($count = 10, $cate = null, $auth = null, $date = null, $tags = null, $search = null, $option = null)
{
    //新版本的使用说明请看
    //https://wiki.zblogcn.com/doku.php?id=zblogphp:development:functions:getlist
    global $zbp;
    $args = array();
    if (is_array($count)) {
        $args = $count;
        if (array_key_exists('count', $args)) {
            $count = (int) $args['count'];
            unset($args['count']);
        } else {
            $count = 10;
        }
        if (array_key_exists('category', $args)) {
            $cate = $args['category'];
            unset($args['category']);
        }
        if (array_key_exists('cate', $args)) {
            $cate = $args['cate'];
            unset($args['cate']);
        }
        if (array_key_exists('author', $args)) {
            $auth = $args['author'];
            unset($args['author']);
        }
        if (array_key_exists('auth', $args)) {
            $auth = $args['auth'];
            unset($args['auth']);
        }
        if (array_key_exists('date', $args)) {
            $date = $args['date'];
            unset($args['date']);
        }
        if (array_key_exists('tags', $args)) {
            $tags = $args['tags'];
            unset($args['tags']);
        }
        if (array_key_exists('search', $args)) {
            $search = $args['search'];
            unset($args['search']);
        }
        if (array_key_exists('option', $args)) {
            $option = $args['option'];
            unset($args['option']);
        }
        if (!is_array($option)) {
            $option = array();
        }
        $option = array_merge($args, $option);
        unset($args);
    }

    if (!is_array($option)) {
        $option = array();
    }
    if (!array_key_exists('post_type', $option)) {
        $option['post_type'] = null;
    }
    if (!array_key_exists('post_status', $option)) {
        $option['post_status'] = 0;
    }
    if (!array_key_exists('only_ontop', $option)) {
        $option['only_ontop'] = false;
    }
    if (!array_key_exists('only_not_ontop', $option)) {
        $option['only_not_ontop'] = false;
    }
    if (!array_key_exists('has_subcate', $option)) {
        $option['has_subcate'] = false;
    }
    if (!array_key_exists('where_custom', $option)) {
        $option['where_custom'] = array();
    }
    if (!array_key_exists('order_custom', $option)) {
        $option['order_custom'] = array();
    }
    if (!array_key_exists('is_related', $option)) {
        $option['is_related'] = false;
    }
    if ($option['is_related']) {
        $at = $zbp->GetPostByID($option['is_related']);
        $tags = $at->Tags;
        if (!$tags) {
            return array();
        }
        $count = ($count + 1);
    }

    $option2 = $option;
    unset($option2['post_type'], $option2['post_status'], $option2['only_ontop'], $option2['only_not_ontop']);
    unset($option2['has_subcate'], $option2['is_related'], $option2['order_by_metas']);
    unset($option2['order_custom'], $option2['where_custom']);

    $list = array();
    $post_type = null;
    $w = array();

    if ($option['post_type'] !== null) {
        $post_type = (int) $option['post_type'];
    } else {
        $post_type = 0;
    }
    $w[] = array('=', 'log_Type', $post_type);

    if ($option['post_status'] !== null) {
        $w[] = array('=', 'log_Status', (int) $option['post_status']);
    }

    if ($option['only_ontop'] == true) {
        $w[] = array('>', 'log_IsTop', 0);
    } elseif ($option['only_not_ontop'] == true) {
        $w[] = array('=', 'log_IsTop', 0);
    }

    if (!is_null($cate)) {
        $category = new Category();
        $category = $zbp->GetCategoryByID($cate);

        if ($category->ID > 0) {
            if (!$option['has_subcate']) {
                $w[] = array('=', 'log_CateID', $category->ID);
            } else {
                $arysubcate = array();
                $arysubcate[] = array('log_CateID', $category->ID);
                if (isset($zbp->categories_all[$category->ID])) {
                    foreach ($zbp->categories_all[$category->ID]->ChildrenCategories as $subcate) {
                        $arysubcate[] = array('log_CateID', $subcate->ID);
                    }
                }
                $w[] = array('array', $arysubcate);
            }
        } else {
            return array();
        }
    }

    if (!is_null($auth)) {
        $author = new Member();
        $author = $zbp->GetMemberByID($auth);

        if ($author->ID > 0) {
            $w[] = array('=', 'log_AuthorID', $author->ID);
        } else {
            return array();
        }
    }

    if (!is_null($date)) {
        $datetime = strtotime($date);
        if ($datetime) {
            $datetitle = str_replace(array('%y%', '%m%'), array(date('Y', $datetime), date('n', $datetime)), $zbp->lang['msg']['year_month']);
            $w[] = array('BETWEEN', 'log_PostTime', $datetime, strtotime('+1 month', $datetime));
        } else {
            return array();
        }
    }

    if (!is_null($tags)) {
        $tag = new Tag();
        if (is_array($tags)) {
            $ta = array();
            foreach ($tags as $t) {
                $ta[] = array('log_Tag', '%{' . $t->ID . '}%');
            }
            $w[] = array('array_like', $ta);
            unset($ta);
        } else {
            if (is_int($tags)) {
                $tag = $zbp->GetTagByID($tags);
            } else {
                $tag = $zbp->GetTagByAliasOrName($tags, $post_type);
            }
            if ($tag->ID > 0) {
                $w[] = array('LIKE', 'log_Tag', '%{' . $tag->ID . '}%');
            } else {
                return array();
            }
        }
    }

    if (is_string($search)) {
        $search = trim($search);
        if ($search !== '') {
            $w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $search);
        } else {
            return array();
        }
    }

    $select = '';

    if (!empty($option['where_custom']) && is_array($option['where_custom'])) {
        foreach ($option['where_custom'] as $key => $value) {
            $w[] = $value;
        }
    }

    if (empty($option['order_custom'])) {
        $order = array('log_PostTime' => 'DESC');
    } else {
        $order = array();
        foreach ($option['order_custom'] as $key => $value) {
            $order[$key] = $value;
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_GetList'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($select, $w, $order, $count, $option2);
    }

    $list = $zbp->GetPostList($select, $w, $order, $count, $option2);

    if ($option['is_related']) {
        foreach ($list as $k => $a) {
            if ($a->ID == $option['is_related']) {
                unset($list[$k]);
            }
        }
        if (count($list) == $count) {
            array_pop($list);
        }
    }
    if (isset($option['order_by_metas'])) { //从meta里的值排序
        if (is_array($option['order_by_metas'])) {
            $orderkey = key($option['order_by_metas']);
            $order = current($option['order_by_metas']);
        } else {
            $orderkey = current($option['order_by_metas']);
            $order = 'asc';
        }
        $orderarray = array();
        foreach ($list as $key => $value) {
            $orderarray[$key] = $value->Metas->$orderkey;
        }
        if (strtolower($order) == 'desc') {
            arsort($orderarray);
        } else {
            asort($orderarray);
        }
        $newlist = array();
        foreach ($orderarray as $key => $value) {
            $newlist[] = $list[$key];
        }
        $list = $newlist;
    }


    foreach ($GLOBALS['hooks']['Filter_Plugin_GetList_Result'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($list);
    }

    return $list;
}

//###############################################################################################################

/**
 * 显示404页面(内置插件函数).
 *
 * 可通过主题中的404.php模板自定义显示效果
 *
 * @param $errorCode
 * @param $errorDescription
 * @param $file
 * @param $line
 *
 * @api Filter_Plugin_Zbp_ShowError
 *
 * @throws Exception
 */
function Include_ShowError404($errorCode, $errorDescription, $file, $line)
{
    global $zbp;
    if (!in_array("Status: 404 Not Found", headers_list())) {
        return;
    }

    $GLOBALS['hooks']['Filter_Plugin_Zbp_ShowError']['Include_ShowError404'] = PLUGIN_EXITSIGNAL_RETURN;

    $zbp->template->SetTags('title', $zbp->title);
    $zbp->template->SetTemplate('404');
    $zbp->template->Display();

    if (IS_CLI && (IS_WORKERMAN || IS_SWOOLE)) {
        return true;
    }

    exit;
}

/**
 * 输出后台指定字体family(内置插件函数).
 */
function Include_AddonAdminFont()
{
    global $zbp;
    $f = $s = '';
    if (isset($zbp->lang['font_family']) && trim($zbp->lang['font_family'])) {
        $f = 'font-family:' . $zbp->lang['font_family'] . ';';
    }

    if (isset($zbp->lang['font_size']) && trim($zbp->lang['font_size'])) {
        $s = 'font-size:' . $zbp->lang['font_size'] . ';';
    }

    if ($f || $s) {
        echo '<style type="text/css">body{' . $s . $f . '}</style>';
    }
}

/**
 * 批处理文章
 *
 * @param int $type
 */
function Include_BatchPost_Article($type)
{
    global $zbp;
    if ($type != ZC_POST_TYPE_ARTICLE) {
        return;
    }
    if (!isset($_POST['id'])) {
        return;
    }
    $arrayid = $_POST['id'];
    foreach ($arrayid as $key => $value) {
        $id = (int) $value;
        $article = new Post();
        $article = $zbp->GetPostByID($id);
        if ($article->ID > 0) {
            if (!$zbp->CheckRights('ArticleAll') && $article->AuthorID != $zbp->user->ID) {
                continue;
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

            foreach ($GLOBALS['hooks']['Filter_Plugin_DelArticle_Succeed'] as $fpname => &$fpsignal) {
                $fpname($article);
            }
        }
    }
    $zbp->AddBuildModule('previous');
    $zbp->AddBuildModule('calendar');
    $zbp->AddBuildModule('comments');
    $zbp->AddBuildModule('archives');
    $zbp->AddBuildModule('tags');
    $zbp->AddBuildModule('authors');

    return true;
}

/**
 * 批处理页面
 *
 * @param int $type
 */
function Include_BatchPost_Page($type)
{
    global $zbp;
    if ($type != ZC_POST_TYPE_PAGE) {
        return;
    }
    if (!isset($_POST['id'])) {
        return;
    }
    $arrayid = $_POST['id'];
    foreach ($arrayid as $key => $value) {
        $id = (int) $value;
        $article = new Post();
        $article = $zbp->GetPostByID($id);
        if ($article->ID > 0) {
            if (!$zbp->CheckRights('PageAll') && $article->AuthorID != $zbp->user->ID) {
                continue;
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
    }
    return true;
}

/**
 * 首页index.php的结尾处理
 */
function Include_Index_End()
{
    global $zbp;
    if ($zbp->option['ZC_RUNINFO_DISPLAY'] == true) {
        RunTime();
    }
}

/**
 * 首页index.php的开头处理
 */
function Include_Index_Begin()
{
    global $zbp;
    $zbp->CheckSiteClosed();

    if ($zbp->template->hasTemplate('404')) {
        Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'Include_ShowError404');
    }

    if ($zbp->option['ZC_ADDITIONAL_SECURITY']) {
        header('X-XSS-Protection: 1; mode=block');
        if ($zbp->isHttps) {
            header('Upgrade-Insecure-Requests: 1');
        }
    }
}

/**
 * “审核中会员”的前台权限拒绝验证
 */
function Include_Frontend_CheckRights($action, $level)
{
    global $zbp;
    if ($zbp->user->Status == ZC_MEMBER_STATUS_AUDITING) {
        if (!in_array($action, array('login', 'logout', 'misc', 'feed', 'ajax', 'verify', 'NoValidCode', 'MemberEdt', 'MemberPst', 'MemberMng'))) {
            $GLOBALS['hooks']['Filter_Plugin_Zbp_CheckRights']['Include_Frontend_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;
            return false;
        }
        if ($zbp->option['ZC_ALLOW_AUDITTING_MEMBER_VISIT_MANAGE'] == false && $action == 'admin') {
            $GLOBALS['hooks']['Filter_Plugin_Zbp_CheckRights']['Include_Frontend_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;
            return false;
        }
    }
}

/**
 * 在ViewList,ViewPost中对view权限进行验证
 */
function Include_ViewListPost_CheckRights_View($route)
{
    global $zbp;
    if (is_array($route)) {
        $posttype = GetValueInArray($route, 'posttype', 0);
        //没权限就返回
        $actions = $zbp->GetPostType($posttype, 'actions');
        if (!$zbp->CheckRights($actions['view'])) {
            SetPluginSignal('Filter_Plugin_ViewList_Begin_V2', __FUNCTION__, PLUGIN_EXITSIGNAL_RETURN);
            SetPluginSignal('Filter_Plugin_ViewPost_Begin_V2', __FUNCTION__, PLUGIN_EXITSIGNAL_RETURN);
            return false;
        }
    }
}

//###############################################################################################################

/**
 * 过滤扩展数据.
 *
 * @param $object
 */
function FilterMeta(&$object)
{
    //$type=strtolower(get_class($object));

    foreach ($_POST as $key => $value) {
        if (substr($key, 0, 5) == 'meta_') {
            $name = substr($key, (5 - strlen($key)));
            $object->Metas->$name = $value;
        }
    }

    foreach ($object->Metas->GetData() as $key => $value) {
        if ($value == '') {
            $object->Metas->Del($key);
        }
    }
}

/**
 * 过滤评论数据.
 *
 * @param $comment
 *
 * @throws Exception
 */
function FilterComment(&$comment)
{
    global $zbp;

    if (!CheckRegExp($comment->Name, '[nickname]')) {
        $zbp->ShowError(15, __FILE__, __LINE__);
    }
    if ($comment->Email && (!CheckRegExp($comment->Email, '[email]'))) {
        $zbp->ShowError(29, __FILE__, __LINE__);
    }
    if ($comment->HomePage && (!CheckRegExp($comment->HomePage, '[homepage]'))) {
        $zbp->ShowError(30, __FILE__, __LINE__);
    }

    $comment->Name = FormatString($comment->Name, '[nohtml]');
    $comment->Name = str_replace(array('<', '>', ' ', '　'), '', $comment->Name);
    $comment->Name = SubStrUTF8_Start($comment->Name, 0, $zbp->option['ZC_USERNAME_MAX']);
    $comment->Email = SubStrUTF8_Start($comment->Email, 0, $zbp->option['ZC_EMAIL_MAX']);
    $comment->HomePage = SubStrUTF8_Start($comment->HomePage, 0, $zbp->option['ZC_HOMEPAGE_MAX']);

    $comment->Content = FormatString($comment->Content, '[nohtml]');

    $comment->Content = SubStrUTF8_Start($comment->Content, 0, 1000);
    $comment->Content = trim($comment->Content);
    if (strlen($comment->Content) == 0) {
        $zbp->ShowError(46, __FILE__, __LINE__);
    }
}

/**
 * 过滤文章数据.
 *
 * @param $article
 */
function FilterPost(&$article)
{
    global $zbp;

    $article->Title = strip_tags($article->Title);
    $article->Title = htmlspecialchars($article->Title);
    $article->Alias = FormatString($article->Alias, '[normalname]');
    $article->Alias = str_replace(' ', '', $article->Alias);
    $article->Alias = str_replace('　', '', $article->Alias);

    if ($article->Type == ZC_POST_TYPE_ARTICLE) {
        if (!$zbp->CheckRights('ArticleAll')) {
            $article->Content = FormatString($article->Content, '[noscript]');
            $article->Intro = FormatString($article->Intro, '[noscript]');
        }
    } elseif ($article->Type == ZC_POST_TYPE_PAGE) {
        if (!$zbp->CheckRights('PageAll')) {
            $article->Content = FormatString($article->Content, '[noscript]');
            $article->Intro = FormatString($article->Intro, '[noscript]');
        }
    } else {
        if (!$zbp->CheckRights('ArticleAll')) {
            $article->Content = FormatString($article->Content, '[noscript]');
            $article->Intro = FormatString($article->Intro, '[noscript]');
        }
    }
}

/**
 * 过滤用户数据.
 *
 * @param $member
 *
 * @throws Exception
 */
function FilterMember(&$member)
{
    global $zbp;
    $member->Intro = FormatString($member->Intro, '[noscript]');
    $member->Alias = FormatString($member->Alias, '[normalname]');
    $member->Alias = str_replace(array('/', '.', ' ', '　', '_'), '', $member->Alias);
    $member->Alias = SubStrUTF8_Start($member->Alias, 0, (int) $zbp->datainfo['Member']['Alias'][2]);
    if (Zbp_StrLen($member->Name) < $zbp->option['ZC_USERNAME_MIN'] || Zbp_StrLen($member->Name) > $zbp->option['ZC_USERNAME_MAX']) {
        $zbp->ShowError(77, __FILE__, __LINE__);
    }

    if (!CheckRegExp($member->Name, '[username]')) {
        $zbp->ShowError(77, __FILE__, __LINE__);
    }

    if ($member->Alias != '' && !CheckRegExp($member->Alias, '[nickname]')) {
        $zbp->ShowError(90, __FILE__, __LINE__);
    }

    if (!CheckRegExp($member->Email, '[email]')) {
        $member->Email = 'null@null.com';
    }
    $member->Email = strtolower($member->Email);

    if (substr($member->HomePage, 0, 4) != 'http') {
        $member->HomePage = 'http://' . $member->HomePage;
    }

    if (!CheckRegExp($member->HomePage, '[homepage]')) {
        $member->HomePage = '';
    }

    if (strlen($member->Email) > $zbp->option['ZC_EMAIL_MAX']) {
        $zbp->ShowError(29, __FILE__, __LINE__);
    }

    if (strlen($member->HomePage) > $zbp->option['ZC_HOMEPAGE_MAX']) {
        $zbp->ShowError(30, __FILE__, __LINE__);
    }
}

/**
 * 过滤模块数据.
 *
 * @param $module
 */
function FilterModule(&$module)
{
    global $zbp;
    $module->FileName = FormatString($module->FileName, '[filename]');
    $module->HtmlID = FormatString($module->HtmlID, '[normalname]');
}

/**
 * 过滤分类数据.
 *
 * @param $category
 */
function FilterCategory(&$category)
{
    global $zbp;
    $category->Name = strip_tags($category->Name);
    $category->Name = trim($category->Name);
    $category->Alias = FormatString($category->Alias, '[normalname]');
    //$category->Alias=str_replace('/','',$category->Alias);
    $category->Alias = str_replace('.', '', $category->Alias);
    $category->Alias = str_replace(' ', '', $category->Alias);
    $category->Alias = str_replace('_', '', $category->Alias);
    $category->Alias = trim($category->Alias);
}

/**
 * 过滤tag数据.
 *
 * @param $tag
 */
function FilterTag(&$tag)
{
    global $zbp;
    $tag->Name = strip_tags($tag->Name);
    $tag->Name = trim($tag->Name);
    $tag->Alias = FormatString($tag->Alias, '[normalname]');
    $tag->Alias = str_replace('.', '', $tag->Alias);
    $tag->Alias = str_replace(' ', '', $tag->Alias);
    $tag->Alias = str_replace('_', '', $tag->Alias);
    $tag->Alias = trim($tag->Alias);
}

//###############################################################################################################
//统计函数

/**
 *统计置顶文章数组.
 *
 * @param int  $type
 * @param null $addplus
 * @param null $delplus
 */
function CountTopPost($type = 0, $addplus = null, $delplus = null)
{
    global $zbp;
    $varname = 'top_post_array_' . $type;
    @$array = unserialize($zbp->cache->$varname);
    if (!is_array($array)) {
        $array = array();
    }

    if ($addplus === null && $delplus === null) {
        $s = $zbp->db->sql->Select($zbp->table['Post'], 'log_ID', array(array('=', 'log_Type', $type), array('>', 'log_IsTop', 0), array('=', 'log_Status', 0)), null, null, null);
        $a = $zbp->db->Query($s);
        foreach ($a as $id) {
            $array[(int) current($id)] = (int) current($id);
        }
    } elseif ($addplus !== null && $delplus === null) {
        $addplus = (int) $addplus;
        $array[$addplus] = $addplus;
    } elseif ($addplus === null && $delplus !== null) {
        $delplus = (int) $delplus;
        unset($array[$delplus]);
    }

    $zbp->cache->$varname = serialize($array);
}

/**
 *统计评论数.
 *
 * @param int $allplus 控制是否要进行全表扫描 总评论
 * @param int $chkplus 控制是否要进行全表扫描 未审核评论
 */
function CountCommentNums($allplus = null, $chkplus = null)
{
    global $zbp;

    if ($allplus === null) {
        $zbp->cache->all_comment_nums = (int) GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Comment'])->count(array('*' => 'num'))->query, 'num');
    } else {
        $zbp->cache->all_comment_nums += $allplus;
    }
    if ($chkplus === null) {
        $zbp->cache->check_comment_nums = (int) GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Comment'])->count(array('*' => 'num'))->where('=', 'comm_Ischecking', '1')->query, 'num');
    } else {
        $zbp->cache->check_comment_nums += $chkplus;
    }
    $zbp->cache->normal_comment_nums = (int) ($zbp->cache->all_comment_nums - $zbp->cache->check_comment_nums);
}

/**
 *统计公开文章数.
 *
 * @param int $plus 控制是否要进行全表扫描
 */
function CountNormalArticleNums($plus = null)
{
    global $zbp;

    if ($plus === null) {
        $s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', 0), array('=', 'log_IsTop', 0), array('=', 'log_Status', 0)));
        $num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

        $zbp->cache->normal_article_nums = $num;
    } else {
        $zbp->cache->normal_article_nums += $plus;
    }
}

/**
 * 统计文章下评论数.
 *
 * @param post $article
 * @param int  $plus    控制是否要进行全表扫描
 */
function CountPost(&$article, $plus = null)
{
    global $zbp;

    if ($plus === null) {
        $id = $article->ID;

        $s = $zbp->db->sql->Count($zbp->table['Comment'], array(array('COUNT', '*', 'num')), array(array('=', 'comm_LogID', $id), array('=', 'comm_IsChecking', 0)));
        $num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

        $article->CommNums = $num;
    } else {
        $article->CommNums += $plus;
    }
}

/**
 * 批量统计指定文章下评论数并保存.
 *
 * @param array $array 记录文章ID的数组
 * @param int   $plus  控制是否要进行全表扫描
 * @param int      $type      post和category的分类Type
 */
function CountPostArray($array, $plus = null, $type = 0)
{
    global $zbp;
    $array = array_unique($array);
    foreach ($array as $value) {
        if ($value == 0) {
            continue;
        }

        $article = $zbp->GetPostByID($value);
        if ($article->ID > 0) {
            CountPost($article, $plus, $type);
            $article->Save();
        }
    }
}

/**
 * 统计分类下文章数.
 *
 * @param Category &$category
 * @param int      $plus      控制是否要进行全表扫描
 * @param int      $type      post和category的分类Type
 */
function CountCategory(&$category, $plus = null, $type = 0)
{
    global $zbp;

    if ($plus === null) {
        $id = $category->ID;

        $s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_Type', $type), array('=', 'log_Status', 0), array('=', 'log_CateID', $id)));
        $num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

        $category->Count = $num;
    } else {
        $category->Count += $plus;
    }
}

/**
 * 批量统计指定分类下文章数并保存.
 *
 * @param array $array 记录分类ID的数组
 * @param int   $plus  控制是否要进行全表扫描
 * @param int   $type  post和category的分类Type
 */
function CountCategoryArray($array, $plus = null, $type = 0)
{
    global $zbp;
    $array = array_unique($array);
    foreach ($array as $value) {
        if ($value == 0) {
            continue;
        }
        if (isset($zbp->categories_all[$value])) {
            CountCategory($zbp->categories_all[$value], $plus, $type);
            $zbp->categories_all[$value]->Save();
        }
    }
}

/**
 * 统计tag下的文章数.
 *
 * @param tag &$tag
 * @param int $plus 控制是否要进行全表扫描
 * @param int $type post和tag的分类Type
 */
function CountTag(&$tag, $plus = null, $type = 0)
{
    global $zbp;

    if ($plus === null) {
        $id = $tag->ID;
        $w = array();
        $w[] = array('=', 'log_Type', $type);
        $w[] = array('LIKE', 'log_Tag', '%{' . $id . '}%');
        $s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), $w);
        $num = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');

        $tag->Count = $num;
    } else {
        $tag->Count += $plus;
    }
}

/**
 * 批量统计指定tag下文章数并保存.
 *
 * @param string $string 类似'{1}{2}{3}{4}{4}'的tagID串
 * @param int    $plus   控制是否要进行全表扫描
 * @param int    $articleid   暂没发现有用处的参数
 *
 * @return bool
 */
function CountTagArrayString($string, $plus = null, $articleid = null)
{
    global $zbp;
    /* @var Tag[] $array */
    $array = $zbp->LoadTagsByIDString($string);

    //添加大数据接口,tag,plus,id
    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_CountTagArray'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($array, $plus, $articleid);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    foreach ($array as &$tag) {
        CountTag($tag, $plus, $tag->Type);
        $tag->Save();
    }

    return true;
}

/**
 * 统计用户下的文章数、页面数、评论数、附件数等.
 *
 * @param $member
 * @param array $plus 设置是否需要完全全表扫描
 */
function CountMember(&$member, $plus = array(null, null, null, null))
{
    global $zbp;
    if (!($member instanceof Member)) {
        return;
    }

    $id = $member->ID;

    if ($plus[0] === null) {
        $s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_AuthorID', $id), array('=', 'log_Type', 0)));
        $member_Articles = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');
        $member->Articles = $member_Articles;
    } else {
        $member->Articles += $plus[0];
    }

    if ($plus[1] === null) {
        $s = $zbp->db->sql->Count($zbp->table['Post'], array(array('COUNT', '*', 'num')), array(array('=', 'log_AuthorID', $id), array('=', 'log_Type', 1)));
        $member_Pages = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');
        $member->Pages = $member_Pages;
    } else {
        $member->Pages += $plus[1];
    }

    if ($plus[2] === null) {
        if ($member->ID > 0) {
            $s = $zbp->db->sql->Count($zbp->table['Comment'], array(array('COUNT', '*', 'num')), array(array('=', 'comm_AuthorID', $id), array('=', 'comm_IsChecking', 0)));
            $member_Comments = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');
            $member->Comments = $member_Comments;
        }
    } else {
        $member->Comments += $plus[2];
    }

    if ($plus[3] === null) {
        $s = $zbp->db->sql->Count($zbp->table['Upload'], array(array('COUNT', '*', 'num')), array(array('=', 'ul_AuthorID', $id)));
        $member_Uploads = GetValueInArrayByCurrent($zbp->db->Query($s), 'num');
        $member->Uploads = $member_Uploads;
    } else {
        $member->Uploads += $plus[3];
    }
}

/**
 * 批量统计指定用户数据并保存.
 *
 * @param array $array 记录用户ID的数组
 * @param array $plus  设置是否需要完全全表扫描
 */
function CountMemberArray($array, $plus = array(null, null, null, null))
{
    global $zbp;
    $array = array_unique($array);
    foreach ($array as $value) {
        if ($value == 0) {
            continue;
        }

        if (isset($zbp->members[$value])) {
            CountMember($zbp->members[$value], $plus);
            $zbp->members[$value]->Save();
        }
    }
}

//###############################################################################################################

/**
 * BuildModule_catalog
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_catalog()
{
    return ModuleBuilder::Catalog();
}

/**
 * BuildModule_calendar
 *
 * @param string $date
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_calendar($date = '')
{
    return ModuleBuilder::Calendar($date);
}

/**
 * BuildModule_comments
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_comments()
{
    return ModuleBuilder::Comments();
}

/**
 * BuildModule_previous
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_previous()
{
    return ModuleBuilder::LatestArticles();
}

/**
 * BuildModule_archives
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_archives()
{
    return ModuleBuilder::Archives();
}

/**
 * BuildModule_navbar
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_navbar()
{
    return ModuleBuilder::Navbar();
}

/**
 * BuildModule_tags
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_tags()
{
    return ModuleBuilder::TagList();
}

/**
 * BuildModule_authors
 *
 * @param int $level
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_authors($level = 4)
{
    return ModuleBuilder::Authors($level);
}

/**
 * BuildModule_statistics
 *
 * @param array $array
 *
 * @deprecated
 *
 * @throws Exception
 *
 * @return string
 */
function BuildModule_statistics($array = array())
{
    return ModuleBuilder::Statistics($array);
}

/**
 * 消除16升级17又退回16后再升级17出的bug;
 */
function Fix_16_to_17_and_17_to_16_Error()
{
    global $zbp;
    $result = $zbp->db->Query("SELECT conf_Name, COUNT(conf_Name) FROM {$zbp->table['Config']} GROUP BY conf_Name");
    $config_list = array();

    foreach ($result as $r) {
        if (is_array($r)) {
            $config_list[current($r)] = next($r);
        }
    }

    foreach ($config_list as $k => $v) {
        if ($config_list[$k] == 1) {
            unset($config_list[$k]);
        }
    }

    if (count($config_list) < 1) {
        return;
    }

    foreach ($config_list as $k => $v) {
        $result = $zbp->db->Query("SELECT conf_Value FROM {$zbp->table['Config']} WHERE conf_Name = '{$k}' LIMIT 1");
        if (is_array($result) && is_array($result[0])) {
            $config_list[$k] = current($result[0]);
        }
    }

    foreach ($config_list as $k => $v) {
        $zbp->db->Delete("DELETE FROM {$zbp->table['Config']} WHERE conf_Name = '{$k}'");
        $zbp->db->Insert("INSERT INTO {$zbp->table['Config']} (conf_Name,conf_Value) VALUES ( '{$k}' , '" . $zbp->db->EscapeString($v) . "' )");
    }

    die;
}
