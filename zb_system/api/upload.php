<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-03
 */

/**
 * 获取附件信息接口.
 */
function api_upload_get()
{
    global $zbp;

    ApiCheckAuth(false, 'UploadMng');

    $uploadId = (int) GetVars('id');

    if ($uploadId > 0) {
        $upload = $zbp->GetUploadByID($uploadId);
        $uploadData = ApiGetObjectArray($upload);

        if ($upload && $upload->ID !== null) {
            ApiResponse($uploadData);
        }
    }

    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
}

/**
 * 新增附件接口.
 */
function api_upload_post()
{
    ApiCheckAuth(true, 'UploadPst');

    try {
        PostUpload();
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }
}

/**
 * 删除附件接口.
 */
function api_upload_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadDel');

    if (DelUpload()) {
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    }

    ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
}

/**
 * 列出附件接口.
 */
function api_upload_list()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadMng');

    $authId = (Int) GetVars('author_id');
    $postId = (Int) GetVars('post_id');

    // 组织查询条件
    $where = array();
    if ($authId > 0) {
        $where[] = array('=', 'ul_AuthorID', $authId);
    }
    if ($postId > 0) {
        $where[] = array('=', 'ul_LogID', $postId);
    }
    $filter = ApiGetRequestFilter(
        $GLOBALS['option']['ZC_DISPLAY_COUNT'],
        array(
            'id' => 'ul_ID',
            'post_time' => 'ul_PostTime',
            'name' => 'ul_Name',
            'source_name' => 'ul_SourceName',
            'downloads' => 'ul_DownNums'
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];

    $listArr = ApiGetObjectArrayList(
        $zbp->GetUploadList('*', $where, $order, $limit)
    );

    ApiResponse($listArr);
}
