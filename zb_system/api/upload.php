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
 *
 * @return array
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
            return array(
                'data' => array('upload' => $uploadData,),
            );
        }
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 新增附件接口.
 *
 * @return array
 */
function api_upload_post()
{
    ApiCheckAuth(true, 'UploadPst');

    try {
        PostUpload();

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
        );
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }
}

/**
 * 删除附件接口.
 *
 * @return array
 */
function api_upload_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadDel');

    ApiVerifyCSRF();
    if (DelUpload()) {
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
 * 列出附件接口.
 *
 * @return array
 */
function api_upload_list()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadMng');

    $authId = (int) GetVars('author_id');
    $postId = (int) GetVars('post_id');

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
    $option = $filter['option'];

    $data = ApiGetObjectArrayList(
        array(
            'list' => ApiGetObjectArrayList(
                $zbp->GetUploadList('*', $where, $order, $limit, $option)
            ),
            'pagination' => ApiGetPaginationInfo($option),
        )
    );

    return compact('data');
}
