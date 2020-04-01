<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * pdo_SQLite数据库操作类.
 */
class Database__PDO_SQLite implements Database__Interface
{
    public $type = 'sqlite';
    public $version = '3';
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
     * @var DbSql|null DbSql实例
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
        return str_ireplace('\'', '\'\'', $s);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function Open($array)
    {
        //pdo_sqlite优先使用sqlite3
        $a = PDO::getAvailableDrivers();
        $dns = 'sqlite';
        if (in_array('sqlite2', $a)) {
            $dns = 'sqlite2';
        }
        if (in_array('sqlite', $a)) {
            $dns = 'sqlite';
        }
        $db_link = new PDO($dns . ':' . $array[0]);
        $this->db = $db_link;
        $this->dbpre = $array[1];
        $this->dbname = $array[0];
        $myver = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
        $this->version = SplitAndGet($myver, '-', 0);

        return true;
    }

    /**
     * 关闭数据库连接.
     */
    public function Close()
    {
        $this->db = null;
    }

    /**
     * 执行多行SQL语句.
     *
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
                $this->db->exec($this->sql->Filter($s));
                $e = $this->db->errorCode();
                if ($e > 0) {
                    $this->error[] = array($e, $this->db->errorInfo());
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
        $results = $this->db->query($this->sql->Filter($query));
        //fetch || fetchAll
        if (is_object($results)) {
            return $results->fetchAll();
        } else {
            return array($results);
        }
    }

    /**
     * @param $query
     *
     * @return bool|mysqli_result
     */
    public function Update($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        return $this->db->query($this->sql->Filter($query));
    }

    /**
     * @param $query
     *
     * @return bool|mysqli_result
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        return $this->db->query($this->sql->Filter($query));
    }

    /**
     * @param $query
     *
     * @return int
     */
    public function Insert($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $this->db->exec($this->sql->Filter($query));

        return $this->db->lastInsertId();
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
        $a = $this->Query($this->sql->ExistTable($table, $this->dbname));
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
