<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
/**
 * Format single member object.
 *
 * @param object $member
 *
 * @return array
 */
function return_member($id)
{
    global $zbp;

    $member = $zbp->GetMemberByID($id);
    $ret = $member->GetData();
    $ret['Url'] = $member->Url;
    unset($ret['Password']);
    unset($ret['Guid']);
    API::$IO->formatObjectName($ret);

    return $ret;
}

/**
 * Get member.
 */
function api_member_get_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    //
    $ret = return_member($id);

    API::$IO->member = $ret;
}
API::$Route->get('/member/', 'api_member_get_function');

/**
 * Get members.
 */
function api_members_get_function()
{
}
API::$Route->get('/members/', 'api_members_get_function');

/**
 * A function will run after Postmember().
 *
 * @param Post $member
 */
function api_member_post_callback(&$member)
{
    $ret = return_member($member->ID);
    API::$IO->member = $ret;
}
/**
 * Create & Update member.
 */
function api_member_post_function()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_PostMember_Succeed', 'api_member_post_callback');
    PostMember();
    $zbp->BuildModule();
    $zbp->SaveCache();
}

/**
 * Create member.
 */
function api_member_create_function()
{
    $_POST['ID'] = 0;
    api_member_post_function();
}

API::$Route->post('/member/create/', 'api_member_create_function');

/**
 * Update member.
 */
function api_member_update_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    $_POST['ID'] = $id;
    api_member_post_function();
}
API::$Route->post('/member/update/', 'api_member_update_function');

/**
 * Update member.
 */
function api_member_delete_function()
{
    $ret = DelMember();
    if ($ret !== true) {
        API::$IO->end(0);
    }
}
API::$Route->post('/member/delete/', 'api_member_delete_function');
