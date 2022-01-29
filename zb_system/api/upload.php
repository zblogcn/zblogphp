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

    ApiCheckAuth(true, 'UploadMng');

    $uploadId = (int) GetVars('id');

    if ($uploadId > 0) {
        $upload = $zbp->GetUploadByID($uploadId);

        if ($upload->AuthorID != $zbp->user->ID) {
            ApiCheckAuth(true, 'UploadAll');
        }

        $uploadData = ApiGetObjectArray(
            $upload,
            array('Url'),
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
    global $zbp;
    ApiCheckAuth(true, 'UploadPst');

    try {
        $upload = PostUpload();

        if ($upload == false) {
            throw new Exception($zbp->lang['error'][21]);
        }

        $array = ApiGetObjectArray($upload, array('Url'));

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array('upload' => $array),
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

    ApiVerifyCSRF(true);

    if ($zbp->GetUploadByID((int) GetVars('id', 'GET'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
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

    if (!$zbp->CheckRights('UploadAll')) {
        if ($authId == $zbp->user->ID) {
            $where[] = array('=', 'ul_AuthorID', $authId);
        } else {
            $where[] = array('=', 'ul_AuthorID', $zbp->user->ID);
        }
    } else {
        if ($authId > 0) {
            $where[] = array('=', 'ul_AuthorID', $authId);
        }
    }

    if (!$zbp->CheckRights('UploadAll')) {
        $post = $zbp->GetPostByID($postId);
        if ($post->AuthorID == $zbp->user->ID) {
            $where[] = array('=', 'ul_LogID', $post->AuthorID);
        }
    } else {
        if ($postId > 0) {
            $where[] = array('=', 'ul_LogID', $postId);
        }
    }

    $filter = ApiGetRequestFilter(
        $zbp->option['ZC_MANAGE_COUNT'],
        array(
            'ID' => 'ul_ID',
            'PostTime' => 'ul_PostTime',
            'DownNums' => 'ul_DownNums'
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    $data = array(
        array(
            'list' => ApiGetObjectArrayList(
                $zbp->GetUploadList('*', $where, $order, $limit, $option),
                array('Url'),
                array(),
                ApiGetAndFilterRelationQuery(
                    array(
                        'Author' => array(
                            'other_props' => array('Url', 'Template', 'Avatar', 'StaticName'),
                            'remove_props' => array('Guid', 'Password', 'IP')
                        ),
                    )
                )
            ),
            'pagination' => ApiGetPagebarInfo($option),
        )
    );

    return compact('data');
}
