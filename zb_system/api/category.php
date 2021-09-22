<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-08-10
 */

/**
 * 获取分类信息接口.
 *
 * @return array
 */
function api_category_get()
{
    global $zbp;

    ApiCheckAuth(false, 'view');

    $category = null;
    $cateId = (int) GetVars('id');
    $cateAlias = GetVars('alias');
    $cateName = GetVars('name');

    if ($cateId > 0) {
        $category = $zbp->GetCategoryByID($cateId);
    } elseif ($cateAlias !== null) {
        $category = $zbp->GetCategoryByAlias($cateAlias);
    } else {
        $category = $zbp->GetCategoryByName($cateName);
    }

    ApiCheckAuth(false, $zbp->GetPostType_Sub($category->Type, 'actions', 'view'));

    $array = ApiGetObjectArray($category, array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'));

    if ($category && $category->ID != null) {
        return array(
            'data' => array('category' => $array),
        );
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 新增/修改分类接口.
 *
 * @return array
 */
function api_category_post()
{
    global $zbp;

    ApiCheckAuth(true, 'CategoryPst');

    try {
        $category = PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();

        $array = ApiGetObjectArray($category, array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'));

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array('category' => $array),
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
 * 删除分类接口.
 *
 * @return array
 */
function api_category_delete()
{
    global $zbp;

    ApiVerifyCSRF(true);
    ApiCheckAuth(true, 'CategoryDel');

    try {
        if ($zbp->GetCategoryByID((int) GetVars('id'))->ID == 0) {
            return array(
                'code' => 404,
                'message' => $GLOBALS['lang']['error']['97'],
            );
        }
        if (!DelCategory()) {
            return array(
                'message' => $GLOBALS['lang']['msg']['operation_failed'],
                'data' => array(
                    'id' => GetVars('id'),
                ),
            );
        }
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 列出分类接口.
 *
 * @return array
 */
function api_category_list()
{
    global $zbp;

    $type = (int) GetVars('type');
    $mng = (int) strtolower((string) GetVars('manage')); //&manage=1
    $rootid = GetVars('rootid');
    if (!is_null($rootid)) {
        $rootid = (int) $rootid;
    }
    $parentid = GetVars('parentid');
    if (!is_null($parentid)) {
        $parentid = (int) $parentid;
    }

    $limitCount = $zbp->option['ZC_MANAGE_COUNT'];

    // 权限验证
    //检查管理模式权限
    if ($mng != 0) {
        //检查管理模式权限
        ApiCheckAuth(true, 'CategoryMng');
        //ApiCheckAuth(true, 'CategoryAll');

        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    } else {
        // 默认非管理模式
        ApiCheckAuth(false, 'view');
        $limitCount = $zbp->option['ZC_MANAGE_COUNT'];
    }

    $filter = ApiGetRequestFilter(
        $limitCount,
        array(
            'ID' => 'cate_ID',
            'Order' => 'cate_Order',
            'Count' => 'cate_Count',
            'Group' => 'cate_Group',
        )
    );

    $where[] = array('=', 'cate_Type', $type);
    if (!is_null($rootid)) {
        $where[] = array('=', 'cate_RootID', $rootid);
    }
    if (!is_null($parentid)) {
        $where[] = array('=', 'cate_ParentID', $parentid);
    }
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    $listArr = ApiGetObjectArrayList(
        $zbp->GetCategoryList('*', $where, $order, $limit, $option),
        array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount')
    );

    $paginationArr = ApiGetPagebarInfo($option);

    return array(
        'data' => array(
            'list' => $listArr,
            'pagebar' => $paginationArr,
        ),
    );
}
