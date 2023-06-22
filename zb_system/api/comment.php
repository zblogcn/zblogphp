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
 * 获取评论接口.
 *
 * @return array
 */
function api_comment_get()
{
    global $zbp;

    ApiCheckAuth(false, 'getcmt');

    $comment = null;
    $commentId = (int) GetVars('id');

    if ($commentId > 0) {
        $comment = $zbp->GetCommentByID($commentId);
        $array = ApiGetObjectArray(
            $comment,
            array(),
            array(),
            ApiGetAndFilterRelationQuery(
                array(
                    'Post' => array(),
                    'Author' => array(
                        'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                        'remove_props' => array('Guid', 'Password', 'IP')
                    ),
                )
            )
        );

        if ($comment && $comment->ID != null) {
            return array(
                'data' => array('comment' => $array),
            );
        }
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 新增评论接口.
 *
 * @return array
 */
function api_comment_post()
{
    global $zbp;

    ApiCheckAuth(true, 'cmt');

    //要求登录后，不再验证系统的验证码。
    $zbp->option['ZC_COMMENT_VERIFY_ENABLE'] = false;

    try {
        $comment = PostComment();
        $zbp->BuildModule();
        $zbp->SaveCache();

        $array = ApiGetObjectArray(
            $comment,
            array(),
            array(),
            ApiGetAndFilterRelationQuery(
                array(
                    'Author' => array(
                        'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                        'remove_props' => array('Guid', 'Password', 'IP')
                    ),
                )
            )
        );

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array('comment' => $array),
        );
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }
}

/**
 * 删除评论接口.
 *
 * @return array
 */
function api_comment_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'CommentDel');

    ApiVerifyCSRF(true);

    if ($zbp->GetCommentByID((int) GetVars('id', 'GET'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    if (DelComment()) {
        $zbp->BuildModule();
        $zbp->SaveCache();

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
        );
    }

    return array(
        'code' => 500,
        'message' => $GLOBALS['lang']['msg']['operation_failed'],
    );
}

/**
 * 列出评论接口.
 *
 * @return array
 */
function api_comment_list()
{
    global $zbp;

    $mng = (int) trim(GetVars('manage')); //&manage=1
    $postId = (int) GetVars('post_id');
    $authId = (int) GetVars('auth_id');
    $ischecking = (int) GetVars('ischecking');
    $listArr = array();
    $where = array();

    if ($mng != 0) {
        // 列出所有评论
        ApiCheckAuth(true, 'CommentMng');
        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
        if (!$zbp->CheckRights('CommentAll')) {
            $authId = $zbp->user->ID;
        }
    } else {
        // 列出指定文章下的评论
        ApiCheckAuth(false, 'getcmt');
        $post = new Post();
        $post->LoadInfoByID($postId);
        if ($post->ID == 0 || $post->Status != 0) {
            return array(
                'code' => 404,
                'message' => $GLOBALS['lang']['error']['97'],
            );
        }
        $limitCount = $GLOBALS['option']['ZC_COMMENTS_DISPLAY_COUNT'];
        $ischecking = false;
    }

    $filter = ApiGetRequestFilter(
        $limitCount,
        array(
            'ID' => 'comm_ID',
            'PostTime' => 'comm_PostTime'
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    if ($postId > 0) {
        $where[] = array('=', 'comm_LogID', $postId);
    }
    if ($authId > 0) {
        $where[] = array('=', 'comm_AuthorID', $authId);
    }
    if (GetVars('root_id') !== null) {
        $rootId = (int) GetVars('root_id');
        $where[] = array('=', 'comm_RootID', $rootId);
    }
    if (GetVars('parent_id') !== null) {
        $parentId = (int) GetVars('parent_id');
        $where[] = array('=', 'comm_ParentID', $parentId);
    }
    $where[] = array('=', 'comm_Ischecking', (int) $ischecking);

    $listArr = ApiGetObjectArrayList(
        $zbp->GetCommentList('*', $where, $order, $limit, $option),
        array(),
        array(),
        ApiGetAndFilterRelationQuery(
            array(
                'Post' => array(),
                'Author' => array(
                    'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                    'remove_props' => array('Guid', 'Password', 'IP')
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

/**
 * 审核评论接口.
 *
 * @return array
 */
function api_comment_check()
{
    global $zbp;

    ApiCheckAuth(true, 'CommentChk');

    CheckComment();
    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 评论批量操作接口.
 *
 * @return array
 */
function api_comment_batch()
{
    global $zbp;

    ApiCheckAuth(true, 'CommentBat');

    BatchComment();
    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}
