<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * pdo_MySQL数据库操作类.
 */
class Database__PDO_MySQL implements Database__Interface
{
    public $type = 'mysql';
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
     * @var string|null 数据库引擎
     */
    public $dbengine = null;
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
        return addslashes($s);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function Open($array)
    {
        /*$array=array(
        'dbmysql_server',
        'dbmysql_username',
        'dbmysql_password',
        'dbmysql_name',
        'dbmysql_pre',
        'dbmysql_port',
        'persistent',
        'engine',
         */
        if ($array[6] == false) {
            $options = array();
        } else {
            $options = array(PDO::ATTR_PERSISTENT => true);
        }

        try {
            $db_link = new PDO('mysql:host=' . $array[0] . ';port=' . $array[5] . ';dbname=' . $array[3], $array[1], $array[2], $options);

            $this->db = $db_link;
            $this->dbpre = $array[4];
            $this->dbname = $array[3];
            $this->dbengine = $array[7];

            $myver = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
            $this->version = substr($myver, 0, strpos($myver, "-"));
            if (version_compare($this->version, '5.5.3') >= 0) {
                $u = "utf8mb4";
            } else {
                $u = "utf8";
            }
            $db_link->query("SET NAMES '" . $u . "'");

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param string $dbmysql_server
     * @param string $dbmysql_port
     * @param string $dbmysql_username
     * @param string $dbmysql_password
     * @param string $dbmysql_name
     */
    public function CreateDB($dbmysql_server, $dbmysql_port, $dbmysql_username, $dbmysql_password, $dbmysql_name)
    {
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        $db_link = new PDO('mysql:host=' . $dbmysql_server . ';port=' . $dbmysql_port, $dbmysql_username, $dbmysql_password, $options);
        $this->db = $db_link;
        $this->dbname = $dbmysql_name;

        $myver = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
        $myver = substr($myver, 0, strpos($myver, "-"));
        if (version_compare($myver, '5.5.3') >= 0) {
            $u = "utf8mb4";
        } else {
            $u = "utf8";
        }
        $db_link->query("SET NAMES '" . $u . "'");

        $s = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$dbmysql_name'";
        $a = $this->Query($s);
        $c = 0;
        if (is_array($a)) {
            $b = current($a);
            if (is_array($b)) {
                $c = (int) current($b);
            }
        }
        if ($c == 0) {
            $r = $this->db->exec($this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
            if ($r === false) {
                return false;
            }

            return true;
        }
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
            //if(true==true){
            if (true !== true) {
                $query = "EXPLAIN " . $query;
                $results2 = $this->db->query($this->sql->Filter($query));
                if (is_object($results2)) {
                    $row = $results2->fetchAll();
                    logs("\r\n" . $query . "\r\n" . var_export($row, true));
                }
            }

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
    public function CreateTable($table, $datainfo, $engine = null)
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
