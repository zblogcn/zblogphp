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
 *
 * @return array
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
        return array(
            'data' => array('tag' => $array),
        );
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 新增/修改标签接口.
 *
 * @return array
 */
function api_tag_post()
{
    global $zbp;

    ApiCheckAuth(true, 'TagPst');

    try {
        $tag = PostTag();
        $zbp->BuildModule();
        $zbp->SaveCache();

        $array = ApiGetObjectArray($tag, array('Url'));

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array('tag' => $array),
        );
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
 * 删除标签接口.
 *
 * @return array
 */
function api_tag_delete()
{
    global $zbp;

    ApiVerifyCSRF();
    ApiCheckAuth(true, 'TagDel');

    if ($zbp->GetTagByID((int) GetVars('id', 'GET'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    if (!DelTag()) {
        return array(
            'message' => $GLOBALS['lang']['msg']['operation_failed'],
        );
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 列出标签接口.
 *
 * @return array
 */
function api_tag_list()
{
    global $zbp;

    ApiCheckAuth(true, 'view');

    $listArr = ApiGetObjectArrayList(
        $zbp->GetTagList(),
        array('Url', 'Template')
    );

    return array(
        'data' => array(
            'list' => $listArr,
        ),
    );
}
