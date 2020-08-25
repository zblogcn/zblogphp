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
        $array = ApiGetObjectArray($comment);

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

    ApiCheckAuth(false, 'cmt');

    try {
        PostComment();
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
 * 删除评论接口.
 *
 * @return array
 */
function api_comment_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'CommentDel');

    ApiVerifyCSRF();

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

    $postId = (int) GetVars('post_id');
    $listArr = array();
    $filter = ApiGetRequestFilter(
        $GLOBALS['option']['ZC_COMMENTS_DISPLAY_COUNT'],
        array(
            'id' => 'comm_ID',
            'post_time' => 'comm_PostTime'
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    if ($postId > 0) {
        // 列出指定文章下的评论
        ApiCheckAuth(false, 'getcmt');
        $post = new Post();
        if ($post->LoadInfoByID($postId)) {
            $listArr = ApiGetObjectArrayList(
                $zbp->GetCommentList(
                    '*',
                    array(
                        array('=', 'comm_LogID', $post->ID)
                    ),
                    $order,
                    $limit,
                    $option
                )
            );
            return array(
                'data' => $listArr,
            );
        }
    } else {
        // 列出所有评论
        ApiCheckAuth(true, 'CommentMng');
        $listArr = ApiGetObjectArrayList($zbp->GetCommentList('*', null, $order, $limit, $option));
    }

    $paginationArr = ApiGetPaginationInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagination' => $paginationArr,
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
