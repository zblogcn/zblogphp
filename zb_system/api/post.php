<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
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

    $relation_info = array(
        'Author' => array(
            'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
            'remove_props' => array('Guid', 'Password', 'IP')
        ),
    );
    $relation_info['Category'] = array(
        'other_props' => array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'),
    );
    $relation_info['Tags'] = array(
        'other_props' => array('Url', 'Template'),
    );

    if ($postId > 0) {
        $post = new Post();
        // 判断 id 是否有效
        if ($post->LoadInfoByID($postId)) {
            //if ($post->Type != ZC_POST_TYPE_PAGE) {
            //}
            if ($post->Status != ZC_POST_STATUS_PUBLIC && $post->AuthorID != $zbp->user->ID) {
                // 不是本人的非公开页面（草稿或审核状态）
                ApiCheckAuth(true, $post->TypeActions['all']);
            }
            if ($post->Status == ZC_POST_STATUS_PUBLIC) {
                // 默认为公开状态的文章/页面
                ApiCheckAuth(false, $post->TypeActions['view']);
            }

            if (GetVars('viewnums') == true) {
                if (isset($zbp->option['ZC_VIEWNUMS_TURNOFF']) && $zbp->option['ZC_VIEWNUMS_TURNOFF'] == false) {
                    if (count($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums']) > 0) {
                        foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_ViewNums'] as $fpname => &$fpsignal) {
                            $post->ViewNums = $fpname($post);
                        }
                    } else {
                        $post->ViewNums += 1;
                        $sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_ViewNums' => $post->ViewNums), array(array('=', 'log_ID', $post->ID)));
                        $zbp->db->Update($sql);
                    }
                }
            }

            $array = ApiGetObjectArray(
                $post,
                array('Url','TagsCount','TagsName','CommentPostKey','ValidCodeUrl'),
                array(),
                ApiGetAndFilterRelationQuery($relation_info)
            );

            return array(
                'data' => array(
                    'post' => $array,
                ),
            );
        }
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
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

    try {
        if ($postType == ZC_POST_TYPE_ARTICLE) {
            // 默认为新增/修改文章
            $post = PostArticle();
        } elseif ($postType == ZC_POST_TYPE_PAGE) {
            // 新增/修改页面
            $post = PostPage();
        } else {
            // 新增/修改其它Post类型
            $post = PostPost();
        }
        $zbp->BuildModule();
        $zbp->SaveCache();

        if ($post === false) {
            return array(
                'code' => 500,
                'message' => $GLOBALS['lang']['error']['11'],
            );
        }

        $array = ApiGetObjectArray(
            $post,
            array('Url','TagsCount','TagsName','CommentPostKey','ValidCodeUrl'),
            array(),
            ApiGetAndFilterRelationQuery(
                array(
                    'Category' => array(
                        'other_props' => array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'),
                    ),
                    'Author' => array(
                        'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                        'remove_props' => array('Guid', 'Password', 'IP')
                    ),
                    'Tags' => array(
                        'other_props' => array('Url', 'Template'),
                    ),
                )
            )
        );

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array(
                'post' => $array,
            ),
        );
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
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
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    $type = $post->Type;

    // 默认为删除文章
    ApiCheckAuth(true, $post->TypeActions['del']);
    try {
        if ($type == ZC_POST_TYPE_ARTICLE) {
            // 默认为删除文章
            DelArticle();
        } elseif ($type == ZC_POST_TYPE_PAGE) {
            // 删除页面
            DelPage();
        } else {
            // 删除其它Post类型
            DelPost();
        }
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
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
    $mng = (int) trim(GetVars('manage')); //&manage=1
    $type = (int) GetVars('type');
    $actions = $zbp->GetPostType($type, 'actions');
    $search = (string) GetVars('search');

    if (GetVars('cate_alias') !== null) {
        $category = $zbp->GetCategoryByAlias(GetVars('cate_alias'));
        $cateId = $category->ID;
    }
    if (GetVars('auth_name') !== null) {
        $member = $zbp->GetMemberByName(GetVars('auth_name'));
        $authId = $member->ID;
    }

    // 组织查询条件
    $where = array();
    if ($cateId > 0) {
        if (GetVars('with_subcate') == false) {
            $where[] = array('=', 'log_CateID', $cateId);
        } else {
            $arysubcate = array();
            $arysubcate[] = array('log_CateID', $cateId);
            if (isset($zbp->categories[$cateId])) {
                foreach ($zbp->categories[$cateId]->ChildrenCategories as $subcate) {
                    $arysubcate[] = array('log_CateID', $subcate->ID);
                }
            }
            $where[] = array('array', $arysubcate);
        }
    }
    if ($tagId > 0) {
        $where[] = array('LIKE', 'log_Tag', '%{' . $tagId . '}%');
    }
    if (!empty($authId)) {
        $where[] = array('=', 'log_AuthorID', $authId);
    }
    if (!empty($date)) {
        $time = strtotime(GetVars('date', 'GET'));
        if (strrpos($date, '-') !== strpos($date, '-')) {
            $where[] = array('BETWEEN', 'log_PostTime', $time, strtotime('+1 day', $time));
        } else {
            $where[] = array('BETWEEN', 'log_PostTime', $time, strtotime('+1 month', $time));
        }
    }
    if (!empty($search)) {
        ApiCheckAuth(false, 'search');
        $type = 0;
        $search = trim(htmlspecialchars($search));
        $where[] = array('search', 'log_Content', 'log_Intro', 'log_Title', $search);
    }

    $where[] = array('=', 'log_Type', $type);
    // 权限验证
    if ($mng != 0) {
        //检查管理模式权限
        ApiCheckAuth(true, $actions['manage']);
        // 如果没有管理all权限
        if (!$zbp->CheckRights($actions['all'])) {
            $where[] = array('=', 'log_AuthorID', $zbp->user->ID);
        }
        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    } else {
        // 默认非管理模式
        ApiCheckAuth(false, $actions['view']);
        $limitCount = $zbp->option['ZC_DISPLAY_COUNT'];
        $where[] = array('=', 'log_Status', 0);
    }

    $filter = ApiGetRequestFilter(
        $limitCount,
        array(
            'ID' => 'log_ID',
            'CreateTime' => 'log_CreateTime',
            'PostTime' => 'log_PostTime',
            'UpdateTime' => 'log_UpdateTime',
            'CommNums' => 'log_CommNums',
            'ViewNums' => 'log_ViewNums'
        )
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
        array('Url', 'TagsCount', 'TagsName', 'CommentPostKey', 'ValidCodeUrl'),
        (GetVars('without_content') != 0) ? array('Content') : array(),
        ApiGetAndFilterRelationQuery(
            array(
                'Category' => array(
                    'other_props' => array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'),
                ),
                'Author' => array(
                    'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                    'remove_props' => array('Guid', 'Password', 'IP')
                ),
                'Tags' => array(
                    'other_props' => array('Url', 'Template'),
                ),
            )
        )
    );
    $paginationArr = ApiGetPagebarInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagebar' => $paginationArr,
        ),
    );
}
