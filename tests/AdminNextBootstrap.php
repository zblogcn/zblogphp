<?php

/**
 * admin-next unit test bootstrap.
 *
 * Pure functions only: navigation collection, XML parsing, admin entry switch.
 * No database connection, no session.
 *
 * PHPUnit scans the test directory and will include this file; it only
 * declares functions, the framework is loaded when zbpn_boot() is called.
 */

function zbpn_boot()
{
    static $booted = false;
    if ($booted) {
        return;
    }

    if (!defined('ZBP_PATH')) {
        require_once dirname(__DIR__) . '/zb_system/function/c_system_base.php';
    }

    if (!defined('ZBP_API_IN_TEST')) {
        define('ZBP_API_IN_TEST', true);
    }

    global $zbp;

    if (!is_object($zbp)) {
        $zbp = new ZBlogPHP();
        $GLOBALS['zbp'] = $zbp;
    }

    if (empty($zbp->table)) {
        $zbp->table = include ZBP_PATH . 'zb_system/defend/table.php';
    }

    if (empty($zbp->datainfo)) {
        $zbp->datainfo = include ZBP_PATH . 'zb_system/defend/datainfo.php';
    }

    if (empty($zbp->option['ZC_USING_CDN_GUESTIP_TYPE'])) {
        $zbp->option['ZC_USING_CDN_GUESTIP_TYPE'] = 'REMOTE_ADDR';
        $zbp->option['ZC_ADDITIONAL_SECURITY'] = false;
    }

    if (empty($zbp->lang)) {
        $zbp->lang = ['msg' => ['anonymous' => 'anonymous']];
    }

    require_once ZBP_PATH . 'zb_system/function/c_system_admin.php';

    if (!isset($GLOBALS['hooks']['Filter_Plugin_Admin_Menu_Items'])) {
        DefinePluginFilter('Filter_Plugin_Admin_Menu_Items');
    }

    require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/rights.php';
    require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/apps.php';
    require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/nav.php';

    $booted = true;
}

/**
 * Build a member object without touching the database.
 */
function zbpn_test_member(array $data = [])
{
    zbpn_boot();

    $member = new Member();
    $member->SetData(array_merge([
        'ID'       => 1,
        'Level'    => 1,
        'Status'   => 0,
        'Name'     => 'admin',
        'Password' => Member::GetPassWordByGuid('admin123456', GetGuid()),
        'Email'    => 'admin@example.com',
        'Alias'    => 'admin',
    ], $data));

    return $member;
}
