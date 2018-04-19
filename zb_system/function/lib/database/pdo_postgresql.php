<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * pdo_SQLite数据库操作类.
 */
class Database__PDO_PostgreSQL implements Database__Interface
{
    public $type = 'postgresql';
    public $version = '';

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
        return str_ireplace("'", "''", $s);
    }

    /**
     * 连接数据库.
     *
     * @param array $array 数据库连接配置
     *                     $array=array(
     *                     'pgsql_server',
     *                     'pgsql_username',
     *                     'pgsql_password',
     *                     'pgsql_name',
     *                     'pgsql_pre',
     *                     'pgsql_port',
     *                     'persistent')
     *                     )
     *
     * @return bool
     */
    public function Open($array)
    {
        $s = "pgsql:host={$array[0]};port={$array[5]};dbname={$array[3]};user={$array[1]};password={$array[2]};options='--client_encoding=UTF8'";
        if (false == $array[5]) {
            $db_link = new PDO($s);
        } else {
            $db_link = new PDO($s, null, null, array(PDO::ATTR_PERSISTENT => true));
        }
        $this->db = $db_link;
        $this->dbpre = $array[4];

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
        $this->db->query($this->sql->Filter($query));
        $seq = explode(' ', $query, 4);
        $seq = $seq[2] . '_seq';
        $id = $this->db->lastInsertId($seq);

        return $id;
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
