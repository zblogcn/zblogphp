<?php

/**
 * XML-RPC接口.
 *
 *
 * https://codex.wordpress.org/XML-RPC_WordPress_API
 * https://codex.wordpress.org/XML-RPC_MetaWeblog_API
 * http://codex.wordpress.org.cn/XML-RPC_MetaWeblog_API
 */
define('ZBP_IN_XMLRPC', true);

require '../function/c_system_base.php';

if (isset($_GET['rsd'])) {
    header('Content-Type: text/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">' . "\n";
    echo '  <service>' . "\n";
    echo '    <engineName>Z-BlogPHP</engineName>' . "\n";
    echo '    <engineLink>http://www.zblogcn.com/</engineLink>' . "\n";
    echo '    <homePageLink>' . $zbp->host . '</homePageLink>' . "\n";
    echo '    <apis>' . "\n";
    echo '      <api name="WordPress" blogID="1" preferred="true" apiLink="' . $zbp->xmlrpcurl . '" />' . "\n";
    echo '      <api name="Movable Type" blogID="1" preferred="false" apiLink="' . $zbp->xmlrpcurl . '" />' . "\n";
    echo '      <api name="MetaWeblog" blogID="1" preferred="false" apiLink="' . $zbp->xmlrpcurl . '" />' . "\n";
    echo '      <api name="Blogger" blogID="1" preferred="false" apiLink="' . $zbp->xmlrpcurl . '" />' . "\n";
    echo '    </apis>' . "\n";
    echo '  </service>' . "\n";
    echo '</rsd>' . "\n";

    die();
}

/**
 * XML-RPC 获取用户站点基本信息.
 *
 * 输出用户站点地址,guid,网站名xml
 * array
 * struct
 * string blogid
 * string url: Homepage URL for this blog.
 * string blogName
 * bool isAdmin
 * string xmlrpc: URL endpoint to use for XML-RPC requests on this blog.
 */
function xmlrpc_getUsersBlogs()
{
    global $zbp;

    $strXML = '<?xml version="1.0" encoding="UTF-8"?><methodResponse><params><param><value><array><data><value><struct><member><name>url</name><value><string>$%#1#%$</string></value></member><member><name>blogid</name><value><string>$%#2#%$</string></value></member><member><name>blogName</name><value><string>$%#3#%$</string></value></member></struct></value></data></array></value></param></params></methodResponse>';

    $strXML = str_replace("$%#1#%$", htmlspecialchars($zbp->host), $strXML);
    $strXML = str_replace("$%#2#%$", htmlspecialchars(md5($zbp->guid . sha1($zbp->path))), $strXML);
    $strXML = str_replace("$%#3#%$", htmlspecialchars($zbp->name), $strXML);

    echo $strXML;
}

/*
array
struct
string blogid
string blogName
string url
string xmlrpc: XML-RPC endpoint for the blog.
bool isAdmin
*/
function xmlrpc_wp_getUsersBlogs()
{
    global $zbp;

    $strXML = '<?xml version="1.0" encoding="UTF-8"?><methodResponse><params><param><value><array><data><value><struct>
<member><name>isAdmin</name><value><boolean>$%#1#%$</boolean></value></member>
<member><name>url</name><value><string>$%#2#%$</string></value></member>
<member><name>blogid</name><value><string>$%#3#%$</string></value></member>
<member><name>blogName</name><value><string>$%#4#%$</string></value></member>
<member><name>xmlrpc</name><value><string>$%#5#%$</string></value></member>
</struct></value></data></array></value></param></params></methodResponse>';
    $strXML = str_replace("$%#1#%$", $zbp->user->Level === 1, $strXML);
    $strXML = str_replace("$%#2#%$", htmlspecialchars($zbp->host), $strXML);
    $strXML = str_replace("$%#3#%$", htmlspecialchars(md5($zbp->guid . sha1($zbp->path))), $strXML);
    $strXML = str_replace("$%#4#%$", htmlspecialchars($zbp->name), $strXML);
    $strXML = str_replace("$%#5#%$", $zbp->host . 'zb_system/xml-rpc/index.php', $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取分类列表.
 *
 * 输出分类列表xml
 * wp.getCategories
 * array
 * struct
 * string categoryId
 * string parentId
 * string categoryName
 * string categoryDescription
 * string description: Name of the category, equivalent to categoryName.
 * string htmlUrl
 * string rssUrl
 */
function xmlrpc_getCategories()
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>categoryId</name><value><string>$%#1#%$</string></value></member>
<member><name>parentId</name><value><string>$%#2#%$</string></value></member>
<member><name>categoryName</name><value><string>$%#3#%$</string></value></member>
<member><name>description</name><value><string>$%#4#%$</string></value></member>
<member><name>httpUrl</name><value><string>$%#5#%$</string></value></member>
<member><name>categoryDescription</name><value><string>$%#6#%$</string></value></member>
</struct></value>';

    $strAll = '';

    foreach ($zbp->categoriesbyorder as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->ParentID), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->Name), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Intro), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->Name), $s);

        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取标签列表.
 *
 * 输出标签列表xml
 * wp.getTags
 * array
 * struct
 * int tag_id
 * string name
 * string slug
 * int count
 * string html_url
 * string rss_url
 */
function xmlrpc_getTags()
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>tag_id</name><value><string>$%#1#%$</string></value></member>
<member><name>name</name><value><string>$%#2#%$</string></value></member>
<member><name>count</name><value><string>$%#3#%$</string></value></member>
<member><name>slug</name><value><string>$%#4#%$</string></value></member>
<member><name>html_url</name><value><string>$%#5#%$</string></value></member>
<member><name>rss_url</name><value><string>$%#6#%$</string></value></member>
</struct></value>';

    $strAll = '';

    $array = $zbp->GetTagList(
        '',
        '',
        array('tag_Count' => 'DESC', 'tag_ID' => 'ASC'),
        array(50),
        ''
    );

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->Name), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->Count), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->Url), $s);

        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取用户列表.
 *
 * 输出用户列表xml
 * wp.getAuthors
 * array
 * struct
 * string user_id
 * string user_login
 * string display_name
 */
function xmlrpc_getAuthors()
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>user_id</name><value><string>$%#1#%$</string></value></member>
<member><name>user_login</name><value><string>$%#2#%$</string></value></member>
<member><name>display_name</name><value><string>$%#3#%$</string></value></member>
</struct></value>';

    $strAll = '';

    foreach ($zbp->members as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->Name), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->Alias), $s);
        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取指定用户创建的页面.
 *
 * 输出页面列表xml
 *
 * @param int $n 用户ID
 */
function xmlrpc_getPages($n)
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#1#%$</dateTime.iso8601></value></member>
<member><name>userid</name><value><string>$%#2#%$</string></value></member>
<member><name>page_id</name><value><int>$%#3#%$</int></value></member>
<member><name>page_status</name><value><string>$%#4#%$</string></value></member>
<member><name>description</name><value><string>$%#5#%$</string></value></member>
<member><name>title</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_allow_comments</name><value><int>$%#9#%$</int></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
</struct></value>';

    $strAll = '';

    $w = array();
    if (!$zbp->CheckRights('PageAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }

    $array = $zbp->GetPageList(
        '',
        $w,
        array('log_PostTime' => 'DESC'),
        array($n),
        ''
    );

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->Time('c')), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->AuthorID), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Status == 0 ? 'publish' : 'draft'), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->Content), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->Title), $s);
        $s = str_replace("$%#7#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#8#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#9#%$", htmlspecialchars($value->IsLock ? '2' : '1'), $s);
        $s = str_replace("$%#10#%$", htmlspecialchars($value->Alias), $s);

        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取指定ID页面.
 *
 * 输出页面列表xml
 *
 * @param int $id 页面ID
 */
function xmlrpc_getPage($id)
{
    global $zbp;

    $strXML = '<methodResponse><params><param>$%#1#%$</param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#1#%$</dateTime.iso8601></value></member>
<member><name>userid</name><value><string>$%#2#%$</string></value></member>
<member><name>page_id</name><value><int>$%#3#%$</int></value></member>
<member><name>page_status</name><value><string>$%#4#%$</string></value></member>
<member><name>description</name><value><string>$%#5#%$</string></value></member>
<member><name>title</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_allow_comments</name><value><int>$%#9#%$</int></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
</struct></value>';

    $strAll = '';

    $article = new Post();
    $article->LoadInfoByID($id);
    if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('PageAll'))) {
        xmlrpc_ShowError(11, __FILE__, __LINE__);
    }

    $array = array();
    $array[] = $article;

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->Time('c')), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->AuthorID), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Status == 0 ? 'publish' : 'draft'), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->Content), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->Title), $s);
        $s = str_replace("$%#7#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#8#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#9#%$", htmlspecialchars($value->IsLock ? '2' : '1'), $s);
        $s = str_replace("$%#10#%$", htmlspecialchars($value->Alias), $s);

        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取指定用户最新文章列表.
 *
 * 输出文章列表xml
 *
 * @param int $n 用户ID
 */
function xmlrpc_getRecentPosts($n)
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>title</name><value><string>$%#1#%$</string></value></member>
<member><name>description</name><value><string>$%#2#%$</string></value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
<member><name>categories</name><value><array><data><value><string>$%#4#%$</string></value></data></array></value></member>
<member><name>postid</name><value><string>$%#5#%$</string></value></member>
<member><name>userid</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_keywords</name><value><string>$%#9#%$</string></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
<member><name>mt_excerpt</name><value><string>$%#11#%$</string></value></member>
<member><name>mt_text_more</name><value><string>$%#12#%$</string></value></member>
<member><name>mt_basname</name><value><string>$%#13#%$</string></value></member>
<member><name>mt_allow_comments</name><value><int>$%#14#%$</int></value></member>
<member><name>page_status</name><value><string>$%#15#%$</string></value></member>
</struct></value>';

    $strAll = '';

    $w = array();
    if (!$zbp->CheckRights('ArticleAll')) {
        $w[] = array('=', 'log_AuthorID', $zbp->user->ID);
    }

    $array = $zbp->GetArticleList(
        '',
        $w,
        array('log_PostTime' => 'DESC'),
        array($n),
        ''
    );

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $description = '';
        $mt_excerpt = '';
        $mt_text_more = '';
        if (strpos($value->Content, '<!--more-->') !== false) {
            $description = GetValueInArray(explode('<!--more-->', $value->Content), 1);
            $mt_text_more = GetValueInArray(explode('<!--more-->', $value->Content), 0);
            //$description=$value->Content;
        } else {
            $description = $value->Content;
            $mt_excerpt = $value->Intro;
        }

        $s = str_replace("$%#1#%$", htmlspecialchars($value->Title), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($description), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->Time('c')), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Category->Name), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->AuthorID), $s);
        $s = str_replace("$%#7#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#8#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#9#%$", htmlspecialchars($value->TagsToNameString()), $s);
        $s = str_replace("$%#10#%$", htmlspecialchars($value->Alias), $s);
        $s = str_replace("$%#11#%$", htmlspecialchars($mt_excerpt), $s);
        $s = str_replace("$%#12#%$", htmlspecialchars($mt_text_more), $s);
        $s = str_replace("$%#13#%$", htmlspecialchars($value->Alias), $s);
        $s = str_replace("$%#14#%$", htmlspecialchars($value->IsLock ? '2' : '1'), $s);
        $s = str_replace("$%#15#%$", htmlspecialchars($value->Status == 0 ? 'publish' : 'draft'), $s);
        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 删除页面.
 *
 * 输出操作结果
 *
 * @param int $id 页面ID
 *
 * @throws Exception
 */
function xmlrpc_delPage($id)
{
    $strXML = '<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';

    $_GET['id'] = $id;

    if (DelPage() == true) {
        $strXML = str_replace("$%#1#%$", 1, $strXML);
        echo $strXML;
    } else {
        xmlrpc_ShowError(0, __FILE__, __LINE__);
    }
}

/**
 * XML-RPC 删除文章.
 *
 * 输出操作结果
 *
 * @param int $id 文章ID
 *
 * @throws Exception
 */
function xmlrpc_deletePost($id)
{
    $strXML = '<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';

    $_GET['id'] = $id;

    if (DelArticle() == true) {
        $strXML = str_replace("$%#1#%$", 1, $strXML);
        echo $strXML;
    } else {
        xmlrpc_ShowError(0, __FILE__, __LINE__);
    }
}

/**
 * XML-RPC 获取指定文章.
 *
 * 输出文章数据xml
 *
 * @param int $id 文章ID
 */
function xmlrpc_getPost($id)
{
    global $zbp;

    $strXML = '<methodResponse><params><param>$%#1#%$</param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>title</name><value><string>$%#1#%$</string></value></member>
<member><name>description</name><value><string>$%#2#%$</string></value></member>
<member><name>dateCreated</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
<member><name>categories</name><value><array><data><value><string>$%#4#%$</string></value></data></array></value></member>
<member><name>postid</name><value><string>$%#5#%$</string></value></member>
<member><name>userid</name><value><string>$%#6#%$</string></value></member>
<member><name>link</name><value><string>$%#7#%$</string></value></member>
<member><name>permaLink</name><value><string>$%#8#%$</string></value></member>
<member><name>mt_keywords</name><value><string>$%#9#%$</string></value></member>
<member><name>wp_slug</name><value><string>$%#10#%$</string></value></member>
<member><name>mt_excerpt</name><value><string>$%#11#%$</string></value></member>
<member><name>mt_text_more</name><value><string>$%#12#%$</string></value></member>
<member><name>mt_basname</name><value><string>$%#13#%$</string></value></member>
<member><name>mt_allow_comments</name><value><int>$%#14#%$</int></value></member>
<member><name>page_status</name><value><string>$%#15#%$</string></value></member>
</struct></value>';

    $strAll = '';

    $article = new Post();
    $article->LoadInfoByID($id);
    if (($article->AuthorID != $zbp->user->ID) && (!$zbp->CheckRights('ArticleAll'))) {
        xmlrpc_ShowError(11, __FILE__, __LINE__);
    }

    $array = array();
    $array[] = $article;

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $description = '';
        $mt_excerpt = '';
        $mt_text_more = '';
        if (strpos($value->Content, '<!--more-->') !== false) {
            $description = GetValueInArray(explode('<!--more-->', $value->Content), 1);
            $mt_text_more = GetValueInArray(explode('<!--more-->', $value->Content), 0);
            //$description=$value->Content;
        } else {
            $description = $value->Content;
            $mt_excerpt = $value->Intro;
        }

        $s = str_replace("$%#1#%$", htmlspecialchars($value->Title), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($description), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars($value->Time('c')), $s);
        $s = str_replace("$%#4#%$", htmlspecialchars($value->Category->Name), $s);
        $s = str_replace("$%#5#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#6#%$", htmlspecialchars($value->AuthorID), $s);
        $s = str_replace("$%#7#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#8#%$", htmlspecialchars($value->Url), $s);
        $s = str_replace("$%#9#%$", htmlspecialchars($value->TagsToNameString()), $s);
        $s = str_replace("$%#10#%$", htmlspecialchars($value->Alias), $s);
        $s = str_replace("$%#11#%$", htmlspecialchars($mt_excerpt), $s);
        $s = str_replace("$%#12#%$", htmlspecialchars($mt_text_more), $s);
        $s = str_replace("$%#13#%$", htmlspecialchars($value->Alias), $s);
        $s = str_replace("$%#14#%$", htmlspecialchars($value->IsLock ? '2' : '1'), $s);
        $s = str_replace("$%#15#%$", htmlspecialchars($value->Status == 0 ? 'publish' : 'draft'), $s);
        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 获取指定文章所属分类信息.
 *
 * 输出指定文章所属分类信息xml
 *
 * @param int $id 文章ID
 */
function xmlrpc_getPostCategories($id)
{
    global $zbp;

    $strXML = '<methodResponse><params><param><value><array><data>$%#1#%$</data></array></value></param></params></methodResponse>';
    $strSingle = '<value><struct>
<member><name>categoryName</name><value><string>$%#1#%$</string></value></member>
<member><name>categoryId</name><value><string>$%#2#%$</string></value></member>
<member><name>isPrimary</name><value><dateTime.iso8601>$%#3#%$</dateTime.iso8601></value></member>
</struct></value>';

    $strAll = '';

    $article = new Post();
    $article->LoadInfoByID($id);

    $array = array();
    $array[] = $article->Category;

    foreach ($array as $key => $value) {
        $s = $strSingle;
        $s = str_replace("$%#1#%$", htmlspecialchars($value->Name), $s);
        $s = str_replace("$%#2#%$", htmlspecialchars($value->ID), $s);
        $s = str_replace("$%#3#%$", htmlspecialchars(1), $s);

        $strAll .= $s;
    }

    $strXML = str_replace("$%#1#%$", $strAll, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 编辑指定文章.
 *
 * 输出操作结果
 *
 * @param int     $id        文章ID
 * @param string  $xmlstring 文章数据xml
 * @param boolval $publish   是否直接发布
 *
 * @throws Exception
 */
function xmlrpc_editPost($id, $xmlstring, $publish)
{
    global $zbp;

    $xml = simplexml_load_string($xmlstring);

    if ($xml) {
        $post = array();
        foreach ($xml->children() as $x) {
            $a = (string) $x->name;
            if ($a == 'categories') {
                $b = $x->value->children()->asXML();
            } else {
                $b = $x->value->children();
            }
            $b = str_replace(array('<array>', '</array>', '<data>', '</data>', '<string>', '</string>'), array(''), $b);
            $post[$a] = $b;
        }

        $_POST['ID'] = $id;

        $_POST['Title'] = $post['title'];

        if ($publish) {
            $_POST['Status'] = 0;
        } else {
            $_POST['Status'] = 1;
        }

        if (isset($post['mt_basename'])) {
            $_POST['Alias'] = $post['mt_basename'];
        }
        if (isset($post['dateCreated'])) {
            date_default_timezone_set('GMT');
            $_POST['PostTime'] = strtotime($post['dateCreated']);
            date_default_timezone_set($zbp->option['ZC_TIME_ZONE_NAME']);
            $_POST['PostTime'] = date('c', $_POST['PostTime']);
        }
        if (isset($post['wp_author_id'])) {
            $_POST['AuthorID'] = $post['wp_author_id'];
        } else {
            $_POST['AuthorID'] = $zbp->user->ID;
        }
        if (isset($post['mt_keywords'])) {
            $_POST['Tag'] = $post['mt_keywords'];
        }
        if (isset($post['mt_allow_comments'])) {
            if ($post['mt_allow_comments'] > 0) {
                $_POST['IsLock'] = ($post['mt_allow_comments'] - 1);
            } else {
                $_POST['IsLock'] = $post['mt_allow_comments'];
            }
        }
        if (isset($post['categories'])) {
            $post['categories'] = str_replace('<value>', '', $post['categories']);
            $catename = trim(GetValueInArray(explode('</value>', $post['categories']), 0));
            $_POST['CateID'] = $zbp->GetCategoryByName($catename)->ID;
        }
        if (isset($post['mt_excerpt'])) {
            $_POST['Intro'] = $post['mt_excerpt'];
        }
        if (isset($post['mt_text_more']) || isset($post['description'])) {
            if (isset($post['mt_text_more'])) {
                if ($post['mt_text_more'] != '') {
                    $_POST['Content'] = $post['description'] . '<!--more-->' . $post['mt_text_more'];
                } else {
                    $_POST['Content'] = $post['description'];
                }
            } else {
                $_POST['Content'] = $post['description'];
            }
        }

        $strXML = '<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';

        if (PostArticle() == true) {
            $strXML = str_replace("$%#1#%$", 1, $strXML);
            echo $strXML;
        } else {
            xmlrpc_ShowError(0, __FILE__, __LINE__);
        }
    }
}

/**
 * XML-RPC 设置文章默认分类.
 *
 * 输出默认分类id=1
 */
function xmlrpc_setPostCategories()
{
    $strXML = '<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';
    $strXML = str_replace("$%#1#%$", 1, $strXML);
    echo $strXML;
}

/**
 * XML-RPC 编辑指定页面.
 *
 * 输出操作结果
 *
 * @param int    $id        页面ID
 * @param string $xmlstring 页面数据xml
 * @param bool   $publish   是否直接发布
 *
 * @throws Exception
 */
function xmlrpc_editPage($id, $xmlstring, $publish)
{
    global $zbp;

    $xml = simplexml_load_string($xmlstring);

    if ($xml) {
        $post = array();
        foreach ($xml->children() as $x) {
            $a = (string) $x->name;
            if ($a == 'categories') {
                $b = $x->value->children()->asXML();
            } else {
                $b = $x->value->children();
            }
            $b = str_replace(array('<array>', '</array>', '<data>', '</data>', '<string>', '</string>'), array(''), $b);
            $post[$a] = $b;
        }

        if ($zbp->CheckItemToNavbar('page', $id)) {
            $_POST['AddNavbar'] = 1;
        }

        $_POST['ID'] = $id;

        $_POST['Title'] = $post['title'];

        $_POST['Content'] = $post['description'];

        $_POST['AuthorID'] = $zbp->user->ID;

        if ($publish) {
            $_POST['Status'] = 0;
        } else {
            $_POST['Status'] = 1;
        }

        if (isset($post['mt_basename'])) {
            $_POST['Alias'] = $post['mt_basename'];
        }
        if (isset($post['wp_author_id'])) {
            $_POST['AuthorID'] = $post['wp_author_id'];
        }
        if (isset($post['mt_allow_comments'])) {
            if ($post['mt_allow_comments'] > 0) {
                $_POST['IsLock'] = ($post['mt_allow_comments'] - 1);
            } else {
                $_POST['IsLock'] = $post['mt_allow_comments'];
            }
        }

        $strXML = '<methodResponse><params><param><value><boolean>$%#1#%$</boolean></value></param></params></methodResponse>';

        if (PostPage() == true) {
            $strXML = str_replace("$%#1#%$", 1, $strXML);
            echo $strXML;
        } else {
            xmlrpc_ShowError(0, __FILE__, __LINE__);
        }
    }
}

/**
 * XML-RPC 上传媒体文件.
 *
 * 输出操作结果
 *
 * @param int    $id        页面ID
 * @param string $xmlstring 上传文件数据xml
 */
function xmlrpc_newMediaObject($xmlstring)
{
    global $zbp;

    $xml = simplexml_load_string($xmlstring);

    if ($xml) {
        $post = array();
        foreach ($xml->children() as $x) {
            $a = (string) $x->name;
            $b = $x->value->children();
            $post[$a] = $b;
        }
        $upload = new Upload();
        $f = GetGuid() . strrchr($post['name'], '.');
        $upload->Name = $f;
        $upload->SourceName = $post['name'];
        $upload->MimeType = $post['type'];
        $upload->Size = 0;
        $upload->AuthorID = $zbp->user->ID;
        $upload->SaveBase64File($post['bits']);
        $upload->Save();

        $strXML = '<methodResponse><params><param><value><struct><member><name>url</name><value><string>$%#1#%$</string></value></member></struct></value></param></params></methodResponse>';
        $strXML = str_replace("$%#1#%$", htmlspecialchars($upload->Url), $strXML);
        echo $strXML;
    }
}

/**
 * XML-RPC辅助.
 *
 * 验证用户登录
 *
 * @param
 * @param
 */
function xmlrpc_Verify($username, $password)
{
    global $zbp;
    if (isset($zbp->option['ZC_XMLRPC_USE_WEBTOKEN']) && $zbp->option['ZC_XMLRPC_USE_WEBTOKEN'] == true) {
        if (!$zbp->Verify_Token($username, $password, 'xmlrpc', $zbp->user)) {
            xmlrpc_ShowError(8, __FILE__, __LINE__, 403);
            die;
        }
    } else {
        if (!$zbp->Verify_Original($username, $password, $zbp->user)) {
            xmlrpc_ShowError(8, __FILE__, __LINE__, 403);
            die;
        }
    }
}

function xmlrpc_ShowError($code, $file, $line, $httpcode = 401)
{
    global $zbp;
    SetHttpStatusCode($httpcode);
    $zbp->ShowError($code, $file, $line);
}

//xml-rpc input

$zbp->Load();

if (isset($zbp->option['ZC_XMLRPC_ENABLE']) && $zbp->option['ZC_XMLRPC_ENABLE'] == false) {
    Http404();
    die;
}

Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'RespondError');

$zbp->CheckSiteClosed();

$xmlstring = file_get_contents('php://input');
//Logs($xmlstring);
//defense XXE
libxml_disable_entity_loader(true);
$xml = simplexml_load_string($xmlstring);

if ($xml) {
    foreach ($GLOBALS['hooks']['Filter_Plugin_Xmlrpc_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($xml);
    }
    $method = (string) $xml->methodName;
    switch ($method) {
        case 'blogger.getUsersBlogs':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('admin')) {
                xmlrpc_getUsersBlogs();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getCategories':
        case 'metaWeblog.getCategories':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getCategories();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'mt.setPostCategories':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_setPostCategories();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'mt.getPostCategories':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getPostCategories((int) $xml->params->param[0]->value->string);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getTags':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getTags();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getAuthors':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getAuthors();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'metaWeblog.getRecentPosts':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getRecentPosts((int) $xml->params->param[3]->value->int);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'metaWeblog.getPost':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleEdt')) {
                xmlrpc_getPost((int) $xml->params->param[0]->value->string);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'blogger.deletePost':
            $username = (string) $xml->params->param[2]->value->string;
            $password = (string) $xml->params->param[3]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticleDel')) {
                xmlrpc_deletePost((int) $xml->params->param[1]->value->string);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'metaWeblog.editPost':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticlePst')) {
                xmlrpc_editPost(
                    (int) $xml->params->param[0]->value->string,
                    $xml->params->param[3]->value->struct->asXML(),
                    (bool) $xml->params->param[4]->value->boolean->asXML()
                );
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'metaWeblog.newPost':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('ArticlePst')) {
                xmlrpc_editPost(
                    0,
                    $xml->params->param[3]->value->struct->asXML(),
                    (bool) $xml->params->param[4]->value->boolean->asXML()
                );
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.newPage':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('PagePst')) {
                xmlrpc_editPage(
                    0,
                    $xml->params->param[3]->value->struct->asXML(),
                    (bool) $xml->params->param[4]->value->boolean->asXML()
                );
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.editPage':
            $username = (string) $xml->params->param[2]->value->string;
            $password = (string) $xml->params->param[3]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('PagePst')) {
                xmlrpc_editPage(
                    (int) $xml->params->param[1]->value->string,
                    $xml->params->param[4]->value->struct->asXML(),
                    (bool) $xml->params->param[5]->value->boolean->asXML()
                );
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getPages':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('PageEdt')) {
                xmlrpc_getPages((int) $xml->params->param[3]->value->int);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getPage':
            $username = (string) $xml->params->param[2]->value->string;
            $password = (string) $xml->params->param[3]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('PageEdt')) {
                xmlrpc_getPage((int) $xml->params->param[1]->value->string);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.deletePage':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('PageDel')) {
                xmlrpc_delPage((int) $xml->params->param[3]->value->string);
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'metaWeblog.newMediaObject':
            $username = (string) $xml->params->param[1]->value->string;
            $password = (string) $xml->params->param[2]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('UploadPst')) {
                xmlrpc_newMediaObject($xml->params->param[3]->value->struct->asXML());
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        case 'wp.getUsersBlogs':
            $username = (string) $xml->params->param[0]->value->string;
            $password = (string) $xml->params->param[1]->value->string;
            xmlrpc_Verify($username, $password);
            if ($zbp->CheckRights('admin')) {
                xmlrpc_wp_getUsersBlogs();
            } else {
                xmlrpc_ShowError(6, __FILE__, __LINE__);
            }
            break;
        default:
            xmlrpc_ShowError(1, __FILE__, __LINE__);
            break;
    }
}
