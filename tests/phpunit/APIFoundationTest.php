<?php

if (! defined('ZBP_API_IN_TEST')) {
    define('ZBP_API_IN_TEST', true);
}

/**
 * API 底层测试.
 */
class APIFoundationTest extends PHPUnit\Framework\TestCase
{
    /** @test */
    public function testCheckAuthWhenNotLoggedIn()
    {
        // 期望抛出 401 未授权(登录)异常
        $this->expectExceptionObject(
            new Exception(
                $GLOBALS['lang']['error']['6']
            )
        );

        // $this->expectOutputString('{"code":401,"message":"'.$GLOBALS['lang']['error']['6'].'","data":null,"error":null}');

        // 未登录状态

        ApiCheckAuth(true);
    }

    /** @test */
    public function testCheckAuthWhenUnauthorized()
    {
        // 指定一个游客用户
        $GLOBALS['zbp']->user = new Member;
        $GLOBALS['zbp']->user->SetData([
            'ID'         => '1',
            'Guid'       => $guid = GetGuid(),
            'Level'      => '6', // 游客没有 admin 权限
            'Status'     => '0',
            'Name'       => 'test_member',
            'Password'   => Member::GetPassWordByGuid('test123456', $guid),
            'Email'      => 'null@null.com',
            'HomePage'   => null,
            'IP'         => '127.0.0.1',
            'CreateTime' => time(),
            'PostTime'   => time(),
            'UpdateTime' => time(),
            'Alias'      => 'test_member',
        ]);

        // 期望抛出 403 未授权异常
        $this->expectExceptionObject(
            new Exception(
                $GLOBALS['lang']['error']['6']
            )
        );

        // $this->expectOutputString('{"code":403,"message":"'.$GLOBALS['lang']['error']['6'].'","data":null,"error":null}');

        ApiCheckAuth(true, 'admin');
    }

    /** @test */
    public function testCheckAuthWhenAuthorized()
    {
        // 指定一个协作者用户
        $GLOBALS['zbp']->user = new Member;
        $GLOBALS['zbp']->user->SetData([
            'ID'         => '1',
            'Guid'       => $guid = GetGuid(),
            'Level'      => '5', // 协作者有 admin 权限
            'Status'     => '0',
            'Name'       => 'test_member',
            'Password'   => Member::GetPassWordByGuid('test123456', $guid),
            'Email'      => 'null@null.com',
            'HomePage'   => null,
            'IP'         => '127.0.0.1',
            'CreateTime' => time(),
            'PostTime'   => time(),
            'UpdateTime' => time(),
            'Alias'      => 'test_member',
        ]);

        // 期望返回 true
        $this->assertTrue(ApiCheckAuth(true, 'admin'));
    }

    /** @test */
    public function testGetObjectArray()
    {
        $object = new class extends Base {
            public function __construct()
            {
                $table = 'test';
                $datainfo = [
                    'ID'          => array('test_ID', 'integer', '', 0),
                    'Value'       => array('test_Value', 'string', 250, ''),
                    'RemoveValue' => array('test_RemoveValue', 'string', 250, ''),
                ];
                parent::__construct($table, $datainfo);
            }
            public function __get($key)
            {
                if ($key === 'OtherValue') {
                    return 'test_other_value';
                }
            }
        };
        $object->SetData([
            'ID'          => '1',
            'Value'       => 'test_value',
            'RemoveValue' => 'rest_remove_value',
        ]);
        $array = ApiGetObjectArray($object, ['OtherValue'], ['RemoveValue', 'Metas']);

        $this->assertEquals([
            'ID' => '1', 'Value' => 'test_value', 'OtherValue' => 'test_other_value'
        ], $array);
    }

    /** @test */
    public function testCSRFCheckWhenUsingCookie()
    {
        $this->expectExceptionObject(new Exception($GLOBALS['lang']['error']['5']));

        // $this->expectOutputString('{"code":419,"message":"'.$GLOBALS['lang']['error']['5'].'","data":null,"error":null}');
        ApiVerifyCSRF(true);
    }
}
