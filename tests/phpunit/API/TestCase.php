<?php

namespace Tests\PHPUnit\API;

use PHPUnit\Framework\TestCase as BaseTestCase;

if (! defined('ZBP_PATH')) {
    require_once __DIR__ . '/../../../zb_system/function/c_system_base.php';
    $GLOBALS['zbp']->Load();
}
if (! defined('ZBP_API_IN_TEST')) {
    define('ZBP_API_IN_TEST', true);
}

class TestCase extends BaseTestCase
{
    public function callAPI($mod, $act = null, $method = 'GET', $query_data = [], $post_data = [], $headers = [])
    {
    }
}

