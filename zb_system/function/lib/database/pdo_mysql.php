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
     * @var string|null 数据库引擎
     */
    public $dbengine = null;

    /**
     * @var DbSql|null DbSql实例
     */
    public $sql = null;

    /**
     * @var 字符集
     */
    public $charset = 'utf8';

    /**
     * @var 字符排序
     */
    public $collate = null;

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
            $this->version = SplitAndGet($myver, '-', 0);
            if (version_compare($this->version, '5.5.3') >= 0) {
                $u = "utf8mb4";
                $c = 'utf8mb4_general_ci';
            } else {
                $u = "utf8";
                $c = 'utf8_general_ci';
            }
            $db_link->query("SET NAMES {$u} COLLATE {$c}");
            $this->charset = $u;
            $this->collate = $c;
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

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
        $this->version = SplitAndGet($myver, '-', 0);
        if (version_compare($this->version, '5.5.3') >= 0) {
            $u = "utf8mb4";
            $c = 'utf8mb4_general_ci';
        } else {
            $u = "utf8";
            $c = 'utf8_general_ci';
        }
        $this->db->query("SET NAMES '" . $u . "'");
        $this->charset = $u;
        $this->collate = $c;

        $s = "CREATE DATABASE IF NOT EXISTS {$dbmysql_name} DEFAULT CHARACTER SET {$u}";
        $r = $this->db->exec($this->sql->Filter($s));
        $this->LogsError();
        if ($r === false) {
            return false;
        }

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
                $this->LogsError();
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
        $e = trim($this->db->errorCode(), '0');
        if ($e != '') {
            trigger_error(implode(' ', $this->db->errorInfo()), E_USER_NOTICE);
        }
        $this->LogsError();
        //Logs($query);
        //fetch || fetchAll
        if (is_object($results)) {
            //if(true==true){
            if (true !== true) {
                try {
                    $query = "EXPLAIN " . $query;
                    $results2 = $this->db->query($this->sql->Filter($query));
                    if (is_object($results2)) {
                        $row = $results2->fetchAll();
                        logs("\r\n" . $query . "\r\n" . var_export($row, true));
                    }
                } catch (PDOException $e) {
                    $i = 0;
                    //die ("Error!: " . $e->getMessage() . "<br/>");
                }
            }
            $result = array();
            try {
                $result = $results->fetchAll();
            } catch (PDOException $e) {
                $i = 0;
                //die ("Error!: " . $e->getMessage() . "<br/>");
            }
            return $result;
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
        $r = $this->db->query($this->sql->Filter($query));
        $this->LogsError();
        return $r;
    }

    /**
     * @param $query
     *
     * @return bool|mysqli_result
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $r = $this->db->query($this->sql->Filter($query));
        $this->LogsError();
        return $r;
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
        $this->LogsError();
        return $this->db->lastInsertId();
    }

    /**
     * @param $table
     * @param $datainfo
     */
    public function CreateTable($table, $datainfo, $engine = null, $charset = null, $collate = null)
    {
        $this->QueryMulit($this->sql->CreateTable($table, $datainfo, $engine, $charset, $collate));
    }

    /**
     * @param $table
     */
    public function DelTable($table)
    {
        $table = str_replace('%pre%', $this->dbpre, $table);
        $this->QueryMulit($this->sql->DelTable($table));
    }

    /**
     * @param $table
     *
     * @return bool
     */
    public function ExistTable($table)
    {
        $table = str_replace('%pre%', $this->dbpre, $table);
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

    private function LogsError()
    {
        $e = trim($this->db->errorCode(), '0');
        if ($e != '') {
            $this->error[] = array($e, $this->db->errorInfo());
        }
    }

    /**
     * 事务处理
     *
     * @param string $query 指令
     *
     * @return bool
     */
    public function Transaction($query)
    {
        if (strcasecmp($query, 'begin') === 0) {
            return $this->db->beginTransaction();
        }
        if (strcasecmp($query, 'commit') === 0) {
            return $this->db->commit();
        }
        if (strcasecmp($query, 'rollback ') === 0) {
            return $this->db->rollBack();
        }
    }

    /**
     * 判断数据表的字段是否存在.
     *
     * @param string $table 表名
     * @param string $field 字段名
     *
     * @return bool
     */
    public function ExistColumn($table, $field)
    {
        $r = null;
        ZBlogException::SuspendErrorHook();
        $s = "SELECT column_name FROM information_schema.columns WHERE table_schema='$this->dbname' AND table_name = '$table' AND column_name = '$field'";
        $r = @$this->Query($s);
        ZBlogException::ResumeErrorHook();
        if (is_array($r) && count($r) == 0) {
            return false;
        }
        return true;
    }

}
