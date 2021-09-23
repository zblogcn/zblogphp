<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * SQLite3数据库操作类.
 */
class Database__SQLite3 implements Database__Interface
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
        return SQLite3::escapeString($s);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public function Open($array)
    {
        if ($this->db = new SQLite3($array[0])) {
            $this->dbpre = $array[1];
            $this->dbname = $array[0];
            $this->version = substr(GetValueInArray(SQLite3::version(), 'versionString'), 1);

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
        $this->db->close();
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
                $this->db->query($this->sql->Filter($s));
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
        $results = @$this->db->query($this->sql->Filter($query));
        $e = $this->db->lastErrorCode();
        if ($e != 0) {
            trigger_error($e . $this->db->lastErrorMsg(), E_USER_NOTICE);
        }
        $this->LogsError();
        if (!($results instanceof Sqlite3Result)) {
            return array($results);
        }
        $data = array();
        if ($results->numColumns() > 0) {
            while ($row = $results->fetchArray()) {
                $data[] = $row;
            }
        } else {
            $data[] = $results->numColumns();
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
        $r = $this->db->query($this->sql->Filter($query));
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
        $r = $this->db->query($this->sql->Filter($query));
        $this->LogsError();
        return $r;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function Insert($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $this->db->query($this->sql->Filter($query));
        $this->LogsError();
        return $this->db->lastInsertRowID();
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

    private function LogsError()
    {
        $e = $this->db->lastErrorCode();
        if ($e > 0) {
            $this->error[] = array($e, $this->db->lastErrorMsg());
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
        ZBlogException::SuspendErrorHook();
        $r = @$this->Query("PRAGMA table_info([$table])");
        ZBlogException::ResumeErrorHook();
        $r = serialize($r);
        if (stripos($r, '"' . $field . '"') !== false) {
            return true;
        } else {
            return false;
        }
    }

}
