<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * PgSQL数据库操作类.
 */
class Database__PostgreSQL implements Database__Interface
{

    public $type = 'postgresql';

    public $version = '';

    public $error = array();

    /**
     * @var string|null 数据库名前缀
     */
    public $dbpre = null;

    protected $db = null; //数据库连接

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
     * @var string 字符集
     */
    public $charset = 'utf8';

    /**
     * @var string 字符排序
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
     * 对字符串进行转义，在指定的字符前添加反斜杠，即执行addslashes函数.
     *
     * @param string $s
     *
     * @return string
     */
    public function EscapeString($s)
    {
        return pg_escape_string($this->db, $s);
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
        $s = "host={$array[0]} port={$array[5]} dbname={$array[3]} user={$array[1]} password={$array[2]} options='--client_encoding=UTF8'";
        $this->ispersistent = $array[6];
        if (!$this->ispersistent) {
            $db_link = pg_connect($s);
        } else {
            $db_link = pg_pconnect($s);
        }

        if (!$db_link) {
            return false;
        } else {
            $this->db = $db_link;
            $this->dbpre = $array[4];
            $this->dbname = $array[3];
            $v = pg_version($db_link);
            if (isset($v['client'])) {
                $this->version = $v['client'];
            }
            if (isset($v['server'])) {
                $this->version = $v['server'];
            }

            $this->isconnected = true;
            return true;
        }
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
        $s = "host={$dbpgsql_server} port={$dbpgsql_port} user={$dbpgsql_username} password={$dbpgsql_password} options='--client_encoding=UTF8'";
        $this->db = pg_connect($s);
        $this->dbname = $dbpgsql_name;

        $this->isconnected = true;

        $isExists = @$this->Query("select count(*) from pg_catalog.pg_database where datname = '$dbpgsql_name';");
        $hasDB = false;
        if (is_array($isExists) && is_array($isExists[0]) && isset($isExists[0]['count'])) {
            if ($isExists[0]['count'] != '0') {
                $hasDB = true;
            }
        }

        if ($hasDB) {
            return false;
        }

        $r = @pg_query($this->db, $this->sql->Filter('CREATE DATABASE ' . $dbpgsql_name));
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
        $this->isconnected = false;
        if ($this->ispersistent == true) {
            $this->db = null;
            return;
        }
        if (is_resource($this->db)) {
            pg_close($this->db);
            $this->db = null;
        }
    }

    /**
     * 执行多行SQL语句.
     *
     * @param string $s 以;号分隔的多条SQL语句
     */
    public function QueryMulit($s)
    {
        return $this->QueryMulti($s);
    }

    //错别字函数，历史原因保留下来

    public function QueryMulti($s)
    {
        $result = false;
        //$a=explode(';',str_replace('%pre%', $this->dbpre,$s));
        $a = explode(';', $s);
        foreach ($a as $s) {
            $s = trim($s);
            if ($s != '') {
                $result = pg_query($this->db, $this->sql->Filter($s));
                $this->LogsError();
            }
        }

        return $result;
    }

    /**
     * 执行SQL查询语句.
     *
     * @param string $query
     *
     * @return array 返回数据数组
     */
    public function Query($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $results = @pg_query($this->db, $this->sql->Filter($query));

        $st = pg_result_status($results);
        if ($st == PGSQL_BAD_RESPONSE || $st == PGSQL_NONFATAL_ERROR || $st == PGSQL_FATAL_ERROR) {
            trigger_error(pg_result_error($results), E_USER_NOTICE);
        }

        $this->LogsError();
        $data = array();

        if (!is_resource($results) && !is_object($results)) {
            return $data;
        }
        if (is_resource($results) || is_object($results)) {
            while ($row = pg_fetch_assoc($results)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * 更新数据.
     *
     * @param string $query SQL语句
     *
     * @return mixed
     */
    public function Update($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $r = pg_query($this->db, $this->sql->Filter($query));
        $this->LogsError();
        return $r;
    }

    /**
     * 删除数据.
     *
     * @param string $query SQL语句
     *
     * @return mixed
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $r = pg_query($this->db, $this->sql->Filter($query));
        $this->LogsError();
        return $r;
    }

    /**
     * 插入数据.
     *
     * @param string $query SQL语句
     *
     * @return int 返回ID序列号
     */
    public function Insert($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        pg_query($this->db, $this->sql->Filter($query));
        $this->LogsError();
        $id = null;
        if (preg_match('/[\s]*INSERT[\s]+INTO[\s]+([\S]+)[\s]+/i', $query, $m) == 1) {
            $seq = $m[1];
            $seq = str_replace(array('"',"'"), '', $seq) . '_seq';
            $query = "select currval('{$seq}'::regclass)";
            $r = pg_query($this->db, $query);
            $id = (int) pg_fetch_result($r, 0, 0);
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
        $query = "select currval('{$seq}'::regclass)";
        $r = pg_query($this->db, $query);
        $id = (int) pg_fetch_result($r, 0, 0);
        return $id;
    }

    /**
     * 新建表.
     *
     * @param string $tablename 表名
     * @param array  $datainfo  表结构
     */
    public function CreateTable($table, $datainfo, $engine = null, $charset = null, $collate = null)
    {
        $this->QueryMulit($this->sql->CreateTable($table, $datainfo));
    }

    /**
     * 删除表.
     *
     * @param string $table 表名
     */
    public function DelTable($table)
    {
        $table = str_replace('%pre%', $this->dbpre, $table);
        $this->QueryMulit($this->sql->DelTable($table));
    }

    /**
     * 判断数据表是否存在.
     *
     * @param string $table 表名
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
        $e = pg_last_error($this->db);
        if (!empty($e)) {
            $this->error[] = array(PGSQL_BAD_RESPONSE, $e);
        }
    }

    /**
     * 事务处理
     *
     * @param string $query 指令
     *
     * @return array
     */
    public function Transaction($query)
    {
        return $this->Query($this->sql->Transaction($query));
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
