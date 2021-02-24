<?php
/**
 * API相关函数.
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}


//###############################################################################################################

/**
 * API TokenVerify
 */
function ApiTokenVerify()
{
    global $zbp;

    if (!(is_subclass_of($zbp->user, 'BaseMember') && $zbp->user->Level > 0 && !empty($zbp->user->ID))) {
        // 在 API 中
        if (($auth = GetVars('HTTP_AUTHORIZATION', 'SERVER')) && (substr($auth, 0, 7) === 'Bearer ')) {
            // 获取 Authorization 头
            $api_token = substr($auth, 7);
        } else {
            // 获取（POST 或 GET 中的）请求参数
            $api_token = GetVars('token');
        }

        $user = $zbp->VerifyAPIToken($api_token);

        if ($user != null) {
            define('ZBP_IN_API_VERIFYBYTOKEN', true);
            $zbp->user = $user;
            $zbp->islogin = true;
        }
    }
}

/**
 * API 报错函数
 */
function ApiDebugDisplay($error)
{
    ApiResponse(null, $error);
}

/**
 * 载入 API Mods.
 */
function ApiLoadMods(&$mods)
{
    global $zbp;

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Extend_Mods'] as $fpname => &$fpsignal) {
        $add_mods = $fpname();

        if (!is_array($add_mods)) {
            continue;
        }

        foreach ($add_mods as $mod => $file) {
            $mod = strtolower($mod);
            if (array_key_exists($mod, $mods)) {
                continue;
            }
    
            $mods[$mod] = $file;
        }
    }

    // 从 zb_system/api/ 目录中载入 mods
    foreach (GetFilesInDir(ZBP_PATH . 'zb_system/api/', 'php') as $mod => $file) {
        $mods[$mod] = $file;
    }
}

/**
 * 移除指定的 API Mods.
 */
function ApiRemoveMods($name)
{
    global $mods;
    unset($mods[$name]);
    return $mods;
}

/**
 * 检查API Mods的白名单和黑名单.
 */
function ApiListCheck(&$mods_allow, &$mods_disallow)
{
    global $zbp, $mod, $act;

    //接口及对$mods_allow, $mods_disallow的添加
    foreach ($GLOBALS['hooks']['Filter_Plugin_API_ListCheck'] as $fpname => &$fpsignal) {
        $new_allow = $new_disallow = array();
        $fpname($new_allow, $new_disallow);

        $mods_allow = array_merge($mods_allow, $new_allow);
        $mods_disallow = array_merge($mods_disallow, $new_disallow);
    }

    $b = false;

    foreach ($mods_allow as $array) {
        if (!empty($array) && is_array($array)) {
            foreach ($array as $m => $a) {
                if ($mod == $m && ($a == '' || $act == $a)) {
                    $b = true;
                    break;
                }
            }
        }
    }

    if (!empty($mods_allow) && $b == false) {
        $zbp->ShowError(96, __FILE__, __LINE__);
    }

    $b = true;

    foreach ($mods_disallow as $array) {
        if (!empty($array) && is_array($array)) {
            foreach ($array as $m => $a) {
                if ($mod == $m && ($a == '' || $act == $a)) {
                    $b = false;
                    break;
                }
            }
        }
    }

    if (!empty($mods_disallow) && $b == false) {
        $zbp->ShowError(96, __FILE__, __LINE__);
    }
}

/**
 * API 响应.
 *
 * @param array|null $data
 * @param ZBlogException|null $error
 * @param int $code
 * @param string|null $message
 */
function ApiResponse($data = null, $error = null, $code = 200, $message = null)
{
    if (!empty($error)) {
        $error_info = array(
            'code' => ZBlogException::$error_id,
            'type' => $error->type,
            'message' => $error->message,
        );

        if ($GLOBALS['option']['ZC_DEBUG_MODE']) {
            $error_info['message_full'] = $error->messagefull;
            $error_info['file'] = $error->file;
            $error_info['line'] = $error->line;
        }

        if ($code === 200) {
            $code = 500;
        }
        if (empty($message)) {
            $message = 'System error: ' . $error->message;
        }
    }

    $response = array(
        'code' => $code,
        'message' => !empty($message) ? $message : 'OK',
        'data' => $data,
        'error' => empty($error) ? null : $error_info,
    );

    // 显示 Runtime 调试信息
    if (!defined('ZBP_API_IN_TEST') && $GLOBALS['option']['ZC_RUNINFO_DISPLAY']) {
        $runtime = RunTime(false);
        $runtime = array_slice($runtime, 0, 3);
        $response['runtime'] = $runtime;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Response'] as $fpname => &$fpsignal) {
        $fpname($response);
    }

    if (!defined('ZBP_API_IN_TEST')) {
        ob_end_clean();
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            if (!empty($error)) {
                SetHttpStatusCode(500);
            } else {
                SetHttpStatusCode(200);
            }
        }
    }

    echo JsonEncode($response);

    if (empty($error) && $code !== 200) {
        // 如果 code 不为 200，又不是系统抛出的错误，再来抛出一个 Exception，适配 phpunit
        ZBlogException::SuspendErrorHook();
        throw new Exception($message, $code);
    }

    die;
}

/**
 * API 检测权限.
 *
 * @param bool $loginRequire
 * @param string $action
 */
function ApiCheckAuth($loginRequire = false, $action = 'view')
{
    // 登录认证
    if ($loginRequire && !$GLOBALS['zbp']->user->ID) {
        ApiResponse(null, null, 401, $GLOBALS['lang']['error']['6']);
    }

    // 权限认证
    if (!$GLOBALS['zbp']->CheckRights($action)) {
        ApiResponse(null, null, 403, $GLOBALS['lang']['error']['6']);
    }

    return true;
}

/**
 * API 获取指定属性的Array
 *
 * @param object $object
 * @param array $other_props 追加的属性
 * @param array $remove_props 要删除的属性
 * @param array $with_relations 要追加的关联对象
 */
function ApiGetObjectArray($object, $other_props = array(), $remove_props = array(), $with_relations = array())
{
    $array = $object->GetData();
    unset($array['Meta']);

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Get_Object_Array'] as $fpname => &$fpsignal) {
        $fpname($object, $array, $other_props, $remove_props, $with_relations);
    }

    foreach ($other_props as $key => $value) {
        $array[$value] = $object->$value;
    }
    switch (get_class($object)) {
        case 'Member':
            $remove_props[] = 'Guid';
            $remove_props[] = 'Password';
            $remove_props[] = 'IP';
            break;
        default:
            # code...
            break;
    }

    foreach ($remove_props as $key => $value) {
        unset($array[$value]);
    }
    foreach ($with_relations as $relation => $info) {
        $relation_obj = $object->$relation;
        if (is_array($relation_obj)) {
            $array[$relation] = ApiGetObjectArrayList(
                $relation_obj,
                isset($info['other_props']) ? $info['other_props'] : array(),
                isset($info['remove_props']) ? $info['remove_props'] : array(),
                isset($info['with_relations']) ? $info['with_relations'] : array()
            );
        } else {
            $array[$relation] = ApiGetObjectArray(
                $relation_obj,
                isset($info['other_props']) ? $info['other_props'] : array(),
                isset($info['remove_props']) ? $info['remove_props'] : array(),
                isset($info['with_relations']) ? $info['with_relations'] : array()
            );
        }
    }
    return $array;
}

/**
 * API 获取指定属性的Array 列表.
 *
 * @param array $list
 * @param array $other_props 追加的属性
 * @param array $remove_props 要删除的属性
 * @param array $with_relations 要追加的关联对象
 */
function ApiGetObjectArrayList($list, $other_props = array(), $remove_props = array(), $with_relations = array())
{
    global $zbp;

    if (array_key_exists('Author', $with_relations)) {
        $zbp->LoadMembersInList($list);
    }

    foreach ($list as &$object) {
        $object = ApiGetObjectArray($object, $other_props, $remove_props, $with_relations);
    }

    return $list;
}

/**
 * API 获取约束过滤条件
 * 将请求中的参数转换为 SQL LIMIT/ORDER 查询条件.
 *
 * @param int $limitDefault 默认记录数
 * @param array $sortableColumns sortby 对应的模块数据表中支持排序的属性
 * @param int $max_count_perpage 每页最多条数
 * @return array
 */
function ApiGetRequestFilter($limitDefault = null, $sortableColumns = array(), $max_count_perpage = null)
{
    global $zbp;

    $condition = array(
        'limit' => array(0, $limitDefault),
        'order' => null,
        'option' => null,
    );
    $sortBy = (string) GetVars('sortby');
    $order = strtoupper((string) GetVars('order'));
    $pageNow = (int) GetVars('page');
    $perPage = (int) GetVars('perpage');

    $max_count_perpage = ($max_count_perpage !== null) ? $max_count_perpage : $zbp->apiMaxCountPerPage;

    if (($perPage > (int) $max_count_perpage) || ((int) $perPage <= 0)) {
        if ($limitDefault !== null) {
            $perPage = $limitDefault;
        } else {
            $perPage = 10;
        }
    }

    // 排序顺序
    if (!empty($sortBy) && isset($sortableColumns[$sortBy])) {
        $condition['order'] = array($sortableColumns[$sortBy] => 'ASC');
    }
    if (!is_null($condition['order']) && $order == 'DESC') {
        $condition['order'][$sortableColumns[$sortBy]] = $order;
    }

    if ($perPage) {
        $p = new Pagebar(null, false); // 第一个参数为 null，不需要分页 Url 处理
        $p->PageNow = (int) $pageNow == 0 ? 1 : (int) $pageNow;
        $p->PageCount = $perPage;
        $limit = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);
        $op = array('pagebar' => &$p);

        $condition['limit'] = $limit;
        $condition['option'] = $op;
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Get_Request_Filter'] as $fpname => &$fpsignal) {
        $fpname($condition);
    }
    return $condition;
}

/**
 * 获取分页信息.
 *
 * @param array|null $option
 * @return array
 */
function ApiGetPagebarInfo($option = null)
{
    if ($option === null) {
        // 用 stdClass 而不用 array() ，为了为空时 json 显示 {} 而不是 []
        return new stdClass;
    }

    $info = array();
    $pagebar = &$option['pagebar'];

    //$info['Count'] = $pagebar->Count;
    $info['AllCount'] = $pagebar->AllCount;
    $info['CurrentCount'] = $pagebar->CurrentCount;
    //$info['PageBarCount'] = $pagebar->PageBarCount;
    //$info['PageCount'] = $pagebar->PageCount;
    $info['PerPageCount'] = $pagebar->PerPageCount;
    $info['PageAll'] = $pagebar->PageAll;
    $info['PageNow'] = $pagebar->PageNow;
    $info['PageCurrent'] = $pagebar->PageCurrent;
    $info['PageFirst'] = $pagebar->PageFirst;
    $info['PageLast'] = $pagebar->PageLast;
    $info['PagePrevious'] = $pagebar->PagePrevious;
    $info['PageNext'] = $pagebar->PageNext;

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Get_Pagination_Info'] as $fpname => &$fpsignal) {
        $fpname($info, $pagebar);
    }
    return $info;
}

/**
 * API 获取及过滤关联对象请求.
 *
 * @param array $info 传入到 ApiGetObjectArray 的关联信息
 * @return array
 */
function ApiGetAndFilterRelationQuery($info)
{
    $relations_req = trim(GetVars('with_relations'));

    if (empty($relations_req)) {
        return array();
    }

    $relations = explode(',', $relations_req);
    $ret_relations = array();

    foreach ($relations as $relation) {
        $relation = trim($relation);
        if (array_key_exists($relation, $info)) {
            $ret_relations[$relation] = $info[$relation];
        }
    }

    return $ret_relations;
}

/**
 * API 传统登录时的 CSRF 验证.
 *
 * @param boolean $force_check 是否强制检查
 */
function ApiVerifyCSRF($force_check = false)
{
    global $zbp, $mod, $act;

    if (! defined('ZBP_IN_API_VERIFYBYTOKEN')) {
        $csrf_token = GetVars('csrf_token');

        if (! $force_check) {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return;
            }

            // 不需要校验 CSRF 的 API
            $skip_acts = array(
                array('mod' => 'member', 'act' => 'login'),
                array('mod' => 'comment', 'act' => 'post')
            );

            foreach ($GLOBALS['hooks']['Filter_Plugin_API_VerifyCSRF_Skip'] as $fpname => &$fpsignal) {
                $fpreturn = $fpname($skip_acts, $csrf_token);
                if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                    $fpsignal = PLUGIN_EXITSIGNAL_NONE;
        
                    return $fpreturn;
                }
            }

            foreach ($skip_acts as $api_act) {
                if (!isset($api_act['mod'])) {
                    continue;
                }
                if (!isset($api_act['act']) && $api_act['mod'] == $mod) {
                    // 如果只定义了 mod 并匹配，放行(比如说定义了 mod=member ，那 member mod 下所有 POST 都放行)
                    return;
                }
                if ($api_act['mod'] == $mod && $api_act['act'] == $act) {
                    // 匹配了 mod 和 act，放行
                    return;
                }
            }
        }

        if (! $zbp->VerifyCSRFToken($csrf_token, 'api')) {
            ApiResponse(null, null, 419, $GLOBALS['lang']['error']['5']);
        }
    }
}

/**
 * API 载入 POST 数据（前端 JSON）.
 */
function ApiLoadPostData()
{
    $input = file_get_contents('php://input');
    if ($input && ($data = json_decode($input, true)) && is_array($data)) {
        $_POST = array_merge($data, $_POST);
    }
}

/**
 * API 派发.
 *
 * @param array       $mods
 * @param string      $mod
 * @param string|null $act
 */
function ApiDispatch($mods, $mod, $act)
{
    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Dispatch'] as $fpname => &$fpsignal) {
        $fpname($mods, $mod, $act);
    }

    if (empty($act)) {
        $act = 'get';
    }

    if (isset($mods[$mod]) && file_exists($mod_file = $mods[$mod])) {
        include_once $mod_file;
        $func = 'api_' . $mod . '_' . $act;
        if (function_exists($func)) {
            $result = call_user_func($func);
    
            ApiResponse(
                isset($result['data']) ? $result['data'] : null,
                isset($result['error']) ? $result['error'] : null,
                isset($result['code']) ? $result['code'] : 200,
                isset($result['message']) ? $result['message'] : 'OK'
            );
        }
    }
    
    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['96']);
}

/**
 * API 地址生成.
 *
 * @param string $mod
 * @param string $act
 * @param array  $query
 *
 * @return string
 */
function ApiUrlGenerate($mod, $act = 'get', $query = array())
{
    global $zbp;

    $mod = strtolower($mod);
    $act = strtolower($act);

    if (count($query) > 0) {
        $query_string = '&' . http_build_query($query);
    } else {
        $query_string = '';
    }

    return $zbp->host . 'zb_system/api.php?mod=' . $mod . '&act=' . $act . $query_string;
}

/**
 * API 限流.
 *
 * @param string  $name
 * @param integer $max_reqs
 * @param integer $period
 */
function ApiThrottle($name = 'default', $max_reqs = 60, $period = 60)
{
    global $zbpcache;

    if (!isset($zbpcache)) {
        return;
    }

    $user_id = md5(GetGuestIP());

    $cache_key = "api-throttle:$name:$user_id";
    $cached_value = $zbpcache->Get($cache_key);
    $cached_req = json_decode($cached_value, true);
    if (!$cached_value || !$cached_req || (time() >= $cached_req['expire_time'])) {
        $cached_req = array('hits' => 0, 'expire_time' => (time() + $period));
    }

    if ($cached_req['hits'] >= $max_reqs) {
        ApiResponse(null, null, 429, 'Too many requests.');
    }

    $cached_req['hits']++;
    $zbpcache->Set($cache_key, json_encode($cached_req), ($cached_req['expire_time'] - time()));
}
