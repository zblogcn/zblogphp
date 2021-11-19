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
     * @param string $engine
     * @param string $charset
     * @param string $collate
     *
     * @return string
     */
    public function CreateTable($table, $datainfo, $engine = null, $charset = null, $collate = null)
    {
        $sql = $this->get();
        $sql->create($table)->data($datainfo);
        if (trim($engine) != '') {
            $sql->option(array('engine' => $engine));
        }
        if (trim($charset) != '') {
            $sql->option(array('charset' => $charset));
        }
        if (trim($collate) != '') {
            $sql->option(array('collate' => $collate));
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

        //定义出key array
        $array = array('COUNT', 'MIN', 'MAX', 'SUM', 'AVG', 'SELECTANY', 'FROM', 'INNERJOIN', 'LEFTJOIN', 'RIGHTJOIN', 'JOIN', 'FULLJOIN', 'USEINDEX', 'FORCEINDEX', 'IGNOREINDEX', 'ON', 'DISTINCT', 'RANDOM', 'COLUMN', 'GROUPBY', 'HAVING', 'WHERE', 'ORDER', 'LIMIT');
        foreach ($array as $key => $keyword) {
            if (isset($option[strtolower($keyword)])) {
                $args = array($option[strtolower($keyword)]);
                call_user_func_array(array($sql, $keyword), $args);
            }
        }

        if (isset($option['pagebar'])) {
            if ($option['pagebar']->Count === null) {
                $sqlpb = $this->get()->select($table)->count(array('*' => 'num'))->where($where)->option($option);
                foreach (array('FROM', 'INNERJOIN', 'LEFTJOIN', 'RIGHTJOIN', 'JOIN', 'FULLJOIN', 'USEINDEX', 'FORCEINDEX', 'IGNOREINDEX', 'ON', 'WHERE', 'GROUPBY', 'HAVING') as $key => $keyword) {
                    if (isset($option[strtolower($keyword)])) {
                        $args = array($option[strtolower($keyword)]);
                        call_user_func_array(array($sqlpb, $keyword), $args);
                    }
                }
                $query = $sqlpb->query;
                $option['pagebar']->Count = GetValueInArrayByCurrent($query, 'num');
            }
            $option['pagebar']->Count = (int) $option['pagebar']->Count;
            $option['pagebar']->Make();
        }

        if (!is_array($select)) {
            if (!empty($select)) {
                $select = array(trim($select));
            }
        }
        $sql->column($select);

        $sql = $sql->sql;

        return $sql;
    }

    /**
     * 构造计数语句.
     *
     * @param string $table
     * @param mixed  $count 不只是count,还有sum,avg,min,max
     * @param mixed  $where
     * @param null   $option
     *
     * @return string 返回构造的语句
     */
    public function Count($table, $countofnum, $where = null, $option = null)
    {
        $sql = $this->get()->select($table)->option($option)->where($where);
        if (count($countofnum) == 1) {
            $countofnum = $countofnum[0];
        }
        //为了兼容以前的做法才写了一堆的语句
        if (count($countofnum) == 3) { //array('sum','*','asname')
            call_user_func_array(array($sql, strtolower($countofnum[0])), array(array($countofnum[1] => $countofnum[2])));
        }
        if (count($countofnum) == 2) {
            if (in_array(strtoupper($countofnum[0]), array('COUNT', 'MIN', 'MAX', 'SUM', 'AVG'))) { //array('AVG','*')
                call_user_func_array(array($sql, $countofnum[0]), array($countofnum[1]));
            } else { //array('*','asname')
                call_user_func_array(array($sql, 'count'), array(array($countofnum[0] => $countofnum[1])));
            }
        }

        return $sql->sql;
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
        return $this->get()->insert($table)->data($keyvalue)->sql;
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
        return $this->get()->delete($table)->where($where)->option($option)->sql;
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
        $_SERVER['_query_count'] = ($_SERVER['_query_count'] + 1);

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
    private $pri_explort_db = null;

    public function Export($table, $keyvalue, $type = 'mysql')
    {
        if ($type == 'mysql' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__MySQL();
        }

        if ($type == 'mysqli' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__MySQLi();
        }

        if ($type == 'pdo_mysql' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__PDO_MySQL();
        }

        if ($type == 'sqlite' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__SQLite();
        }

        if ($type == 'sqlite3' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__SQLite3();
        }

        if ($type == 'pdo_sqlite' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__PDO_SQLite();
        }

        if ($type == 'postgresql' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__PostgreSQL();
        }

        if ($type == 'pdo_postgresql' && $this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__PDO_PostgreSQL();
        }

        if ($this->pri_explort_db === null) {
            $this->pri_explort_db = new Database__MySQL();
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

            $v = $this->pri_explort_db->EscapeString($v);
            $sql .= $comma . "'$v'";
            $comma = ',';
        }
        $sql .= ')';

        return $sql . ";\r\n";
    }

    //command = 'begin','commit','rollback'
    public function Transaction($command)
    {
        $command = strtoupper(trim($command));
        if ($command == 'BEGIN' || $command == 'COMMIT' || $command == 'ROLLBACK') {
            return $command;
        }
    }

}
