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
 * 获取分类信息接口.
 */
function api_category_get()
{
    global $zbp;

    ApiCheckAuth(false, 'ajax');

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

    $array = ApiGetObjectArray($category, array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount'));

    if ($category && $category->ID != null) {
        ApiResponse(array('category' => $array));
    }

    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
}

/**
 * 新增/修改分类接口.
 */
function api_category_post()
{
    global $zbp;

    ApiCheckAuth(true, 'CategoryPst');

    try {
        PostCategory();
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 删除分类接口.
 */
function api_category_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'CategoryDel');

    if (!DelCategory()) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 列出分类接口.
 */
function api_category_list()
{
    global $zbp;

    ApiCheckAuth(true, 'view');

    $listArr = ApiGetObjectArrayList(
        $zbp->GetCategoryList(),
        array('Url', 'Symbol', 'Level', 'SymbolName', 'AllCount')
    );

    ApiResponse($listArr);
}
