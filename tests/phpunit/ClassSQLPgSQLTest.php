<?php

class ClassSQLPgSQLTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');
    protected static $db = null;

    public function setUp()
    {
        self::$db = new SQLPgSQL($GLOBALS['zbp']->db);
    }

    public function tearDown()
    {
        self::$db->reset();
        self::$db = null;
    }

    public function testExist()
    {
        self::$db->exist('zbp_post');
        $this->assertEquals('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=\'public\' AND  table_name =\'zbp_post\'', self::$db->sql);
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
            'b' => array('b', 'integer', 'tinyint', 0),
            'c' => array('c', 'integer', 'smallint', 0),
            'd' => array('d', 'integer', 'mediumint', 0),
            'e' => array('e', 'integer', 'int', 0),
            'f' => array('f', 'integer', 'bigint', 0),
            'g' => array('g', 'integer', '', 0),
            'h' => array('h', 'timestamp', '', false),
            'i' => array('i', 'boolean', '', false),
            'j' => array('j', 'string', 'char', ''),
            'k' => array('k', 'string', 250, ''),
            'l' => array('l', 'string', 'tinytext', 'a'),
            'm' => array('m', 'string', 'text', ''),
            'n' => array('n', 'string', 'mediumtext', ''),
            'o' => array('o', 'string', 'longtext', ''),
            'p' => array('p', 'string', '', ''),
            'q' => array('q', 'datetime', '', ''),
            'r' => array('r', 'float', '', ''),
            's' => array('s', 'double', '', ''),
            't' => array('t', 'date', '', ''),
        );
        self::$db->create('zbp_post')->data($tableData);
        $this->assertEquals('CREATE SEQUENCE zbp_post_seq; CREATE TABLE zbp_post ( a INT NOT NULL DEFAULT nextval(\'zbp_post_seq\'), b integer NOT NULL DEFAULT \'0\', c smallint NOT NULL DEFAULT \'0\', d integer NOT NULL DEFAULT \'0\', e integer NOT NULL DEFAULT \'0\', f bigint NOT NULL DEFAULT \'0\', g integer NOT NULL DEFAULT \'0\', h timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, i char(1) NOT NULL DEFAULT \'0\', j char() NOT NULL DEFAULT \'\', k varchar(250) NOT NULL DEFAULT \'\', l text NOT NULL DEFAULT \'\', m text NOT NULL DEFAULT \'\', n text NOT NULL DEFAULT \'\', o text NOT NULL DEFAULT \'\', p text NOT NULL DEFAULT \'\', q time NOT NULL, r real NOT NULL DEFAULT 0, s double precision NOT NULL DEFAULT 0, t date NOT NULL, PRIMARY KEY (a) ); CREATE INDEX zbp_post_ix_id on zbp_post(a);', self::$db->sql);
    }

    public function testDropTable()
    {
        self::$db->drop('zbp_post');
        $this->assertEquals('DROP TABLE zbp_post; DROP SEQUENCE zbp_post_seq;', self::$db->sql);
    }
}
