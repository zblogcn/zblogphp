<?php
class ClassSQLMySQLTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = ['zbp'];
    protected static $db = null;

    public function setUp() {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
    }

    public function tearDown() {
        self::$db->reset();
        self::$db = null;
    }

    public function testExist() {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db->exist('zbp_post', 'zbphp');
           $this->assertEquals('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=\'zbphp\' AND TABLE_NAME=\'zbp_post\'', self::$db->sql);
    }

    public function testIndex() {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db->create('zbp_post')->index(array('indexname' => array('ddd', 'eee', 'eeee')));
        $this->assertEquals('CREATE INDEX indexname ( ddd , eee , eeee ) ;', self::$db->sql);
    }

}
