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
 * 获取标签信息接口.
 */
function api_tag_get()
{
    global $zbp;

    ApiCheckAuth(false, 'ajax');

    $tag = null;
    $tagId = (int) GetVars('id');
    $tagAlias = GetVars('alias');
    $tagName = GetVars('name');

    if ($tagId > 0) {
        $tag = $zbp->GetTagByID($tagId);
    } elseif ($tagAlias !== null) {
        $tag = $zbp->GetTagByAlias($tagAlias);
    } else {
        $tag = $zbp->GetTagByName($tagName);
    }

    $array = ApiGetObjectArray($tag, array('Url', 'Template'));

    if ($tag && $tag->ID != null) {
        ApiResponse($array);
    }

    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
}

/**
 * 新增/修改标签接口.
 */
function api_tag_post()
{
    global $zbp;

    ApiCheckAuth(true, 'TagPst');

    try {
        PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 删除标签接口.
 */
function api_tag_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'TagDel');

    if (!DelTag()) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 列出标签接口.
 */
function api_tag_list()
{
    global $zbp;

    ApiCheckAuth(true, 'view');

    $listArr = ApiGetObjectArrayList(
        $zbp->GetTagList(),
        array('Url', 'Template')
    );

    ApiResponse($listArr);
}
