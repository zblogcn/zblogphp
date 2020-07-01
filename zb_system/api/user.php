<?php

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

    if ($zbp->Verify_MD5(trim(GetVars('username', 'POST')), trim(GetVars('password', 'POST')), $member)) {
        $zbp->user = $member;
        $sd = (float) GetVars('savedate', 'POST');
        $sd = ($sd < 1) ? 1 : $sd;
        $sdt = time() + 3600 * 24 * $sd;

        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }

        ApiResponse(array(
            'user' => array(
                'id' => $zbp->user->ID,
                'username' => $zbp->user->Name,
                'static_name' => $zbp->user->StaticName,
                'level_name' => $zbp->user->LevelName,
                'level' => $zbp->user->Level,
            ),
            'token' => base64_encode($zbp->user->Name.'-'.$zbp->GenerateUserToken($member, (int) $sdt)),
        ), null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
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
        ApiResponse(array(
            'id' => $member->ID,
            'name' => $member->Name,
            'static_name' => $member->StaticName,
            'level_name' => $member->LevelName,
            'status' => $member->Status,
            'intro' => $member->Intro,
            'url' => $member->Url,
            'email' => $member->Email,
            'alias' => $member->Alias,
            'articles' => $member->Articles,
            'pages' => $member->Pages,
            'comments' => $member->Comments,
            'uploads' => $member->Uploads,
        ));
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

    $memberList = $zbp->GetMemberList();
    $listArr = array();

    foreach ($memberList as $member) {
        $memberArr = array();
        $memberArr['id'] = $member->ID;
        $memberArr['name'] = $member->Name;
        $memberArr['static_name'] = $member->StaticName;
        $memberArr['level_name'] = $member->LevelName;
        $memberArr['status'] = $member->Status;
        $memberArr['intro'] = $member->Intro;
        $memberArr['url'] = $member->Url;
        $memberArr['email'] = $member->Email;
        $memberArr['alias'] = $member->Alias;
        $memberArr['articles'] = $member->Articles;
        $memberArr['pages'] = $member->Pages;
        $memberArr['comments'] = $member->Comments;
        $memberArr['uploads'] = $member->Uploads;
        $listArr[] = $memberArr;
    }

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
