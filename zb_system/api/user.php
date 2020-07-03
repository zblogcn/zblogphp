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
function api_user_login()
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

        $user_array = ApiGetObjectArray(
            $zbp->user,
            array('Url', 'Template', 'Avatar', 'StaticName'),
            array('Guid', 'Password', 'IP')
        );

        ApiResponse(
            array(
                'user' => $user_array,
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
function api_user_logout()
{
    global $zbp;

    ApiCheckAuth(true, 'logout');

    // 客户端自行删除 token 即可

    foreach ($GLOBALS['hooks']['Filter_Plugin_Logout_Succeed'] as $fpname => &$fpsignal) {
        $fpname();
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 新增用户接口.
 */
function api_user_post()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberNew');

    try {
        PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 获取用户信息接口.
 */
function api_user_get()
{
    global $zbp;

    ApiCheckAuth(false, 'ajax');

    $member = null;
    $memberId = (int) GetVars('id');
    $memberName = GetVars('name');

    if ($memberId > 0) {
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
 * 更新用户接口.
 */
function api_user_update()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberEdt');

    try {
        PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage());
    }

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 删除用户接口.
 */
function api_user_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberDel');

    if (!DelMember()) {
        ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 列出用户接口.
 */
function api_user_list()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberAll');

    $listArr = ApiGetObjectArrayList(
        $zbp->GetMemberList()
    );

    ApiResponse($listArr);
}

/**
 * 获取用户权限接口.
 */
function api_user_get_auth()
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
