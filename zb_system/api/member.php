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
 *
 * @return array
 */
function api_member_login()
{
    global $zbp;

    ApiCheckAuth(false, 'login');

    $member = null;

    $password = trim(GetVars('password', 'POST'));
    $verify_ret = false;
    if ((bool) preg_match("/^[a-z0-9]{32}$/", $password) === true) {
        // 如果格式是 md5，优先直接验证（只是大概猜测是 md5，不保证是，所以如果失败会在下面重新验）
        $verify_ret = $zbp->Verify_MD5(trim(GetVars('username', 'POST')), $password, $member);
    }
    if ($verify_ret === false) {
        // 如果直接验证失败，或者没有验证，再接着验证
        $verify_ret = $zbp->Verify_MD5(trim(GetVars('username', 'POST')), md5($password), $member);
    }

    if ($verify_ret) {
        $zbp->user = $member;
        $sd = (float) GetVars('savedate', 'POST');
        $sd = ($sd < 1) ? 1 : $sd;
        $sd = ($sd > 365) ? 365 : $sd;
        $sdt = (int) (time() + 3600 * 24 * $sd);

        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }

        $member_array = ApiGetObjectArray(
            $zbp->user,
            array('Url', 'Template', 'Avatar', 'StaticName'),
            array('Guid', 'Password', 'IP')
        );

        return array(
            'data' => array(
                'user' => $member_array,
                'token' => $zbp->GenerateApiToken($member, $sdt),
                'expire_time' => $sdt,
            ),
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
        );
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Failed'] as $fpname => &$fpsignal) {
        $fpname();
    }

    return array(
        'code' => 401,
        'message' => $GLOBALS['lang']['error']['8'],
    );
}

/**
 * 用户登出接口.
 *
 * @return array
 */
function api_member_logout()
{
    ApiCheckAuth(true, 'logout');

    // 客户端自行删除 token 即可

    foreach ($GLOBALS['hooks']['Filter_Plugin_Logout_Succeed'] as $fpname => &$fpsignal) {
        $fpname();
    }

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 新增/修改用户接口.
 *
 * @return array
 */
function api_member_post()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberPst');

    // 避免 PostMember 出错
    if (! isset($_POST['Password'])) {
        $_POST['Password'] = '';
    }
    if (! isset($_POST['PasswordRe'])) {
        $_POST['PasswordRe'] = '';
    }

    try {
        //PostMember()内部有判断'MemberNew' or 'MemberEdt' or 'MemberAll'
        $member = PostMember();
        $zbp->BuildModule();
        $zbp->SaveCache();

        if ($member === false) {
            return array(
                'code' => 500,
                'message' => $GLOBALS['lang']['msg']['operation_failed'],
            );
        }

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array(
                'member' => ApiGetObjectArray(
                    $member,
                    array('Url', 'Template', 'Avatar', 'StaticName'),
                    array('Guid', 'Password', 'IP')
                ),
            ),
        );
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }
}

/**
 * 获取用户信息接口.
 *
 * @return array
 */
function api_member_get()
{
    global $zbp;

    $member = null;
    $memberId = GetVars('id');

    if ($memberId !== null) {
        $member = $zbp->GetMemberByID($memberId);
        ApiCheckAuth(true, 'MemberMng');
    } else {
        $member = $zbp->GetMemberByID($zbp->user->ID);
        ApiCheckAuth(false, 'api');
    }

    //如果不是读本人的
    if ($member->ID != $zbp->user->ID) {
        ApiCheckAuth(true, 'MemberAll');
    }

    if ($member && $member->ID != null) {
        return array(
            'data' => array(
                'member' => ApiGetObjectArray(
                    $member,
                    array('Url', 'Template', 'Avatar', 'StaticName'),
                    array('Guid', 'Password', 'IP')
                ),
            ),
        );
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 删除用户接口.
 *
 * @return array
 */
function api_member_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberDel');

    ApiVerifyCSRF(true);

    if ($zbp->GetMemberByID((int) GetVars('id'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    if (DelMember()) {
        $zbp->BuildModule();
        $zbp->SaveCache();
        
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
 * 列出用户接口.
 *
 * @return array
 */
function api_member_list()
{
    global $zbp;

    ApiCheckAuth(true, 'MemberMng');

    $level = GetVars('level');
    $status = GetVars('status');

    // 组织查询条件
    $where = array();
    if (!$zbp->CheckRights('MemberAll')) {
        $where[] = array('=', 'mem_ID', $zbp->user->ID);
    }
    if (!is_null($level)) {
        $where[] = array('=', 'mem_Level', $level);
    }
    if (!is_null($status)) {
        $where[] = array('=', 'mem_Status', $status);
    }
    $filter = ApiGetRequestFilter(
        $zbp->option['ZC_MANAGE_COUNT'],
        array(
            'ID' => 'mem_ID',
            'CreateTime' => 'mem_CreateTime',
            'PostTime' => 'mem_PostTime',
            'UpdateTime' => 'mem_UpdateTime',
            'Articles' => 'mem_Articles',
            'Pages' => 'mem_Pages',
            'Comments' => 'mem_Comments',
            'Uploads' => 'mem_Uploads',
        )
    );
    $order = $filter['order'];
    $limit = $filter['limit'];
    $option = $filter['option'];

    return array(
        'data' => array(
            'list' => ApiGetObjectArrayList(
                $zbp->GetMemberList('*', $where, $order, $limit, $option)
            ),
            'pagebar' => ApiGetPagebarInfo($option),
        ),
    );
}

/**
 * 获取用户权限接口.
 *
 * @return array
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
                'name' => $zbp->GetActionName($key),
                'checked' => $zbp->CheckRights($key) ? true : false
            );
        }
    }

    return array(
        'data' => $authArr,
    );
}
