<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * SQLite2数据库操作类.
 */
class Database__SQLite implements Database__Interface
{
    public $type = 'sqlite';
    public $version = '2';
    public $error = array();

    /**
     * @var string|null 数据库名前缀
     */
    public $dbpre = null;
    private $db = null; //数据库连接实例
    /**
     * @var string|null 数据库名
     */
    public $dbname = null;
    /**
     * @var DbSql|null
     */
    public $sql = null;

    /**
     * 构造函数，实例化$sql参数.
     */
    public function __construct()
    {
        $this->sql = new DbSql($this);
    }

    /**
     * @param $s
     *
     * @return string
     */
    public function EscapeString($s)
    {
        return sqlite_escape_string($s);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function Open($array)
    {
        if ($this->db = sqlite_open($array[0], 0666, $sqliteerror)) {
            $this->dbpre = $array[1];
            $this->dbname = $array[0];
            $this->version = sqlite_libversion();

            return true;
        } else {
            return false;
        }
    }

    /**
     * 关闭数据库连接.
     */
    public function Close()
    {
        sqlite_close($this->db);
    }

    /**
     * @param $s
     */
    public function QueryMulit($s)
    {
        return $this->QueryMulti($s);
    }

    //错别字函数，历史原因保留下来

    public function QueryMulti($s)
    {
        //$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
        $a = explode(';', $s);
        foreach ($a as $s) {
            $s = trim($s);
            if ($s != '') {
                sqlite_query($this->db, $this->sql->Filter($s));
                $e = sqlite_last_error($this->db);
                if ($e > 0) {
                    $this->error[] = array($e, sqlite_error_string($e));
                }
            }
        }
    }

    /**
     * @param $query
     *
     * @return array
     */
    public function Query($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        // 遍历出来
        $results = sqlite_query($this->db, $this->sql->Filter($query));
        $data = array();
        if (is_resource($results)) {
            while ($row = sqlite_fetch_array($results)) {
                $data[] = $row;
            }
        } else {
            $data[] = $results;
        }

        return $data;
    }

    /**
     * @param $query
     *
     * @return SQLiteResult
     */
    public function Update($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        return sqlite_query($this->db, $this->sql->Filter($query));
    }

    /**
     * @param $query
     *
     * @return SQLiteResult
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        return sqlite_query($this->db, $this->sql->Filter($query));
    }

    /**
     * @param $query
     *
     * @return int
     */
    public function Insert($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        sqlite_query($this->db, $this->sql->Filter($query));

        return sqlite_last_insert_rowid($this->db);
    }

    /**
     * @param $table
     * @param $datainfo
     */
    public function CreateTable($table, $datainfo)
    {
        $this->QueryMulit($this->sql->CreateTable($table, $datainfo));
    }

    /**
     * @param $table
     */
    public function DelTable($table)
    {
        $this->QueryMulit($this->sql->DelTable($table));
    }

    /**
     * @param $table
     *
     * @return bool
     */
    public function ExistTable($table)
    {
        $a = $this->Query($this->sql->ExistTable($table));
        if (!is_array($a)) {
            return false;
        }

        $b = current($a);
        if (!is_array($b)) {
            return false;
        }

        $c = (int) current($b);
        if ($c > 0) {
            return true;
        } else {
            return false;
        }
    }
}
