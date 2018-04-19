<?php

class ClassSQLGlobalTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');
    protected static $db = null;

    public function setUp()
    {
        /*
         * Use MySQL to test Global
         */
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
    }

    public function tearDown()
    {
        self::$db->reset();
        self::$db = null;
    }

    // Basic test cases
    public function testSelect()
    {
        self::$db->select('zbp_post');
        $this->assertEquals('SELECT * FROM  zbp_post ', self::$db->sql);
    }

    public function testInsert()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db->insert('zbp_post')->data(array('log_Title' => 'test'));
        $this->assertEquals('INSERT INTO  zbp_post  (log_Title)  VALUES (  \'test\'  )', self::$db->sql);
    }

    public function testDelete()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db->delete('zbp_post');
        $this->assertEquals('DELETE FROM  zbp_post ', self::$db->sql);
    }

    public function testDrop()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db->drop('zbp_post');
        $this->assertEquals('DROP TABLE  zbp_post ', self::$db->sql);
    }

    public function testCreate()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db
            ->create('zbp_post')
            ->data(
                array(
                'ID' => array('log_ID', 'integer', '', 0),
                )
            );
        $this->assertEquals('CREATE TABLE IF NOT EXISTS zbp_post  ( log_ID int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (log_ID) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;', self::$db->sql);
    }

    public function testUpdate()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        self::$db
            ->update('zbp_post')
            ->data(array('log_Title' => 'test'));
        $this->assertEquals('UPDATE  zbp_post   SET log_Title = \'test\'', self::$db->sql);
    }

    public function testCount()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        $this->assertEquals(
            'SELECT  COUNT(log_id) AS countid  FROM  zbp_post ', self::$db
                ->select("zbp_post")
                ->count('log_id', 'countid')
                ->sql
        );
        $this->assertEquals(
            'SELECT  COUNT(log_id) AS countid  FROM  zbp_post ', self::$db
                ->select("zbp_post")
                ->count(array('log_id', 'countid'))
                ->sql
        );
        $this->assertEquals(
            'SELECT  COUNT(log_id)  FROM  zbp_post ', self::$db
                ->select("zbp_post")
                ->count('log_id')
                ->sql
        );
        $this->assertEquals(
            'SELECT  COUNT(log_id),log_authorid  FROM  zbp_post ', self::$db
                ->select("zbp_post")
                ->count('log_id')
                ->column('log_authorid')
                ->sql
        );
    }

    public function testWhere()
    {
        self::$db = new SQLMySQL($GLOBALS['zbp']->db);
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  log_ID = \'1\' ',
            self::$db
                ->select("zbp_post")
                ->where(array('=', 'log_ID', "1"))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  `log_ID` = \'1\' ',
            self::$db
                ->select("zbp_post")
                ->where(' `log_ID` = \'1\' ')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  EXISTS ( SELECT 1 ) ',
            self::$db
                ->select("zbp_post")
                ->where(array('exists', 'SELECT 1'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  log_ID = \'1\' ',
            self::$db
                ->select("zbp_post")
                ->where(array('=', 'log_ID', "1"))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (log_ID BETWEEN \'1\' AND \'3\') ',
            self::$db
                ->select("zbp_post")
                ->where(array('between', 'log_ID', "1", "3"))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND ( (log_Title LIKE \'%Test%\') ) )',
            self::$db
                ->select("zbp_post")
                ->where(array('search', 'log_Title', "Test"))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND ( log_ID = \'1\'  OR  log_Title = \'2\' ) )',
            self::$db
                ->select("zbp_post")
                ->where(
                    array('array',
                    array(
                    array('log_ID', '1'),
                    array('log_Title', '2'),
                    ),
                    )
                )
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND ( log_ID <> \'1\'  OR  log_Title <> \'2\' ) )',
            self::$db
                ->select("zbp_post")
                ->where(
                    array('not array',
                    array(
                    array('log_ID', '1'),
                    array('log_Title', '2'),
                    ),
                    )
                )
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND ( log_ID LIKE \'1\'  OR  log_Title LIKE \'2\' ) )',
            self::$db
                ->select("zbp_post")
                ->where(
                    array('like array',
                    array(
                    array('log_ID', '1'),
                    array('log_Title', '2'),
                    ),
                    )
                )
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND ( log_ID ILIKE \'1\'  OR  log_Title ILIKE \'2\' ) )',
            self::$db
                ->select("zbp_post")
                ->where(
                    array('ilike array',
                    array(
                    array('log_ID', '1'),
                    array('log_Title', '2'),
                    ),
                    )
                )
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  ((1 = 1) AND (log_ID IN ( \'1\' ,  \'2\' ,  \'3\' ,  \'4\' ) ) )',
            self::$db
                ->select("zbp_post")
                ->where(array('IN', 'log_ID', array(1, 2, 3, 4)))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (log_ID IN ( \'1\' ,  \'2\' ,  \'3\' ,  \'4\' )) ',
            self::$db
                ->select("zbp_post")
                ->where(array('IN', 'log_ID', ' \'1\' ,  \'2\' ,  \'3\' ,  \'4\' '))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE (log_Meta LIKE \'%s:10:\"meta_Value\";%\')',
            self::$db
                ->select("zbp_post")
                ->where(array('META_NAME', 'log_Meta', 'meta_Value'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE (log_Meta LIKE \'%s:9:\"meta_Name\";s:10:\"meta_Value\"%\')',
            self::$db
                ->select("zbp_post")
                ->where(array('META_NAMEVALUE', 'log_Meta', 'meta_Name', 'meta_Value'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE 1=1',
            self::$db
                ->select("zbp_post")
                ->where(array('CUSTOM', '1=1'))
                ->sql
        );
    }

    public function testOrderby()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post  ORDER BY bbb desc, aaa ',
            self::$db
                ->select("zbp_post")
                ->orderBy(array('bbb' => 'desc'), 'aaa')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  ORDER BY bbb desc',
            self::$db
                ->select("zbp_post")
                ->orderBy(array('bbb' => 'desc'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  ORDER BY bbb desc, aaa asc',
            self::$db
                ->select("zbp_post")
                ->orderBy(array('bbb' => 'desc', 'aaa' => 'asc'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  ORDER BY aaaa ',
            self::$db
                ->select("zbp_post")
                ->orderBy('aaaa')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  ORDER BY a, b, c',
            self::$db
                ->select("zbp_post")
                ->orderBy(array('a', 'b', 'c'))
                ->sql
        );
    }

    public function testGroupBy()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post  GROUP BY bbb, aaa',
            self::$db
                ->select("zbp_post")
                ->groupBy(array('bbb', 'aaa'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  GROUP BY bbb, aaa',
            self::$db
                ->select("zbp_post")
                ->groupBy('bbb', 'aaa')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  GROUP BY aaaa',
            self::$db
                ->select("zbp_post")
                ->groupBy('aaaa')
                ->sql
        );
    }

    public function testHaving()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post  HAVING   bbb > 0 AND aaa < 0',
            self::$db
                ->select("zbp_post")
                ->having(array('bbb > 0', 'aaa < 0'))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  HAVING   bbb > 0 AND aaa < 0',
            self::$db
                ->select("zbp_post")
                ->having('bbb > 0', 'aaa < 0')
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  HAVING   aaaa > 0',
            self::$db
                ->select("zbp_post")
                ->having('aaaa > 0')
                ->sql
        );
    }

    public function testLimit()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post  LIMIT 5',
            self::$db
                ->select("zbp_post")
                ->limit(5)
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  LIMIT 1 OFFSET 10',
            self::$db
                ->select("zbp_post")
                ->limit(10, 1)
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  LIMIT 1 OFFSET 10',
            self::$db
                ->select("zbp_post")
                ->limit(array(10, 1))
                ->sql
        );
    }

    public function testColumn()
    {
        $this->assertEquals(
            'SELECT  *  FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->column('*')
                ->sql
        );
        $this->assertEquals(
            'SELECT  * AS sum,log_Content AS content,log_AuthorID AS author,log_Title,log_PostTime,log_ID AS ID  FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->column(array('*', 'sum'))
                ->column(array(array('log_Content', 'content'), array('log_AuthorID', 'author')))
                ->column('log_Title', 'log_PostTime', array('log_ID', 'ID'))
                ->sql
        );
    }

    public function testOption()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->option(array('test' => 'test'))
                ->sql
        );
    }

    public function testInvalid()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->orderBy(null)
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->groupBy(null)
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post ',
            self::$db
                ->select("zbp_post")
                ->having(null)
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('array', null))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('IN', 'log_ID', null))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('IN', 'log_ID', array()))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('array', array()))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('array', array()))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('meta_namevalue', array()))
                ->sql
        );
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  (1 = 1) ',
            self::$db
                ->select("zbp_post")
                ->where(array('meta_name', array()))
                ->sql
        );
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Unimplemented fuck
     */
    public function testExpectionInCall()
    {
        self::$db->fuck();
    }

    public function testIssue121InitializeNewInstance()
    {
        $this->assertEquals(
            'SELECT * FROM  zbp_post  WHERE  EXISTS ( SELECT * FROM  zbp_post  WHERE  log_ID = \'2\'  ) ',
            self::$db->select("zbp_post")->where(
                array('EXISTS', self::$db->init()->select("zbp_post")->where(array('=', 'log_ID', "2"))->sql)
            )->sql
        );
    }
}
