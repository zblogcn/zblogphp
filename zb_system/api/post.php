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
    $postId = (int) GetVars('id');

    $relation_info = array(
        'Author' => array(
            'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
            'remove_props' => array('Guid', 'Password', 'IP')
        ),
    );

    if ($postId > 0) {
        $post = new Post();
        // 判断 id 是否有效
        if ($post->LoadInfoByID($postId)) {
            // 判断是文章还是页面
            if ($post->Type) {
                // 页面
                if ($post->Status > 0) {
                    // 非公开页面（草稿或审核状态）
                    ApiCheckAuth(true, 'PageEdt');
                }
            } else {
                // 文章
                if ($post->Status === 2) {
                    // 待审核文章
                    ApiCheckAuth(true, 'ArticlePub');
                } elseif ($post->Status === 1) {
                    // 草稿文章
                    ApiCheckAuth(true, 'ArticleEdt');
                }
                $relation_info['Category'] =  array(
                    'other_props' => array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'),
                );
            }
            // 默认为公开状态的文章/页面
            ApiCheckAuth(false, 'view');
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

    if ($postType == 1) {
        // 新增/修改页面
        ApiCheckAuth(true, 'PagePst');
        try {
            $post = PostPage();
            $zbp->BuildModule();
            $zbp->SaveCache();

            $array = ApiGetObjectArray(
                $post,
                array('Url','TagsCount','TagsName','CommentPostKey','ValidCodeUrl'),
                array(),
                ApiGetAndFilterRelationQuery(array(
                    'Author' => array(
                        'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                        'remove_props' => array('Guid', 'Password', 'IP')
                    ),
                ))
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
    } elseif ($postType == 0) {
        // 默认为新增/修改文章
        ApiCheckAuth(true, 'ArticlePst');
        try {
            $post = PostArticle();
            $zbp->BuildModule();
            $zbp->SaveCache();

            $array = ApiGetObjectArray(
                $post,
                array('Url','TagsCount','TagsName','CommentPostKey','ValidCodeUrl'),
                array(),
                ApiGetAndFilterRelationQuery(array(
                    'Category' => array(
                        'other_props' => array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'),
                    ),
                    'Author' => array(
                        'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                        'remove_props' => array('Guid', 'Password', 'IP')
                    ),
                ))
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

    ApiVerifyCSRF();

    if ($zbp->GetPostByID((int) GetVars('id'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    $type = strtolower((string) GetVars('type', 'POST'));

    if (!empty($type) && $type == 'page') {
        // 删除页面
        ApiCheckAuth(true, 'PageDel');
        try {
            DelPage();
            $zbp->BuildModule();
            $zbp->SaveCache();
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
            );
        }
    } else {
        // 默认为删除文章
        ApiCheckAuth(true, 'ArticleDel');
        try {
            DelArticle();
            $zbp->BuildModule();
            $zbp->SaveCache();
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
            );
        }
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
    $mng = strtolower((string) GetVars('mng'));
    $type = (int) GetVars('type');

    // 组织查询条件
    $where = array();
    if ($cateId > 0) {
        $where[] = array('=', 'log_CateID', $cateId);
    }
    if ($tagId > 0) {
        $where[] = array('LIKE', 'log_Tag', '%{' . $tagId . '}%');
    }
    if ($authId > 0) {
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
    $filter = ApiGetRequestFilter(
        $GLOBALS['option']['ZC_DISPLAY_COUNT'],
        array(
            'ID' => 'log_ID',
            'CreateTime' => 'log_CreateTime',
            'PostTime' => 'log_PostTime',
            'UpdateTime' => 'log_UpdateTime',
            'CommNums' => 'log_CommNums',
            'ViewNums' => 'log_ViewNums'
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    // 权限验证
    if ($type == ZC_POST_TYPE_PAGE) {
        // 列出页面
        $where[] = array('=', 'log_Type', ZC_POST_TYPE_PAGE);
        if (!empty($mng)) {
            // 管理页面
            ApiCheckAuth(true, 'PageMng');
        } else {
            // 默认非管理模式
            ApiCheckAuth(false, 'view');
        }
    } elseif ($type == ZC_POST_TYPE_ARTICLE) {
        // 列出文章
        $where[] = array('=', 'log_Type', ZC_POST_TYPE_ARTICLE);
        if ($mng == 'author') {
            // 管理作者所属文章
            ApiCheckAuth(true, 'ArticleMng');
        } elseif ($mng == 'admin') {
            // 管理所有文章
            ApiCheckAuth(true, 'ArticleAll');
        } else {
            // 默认非管理模式
            ApiCheckAuth(false, 'view');
        }
    } else {
        // 列出文章和页面
        ApiCheckAuth(true, 'ArticleAll');
    }

    $listArr = ApiGetObjectArrayList(
        $zbp->GetPostList('*', $where, $order, $limit, $option),
        array('Url','TagsCount','TagsName','CommentPostKey','ValidCodeUrl'),
        array(),
        ApiGetAndFilterRelationQuery(array(
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
        ))
    );
    $paginationArr = ApiGetPaginationInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagination' => $paginationArr,
        ),
    );
}
