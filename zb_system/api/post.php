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
            }
            // 默认为公开状态的文章/页面
            ApiCheckAuth(false, 'view');
            $array = ApiGetObjectArray($post);

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

    $postType = (Int) GetVars('Type', 'POST');

    if ($postType == 1) {
        // 新增/修改页面
        ApiCheckAuth(true, 'PagePst');
        try {
            PostPage();
            $zbp->BuildModule();
            $zbp->SaveCache();
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
            );
        }
    } else {
        // 默认为新增/修改文章
        ApiCheckAuth(true, 'ArticlePst');
        try {
            PostArticle();
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
 * 删除文章/页面接口.
 *
 * @return array
 */
function api_post_delete()
{
    global $zbp;

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
    $mng = strtolower((String) GetVars('mng'));
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
            'id' => 'log_ID',
            'create_time' => 'log_CreateTime',
            'post_time' => 'log_PostTime',
            'update_time' => 'log_UpdateTime',
            'comm_num' => 'log_CommNums',
            'view_num' => 'log_ViewNums'
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

    $listArr = ApiGetObjectArrayList($zbp->GetPostList('*', $where, $order, $limit, $option));
    $paginationArr = ApiGetPaginationInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagination' => $paginationArr,
        ),
    );
}
