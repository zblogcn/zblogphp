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

    public $error = array();

    /**
     * @var string|null 数据库名前缀
     */
    public $dbpre = null;

    protected $db = null; //数据库连接实例

    private $isconnected = false; //是否已打开连接

    private $ispersistent = false; //是否持久连接

    /**
     * @var string|null 数据库名
     */
    public $dbname = null;

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
        if ($this->isconnected) {
            return true;
        }
        $array[3] = strtolower($array[3]);
        $s = "pgsql:host={$array[0]};port={$array[5]};dbname={$array[3]};user={$array[1]};password={$array[2]};options='--client_encoding=UTF8'";
        $this->ispersistent = $array[6];
        if (false == $this->ispersistent) {
            $db_link = new PDO($s);
        } else {
            $db_link = new PDO($s, null, null, array(PDO::ATTR_PERSISTENT => true));
        }
        $this->db = $db_link;
        $this->dbpre = $array[4];
        $this->dbname = $array[3];
        $myver = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
        $this->version = SplitAndGet($myver, '-', 0);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        //$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->isconnected = true;
        return true;
    }

    /**
     * @param string $dbpgsql_server
     * @param string $dbpgsql_port
     * @param string $dbpgsql_username
     * @param string $dbpgsql_password
     * @param string $dbpgsql_name
     */
    public function CreateDB($dbpgsql_server, $dbpgsql_port, $dbpgsql_username, $dbpgsql_password, $dbpgsql_name)
    {
        $dbpgsql_name = strtolower($dbpgsql_name);
        $db_link = new PDO('pgsql:host=' . $dbpgsql_server . ';port=' . $dbpgsql_port, $dbpgsql_username, $dbpgsql_password);
        $this->db = $db_link;
        $this->dbname = $dbpgsql_name;

        //$db_link->query("SET client_encoding='UTF-8';");
        $this->isconnected = true;

        $isExists = @$this->Query("select count(*) from pg_catalog.pg_database where datname = '$dbpgsql_name';");
        $hasDB = false;
        if (is_array($isExists) && is_array($isExists[0]) && isset($isExists[0]['count'])) {
            if ($isExists[0]['count'] == '0') {
                $hasDB = false;
            } else {
                $hasDB = true;
            }
        }

        if ($hasDB == true) {
            return false;
        }

        $r = $this->db->exec($this->sql->Filter('CREATE DATABASE ' . $dbpgsql_name));
        $this->LogsError();
        return true;
    }

    /**
     * 关闭数据库连接.
     */
    public function Close()
    {
        if (!$this->isconnected) {
            return;
        }
        $this->db = null;
        $this->isconnected = false;
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
        $result = false;
        //$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
        $a = explode(';', $s);
        foreach ($a as $s) {
            $s = trim($s);
            if ($s != '') {
                $result = $this->db->exec($this->sql->Filter($s));
                $this->LogsError();
            }
        }

        return $result;
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
     * @return bool|pgsql_result
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
     * @return bool|pgsql_result
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
        $this->db->query($this->sql->Filter($query));
        $this->LogsError();
        $id = null;
        if (preg_match('/[\s]*INSERT[\s]+INTO[\s]+([\S]+)[\s]+/i', $query, $m) == 1) {
            $seq = $m[1];
            $seq = str_replace(array('"',"'"), '', $seq) . '_seq';
            $id = $this->db->lastInsertId($seq);
        }
        return $id;
    }

    /**
     * @return int
     */
    public function GetInsertId($table = null)
    {
        $seq = $table;
        $seq = str_replace(array('"',"'"), '', $seq) . '_seq';
        $id = $this->db->lastInsertId($seq);
        return $id;
    }

    /**
     * @param $table
     * @param $datainfo
     */
    public function CreateTable($table, $datainfo, $engine = null, $charset = null, $collate = null)
    {
        $this->QueryMulit($this->sql->CreateTable($table, $datainfo));
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

    protected function LogsError()
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
        $table = strtolower($table);
        $field = strtolower($field);
        ZbpErrorControl::SuspendErrorHook();
        $s = "SELECT * FROM information_schema.columns WHERE table_schema = 'public' AND table_name = '$table' AND column_name = '$field'";
        $r = @$this->Query($s);
        ZbpErrorControl::ResumeErrorHook();
        if (is_array($r) && count($r) == 0) {
            return false;
        }
        return true;
    }

}
