<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 数据库操作接口.
 */

/**
 * SQL语句生成类.
 */
class DbSql
{
    /**
     * @var Database__Interface 数据库连接实例
     */
    private $db = null;
    /**
     * @var null|string 数据库类型名称
     */
    private $dbclass = null;
    /**
     * @param object $db
     */
    private $sql = null;

    public function __construct(&$db = null)
    {
        $this->db = &$db;
        $this->dbclass = get_class($this->db);
        $this->sql = 'sql__' . $this->db->type;
    }

    /**
     * 替换数据表前缀
     *
     * @param string $s
     *
     * @return string
     */
    public function ReplacePre(&$s)
    {
        $s = str_replace('%pre%', $this->db->dbpre, $s);

        return $s;
    }

    /**
     * @return SQL__Global
     */
    public function get()
    {
        $sql = new $this->sql($this->db);

        return $sql;
    }

    /**
     * 删除表,返回SQL语句.
     *
     * @param string $table
     *
     * @return string
     */
    public function DelTable($table)
    {
        return $this->get()->drop("$table")->sql;
    }

    /**
     * 检查表是否存在，返回SQL语句.
     *
     * @param string $table
     * @param string $dbname
     *
     * @return string
     */
    public function ExistTable($table, $dbname = '')
    {
        return $this->get()->exist($table, $dbname)->sql;
    }

    /**
     * 创建表，返回构造完整的SQL语句.
     *
     * @param string $table
     * @param array  $datainfo
     * @param null   $engine
     *
     * @return string
     */
    public function CreateTable($table, $datainfo, $engine = null)
    {
        $sql = $this->get();
        $sql->create($table)->data($datainfo);
        if (!is_null($engine)) {
            $sql->option(array('engine' => $engine));
        }

        return $sql->sql;
    }

    /**
     * 构造查询语句.
     *
     * @param string     $table
     * @param string     $select
     * @param string     $where
     * @param string     $order
     * @param string     $limit
     * @param array|null $option
     *
     * @return string 返回构造的语句
     */
    public function Select($table, $select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (!is_array($option)) {
            $option = array();
        }

        $sql = $this->get()->select($table)->option($option)->where($where)->orderBy($order)->limit($limit);

        if (isset($option['select2count'])) {
            foreach ($select as $key => $value) {
                if (count($value) > 2) {
                    $sql->count(array_slice($value, 1));
                } else {
                    $sql->count($value);
                }
            }
        } else {
            if (!is_array($select)) {
                $select = array($select);
            }
            call_user_func_array(array($sql, 'column'), $select);
        }

        if (!empty($option)) {
            if (isset($option['pagebar'])) {
                if ($option['pagebar']->Count === null) {
                    $s2 = $this->Count($table, array(array('*', 'num')), $where);
                    $option['pagebar']->Count = GetValueInArrayByCurrent($this->db->Query($s2), 'num');
                }
                $option['pagebar']->Count = (int) $option['pagebar']->Count;
                $option['pagebar']->make();
            }
        }
        $sql = $sql->sql;

        return $sql;
    }

    /**
     * 构造计数语句.
     *
     * @param string $table
     * @param mixed  $count
     * @param mixed  $where
     * @param null   $option
     *
     * @return string 返回构造的语句
     */
    public function Count($table, $count, $where = null, $option = null)
    {
        if (!is_array($option)) {
            $option = array('select2count' => true);
        }

        return $this->Select($table, $count, $where, null, null, $option);
    }

    /**
     * 构造数据更新语句.
     *
     * @param string     $table
     * @param mixed      $keyvalue
     * @param mixed      $where
     * @param array|null $option
     *
     * @return string 返回构造的语句
     */
    public function Update($table, $keyvalue, $where, $option = null)
    {
        return $this->get()->update($table)->data($keyvalue)->where($where)->option($option)->sql;
    }

    /**
     * 构造数据插入语句.
     *
     * @param string $table
     * @param mixed  $keyvalue
     *
     * @return string 返回构造的语句
     */
    public function Insert($table, $keyvalue)
    {
        return $this->get()->insert($this->db)->insert($table)->data($keyvalue)->sql;
    }

    /**
     * 构造数据删除语句.
     *
     * @param string     $table
     * @param mixed      $where
     * @param array|null $option
     *
     * @return string 返回构造的语句
     */
    public function Delete($table, $where, $option = null)
    {
        return $this->get()->delete($this->db)->delete($table)->where($where)->option($option)->sql;
    }

    /**
     * 返回经过过滤的SQL语句.
     *
     * @param $sql
     *
     * @return mixed
     */
    public function Filter($sql)
    {
        $_SERVER['_query_count'] = $_SERVER['_query_count'] + 1;

        foreach ($GLOBALS['hooks']['Filter_Plugin_DbSql_Filter'] as $fpname => &$fpsignal) {
            $fpname($sql);
        }
        //Logs($sql);
        return $sql;
    }

    /**
     * 导出sql生成语句，用于备份数据用。
     *
     * @param $type 数据连接类型
     *
     * @return mixed
     */
    private $_explort_db = null;

    public function Export($table, $keyvalue, $type = 'mysql')
    {
        if ($type == 'mysql' && $this->_explort_db === null) {
            $this->_explort_db = new Database_MySQL();
        }

        if ($type == 'mysqli' && $this->_explort_db === null) {
            $this->_explort_db = new Database_MySQLi();
        }

        if ($type == 'pdo_mysql' && $this->_explort_db === null) {
            $this->_explort_db = new Database_PDOMySQL();
        }

        if ($type == 'sqlite' && $this->_explort_db === null) {
            $this->_explort_db = new Database_SQLite();
        }

        if ($type == 'sqlite3' && $this->_explort_db === null) {
            $this->_explort_db = new Database_SQLite();
        }

        if ($type == 'pdo_sqlite' && $this->_explort_db === null) {
            $this->_explort_db = new Database_SQLite3();
        }

        if ($this->_explort_db === null) {
            $this->_explort_db = new Database_MySQL();
        }

        $sql = "INSERT INTO $table ";

        $sql .= '(';
        $comma = '';
        foreach ($keyvalue as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            $sql .= $comma . "$k";
            $comma = ',';
        }
        $sql .= ')VALUES(';

        $comma = '';
        foreach ($keyvalue as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            $v = $this->_explort_db->EscapeString($v);
            $sql .= $comma . "'$v'";
            $comma = ',';
        }
        $sql .= ')';

        return $sql . ";\r\n";
    }
}
