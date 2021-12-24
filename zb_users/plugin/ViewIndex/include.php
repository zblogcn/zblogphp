<?php
#注册插件
RegisterPlugin("ViewIndex", "ActivePlugin_ViewIndex");

function ActivePlugin_ViewIndex()
{
    global $zbp;
    $zbp->RegRoute(
        array (
            'prefix' => 'api',
            'posttype' => null,
            'type' => 'active',
            'name' => 'viewindex_api',
            'call' => 'ViewIndex_Api',
            'must_get' => 
            array (
              0 => 'mod',
              1 => 'act',
            ),
            'urlrule' => '{%host%}api/?',
        ),
        true
    );
    $zbp->RegRoute(
        array (
            'posttype' => 0,
            'type' => 'active',
            'name' => 'viewindex_feed',
            'call' => 'ViewIndex_Feed',
            'get' =>
            array(
              0 => '',
              1 => 'cate',
              2 => 'auth',
              3 => 'dat',
              4 => 'tags',
            ),
            'must_get' => 
            array (
              0 => 'feed',
            ),
            'urlrule' => '{%host%}?feed',
        ),
        true
    );
    $zbp->RegRoute(
        array (
            'posttype' => 0,
            'type' => 'active',
            'name' => 'viewindex_search',
            'call' => 'ViewIndex_Search',
            'must_get' => 
            array (
              0 => 'search',
            ),
            'get' => 
            array (
              0 => 'page',
            ),
            'urlrule' => '{%host%}?search={%q%}&page={%page%}',
        ),
        true
    );
/*
    $zbp->RegRoute(
        array (
            'posttype' => 0,
            'type' => 'rewrite',
            'name' => 'post_article_search',
            'call' => 'ViewSearch',
            'prefix' => 'search',
            'urlrule' => '{%host%}{%q%}_{%page%}.html',
            'args' => 
            array (
              'q' => '[^\\/_]+',
              0 => 'page',
            ),
            'args_with' => 
            array (
            ),
            'request_method' => 
            array (
              0 => 'GET',
              1 => 'POST',
            ),
            'only_match_page' => false,
        ),
        false
    );
*/
    $zbp->RegRoute(
        array (
            'posttype' => 0,
            'type' => 'rewrite',
            'name' => 'viewindex_feed_rewrite',
            'call' => 'ViewIndex_Feed',
            'urlrule' => '{%host%}feed.xml',
            'get' =>
            array(
              0 => '',
              1 => 'cate',
              2 => 'auth',
              3 => 'dat',
              4 => 'tags',
            ),
            'urlrule' => '{%host%}feed.xml',
        ),
        false
    );
    Add_Filter_Plugin('Filter_Plugin_Http_Request_Convert_To_Global', 'ViewIndex_ChangeUrl');
    if (IS_SCF && $zbp->db->type == 'sqlite') {
        Add_Filter_Plugin('Filter_Plugin_ViewPost_ViewNums', 'ViewIndex_NoViewNums');
    }
}

function ViewIndex_NoViewNums(){
    //
}

function ViewIndex_ChangeUrl(){
    global $zbp;
    static $already_set = false;
    if (!$already_set) {
        $zbp->apiurl = $zbp->host . 'api/';
        $zbp->feedurl = $zbp->host . 'feed.xml';
        $zbp->searchurl = $zbp->host . '?search';
        $zbp->posttype[0]['search_urlrule'] = '{%host%}?search={%q%}&page={%page%}';
        $already_set = true;
    }
}

function ViewIndex_Api(){
    global $zbp;
    // 标记为 API 运行模式
    defined('ZBP_IN_API') || define('ZBP_IN_API', true);

    try{
        ApiCheckEnable();

        HookFilterPlugin('Filter_Plugin_API_Begin');

        ApiCheckAuth(false, 'api');

        ApiCheckLimit();

        $GLOBALS['mods'] = array();
        $GLOBALS['mods_allow'] = array(); //格式为 array( array('模块名'=>'方法名') )
        $GLOBALS['mods_disallow'] = array(); //如果是 array( array('模块名'=>'') )方法名为空将匹配整个模块
        $GLOBALS['mod'] = strtolower(GetVars('mod', 'GET'));
        $GLOBALS['act'] = strtolower(GetVars('act', 'GET'));

        // 载入系统和应用的 mod
        ApiLoadMods($GLOBALS['mods']);

        //进行Api白名单和黑名单的检查
        ApiCheckMods($GLOBALS['mods_allow'], $GLOBALS['mods_disallow']);

        ApiLoadPostData();

        ApiVerifyCSRF();

        // 派发 API
        $r = ApiDispatch($GLOBALS['mods'], $GLOBALS['mod'], $GLOBALS['act']);
        return array('StatusCode' => 200, 'Content' => $r, 'Content-Type' => 'application/json; charset=utf-8');
    }
    catch (\Throwable $e) {
        $r = ApiResponse(null, $e);
        return array('StatusCode' => 500, 'Content' => $r, 'Content-Type' => 'application/json; charset=utf-8');
    }
}

function ViewIndex_Feed(){
    global $zbp;
    $route = func_get_arg(0);
    try{
        if (!$zbp->CheckRights('feed')) {
            $zbp->ShowError(6);
        }
        ViewFeed($route);
        return array('StatusCode' => 200, 'Content-Type' => 'text/xml; Charset=utf-8');
    }
    catch (\Throwable $e) {
        $rt = RunTime(false);
        $r = print_r(array($e->getCode(), $e->getMessage(), $rt), true);
        $r = '<xml>' . htmlentities($r) . '</xml>';
        return array('StatusCode' => 500, 'Content' => $r, 'Content-Type' => 'text/xml; Charset=utf-8');
    }
}

function ViewIndex_Search(){
    global $zbp;
    $route = func_get_arg(0);
    if (!$zbp->CheckRights('search')) {
        $zbp->ShowError(6);
    }
    ViewSearch($route);
    return array('StatusCode' => 200, 'Content-Type' => 'text/html; Charset=utf-8');
}
