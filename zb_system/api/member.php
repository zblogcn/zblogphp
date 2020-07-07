<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-01
 */

/**
 * 用户登录接口.
 */
function api_member_login()
{
    global $zbp;

    ApiCheckAuth(false, 'login');

    $member = null;

    if ($zbp->Verify_MD5(trim(GetVars('username', 'POST')), md5(trim(GetVars('password', 'POST'))), $member)) {
        $zbp->user = $member;
        $sd = (float) GetVars('savedate', 'POST');
        $sd = ($sd < 1) ? 1 : $sd;
        $sdt = (time() + 3600 * 24 * $sd);

        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }

        $member_array = ApiGetObjectArray(
            $zbp->user,
            array('Url', 'Template', 'Avatar', 'StaticName'),
            array('Guid', 'Password', 'IP')
        );

        ApiResponse(
            array(
                'user' => $member_array,
                'token' => base64_encode($zbp->user->Name . '-' . $zbp->GenerateUserToken($member, (int) $sdt)),
            ),
            null,
            200,
            $GLOBALS['lang']['msg']['operation_succeed']
        );
    }

    ApiResponse(null, null, 401, $GLOBALS['lang']['error']['8']);
}

/**
 * 用户登出接口.
 */
function api_member_logout()
{
    ApiCheckAuth(true, 'logout');

    // 客户端自行删除 token 即可

    foreach ($GLOBALS['hooks']['Filter_Plugin_Logout_Succeed'] as $fpname => &$fpsignal) {
        $fpname();
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 新增/修改用户接口.
 */
function api_member_post()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberPst');

    try {
        PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }
}

/**
 * 获取用户信息接口.
 */
function api_member_get()
{
    global $zbp;

    ApiCheckAuth(false, 'ajax');

    $member = null;
    $memberId = GetVars('id');
    $memberName = GetVars('name');

    if ($memberId !== null) {
        $member = $zbp->GetMemberByID($memberId);
    } elseif (!empty($memberName)) {
        $member = $zbp->GetMemberByName($memberName);
    }

    if ($member && $member->ID !== null) {
        ApiResponse(
            ApiGetObjectArray(
                $member,
                array('Url', 'Template', 'Avatar', 'StaticName'),
                array('Guid', 'Password', 'IP')
            )
        );
    }

    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
}

/**
 * 删除用户接口.
 */
function api_member_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberDel');

    if (DelMember()) {
        $zbp->BuildModule();
        $zbp->SaveCache();
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    }

    ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
}

/**
 * 列出用户接口.
 */
function api_member_list()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberAll');

    $level = GetVars('level');
    $status = GetVars('status');

    // 组织查询条件
    $where = array();
    if (!is_null($level)) {
        $where[] = array('=', 'mem_Level', $level);
    }
    if (!is_null($status)) {
        $where[] = array('=', 'mem_Status', $status);
    }
    $filter = ApiGetRequestFilter(
        $GLOBALS['option']['ZC_DISPLAY_COUNT'],
        array(
            'id' => 'mem_ID',
            'create_time' => 'mem_CreateTime',
            'post_time' => 'mem_PostTime',
            'update_time' => 'mem_UpdateTime',
            'articles' => 'mem_Articles',
            'pages' => 'mem_Pages',
            'comments' => 'mem_Comments',
            'uploads' => 'mem_Uploads',
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    ApiResponse(
        array(
            'list' => ApiGetObjectArrayList(
                $zbp->GetMemberList('*', $where, $order, $limit, $option)
            ),
            'pagination' => ApiGetPaginationInfo($option),
        )
    );
}

/**
 * 获取用户权限接口.
 */
function api_member_get_auth()
{
    global $zbp;

    ApiCheckAuth(false, 'misc');

    $authArr = array(
        'user' => $zbp->user->Name,
        'auth' => array(),
    );

    foreach ($GLOBALS['actions'] as $key => $value) {
        if ($zbp->CheckRights($key)) {
            $authArr['auth'][$key] = array(
                'description' => $zbp->GetActionDescription($key),
                'checked' => $zbp->CheckRights($key) ? true : false
            );
        }
    }

    ApiResponse($authArr);
}
