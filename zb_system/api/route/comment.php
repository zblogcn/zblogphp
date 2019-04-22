<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
/**
 * Format single comment object.
 *
 * @param object $comment
 *
 * @return array
 */
function return_comment($id)
{
    global $zbp;

    $comment = $zbp->GetCommentByID($id);
    $ret = $comment->GetData();
    API::$IO->formatObjectName($ret);

    return $ret;
}

/**
 * Get comment.
 */
function api_comment_get_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    //
    $ret = return_comment($id);

    API::$IO->comment = $ret;
}
API::$Route->get('/comment/', 'api_comment_get_function');

/**
 * Get comments.
 */
function api_comments_get_function()
{
}
API::$Route->get('/comments/', 'api_comments_get_function');

/**
 * Create comment.
 */
function api_comment_create_function()
{
}
API::$Route->post('/comment/create/', 'api_comment_create_function');

/**
 * Update comment.
 */
function api_comment_update_function()
{
}
API::$Route->post('/comment/update/', 'api_comment_update_function');

/**
 * Update comment.
 */
function api_comment_delete_function()
{
}
API::$Route->post('/comment/delete/', 'api_comment_delete_function');
