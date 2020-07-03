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

    $upload = null;
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
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 删除附件接口.
 */
function api_upload_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadDel');

    if (!DelUpload()) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 列出附件接口.
 */
function api_upload_list()
{
    global $zbp;

    ApiCheckAuth(true, 'UploadMng');

    $listArr = ApiGetObjectArrayList(
        $zbp->GetUploadList()
    );

    ApiResponse($listArr);
}
