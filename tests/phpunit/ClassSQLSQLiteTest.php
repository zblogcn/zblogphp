<?php

class ClassSQLSQLiteTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');
    protected static $db = null;

    public function setUp()
    {
        self::$db = new SQLSQLite($GLOBALS['zbp']->db);
    }

    public function tearDown()
    {
        self::$db->reset();
        self::$db = null;
    }

    public function testExist()
    {
        self::$db->exist('zbp_post');
        $this->assertEquals('SELECT COUNT(*) FROM sqlite_master WHERE type=\'table\' AND name=\'zbp_post\'', self::$db->sql);
    }

    public function testIndex()
    {
        //self::$db->create('zbp_post')->index(array('indexname' => array('ddd', 'eee', 'eeee')));
        //$this->assertEquals('CREATE INDEX indexname ( ddd , eee , eeee ) ;', self::$db->sql);
    }

    public function testCreateTable()
    {
        $tableData = array(
            'a' => array('a', 'integer', '', 0),
            'b' => array('b', 'integer', '', 0),
            'i' => array('i', 'boolean', '', false),
            'j' => array('j', 'string', 'char', ''),
            'k' => array('k', 'string', 250, ''),
            'p' => array('p', 'string', '', ''),
            'q' => array('q', 'datetime', '', ''),
            'r' => array('r', 'float', '', ''),
            's' => array('s', 'timestamp', '', ''),
        );
        self::$db->create('zbp_post')->data($tableData);
        $this->assertEquals('CREATE TABLE zbp_post  ( a integer primary key autoincrement, b integer NOT NULL DEFAULT \'0\', i bit NOT NULL DEFAULT \'0\', j char() NOT NULL DEFAULT \'\', k varchar(250) NOT NULL DEFAULT \'\', p text NOT NULL DEFAULT \'\', q datetime NOT NULL, r float NOT NULL DEFAULT 0, s timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ); CREATE UNIQUE INDEX zbp_post_a  on zbp_post (a);', self::$db->sql);
    }
}
