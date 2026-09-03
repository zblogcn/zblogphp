<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 *
 * @version 1.0 2020-07-04
 */

/**
 * 获取文章/页面接口.
 *
 * @return array
 */
function api_post_get()
{
    global $zbp;

    $postId = (int) GetVars('id');

    $relation_info = [
        'Author' => [
            'other_props' => ['Url', 'Template', 'Avatar', 'StaticName'],
            'remove_props' => ['Guid', 'Password', 'IP'],
        ],
    ];
    $relation_info['Category'] = [
        'other_props' => ['Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'],
    ];
    $relation_info['Tags'] = [
        'other_props' => ['Url', 'Template'],
    ];

    if ($postId > 0) {
        $post = new Post();
        // 判断 id 是否有效
        if ($post->LoadInfoByID($postId)) {
            //if ($post->Type != ZC_POST_TYPE_PAGE) {
            //}
            if (ZC_POST_STATUS_PUBLIC != $post->Status
                && !($zbp->user->ID > 0 && $post->AuthorID == $zbp->user->ID)
            ) {
                // 非公开内容（草稿或审核状态）仅作者本人（已登录）或拥有 all 权限者可读取；
                // 作者被删除后 AuthorID 置为 0，匿名用户 ID 同为 0，仍需鉴权，不可匿名读取。
                ApiCheckAuth(true, $post->TypeActions['all']);
            }
            if (ZC_POST_STATUS_PUBLIC == $post->Status) {
                // 默认为公开状态的文章/页面
                ApiCheckAuth(false, $post->TypeActions['view']);
            }

            if (true == GetVars('viewnums')) {
                if (isset($zbp->option['ZC_VIEWNUMS_TURNOFF']) && false == $zbp->option['ZC_VIEWNUMS_TURNOFF']) {
                    if (count($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums']) > 0) {
                        foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums'] as $fpname => &$fpsignal) {
                            $post->ViewNums = $fpname($post);
                        }
                    } else {
                        ++$post->ViewNums;
                        $sql = $zbp->db->sql->Update($zbp->table['Post'], ['log_ViewNums' => $post->ViewNums], [['=', 'log_ID', $post->ID]]);
                        $zbp->db->Update($sql);
                    }
                }
            }

            $array = ApiGetObjectArray(
                $post,
                ['Url', 'TagsCount', 'TagsName', 'CommentPostKey', 'ValidCodeUrl'],
                [],
                ApiGetAndFilterRelationQuery($relation_info),
            );

            return [
                'data' => [
                    'post' => $array,
                ],
            ];
        }
    }

    return [
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    ];
}

/**
 * 新增/修改 文章/页面接口.
 *
 * @return array
 */
function api_post_post()
{
    global $zbp;

    $postType = (int) GetVars('Type', 'POST');
    $actions = $zbp->GetPostType($postType, 'actions');

    ApiCheckAuth(true, $actions['post']);

    //如果直接给分类名称没有给分类ID的话
    if (!isset($_POST['CateID']) && isset($_POST['CateName'])) {
        $_POST['CateID'] = $zbp->GetCategoryByName(trim($_POST['CateName']), $postType)->ID;
    }

    // 统一兼容：PostTime 可为时间字符串或时间戳（秒/毫秒）
    // - 数字：支持秒/毫秒时间戳
    // - 字符串：尝试 strtotime 解析并格式化为 'Y-m-d H:i:s'
    if (isset($_POST['PostTime'])) {
        $pt = $_POST['PostTime'];
        if (is_numeric($pt)) {
            $ptStr = (string) $pt;
            if (strlen($ptStr) >= 13) {
                $ts = (int) floor(((float) $ptStr) / 1000);
            } else {
                $ts = (int) $ptStr;
            }
        } else {
            $ts = strtotime((string) $pt);
            if (false === $ts || -1 === $ts) {
                return [
                    'code' => 500,
                    'message' => $GLOBALS['lang']['error']['103'],
                ];
            }
        }
        $_POST['PostTime'] = date('Y-m-d H:i:s', $ts);
    }

    try {
        if (ZC_POST_TYPE_ARTICLE == $postType) {
            // 默认为新增/修改文章
            $post = PostArticle();
        } elseif (ZC_POST_TYPE_PAGE == $postType) {
            // 新增/修改页面
            $post = PostPage();
        } else {
            // 新增/修改其它Post类型
            $post = PostPost();
        }
        $zbp->BuildModule();
        $zbp->SaveCache();

        if (false === $post) {
            return [
                'code' => 500,
                'message' => $GLOBALS['lang']['error']['11'],
            ];
        }

        $array = ApiGetObjectArray(
            $post,
            ['Url', 'TagsCount', 'TagsName', 'CommentPostKey', 'ValidCodeUrl'],
            [],
            ApiGetAndFilterRelationQuery(
                [
                    'Category' => [
                        'other_props' => ['Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'],
                    ],
                    'Author' => [
                        'other_props' => ['Url', 'Template', 'Avatar', 'StaticName'],
                        'remove_props' => ['Guid', 'Password', 'IP'],
                    ],
                    'Tags' => [
                        'other_props' => ['Url', 'Template'],
                    ],
                ],
            ),
        );

        return [
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => [
                'post' => $array,
            ],
        ];
    } catch (Exception $e) {
        return [
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        ];
    }
}

/**
 * 删除文章/页面接口.
 *
 * @return array
 */
function api_post_delete()
{
    global $zbp;

    ApiVerifyCSRF(true);

    $post = $zbp->GetPostByID((int) GetVars('id'));
    if (empty($post->ID)) {
        return [
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        ];
    }
    $type = $post->Type;

    // 默认为删除文章
    ApiCheckAuth(true, $post->TypeActions['del']);

    try {
        if (ZC_POST_TYPE_ARTICLE == $type) {
            // 默认为删除文章
            DelArticle();
        } elseif (ZC_POST_TYPE_PAGE == $type) {
            // 删除页面
            DelPage();
        } else {
            // 删除其它Post类型
            DelPost();
        }
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        return [
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        ];
    }

    return [
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    ];
}

/**
 * 列出文章/页面接口.
 *
 * @return array
 */
function api_post_list()
{
    global $zbp;

    $cateId = (int) GetVars('cate_id');
    $tagId = (int) GetVars('tag_id');
    $authId = (int) GetVars('auth_id');
    $date = GetVars('date');
    $mng = (int) trim(GetVars('manage', '', '')); //&manage=1
    $type = (int) GetVars('type');
    $actions = $zbp->GetPostType($type, 'actions');
    $search = (string) GetVars('search');

    if (null !== GetVars('cate_alias')) {
        $category = $zbp->GetCategoryByAlias(GetVars('cate_alias'));
        $cateId = $category->ID;
    }
    if (null !== GetVars('auth_name')) {
        $member = $zbp->GetMemberByName(GetVars('auth_name'));
        $authId = $member->ID;
    }

    // 组织查询条件
    $where = [];
    if ($cateId > 0) {
        if (false == GetVars('with_subcate')) {
            $where[] = ['=', 'log_CateID', $cateId];
        } else {
            $arysubcate = [];
            $arysubcate[] = ['log_CateID', $cateId];
            if (isset($zbp->categories[$cateId])) {
                foreach ($zbp->categories[$cateId]->ChildrenCategories as $subcate) {
                    $arysubcate[] = ['log_CateID', $subcate->ID];
                }
            }
            $where[] = ['array', $arysubcate];
        }
    }
    if ($tagId > 0) {
        $where[] = ['LIKE', 'log_Tag', '%{' . $tagId . '}%'];
    }
    if (!empty($authId)) {
        $where[] = ['=', 'log_AuthorID', $authId];
    }
    if (!empty($date)) {
        $time = strtotime(GetVars('date', 'GET', ''));
        if (strrpos($date, '-') !== strpos($date, '-')) {
            $where[] = ['BETWEEN', 'log_PostTime', $time, strtotime('+1 day', $time)];
        } else {
            $where[] = ['BETWEEN', 'log_PostTime', $time, strtotime('+1 month', $time)];
        }
    }
    if (!empty($search)) {
        ApiCheckAuth(false, 'search');
        $type = 0;
        $search = trim(htmlspecialchars($search));
        $where[] = ['search', 'log_Content', 'log_Intro', 'log_Title', $search];
    }

    $where[] = ['=', 'log_Type', $type];
    // 权限验证
    if (0 != $mng) {
        //检查管理模式权限
        ApiCheckAuth(true, $actions['manage']);
        // 如果没有管理all权限
        if (!$zbp->CheckRights($actions['all'])) {
            $where[] = ['=', 'log_AuthorID', $zbp->user->ID];
        }
        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    } else {
        // 默认非管理模式
        ApiCheckAuth(false, $actions['view']);
        $limitCount = $zbp->option['ZC_API_DISPLAY_COUNT'];
        $where[] = ['=', 'log_Status', 0];
    }

    $filter = ApiGetRequestFilter(
        $limitCount,
        [
            'ID' => 'log_ID',
            'CreateTime' => 'log_CreateTime',
            'PostTime' => 'log_PostTime',
            'UpdateTime' => 'log_UpdateTime',
            'CommNums' => 'log_CommNums',
            'ViewNums' => 'log_ViewNums',
        ],
    );
    $select = '';
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    foreach ($GLOBALS['hooks']['Filter_Plugin_API_Post_List_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($select, $where, $order, $limit, $option);
    }

    $listArr = ApiGetObjectArrayList(
        $zbp->GetPostList($select, $where, $order, $limit, $option),
        ['Url', 'TagsCount', 'TagsName', 'CommentPostKey', 'ValidCodeUrl'],
        (0 != GetVars('without_content')) ? ['Content'] : [],
        ApiGetAndFilterRelationQuery(
            [
                'Category' => [
                    'other_props' => ['Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'],
                ],
                'Author' => [
                    'other_props' => ['Url', 'Template', 'Avatar', 'StaticName'],
                    'remove_props' => ['Guid', 'Password', 'IP'],
                ],
                'Tags' => [
                    'other_props' => ['Url', 'Template'],
                ],
            ],
        ),
    );
    $paginationArr = ApiGetPagebarInfo($option);

    return [
        'data' => [
            'list' => $listArr,
            'pagebar' => $paginationArr,
        ],
    ];
}
