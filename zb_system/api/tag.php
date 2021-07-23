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

    ApiCheckAuth(false, 'view');

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

    ApiVerifyCSRF(true);
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

    $type = (int) GetVars('type');
    $mng = (int) strtolower((string) GetVars('manage')); //&manage=1

    $where = array();
    $where[] = array('=', 'tag_Type', $type);

    // 权限验证
    //检查管理模式权限
    if ($mng != 0) {
        //检查管理模式权限
        ApiCheckAuth(true, 'TagMng');
        //ApiCheckAuth(true, 'TagAll');

        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    } else {
        // 默认非管理模式
        ApiCheckAuth(true, 'view');
        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    }

    $filter = ApiGetRequestFilter(
        $limitCount,
        array(
            'ID' => 'tag_ID',
            'Order' => 'tag_Order',
            'Count' => 'tag_Count',
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    $listArr = ApiGetObjectArrayList(
        $zbp->GetTagList('*', $where, $order, $limit, $option),
        array('Url', 'Template')
    );
    $paginationArr = ApiGetPagebarInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagebar' => $paginationArr,
        ),
    );
}
