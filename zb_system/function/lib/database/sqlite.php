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

    protected $db = null; //数据库连接实例

    private $isconnected = false; //是否已打开连接

    /**
     * @var string|null 数据库名
     */
    public $dbname = null;

    /**
     * @var DbSql|null
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
        return sqlite_escape_string($s);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function Open($array)
    {
        if ($this->isconnected) {
            return true;
        }
        if ($this->db = sqlite_open($array[0], 0666, $sqliteerror)) {
            $this->dbpre = $array[1];
            $this->dbname = $array[0];
            $this->version = sqlite_libversion();

            $this->isconnected = true;
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
        if (!$this->isconnected) {
            return;
        }
        sqlite_close($this->db);
        $this->isconnected = false;
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
        $result = false;
        //$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
        $a = explode(';', $s);
        foreach ($a as $s) {
            $s = trim($s);
            if ($s != '') {
                $result = sqlite_query($this->db, $this->sql->Filter($s));
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
        $results = @sqlite_query($this->db, $this->sql->Filter($query));
        $e = sqlite_last_error($this->db);
        if ($e != 0) {
            trigger_error($e . sqlite_error_string($e), E_USER_NOTICE);
        }
        $this->LogsError();
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
     * @return mixed
     */
    public function Update($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $r = sqlite_query($this->db, $this->sql->Filter($query));
        $this->LogsError();
        return $r;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $r = sqlite_query($this->db, $this->sql->Filter($query));
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
        $this->LogsError();
        sqlite_query($this->db, $this->sql->Filter($query));

        return sqlite_last_insert_rowid($this->db);
    }

    /**
     * @return int
     */
    public function GetInsertId()
    {
        return sqlite_last_insert_rowid($this->db);
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

    protected function LogsError()
    {
        $e = sqlite_last_error($this->db);
        if ($e != 0) {
            $this->error[] = array($e, sqlite_error_string($e));
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
        ZbpErrorControl::SuspendErrorHook();
        $r = @$this->Query("PRAGMA table_info([$table])");
        ZbpErrorControl::ResumeErrorHook();
        $r = serialize($r);
        if (stripos($r, '"' . $field . '"') !== false) {
            return true;
        } else {
            return false;
        }
    }

}
