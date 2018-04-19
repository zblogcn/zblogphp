<?php

class ClassRegExpTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testNickname()
    {
        $this->assertFalse(CheckRegExp("@", '[nickname]'));
        $this->assertFalse(CheckRegExp("\x05", '[nickname]'));
        $this->assertFalse(CheckRegExp("⁭", '[nickname]'));

        $this->assertTrue(CheckRegExp("中日韩CJK試験テスト테스트", '[nickname]'));
        $this->assertTrue(CheckRegExp("δοκιμήপরীক্ষাការធ្វើតេស្តтест", '[nickname]'));
        $this->assertTrue(CheckRegExp('123', '[nickname]'));
        $this->assertTrue(CheckRegExp('Just a English Name', '[nickname]'));
    }
}
