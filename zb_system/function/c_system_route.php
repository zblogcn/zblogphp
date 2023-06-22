<?php

/**
 * 路由和控制器相关函数.
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

//###############################################################################################################

/**
 * 根据url路由规则显示页面的主路由器函数.
 *
 *
 * @api Filter_Plugin_ViewAuto_Begin
 * @api Filter_Plugin_ViewAuto_End
 *
 * @throws Exception
 *
 * @return null|string
 */
function ViewAuto()
{
    global $zbp;

    $original_url = $zbp->currenturl;

    $url = GetValueInArray(explode('?', $original_url), '0');

    if ($zbp->cookiespath === substr($url, 0, strlen($zbp->cookiespath))) {
        $url = substr($url, strlen($zbp->cookiespath));
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewAuto_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($original_url, $url);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    $url = urldecode($url);
    $active_routes = $rewrite_routes = $default_routes = array();

    foreach ($zbp->routes as $key => $route) {
        $route['original_url'] = $original_url;
        $route['url'] = $url;
        if ($route['type'] == 'active') {
            if (GetValueInArray($route, 'suspended', false) == false) {
                $active_routes[] = $route;
            }
        } elseif ($route['type'] == 'rewrite') {
            if ($zbp->option['ZC_STATIC_MODE'] == 'REWRITE' && GetValueInArray($route, 'suspended', false) == false) {
                $rewrite_routes[] = $route;
            }
        } elseif ($route['type'] == 'default') {
            if (GetValueInArray($route, 'suspended', false) == false) {
                if (GetValueInArray($route, 'only_rewrite', false) == true) {
                    if ($zbp->option['ZC_STATIC_MODE'] == 'REWRITE') {
                        $default_routes[] = $route;
                    }
                } elseif (GetValueInArray($route, 'only_active', false) == true) {
                    if ($zbp->option['ZC_STATIC_MODE'] == 'ACTIVE') {
                        $default_routes[] = $route;
                    }
                } else {
                    $default_routes[] = $route;
                }
            }
        }
    }

    //匹配动态路由（某些情况下，在伪静开启时匹配但不输出内容，如果是符合条件就可以跳转）
    foreach ($active_routes as $key => $route) {
        $prefix = GetValueInArray($route, 'prefix', '');
        $prefix = empty($prefix) ? '' : ($prefix . '/');
        if (($url == $prefix . '') || ($url == $prefix . 'index.php') || (($zbp->option['ZC_STATIC_MODE'] == 'REWRITE') && GetValueInArray($_GET, 'rewrite', null) == true)) {
            $b = ViewAuto_Check_Get_And_Not_Get_And_Must_Get(GetValueInArray($route, 'get', array()), GetValueInArray($route, 'not_get', array()), GetValueInArray($route, 'must_get', array()));
            $b = $b && ViewAuto_Check_Request_Method(GetValueInArray($route, 'request_method', ''));
            //如果条件符合就组合参数数组并调用函数
            if ($b) {
                $array = array();
                ViewAuto_Process_Args_get($array, GetValueInArray($route, 'args_get', array()), $route);
                ViewAuto_Process_Args_with($array, GetValueInArray($route, 'args_with', array()), $route);
                ViewAuto_Process_Args_Merge($route);
                $b_redirect = ViewAuto_Check_To_Permalink($route, $array);
                $result = ViewAuto_Check_Redirect_To($route);
                if (is_array($result)) {
                    return $result;
                }
                $result = ViewAuto_Call_Auto($route, $array);
                if ($result === false) {
                    continue;
                }
                //如果开启伪静且$b_redirect=true和返回array，那么通过原动态访问的会跳转至$result
                if ($b_redirect == true && is_array($result)) {
                    $result2 = ViewAuto_Check_Redirect_To($result);
                    if (is_array($result2)) {
                        return $result2;
                    }
                }
                return $result;
            }
        }
    }

    //匹配伪静路由
    foreach ($rewrite_routes as $key => $route) {
        //如果条件符合就组合参数数组并调用函数
        $b = ViewAuto_Check_Get_And_Not_Get_And_Must_Get(GetValueInArray($route, 'get', array()), GetValueInArray($route, 'not_get', array()), GetValueInArray($route, 'must_get', array()));
        $b = $b && ViewAuto_Check_Request_Method(GetValueInArray($route, 'request_method', ''));
        if ($b) {
            $c = false;
            //$match_with_page 默认匹配1次 (false)，有page参数可以匹配2次 [false=(remove page), true=(keep page)]
            $match_with_page = $parameters = $m = array();
            ViewAuto_Get_Parameters_And_Match_with_page($route, $parameters, $match_with_page);
            foreach ($match_with_page as $match) {
                $r = ViewAuto_Get_Compiled_Urlrule($route, $match);
                if (($r != '' && preg_match($r, $url, $m) == 1) || ($r == '' && $url == '') || ($r == '' && $url == 'index.php') || ($r == '/(?J)^index\.php\/$/' && $url == '')) {
                    $array = $m;
                    ViewAuto_Process_Args_get($array, GetValueInArray($route, 'args_get', array()), $route);
                    ViewAuto_Process_Args($array, $parameters, $m);
                    ViewAuto_Process_Args_with($array, GetValueInArray($route, 'args_with', array()), $route);
                    ViewAuto_Process_Args_Merge($route);
                    //var_dump($match, $route['urlrule'], $r, $url, $m, $array);//die;
                    $result = ViewAuto_Check_Redirect_To($route);
                    if (is_array($result)) {
                        return $result;
                    }
                    $result = ViewAuto_Call_Auto($route, $array);
                    if ($result === false) {
                        continue;
                    }
                    return $result;
                }
            }
            //var_dump($route['name'],$match, $route['urlrule'], $r, $url, $m);//die;
        }
    }

    //都不能匹配时，进入一次默认路由
    foreach ($default_routes as $key => $route) {
        $b = ViewAuto_Check_Get_And_Not_Get_And_Must_Get(GetValueInArray($route, 'get', array()), GetValueInArray($route, 'not_get', array()), GetValueInArray($route, 'must_get', array()));
        $b = $b && ViewAuto_Check_Request_Method(GetValueInArray($route, 'request_method', ''));
        if ($b) {
            $array_for = array();
            $match_with_page = $parameters = $m = array();
            //判断规则是动态还是伪静规则
            $c = (isset($route['args']) && !empty($route['args']));
            $c = $c || (!isset($route['get']) && !isset($route['not_get']) && !isset($route['must_get']));
            if ($c) {
                ViewAuto_Get_Parameters_And_Match_with_page($route, $parameters, $match_with_page);
                foreach ($match_with_page as $match) {
                    $r = ViewAuto_Get_Compiled_Urlrule($route, $match);
                    if (stristr($r, 'index\.php') === false) {
                        $url = str_ireplace('index.php', '', $url);
                    }
                    if (($r != '' && preg_match($r, $url, $m) == 1) || ($r == '' && $url == '') || ($r == '' && $url == 'index.php') || ($r == '/(?J)^index\.php\/$/' && $url == '')) {
                        $array_for[] = array(true, $parameters, $m);
                    }
                }
            } else {
                $prefix = GetValueInArray($route, 'prefix', '');
                $prefix = empty($prefix) ? '' : ($prefix . '/');
                if ($prefix == '' || ($prefix == substr($url, 0, strlen($prefix)))) {
                    $array_for[] = array(true, array(), array());
                }
            }
            foreach ($array_for as $for_value) {
                if ($for_value[0] == true) {
                    $array = $for_value[2];
                    ViewAuto_Process_Args_get($array, GetValueInArray($route, 'args_get', array()), $route);
                    ViewAuto_Process_Args($array, $for_value[1], $for_value[2]);
                    ViewAuto_Process_Args_with($array, GetValueInArray($route, 'args_with', array()), $route);
                    ViewAuto_Process_Args_Merge($route);
                    $result = ViewAuto_Check_Redirect_To($route);
                    if (is_array($result)) {
                        return $result;
                    }
                    $result = ViewAuto_Call_Auto($route, $array);
                    if ($result === false) {
                        continue;
                    }
                    return $result;
                }
            }
        }
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewAuto_End'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($url, $original_url);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    $zbp->ShowError(2, __FILE__, __LINE__);

    return false;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Process_Args(&$array, $parameters, $m)
{
    foreach ($parameters as $key => $value) {
        if (isset($m[(string) $value['name']])) {
            $array[$value['name']] = $m[(string) $value['name']];
            if ($value['alias'] != '') {
                $array[$value['alias']] = $m[(string) $value['name']];
            }
        }
    }
    return $array;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Process_Args_get(&$array, $args_get, $route)
{
    $get = array_merge(GetValueInArray($route, 'get', array()), GetValueInArray($route, 'must_get', array()));

    if (isset($args_get) && is_array($args_get)) {
        foreach ($get as $key => $value) {
            $value = trim($value);
            if ($value !== '') {
                $args_get[] = $value;
            }
        }
        foreach ($args_get as $key => $value) {
            if (isset($_GET[$value])) {
                $array[$value] = $_GET[$value];
            } else {
                $array[$value] = null;
            }
        }
    }
    return $array;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Process_Args_with(&$array, $args_with, $route)
{
    if (isset($args_with) && is_array($args_with)) {
        foreach ($args_with as $key => $value) {
            if (is_integer($key) && is_scalar($value)) {
                if (isset($_GET[$value])) {
                    $array[$value] = $_GET[$value];
                }
                if (isset($route[$value])) {
                    $array[$value] = $route[$value];
                }
            } elseif (is_string($key)) {
                $array[$key] = $value;
            }
        }
    }
    if (isset($route['posttype']) && !is_null($route['posttype'])) {
        $array['posttype'] = $route['posttype'];
    }
    if (isset($route['verify_permalink'])) {
        $array['_verify_permalink'] = $route['verify_permalink'];
    }
    return $array;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Process_Args_Merge(&$route)
{
    $array = array();
    $get = array_merge(GetValueInArray($route, 'get', array()), GetValueInArray($route, 'must_get', array()), GetValueInArray($route, 'args_get', array()));
    foreach ($get as $key => $value) {
        if (isset($_GET[$value])) {
            $array[$value] = $_GET[$value];
        } else {
            $array[$value] = '';
        }
    }

    $with = GetValueInArray($route, 'args_with', array());
    foreach ($with as $key => $value) {
        if (is_integer($key) && is_scalar($value)) {
            if (isset($_GET[$value])) {
                $array[$value] = $_GET[$value];
            }
            if (isset($route[$value])) {
                $array[$value] = $route[$value];
            }
        } elseif (is_string($key)) {
            $array[$key] = $value;
        }
    }

    if (!(isset($route['args']) && is_array($route['args']))) {
        $route['args'] = array();
    }
    foreach ($array as $key => $value) {
        $value = trim($value);
        $route['args'][] = array('name' => $key, 'value' => $value);
    }
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Check_Redirect_To($route)
{
    if (isset($route['StatusCode']) && isset($route['Location'])) {
        if ($route['StatusCode'] == 301) {
            Redirect301($route['Location']);
        } else {
            Redirect302($route['Location']);
        }
        return array('StatusCode' => $route['StatusCode'], 'Location' => $route['Location']);
    }
    if (isset($route['redirect_to'])) {
        Redirect($route['redirect_to']);
        return array('StatusCode' => 302, 'Location' => $route['redirect_to']);
    }
    if (isset($route['redirect301_to'])) {
        Redirect301($route['redirect301_to']);
        return array('StatusCode' => 301, 'Location' => $route['redirect_to']);
    }
    return false;
}

/**
 * ViewAuto的辅助函数
 * $route['call']参数，可以是1函数名 2类名::静态方法名 3全局变量名@动态方法名 4类名@动态方法名) 5全局匿名函数
 * 借用了plugin里的ParseFilterPlugin函数去解析$function
 */
function ViewAuto_Call_Auto($route, $array)
{
    $function = $route['call'];
    $array['_route'] = $route;
    return call_user_func(ParseFilterPlugin($function), $array);
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Check_To_Permalink($route, &$array)
{
    global $zbp;
    if (GetValueInArray($route, 'to_permalink', false) == false) {
        return false;
    }
    if (!($zbp->option['ZC_STATIC_MODE'] == 'REWRITE')) {
        return false;
    }
    if (($zbp->option['ZC_STATIC_MODE'] == 'REWRITE') && GetValueInArray($_GET, 'rewrite', null) == true) {
        return false;
    }
    //检查有不存在的参数就返回false
    $get = GetValueInArray($route, 'get', array());
    foreach ($_GET as $key => $value) {
        if (!empty($get) && in_array($key, $get) == false) {
            return false;
        }
    }
    //检查生成规则为空就返回false
    $urlrule = GetValueInArray($route, 'urlrule', '');
    if (empty($urlrule)) {
        return false;
    }
    $match_with_page = false;
    if (array_key_exists('page', $_GET)) {
        $match_with_page = true;
    }
    $r = UrlRule::OutputUrlRegEx_Route($route, $match_with_page);
    if ($r != '') {
        $array['_return_url'] = true;
        return true;
    }
    return false;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Get_Parameters_And_Match_with_page($route, &$parameters, &$match_with_page)
{
    if (isset($route['args']) && is_array($route['args'])) {
        $parameters = UrlRule::ProcessParameters($route);
    } else {
        $parameters = array();
    }

    $match_with_page = array('remove_page' => false);

    //如果指定了无需编译的正则式的规则，就强定指定一次且只有false
    if (isset($route['urlrule_regex']) && trim($route['urlrule_regex']) != '') {
        return true;
    }

    $haspage = false;
    foreach ($parameters as $key => $value) {
        if ($value['name'] == 'page') {
            $haspage = true;
        }
    }
    if ($haspage == true) {
        $match_with_page['keep_page'] = true;
    }

    $only_match_page = GetValueInArray($route, 'only_match_page', false);
    if ($only_match_page == true) {
        unset($match_with_page['remove_page']);
    }

    if (empty($match_with_page)) {
        $match_with_page = array('remove_page' => false);
    }

    return true;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Get_Compiled_Urlrule($route, $match)
{
    //如果直接指定了$route['urlrule_regex']，就不调用UrlRule::OutputUrlRegEx，直接preg_match
    if (isset($route['urlrule_regex']) && trim($route['urlrule_regex']) != '') {
        $r = trim($route['urlrule_regex']);
    } else {
        //$r = UrlRule::OutputUrlRegEx_V2($zbp->GetPostType(0, 'list_urlrule'), 'list', $match);
        $r = UrlRule::OutputUrlRegEx_Route($route, $match);
    }
    return $r;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Check_Request_Method($request_method)
{
    $b = false;
    if (!empty($request_method)) {
        $m = $_SERVER['REQUEST_METHOD'];
        if (is_array($request_method)) {
            foreach ($request_method as $key => $value) {
                if (strcasecmp($value, $m) == 0) {
                    $b = true;
                    break;
                }
            }
        } else {
            if (strcasecmp($request_method, $m) == 0) {
                $b = true;
            }
        }
    } else {
        $b = true;
    }
    return $b;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Check_Get_And_Not_Get_And_Must_Get($get, $notget, $mustget)
{
    $b = false;
    //检查GET参数是否存在(如果有2个或2个以上，必须存在1个) OR (如果只有1个，则可有可无)
    if (!empty($get)) {
        if (count($get) == 1) {
            $b = true;
        } else {
            $get = array_merge($get, $mustget);
            foreach ($get as $key => $value) {
                if (isset($_GET[$value])) {
                    $b = true;
                    break;
                }
                if ($value === '') {
                    $b = true;
                    break;
                }
            }
        }
    } else {
        $b = true;
    }

    //检查GET参数是否有不需要的存在(全部不能存在) NOT
    if (!empty($notget)) {
        foreach ($notget as $key => $value) {
            if ((substr($value, 0, 1) == '/' && substr_count($value, '/') > 1) || (substr($value, 0, 1) == '#' && substr_count($value, '#') > 1) || (substr($value, 0, 1) == '~' && substr_count($value, '~') > 1)) {
                foreach ($_GET as $key2 => $value2) {
                    if (@preg_match($value, $key2) === 1) {
                        $b = false;
                        return $b;
                    }
                }
            } else {
                if (isset($_GET[$value])) {
                    $b = false;
                    return $b;
                }
            }
        }
    }
    //检查GET参数是否有必须的存在(全部必存在) AND
    if (!empty($mustget)) {
        $c = array();
        foreach ($mustget as $key => $value) {
            if (isset($_GET[$value])) {
                $c[] = true;
            } else {
                $c[] = false;
            }
        }
        if (in_array(false, $c, true) == true) {
            $b = false;
        }
    }
    return $b;
}

/**
 * ViewAuto的辅助函数
 */
function ViewAuto_Process_Pagebar_Replace_Array(&$pagebar, $route, $args)
{
    if (empty($route)) {
        return;
    }

    $args = is_array($args) ? $args : array();
    $array = GetValueInArray($args, 0, array());
    $array = is_array($array) ? $array : array();
    $parameters = UrlRule::ProcessParameters($route);
    $replace = array();

    foreach ($parameters as $key => $value) {
        if (isset($array[$key])) {
            $replace[$value['name']] = $array[$key];
        }
    }
    $rules = &$pagebar->UrlRule->Rules;
    foreach ($replace as $key => $value) {
        if (!isset($rules['{%' . $key . '%}'])) {
            $rules['{%' . $key . '%}'] = $value;
        }
    }
    foreach ($array as $key => $value) {
        if (is_string($key) && is_scalar($value) && !isset($rules['{%' . $key . '%}'])) {
            $rules['{%' . $key . '%}'] = $value;
        }
    }
}

//###############################################################################################################

/**
 * ViewIndex,首页，搜索页，feed页的主函数.
 *
 * @api Filter_Plugin_ViewIndex_Begin
 *
 * @throws Exception
 *
 * @return mixed
 */
function ViewIndex()
{
    global $zbp, $action;

    $url = $zbp->currenturl;

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewIndex_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($url);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    switch ($action) {
        case 'feed':
            if (!$zbp->CheckRights($GLOBALS['action'])) {
                Http404();
                return false;
            }
            ViewFeed();
            break;
        case 'search':
            if (!$zbp->CheckRights($GLOBALS['action'])) {
                Http404();
                return false;
            }
            ViewSearch();
            break;
        case 'view':
        case '':
        default:
            ViewAuto();
    }

    return true;
}

/**
 * 显示RSS2Feed.
 *
 * @api Filter_Plugin_ViewFeed_Begin
 */
function ViewFeed()
{
    $fpargs = func_get_args();
    global $zbp;

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewFeed_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname();
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    $args = GetValueInArray($fpargs, 0, null);
    if (is_array($args)) {
        $posttype = GetValueInArray($args, 'posttype', 0);
        $cate = GetValueInArray($args, 'cate', null);
        $auth = GetValueInArray($args, 'auth', null);
        $date = GetValueInArray($args, 'date', null);
        $tags = GetValueInArray($args, 'tags', null);
    } else {
        $posttype = 0;
        $cate = GetVars('cate', 'GET');
        $auth = GetVars('auth', 'GET');
        $date = GetVars('date', 'GET');
        $tags = GetVars('tags', 'GET');
    }

    $rss2 = new Rss2($zbp->name, $zbp->host, $zbp->subname);

    $w = array(array('=', 'log_Status', 0));

    //没权限就显示空XML
    $actions = $zbp->GetPostType($posttype, 'actions');
    if (!$zbp->CheckRights($actions['view'])) {
        $w[] = array('=', 'log_ID', 0);
    }

    $w[] = array('=', 'log_Type', $posttype);

    if ($cate != null) {
        $w[] = array('=', 'log_CateID', (int) $cate);
    } elseif ($auth != null) {
        $w[] = array('=', 'log_AuthorID', (int) $auth);
    } elseif ($date != null) {
        $d = strtotime($date);
        if (strrpos($date, '-') !== strpos($date, '-')) {
            $w[] = array('BETWEEN', 'log_PostTime', $d, strtotime('+1 day', $d));
        } else {
            $w[] = array('BETWEEN', 'log_PostTime', $d, strtotime('+1 month', $d));
        }
    } elseif ($tags != null) {
        $w[] = array('LIKE', 'log_Tag', '%{' . (int) $tags . '}%');
    }

    $select = '*';
    $order = array($zbp->option['ZC_RSS2_ORDER'] => 'DESC', 'log_ID' => 'DESC');
    $limit = $zbp->option['ZC_RSS2_COUNT'];
    $option = array();

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewFeed_Core'] as $fpname => &$fpsignal) {
        $fpname($w);
    }

    $articles = $zbp->GetPostList(
        $select,
        $w,
        $order,
        $limit,
        $option
    );

    foreach ($articles as $article) {
        $rss2->addItem($article->Title, $article->Url, ($zbp->option['ZC_RSS_EXPORT_WHOLE'] == true ? $article->Content : $article->Intro), $article->PostTime);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewFeed_End'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($rss2);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    @ob_clean();
    if (!headers_sent()) {
        header("Content-type:text/xml; charset=utf-8");
    }

    echo $rss2->saveXML();

    return true;
}

/**
 * 展示搜索结果.
 *
 * @api Filter_Plugin_ViewSearch_Begin
 * @api Filter_Plugin_ViewPost_Template
 *
 * @throws Exception
 *
 * @return mixed
 */
function ViewSearch()
{
    global $zbp;
    $fpargs = func_get_args();

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewSearch_Begin'] as $fpname => &$fpsignal) {
        $fpreturn = call_user_func_array($fpname, $fpargs);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;
            return $fpreturn;
        }
    }

    $args = GetValueInArray($fpargs, 0, null);
    if (is_array($args)) {
        $return_url = GetValueInArray($args, '_return_url', false);
        $posttype = GetValueInArray($args, 'posttype', 0);
        $q = GetValueInArray($args, 'q', '');
        if (isset($args['search']) && $args['search']) {
            $q = $args['search'];
        }
        $page = GetValueInArray($args, 'page', 0);
        $route = GetValueInArray($args, '_route', array());
        $disablebot = GetValueInArray($args, 'disablebot', true);
    } else {
        $return_url = false;
        $posttype = 0;
        $q = GetVars('q', 'GET');
        $page = GetVars('page', 'GET');
        $route = array('urlrule' => $zbp->GetPostType(0, 'search_urlrule'));
        $disablebot = true;
    }

    $q = trim(htmlspecialchars($q));
    $page = max(1, (int) $page);

    $w = array();
    $w[] = array('=', 'log_Type', $posttype);

    //没有权限就搜索空的
    $actions = $zbp->GetPostType($posttype, 'actions');
    if (!$zbp->CheckRights($actions['search'])) {
        $w[] = array('=', 'log_ID', 0);
    }

    $article = new Post();
    $article->ID = 0;
    $article->Title = $zbp->langs->msg->search . '&nbsp;&quot;<span>' . $q . '</span>&quot;';
    $article->IsLock = true;
    $article->Type = $posttype;

    if ($q) {
        $w[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $q);
    } else {
        $w[] = array('=', 'log_ID', 0);
    }

    if (!($zbp->CheckRights($article->TypeActions['all']))) {
        $w[] = array('=', 'log_Status', 0);
    }
    $order = array($zbp->displayorder => 'DESC');

    $pagebar = new Pagebar($route);
    $pagebar->PageCount = $zbp->searchcount;
    $pagebar->PageNow = $page;
    $pagebar->PageBarCount = $zbp->pagebarcount;
    $pagebar->UrlRule->Rules['{%page%}'] = $page;
    $pagebar->UrlRule->Rules['{%q%}'] = rawurlencode($q);
    $pagebar->UrlRule->Rules['{%search%}'] = rawurlencode($q);
    ViewAuto_Process_Pagebar_Replace_Array($pagebar, $route, $fpargs);

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewSearch_Core'] as $fpname => &$fpsignal) {
        $fpname($q, $page, $w, $pagebar, $order);
    }

    $array = $zbp->GetPostList(
        '',
        $w,
        $order,
        array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
        array('pagebar' => $pagebar)
    );

    $results = array();

    foreach ($array as $a) {
        $r = new Post();
        $r->LoadInfoByDataArray($a->GetData());
        $article->Content .= '<p><a href="' . $a->Url . '">' . str_ireplace($q, '<strong>' . $q . '</strong>', $a->Title) . '</a><br/>';
        $s = strip_tags($a->Intro) . ' ' . strip_tags($a->Content);
        $i = Zbp_Stripos($s, $q, 0);
        if ($i !== false) {
            if ($i > 50) {
                $t = SubStrUTF8_Start($s, ($i - 50), 100);
            } else {
                $t = SubStrUTF8_Start($s, 0, 100);
            }
            $article->Content .= str_ireplace($q, '<strong>' . $q . '</strong>', $t) . '<br/>';
            $r->Intro = str_ireplace($q, '<strong>' . $q . '</strong>', $t);
            $r->Content = $a->Content;
        } else {
            $s = strip_tags($a->Title);
            $i = Zbp_Strpos($s, $q, 0);
            if ($i > 50) {
                $t = SubStrUTF8_Start($s, ($i - 50), 100);
            } else {
                $t = SubStrUTF8_Start($s, 0, 100);
            }
            $article->Content .= str_replace($q, '<strong>' . $q . '</strong>', $t) . '<br/>';
            $r->Intro = str_replace($q, '<strong>' . $q . '</strong>', $t);
            $r->Content = $a->Content;
        }
        $r->Title = str_ireplace($q, '<strong>' . $q . '</strong>', $r->Title);
        $article->Content .= '<a href="' . $a->Url . '">' . $a->Url . '</a><br/></p>';
        $results[] = $r;
    }

    $template = &$zbp->GetTemplate();

    if ($disablebot) {
        $template->SetTags('header', $template->GetTags('header') . '    <meta name="robots" content="noindex,nofollow,noarchive" />' . "\r\n");
    }
    $template->SetTags('title', str_replace(array('<span>', '</span>'), '', $article->Title));
    $template->SetTags('article', $article);
    $template->SetTags('articles', $results);
    $template->SetTags('search', $q);
    $template->SetTags('page', $page);
    $template->SetTags('pagebar', $pagebar);
    $template->SetTags('comments', array());
    $template->SetTags('issearch', true);
    $template->SetTags('posttype', $posttype);
    if (is_object($pagebar) && isset($pagebar->buttons[$pagebar->PageNow])) {
        $template->SetTags('url', $pagebar->buttons[$pagebar->PageNow]);
    } else {
        $template->SetTags('url', $zbp->host);
    }
    $template->SetTags('args', $fpargs);
    $template->SetTags('route', $route);

    //1.6统一改为search模式
    $template->SetTags('type', 'search');
    //1.7指定搜索模板为优先为search或是index
    if ($template->HasTemplate($zbp->GetPostType($posttype, 'search_template'))) {
        $template->SetTemplate($zbp->GetPostType($posttype, 'search_template'));
    } else {
        $template->SetTemplate($zbp->GetPostType($posttype, 'list_template'));
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewSearch_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($template);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    if ($return_url == true) {
        $url = $template->GetTags('url');
        return array('StatusCode' => 302, 'Location' => $url);
    }

    $template->Display();
    return true;
}

//###############################################################################################################

/**
 * 显示列表页面.
 *
 * @param int   $page (1.7起做为主要array型参数，后续的都作废了)
 * @param mixed $cate           分类 id或alias
 * @param mixed $auth           作者 id或alias
 * @param mixed $date           日期
 * @param mixed $tags           tags id或alias
 * @param mixed $isrewrite      是否启用urlrewrite
 * @param array $object         把1.7里新增array型参数传给旧版本的接口
 *
 * @api Filter_Plugin_ViewList_Begin
 * @api Filter_Plugin_ViewList_Begin_V2
 * @api Filter_Plugin_ViewList_Template
 *
 * @throws Exception
 *
 * @return string
 */
function ViewList($page = null, $cate = null, $auth = null, $date = null, $tags = null, $isrewrite = false, $object = array())
{
    global $zbp;
    $fpargs = func_get_args();

    $fpargs_count = count($fpargs);

    //新版本的函数V2 (v2版本传入的第一个参数是array且只传一个array)
    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewList_Begin_V2'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($page);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    //修正首个参数使用array而不传入后续参数的情况
    if (is_array($page) && $fpargs_count == 1) {
        $object = $page;
        $isrewrite = true;
        $cate = GetValueInArray($page, 'cate', null);
        $auth = GetValueInArray($page, 'auth', null);
        $date = GetValueInArray($page, 'date', null);
        $tags = GetValueInArray($page, 'tags', null);
        $posttype = GetValueInArray($page, 'posttype', 0);
        $return_url = GetValueInArray($page, '_return_url', false);
        $route = GetValueInArray($page, '_route', array());
        $page = GetValueInArray($page, 'page', null);
    } else {
        if (!is_array($object)) {
            $object = array();
        }
        $return_url = GetValueInArray($object, '_return_url', false);
        $route = GetValueInArray($object, '_route', array());
        $posttype = GetValueInArray($object, 'posttype', 0);
    }

    //老版本的兼容接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
        $fpargs_v1 = array($page, $cate, $auth, $date, $tags, $isrewrite, $object);
        $fpreturn = call_user_func_array($fpname, $fpargs_v1);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    $type = 'index';
    if ($cate !== null) {
        $type = 'category';
    }

    if ($auth !== null) {
        $type = 'author';
    }

    if ($date !== null) {
        $type = 'date';
    }

    if ($tags !== null) {
        $type = 'tag';
    }

    $category = null;
    $author = null;
    $datetime = null;
    $tag = null;

    $w = array();
    $w[] = array('=', 'log_Type', $posttype);
    $w[] = array('=', 'log_Status', 0);

    $page = (int) $page == 0 ? 1 : (int) $page;

    $articles = array();
    $articles_top = array();

    switch ($type) {
            //#######################################################################################################
        case 'index':
            if (!empty($route)) {
                $pagebar = new Pagebar($route);
            } else {
                $pagebar = new Pagebar($zbp->GetPostType($posttype, 'list_urlrule'), true, true);
            }
            if ($zbp->option['ZC_LISTONTOP_TURNOFF'] == false) {
                $w[] = array('=', 'log_IsTop', 0);
            } else {
                $w[] = array('>=', 'log_IsTop', 0);
            }
            if (0 == $posttype) {
                $pagebar->Count = $zbp->cache->normal_article_nums;
            }
            $list_template = $zbp->GetPostType($posttype, 'list_template');
            if ($page == 1) {
                $zbp->title = $zbp->subname;
            } else {
                $zbp->title = str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
            }
            break;
            //#######################################################################################################
        case 'category':
            if (!empty($route)) {
                $pagebar = new Pagebar($route);
            } else {
                $pagebar = new Pagebar($zbp->GetPostType($posttype, 'list_category_urlrule'));
            }
            if ($zbp->option['ZC_LISTONTOP_TURNOFF'] == false) {
                $w[] = array('=', 'log_IsTop', 0);
            } else {
                $w[] = array('>=', 'log_IsTop', 0);
            }
            $category = new Category();

            if (!is_array($cate)) {
                $cateId = $cate;
                $cate = array();
                if (strpos($zbp->GetPostType($posttype, 'list_category_urlrule'), '{%id%}') !== false) {
                    $cate['id'] = $cateId;
                }
                if (strpos($zbp->GetPostType($posttype, 'list_category_urlrule'), '{%alias%}') !== false) {
                    $cate['alias'] = $cateId;
                }
            }
            if (isset($cate['id'])) {
                $category = $zbp->GetCategoryByID($cate['id']);
            } else {
                $category = $zbp->GetCategoryByAlias($cate['alias'], $posttype);
                if (empty($category->ID)) {
                    $category = $zbp->GetCategoryByAliasOrName($cate['alias'], $posttype);
                }
            }

            if (empty($category->ID)) {
                if (!empty($route) || $isrewrite == true) {
                    return false;
                }

                $zbp->ShowError(2, __FILE__, __LINE__);
            }
            if ($page == 1) {
                $zbp->title = $category->Name;
            } else {
                $zbp->title = $category->Name . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
            }
            $list_template = $category->Template;

            if (!$zbp->option['ZC_DISPLAY_SUBCATEGORYS']) {
                $w[] = array('=', 'log_CateID', $category->ID);
                $pagebar->Count = $category->Count;
            } else {
                $arysubcate = array();
                $arysubcate[] = array('log_CateID', $category->ID);
                if (isset($zbp->categories[$category->ID])) {
                    foreach ($zbp->categories[$category->ID]->ChildrenCategories as $subcate) {
                        $arysubcate[] = array('log_CateID', $subcate->ID);
                    }
                }
                $w[] = array('array', $arysubcate);
            }

            $pagebar->UrlRule->Rules['{%id%}'] = $category->ID;
            $pagebar->UrlRule->Rules['{%alias%}'] = $category->Alias == '' ? rawurlencode($category->Name) : $category->Alias;
            $pagebar->UrlRule->RulesObject = $category;
            break;
            //#######################################################################################################
        case 'author':
            if (!empty($route)) {
                $pagebar = new Pagebar($route);
            } else {
                $pagebar = new Pagebar($zbp->GetPostType($posttype, 'list_author_urlrule'));
            }
            $w[] = array('>=', 'log_IsTop', 0);

            $author = new Member();

            if (!is_array($auth)) {
                $authId = $auth;
                $auth = array();
                if (strpos($zbp->GetPostType($posttype, 'list_author_urlrule'), '{%id%}') !== false) {
                    $auth['id'] = $authId;
                }
                if (strpos($zbp->GetPostType($posttype, 'list_author_urlrule'), '{%alias%}') !== false) {
                    $auth['alias'] = $authId;
                }
            }
            if (isset($auth['id'])) {
                /* @var Member $author */
                $author = $zbp->GetMemberByID($auth['id']);
            } else {
                /* @var Member $author */
                $author = $zbp->GetMemberByNameOrAlias($auth['alias']);
            }

            if (empty($author->ID)) {
                if (!empty($route) || $isrewrite == true) {
                    return false;
                }

                $zbp->ShowError(2, __FILE__, __LINE__);
            }
            if ($page == 1) {
                $zbp->title = $author->StaticName;
            } else {
                $zbp->title = $author->StaticName . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
            }
            $list_template = $author->Template;
            $w[] = array('=', 'log_AuthorID', $author->ID);
            //$pagebar->Count = $author->Articles;
            $pagebar->UrlRule->Rules['{%id%}'] = $author->ID;
            $pagebar->UrlRule->Rules['{%alias%}'] = $author->Alias == '' ? rawurlencode($author->Name) : $author->Alias;
            $pagebar->UrlRule->RulesObject = $author;
            break;
            //#######################################################################################################
        case 'date':
            if (!empty($route)) {
                $pagebar = new Pagebar($route);
            } else {
                $pagebar = new Pagebar($zbp->GetPostType($posttype, 'list_date_urlrule'));
            }
            $w[] = array('>=', 'log_IsTop', 0);

            if (!is_array($date)) {
                $datetime = $date;
            } else {
                $datetime = $date['date'];
            }

            $hasDay = false;

            $datetime_txt = $datetime;
            $datetime = null;

            if (function_exists('date_create_from_format')) {
                $objdate = date_create_from_format($zbp->option['ZC_DATETIME_WITHDAY_RULE'], $datetime_txt);
                if ($objdate !== false) {
                    $datetime = strtotime($objdate->format('Y-n-j'));
                    $hasDay = true;
                } else {
                    $objdate = date_create_from_format($zbp->option['ZC_DATETIME_RULE'] . '-j', $datetime_txt . '-1');
                    if ($objdate !== false) {
                        $datetime = strtotime($objdate->format('Y-n'));
                    }
                }
            } else {
                $datetime_txt = str_replace($zbp->option['ZC_DATETIME_SEPARATOR'], '-', $datetime_txt);
                if (substr_count($datetime_txt, '-') > 1) {
                    $hasDay = true;
                }
                if (strtotime($datetime_txt) !== false) {
                    $datetime = strtotime($datetime_txt);
                }
            }
            if (!is_int($datetime)) {
                if (!empty($route) || $isrewrite == true) {
                    return false;
                }

                $zbp->ShowError(2, __FILE__, __LINE__);
            }

            if ($hasDay) {
                $datetitle = str_replace(array('%y%', '%m%', '%d%'), array(date('Y', $datetime), date('n', $datetime), date('j', $datetime)), $zbp->lang['msg']['year_month_day']);
            } else {
                $datetitle = str_replace(array('%y%', '%m%'), array(date('Y', $datetime), date('n', $datetime)), $zbp->lang['msg']['year_month']);
            }

            if ($page == 1) {
                $zbp->title = $datetitle;
            } else {
                $zbp->title = $datetitle . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
            }

            $zbp->modulesbyfilename['calendar']->Content = ModuleBuilder::Calendar(date('Y', $datetime) . '-' . date('n', $datetime));

            $list_template = $zbp->GetPostType($posttype, 'date_template');

            if ($hasDay) {
                $w[] = array('BETWEEN', 'log_PostTime', $datetime, strtotime('+1 day', $datetime));
                $pagebar->UrlRule->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_WITHDAY_RULE'], $datetime);
            } else {
                $w[] = array('BETWEEN', 'log_PostTime', $datetime, strtotime('+1 month', $datetime));
                $pagebar->UrlRule->Rules['{%date%}'] = date($zbp->option['ZC_DATETIME_RULE'], $datetime);
            }

            $pagebar->UrlRule->RulesObject = new ZbpDate($datetime);
            $datetime = Metas::ConvertArray(getdate($datetime));
            break;
            //#######################################################################################################
        case 'tag':
            if (!empty($route)) {
                $pagebar = new Pagebar($route);
            } else {
                $pagebar = new Pagebar($zbp->GetPostType($posttype, 'list_tag_urlrule'));
            }
            $w[] = array('>=', 'log_IsTop', 0);

            $tag = new Tag();

            if (!is_array($tags)) {
                $tagId = $tags;
                $tags = array();
                if (strpos($zbp->GetPostType($posttype, 'list_tag_urlrule'), '{%id%}') !== false) {
                    $tags['id'] = $tagId;
                }
                if (strpos($zbp->GetPostType($posttype, 'list_tag_urlrule'), '{%alias%}') !== false) {
                    $tags['alias'] = $tagId;
                }
            }
            if (isset($tags['id'])) {
                $tag = $zbp->GetTagByID($tags['id']);
            } else {
                $tag = $zbp->GetTagByAliasOrName($tags['alias'], $posttype);
            }

            if ($tag->ID == 0) {
                if (!empty($route) || $isrewrite == true) {
                    return false;
                }

                $zbp->ShowError(2, __FILE__, __LINE__);
            }

            if ($page == 1) {
                $zbp->title = $tag->Name;
            } else {
                $zbp->title = $tag->Name . ' ' . str_replace('%num%', $page, $zbp->lang['msg']['number_page']);
            }

            $list_template = $tag->Template;
            $w[] = array('LIKE', 'log_Tag', '%{' . $tag->ID . '}%');
            $pagebar->UrlRule->Rules['{%id%}'] = $tag->ID;
            $pagebar->UrlRule->Rules['{%alias%}'] = $tag->Alias == '' ? rawurlencode($tag->Name) : $tag->Alias;
            $pagebar->UrlRule->RulesObject = $tag;
            break;
        default:
            throw new Exception('Unknown type');
    }

    $pagebar->PageCount = $zbp->displaycount;
    $pagebar->PageNow = $page;
    $pagebar->PageBarCount = $zbp->pagebarcount;
    $pagebar->UrlRule->Rules['{%page%}'] = $page;

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewList_Core'] as $fpname => &$fpsignal) {
        $fpname($type, $page, $category, $author, $datetime, $tag, $w, $pagebar, $list_template);
    }

    if ($zbp->option['ZC_LISTONTOP_TURNOFF'] == false) {
        $articles_top_notorder = $zbp->GetTopPost($posttype);
        foreach ($articles_top_notorder as $articles_top_notorder_post) {
            if ($articles_top_notorder_post->TopType == 'global') {
                $articles_top[] = $articles_top_notorder_post;
            }
        }
        if ($type == 'index' && $page == 1) {
            foreach ($articles_top_notorder as $articles_top_notorder_post) {
                if ($articles_top_notorder_post->TopType == 'index') {
                    $articles_top[] = $articles_top_notorder_post;
                }
            }
        }
        if ($type == 'category' && $page == 1) {
            foreach ($articles_top_notorder as $articles_top_notorder_post) {
                if ($articles_top_notorder_post->TopType == 'category' && $articles_top_notorder_post->CateID == $category->ID) {
                    $articles_top[] = $articles_top_notorder_post;
                }
            }
        }
        if ($type == 'category' && $page == 1) {
            foreach ($articles_top_notorder as $articles_top_notorder_post) {
                if ($articles_top_notorder_post->TopType == 'categorys' && (($articles_top_notorder_post->Category->IsParents($category->ID)) || $articles_top_notorder_post->Category->ID == $category->ID)) {
                    $articles_top[] = $articles_top_notorder_post;
                }
            }
        }
    }

    $select = '';
    $order = array($zbp->displayorder => 'DESC');
    $limit = array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount);
    $option = array('pagebar' => $pagebar);
    ViewAuto_Process_Pagebar_Replace_Array($pagebar, $route, $fpargs);

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Article'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($select, $w, $order, $limit, $option, $type);
    }

    $articles = $zbp->GetPostList(
        $select,
        $w,
        $order,
        $limit,
        $option
    );

    if (count($articles) <= 0 && $page > 1) {
        $zbp->ShowError(2, __FILE__, __LINE__);
    }

    $articles_top_ids_array = GetIDArrayByList($articles_top, 'ID');
    if (!empty($articles_top_ids_array)) {
        foreach ($articles as $articles_key => $articles_value) {
            if (in_array($articles_value->ID, $articles_top_ids_array)) {
                unset($articles[$articles_key]);
            }
        }
    }

    $articles = array_merge($articles_top, $articles);

    $tagstring = null;
    foreach ($articles as $key => $article) {
        $tagstring .= $article->Tag;
    }
    $zbp->LoadTagsByIDString($tagstring);

    $zbp->LoadMembersInList($articles);

    $template = &$zbp->GetTemplate();

    $template->SetTags('title', $zbp->title);
    $template->SetTags('articles', $articles);
    if ($pagebar->PageAll == 0) {
        $pagebar = null;
    }

    $template->SetTags('posttype', $posttype);
    $template->SetTags('pagebar', $pagebar);
    $template->SetTags('type', $type);
    $template->SetTags('page', $page);

    $template->SetTags('date', $datetime);
    $template->SetTags('tag', $tag);
    $template->SetTags('author', $author);
    $template->SetTags('category', $category);

    if (is_object($pagebar) && isset($pagebar->buttons[$pagebar->PageNow])) {
        $template->SetTags('url', $pagebar->buttons[$pagebar->PageNow]);
    } else {
        $template->SetTags('url', $zbp->host);
    }
    $template->SetTags('args', $fpargs);
    $template->SetTags('route', $route);

    if ($template->hasTemplate($list_template)) {
        $template->SetTemplate($list_template);
    } else {
        $template->SetTemplate('index');
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewList_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($template);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    if ($return_url == true) {
        $url = $template->GetTags('url');
        return array('StatusCode' => 302, 'Location' => $url);
    }

    $template->Display();
    return true;
}

/**
 * 显示文章.
 *
 * @param array|int|string $id         文章ID/ ID/别名对象 (1.7起做为主要array型参数，后续的都作废了)
 * @param string           $alias     （如果有的话）文章别名
 * @param bool             $isrewrite  是否启用urlrewrite
 * @param array            $object     把1.7里新增array型参数传给旧版本的接口
 *
 * @api Filter_Plugin_ViewPost_Begin
 * @api Filter_Plugin_ViewPost_Begin_V2
 * @api Filter_Plugin_ViewPost_Template
 *
 * @throws Exception
 *
 * @return string
 */
function ViewPost($id = null, $alias = null, $isrewrite = false, $object = array())
{
    global $zbp;
    $fpargs = func_get_args();

    $fpargs_count = count($fpargs);

    //新版本的函数V2 (v2版本传入的第一个参数是array且只传一个array)
    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_Begin_V2'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($id);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    //修正首个参数使用array而不传入后续参数的情况
    if (is_array($id) && $fpargs_count == 1) {
        $object = $id;
        $isrewrite = true;
        $posttype = GetValueInArray($object, 'posttype', 0);
        $return_url = GetValueInArray($object, '_return_url', false);
        $route = GetValueInArray($object, '_route', array());
        $alias = GetValueInArray($object, 'alias', null);
        $id = GetValueInArray($object, 'id', null);
        //从别名post中读取正确的$id或$alias
        if (isset($route['args']) && is_array($route['args'])) {
            $parameters = UrlRule::ProcessParameters($route);
            foreach ($parameters as $key => $value) {
                if ($value['name'] == 'id' && $value['alias'] != '') {
                    $id = GetValueInArray($object, $value['alias'], null);
                }
                if ($value['name'] == 'alias' && $value['alias'] != '') {
                    $alias = GetValueInArray($object, $value['alias'], null);
                }
            }
        }
    } else {
        if (!is_array($object)) {
            $object = array();
        }
        $return_url = GetValueInArray($object, '_return_url', false);
        $route = GetValueInArray($object, '_route', array());
        $posttype = GetValueInArray($object, 'posttype', 0);
        if (is_array($id)) {
            $object = array_merge($object, $id);
            $id = isset($object['id']) ? $object['id'] : null;
            $alias = isset($object['alias']) ? $object['alias'] : null;
        } else {
            $object['id'] = $id;
            $object['alias'] = $alias;
            $object[0] = empty($alias) ? $id : $alias;
        }
    }

    //兼容老版本的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_Begin'] as $fpname => &$fpsignal) {
        $fpargs_v1 = array($id, $alias, $isrewrite, $object);
        $fpreturn = call_user_func_array($fpname, $fpargs_v1);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    $select = '';
    $w = array();
    $order = null;
    $limit = 1;
    $option = null;

    $w[] = array('=', 'log_Type', $posttype);

    if ($id !== null && is_numeric($id)) {
        $id = trim($id);
        if (function_exists('ctype_digit') && !ctype_digit((string) $id)) {
            $zbp->ShowError(3, __FILE__, __LINE__);
        }

        $w[] = array('=', 'log_ID', $id);
    } elseif ($alias !== null) {
        $alias = trim($alias);
        if ($zbp->option['ZC_POST_ALIAS_USE_ID_NOT_TITLE'] == false) {
            $w[] = array('array', array(array('log_Alias', $alias), array('log_Title', $alias)));
        } else {
            if (preg_match('/^[0-9]+$/', $alias) == 1) {
                $w[] = array('array', array(array('log_Alias', $alias), array('log_ID', $alias)));
            } else {
                $w[] = array('=', 'log_Alias', $alias);
            }
        }
    } else {
        $zbp->ShowError(2, __FILE__, __LINE__);
    }

    if (empty($zbp->user->ID)) {
        $w[] = array('=', 'log_Status', 0);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_Core'] as $fpname => &$fpsignal) {
        $fpname($select, $w, $order, $limit, $option);
    }

    $articles = $zbp->GetPostList($select, $w, $order, $limit, $option);
    if (count($articles) == 0) {
        if (!empty($route) || $isrewrite == true) {
            return false;
        }
        $zbp->ShowError(2, __FILE__, __LINE__);
    }

    $article = $articles[0];

    if ($posttype != $article->Type) {
        return false;
    }

    if ($article->Status != 0 && !$zbp->CheckRights($article->TypeActions['all']) && ($article->AuthorID != $zbp->user->ID)) {
        $zbp->ShowError(2, __FILE__, __LINE__);
    }

    if (!empty($route) || $isrewrite == true) {
        if (isset($object[0]) && !isset($object['page']) && (!isset($object['_verify_permalink']) || (isset($object['_verify_permalink']) && $object['_verify_permalink'] != false))) {
            if (strcasecmp($zbp->host . $object[0], urldecode($article->Url)) != 0) {
                //$zbp->ShowError(2, __FILE__, __LINE__);
                return false;
            }
        }
    }

    $zbp->LoadTagsByIDString($article->Tag);

    if (isset($zbp->option['ZC_VIEWNUMS_TURNOFF']) && $zbp->option['ZC_VIEWNUMS_TURNOFF'] == false) {
        if (count($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums']) > 0) {
            foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums'] as $fpname => &$fpsignal) {
                $article->ViewNums = $fpname($article);
            }
        } else {
            $article->ViewNums += 1;
            $sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_ViewNums' => $article->ViewNums), array(array('=', 'log_ID', $article->ID)));
            $zbp->db->Update($sql);
        }
    }

    $pagebar = new Pagebar('javascript:zbp.comment.get(\'' . $article->ID . '\',\'{%page%}\');', false);
    $pagebar->PageCount = $zbp->commentdisplaycount;
    $pagebar->PageNow = max((int) GetVars('cmt_page', 'GET'), 1);
    $pagebar->PageBarCount = $zbp->pagebarcount;
    $pagebar->UrlRule->RulesObject = &$article;

    if ($zbp->option['ZC_COMMENT_TURNOFF']) {
        $article->IsLock = true;
    }

    $comments = array();

    if (!$article->IsLock && $zbp->socialcomment == null) {
        $comments = $zbp->GetCommentList(
            '*',
            array(
                array('=', 'comm_LogID', $article->ID),
                array('=', 'comm_RootID', 0),
                array('=', 'comm_IsChecking', 0),
            ),
            array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
            array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
            array('pagebar' => $pagebar)
        );
        $rootid = array();
        foreach ($comments as &$comment) {
            $rootid[] = $comment->ID;
        }
        $comments2 = $zbp->GetCommentList(
            '*',
            array(
                array('=', 'comm_LogID', $article->ID),
                array('IN', 'comm_RootID', $rootid),
                array('=', 'comm_IsChecking', 0),
            ),
            array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
            null,
            null
        );
        $floorid = (($pagebar->PageNow - 1) * $pagebar->PageCount);
        foreach ($comments as &$comment) {
            $floorid += 1;
            $comment->FloorID = $floorid;
            $comment->Content = FormatString($comment->Content, '[enter]');
            if ($zbp->autofill_template_htmltags && strpos($zbp->template->templates['comment'], 'id="AjaxComment') === false) {
                $comment->Content .= '<label id="AjaxComment' . $comment->ID . '"></label>';
            }
        }
        foreach ($comments2 as &$comment) {
            $comment->Content = FormatString($comment->Content, '[enter]');
            if ($zbp->autofill_template_htmltags && strpos($zbp->template->templates['comment'], 'id="AjaxComment') === false) {
                $comment->Content .= '<label id="AjaxComment' . $comment->ID . '"></label>';
            }
        }
    }

    $zbp->LoadMembersInList($comments);

    $template = &$zbp->GetTemplate();

    $template->SetTags('posttype', $article->Type);
    $template->SetTags('title', ($article->Status == 0 ? '' : '[' . $zbp->lang['post_status_name'][$article->Status] . ']') . $article->Title);
    $template->SetTags('url', $article->Url);
    $template->SetTags('article', $article);
    $template->SetTags('type', $article->TypeName);
    $template->SetTags('page', 1);

    if ($pagebar->PageAll == 0 || $pagebar->PageAll == 1) {
        $pagebar = null;
    }
    $template->SetTags('pagebar', $pagebar);
    $template->SetTags('commentspagebar', $pagebar);
    $template->SetTags('commentspage', 1);
    $template->SetTags('comments', $comments);

    $template->SetTags('args', $fpargs);
    $template->SetTags('route', $route);

    if ($template->hasTemplate($article->Template)) {
        $template->SetTemplate($article->Template);
    } else {
        $template->SetTemplate($zbp->option['ZC_POST_DEFAULT_TEMPLATE']);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($template);
        if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
            $fpsignal = PLUGIN_EXITSIGNAL_NONE;

            return $fpreturn;
        }
    }

    if ($return_url == true) {
        $url = $template->GetTags('url');
        return array('StatusCode' => 302, 'Location' => $url);
    }

    $template->Display();
    return true;
}

/**
 * 显示文章下评论列表.
 *
 * @param int $postid 文章ID
 * @param int $page   页数
 *
 * @throws Exception
 *
 * @return bool
 */
function ViewComments($postid, $page)
{
    global $zbp;

    $post = new Post();
    $post = $zbp->GetPostByID($postid);
    $page = $page == 0 ? 1 : $page;
    $cmt_template = 'comments';

    $pagebar = new Pagebar('javascript:zbp.comment.get(\'' . $post->ID . '\',\'{%page%}\');');
    $pagebar->PageCount = $zbp->commentdisplaycount;
    $pagebar->PageNow = $page;
    $pagebar->PageBarCount = $zbp->pagebarcount;
    //$pagebar->Count = $post->CommNums;

    $comments = array();

    $comments = $zbp->GetCommentList(
        '*',
        array(
            array('=', 'comm_LogID', $post->ID),
            array('=', 'comm_RootID', 0),
            array('=', 'comm_IsChecking', 0),
        ),
        array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
        array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount),
        array('pagebar' => $pagebar)
    );
    $rootid = array();
    foreach ($comments as $comment) {
        $rootid[] = array('comm_RootID', $comment->ID);
    }
    $comments2 = $zbp->GetCommentList(
        '*',
        array(
            array('=', 'comm_LogID', $post->ID),
            array('array', $rootid),
            array('=', 'comm_IsChecking', 0),
        ),
        array('comm_ID' => ($zbp->option['ZC_COMMENT_REVERSE_ORDER'] ? 'DESC' : 'ASC')),
        null,
        null
    );

    $floorid = (($pagebar->PageNow - 1) * $pagebar->PageCount);
    foreach ($comments as &$comment) {
        $floorid += 1;
        $comment->FloorID = $floorid;
        $comment->Content = FormatString($comment->Content, '[enter]');
        if ($zbp->autofill_template_htmltags && strpos($zbp->template->templates['comment'], 'id="AjaxComment') === false) {
            $comment->Content .= '<label id="AjaxComment' . $comment->ID . '"></label>';
        }
    }
    foreach ($comments2 as &$comment) {
        $comment->Content = FormatString($comment->Content, '[enter]');
        if ($zbp->autofill_template_htmltags && strpos($zbp->template->templates['comment'], 'id="AjaxComment') === false) {
            $comment->Content .= '<label id="AjaxComment' . $comment->ID . '"></label>';
        }
    }

    $template = &$zbp->GetTemplate();

    $template->SetTags('title', $zbp->title);
    $template->SetTags('article', $post);
    $template->SetTags('type', 'comment');
    if ($pagebar->PageAll == 1) {
        $pagebar = null;
    }
    $template->SetTags('pagebar', $pagebar);
    $template->SetTags('commentspagebar', $pagebar);
    $template->SetTags('commentspage', $page);
    $template->SetTags('comments', $comments);

    $template->SetTemplate($cmt_template);

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewComments_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($template);
    }

    $s = $template->Output();

    $a = explode('<label id="AjaxCommentBegin"></label>', $s);
    $s = $a[1];
    $a = explode('<label id="AjaxCommentEnd"></label>', $s);
    $s = $a[0];

    echo $s;

    return true;
}

/**
 * 显示评论.
 *
 * @param int $id 评论ID
 *
 * @throws Exception
 *
 * @return bool
 */
function ViewComment($id)
{
    global $zbp;

    $cmt_template = 'comment';
    /* @var Comment $comment */
    $comment = $zbp->GetCommentByID($id);
    $post = new Post();
    $post = $zbp->GetPostByID($comment->LogID);

    $comment->Content = FormatString(htmlspecialchars($comment->Content), '[enter]');
    if ($zbp->autofill_template_htmltags && strpos($zbp->template->templates['comment'], 'id="AjaxComment') === false) {
        $comment->Content .= '<label id="AjaxComment' . $comment->ID . '"></label>';
    }

    $template = &$zbp->GetTemplate();

    $template->SetTags('title', $zbp->title);
    $template->SetTags('comment', $comment);
    $template->SetTags('article', $post);
    $template->SetTags('type', 'comment');
    $template->SetTags('page', 1);
    $template->SetTemplate($cmt_template);

    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewComment_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($template);
    }

    $template->Display();

    return true;
}
