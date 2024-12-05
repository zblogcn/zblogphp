<?php

class ClassSQL__MySQLTest extends PHPUnit\Framework\TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');
    protected static $db = null;

    public function setUp(): void
    {
        self::$db = new SQL__MySQL($GLOBALS['zbp']->db);
    }

    public function tearDown(): void
    {
        self::$db->reset();
        self::$db = null;
    }

    public function testTruncate(): void
    {
        $this->assertEquals('TRUNCATE TABLE  zbp_post ', self::$db->truncate("zbp_post")->sql);
    }

    public function testExist(): void
    {
        self::$db->exist('zbp_post', 'zbphp');
        $this->assertEquals('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=\'zbphp\' AND TABLE_NAME=\'zbp_post\'', self::$db->sql);
    }

    public function testIndex()
    {
        self::$db->create('zbp_post')->index(array('indexname' => array('ddd', 'eee', 'eeee')));
        $this->assertEquals('CREATE INDEX zbp_post_indexname ON zbp_post ( ddd , eee , eeee )', self::$db->sql);
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
        );
        self::$db->create('zbp_post')->data($tableData)->option(array('engine' => 'MyISAM'))->option(array('charset' => 'utf8'))->option(array('collate' => 'utf8_general_ci'));
        $this->assertEquals('CREATE TABLE IF NOT EXISTS zbp_post  ( a int(11) NOT NULL AUTO_INCREMENT, b tinyint(4) NOT NULL DEFAULT \'0\', c smallint(6) NOT NULL DEFAULT \'0\', d mediumint(9) NOT NULL DEFAULT \'0\', e int(11) NOT NULL DEFAULT \'0\', f bigint(20) NOT NULL DEFAULT \'0\', g int(11) NOT NULL DEFAULT \'0\', h timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, i tinyint(1) NOT NULL DEFAULT \'0\', j char(250) NOT NULL DEFAULT \'\', k varchar(250) NOT NULL DEFAULT \'\', l tinytext NOT NULL , m text NOT NULL , n mediumtext NOT NULL , o longtext NOT NULL , p longtext NOT NULL , q datetime NOT NULL, r float NOT NULL DEFAULT 0, PRIMARY KEY (a) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;', self::$db->sql);
    }

    public function testOption()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post   USE INDEX ( test )',
            self::$db
                ->select("zbp_post")
                ->useindex('test')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post   USE INDEX ( a, b )',
            self::$db
                ->select("zbp_post")
                ->useindex(array('a, b'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post   FORCE INDEX ( test )',
            self::$db
                ->select("zbp_post")
                ->forceindex('test')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post   FORCE INDEX ( a, b )',
            self::$db
                ->select("zbp_post")
                ->forceindex('a, b')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post   IGNORE INDEX ( test )',
            self::$db
                ->select("zbp_post")
                ->ignoreindex('test')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post   IGNORE INDEX ( a, b )',
            self::$db
                ->select("zbp_post")
                ->ignoreindex(array('a, b'))
                ->sql
        );
        $this->assertEquals(
            'SELECT SQL_NO_CACHE  * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->option(array('sql_no_cache' => true))
                ->sql
        );
        $this->assertEquals(
            'SELECT SQL_CACHE  * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->option(array('sql_cache' => true))
                ->sql
        );
        $this->assertEquals(
            'SELECT SQL_BUFFER_RESULT  * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->option(array('sql_buffer_result' => true))
                ->sql
        );
        $this->assertEquals(
            'SELECT  log_ID  FROM zbp_post AS p STRAIGHT_JOIN zbp_postrelation AS pr ON p.log_ID = pr.pr_PostID WHERE 1 = 1',
                  self::$db->selectany('log_ID')
                           ->from(array('zbp_post'=>'p'))
                           ->innerjoin(array('zbp_postrelation'=>'pr'))
                           ->on('p.log_ID = pr.pr_PostID')
                           ->where('1 = 1')
                           ->option(array('straight_join' => true))
                           ->sql
        );
        $tableData = array(
            'a' => array('a', 'integer', '', 0),
            'i' => array('i', 'boolean', '', false),
            'j' => array('j', 'string', 'char250', ''),
            'k' => array('k', 'string', 250, ''),
            'o' => array('o', 'string', 'longtext', ''),
            'p' => array('p', 'string', '', ''),
        );
        self::$db->create('zbp_post2')->data($tableData)
        ->option(array('temporary' => true))
        ->option(array('engine' => 'Memory'))
        ->option(array('charset' => 'utf8'))
        ->option(array('collate' => 'utf8_general_ci'));
        $this->assertEquals('CREATE TEMPORARY TABLE IF NOT EXISTS zbp_post2  ( a int(11) NOT NULL AUTO_INCREMENT, i tinyint(1) NOT NULL DEFAULT \'0\', j char(250) NOT NULL DEFAULT \'\', k varchar(250) NOT NULL DEFAULT \'\', o longtext NOT NULL , p longtext NOT NULL , PRIMARY KEY (a) ) ENGINE=Memory DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;', self::$db->sql);
    }

    public function testJoin(): void
    {
        $this->assertEquals(
            'SELECT  log_ID  FROM zbp_post AS p LEFT JOIN zbp_postrelation AS pr ON p.log_ID = pr.pr_PostID LEFT JOIN zbp_postrelation2 AS pr2 ON p.log_ID = pr2.pr_PostID AND p.log_DD = pr2.pr_DD WHERE 1 = 1',
                  self::$db->selectany('log_ID')
                           ->from(array('zbp_post'=>'p'))
                           ->leftjoin(array('zbp_postrelation'=>'pr'), 'p.log_ID = pr.pr_PostID')
                           ->leftjoin('zbp_postrelation2 AS pr2', array('p.log_ID = pr2.pr_PostID', 'p.log_DD = pr2.pr_DD'))
                           ->where('1 = 1')
                           ->sql
        );

        $this->assertEquals(
            'SELECT  log_ID  FROM zbp_post AS p LEFT JOIN zbp_postrelation AS pr ON p.log_ID = pr.pr_PostID LEFT JOIN zbp_postrelation2 AS pr2 ON p.log_ID = pr2.pr_PostID AND p.log_DD = pr2.pr_DD WHERE 1 = 1',
                  self::$db->selectany('log_ID')
                           ->from(array('zbp_post'=>'p'))
                           ->leftjoin(array('zbp_postrelation'=>'pr'))
                           ->on('p.log_ID = pr.pr_PostID')
                           ->leftjoin('zbp_postrelation2 AS pr2')
                           ->on('p.log_ID = pr2.pr_PostID', 'p.log_DD = pr2.pr_DD')
                           ->where('1 = 1')
                           ->sql
        );
    }
}
