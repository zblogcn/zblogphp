<?php
/**
 * 数据库操作接口
 *
 * @package Z-BlogPHP
 * @subpackage Interface/DataBase 类库
 */
interface iDataBase {

    public function Open($array);

    public function Close();

    public function Query($query);

    public function Insert($query);

    public function Update($query);

    public function Delete($query);

    public function QueryMulti($s);

    public function EscapeString($s);

    public function CreateTable($table, $datainfo);

    public function DelTable($table);

    public function ExistTable($table);

}

/**
 * SQL语句生成类
 * @package Z-BlogPHP
 * @subpackage ClassLib/DataBase
 */
class DbSql {
    /**
     * @var null 数据库连接实例
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

    public function __construct(&$db = null) {
        $this->db = &$db;
        $this->dbclass = get_class($this->db);
        $this->sql = 'sql' . $this->db->type;
    }
    /**
     * 替换数据表前缀
     * @param string $
     * @return string
     */
    public function ReplacePre(&$s) {
        $s = str_replace('%pre%', $this->db->dbpre, $s);

        return $s;
    }

    /**
     * 删除表,返回SQL语句
     * @param string $table
     * @return string
     */
    public function DelTable($table) {
        $this->ReplacePre($table);
        $sql = new $this->sql($this->db);

        return $sql->drop("$table")->sql;}

    /**
     * 检查表是否存在，返回SQL语句
     * @param string $table
     * @param string $dbname
     * @return string
     */
    public function ExistTable($table, $dbname = '') {
        $this->ReplacePre($table);
        $sql = new $this->sql($this->db);

        return $sql->exist($table);
    }

    /**
     * 创建表，返回构造完整的SQL语句
     * @param string $table
     * @param array $datainfo
     * @return string
     */
    public function CreateTable($table, $datainfo, $engine = null) {

        $sql = new $this->sql($this->db);
        $sql->create($table)->data($datainfo);
        if (!is_null($engine)) {
            $sql->option(array('engine' => $engine));
        }

        return $sql->sql;

        $this->ReplacePre($s);

        return $s;
    }

    /**
     * 构造条件查询语句
     * @param array $where
     * @param null $changewhere 是否更改'WHERE'，放空表示不更改，如设为'like'等将替换'WHERE'
     * @return null|string 返回构造的语句
     */
    public function ParseWhere($where, $changewhere = null) {

        $sqlw = null;
        if (empty($where)) {
            return null;
        }

        if (!is_null($changewhere)) {
            $sqlw .= " $changewhere ";
        } else {
            $sqlw .= ' WHERE ';
        }

        if (!is_array($where)) {
            return $sqlw . $where;
        }

        $comma = '';
        foreach ($where as $k => $w) {
            $eq = strtoupper($w[0]);
            if ($eq == '=' | $eq == '<' | $eq == '>' | $eq == 'LIKE' | $eq == '<>' | $eq == '<=' | $eq == '>=' | $eq == 'NOT LIKE' | $eq == 'ILIKE' | $eq == 'NOT ILIKE') {
                $x = (string) $w[1];
                $y = (string) $w[2];
                $y = $this->db->EscapeString($y);
                $sqlw .= $comma . " $x $eq '$y' ";
            }
            if ($eq == 'EXISTS' | $eq == 'NOT EXISTS') {
                if (!isset($w[2])) {
                    $sqlw .= $comma . ' ' . $eq . ' (' . $w[1] . ') ';
                } else {
                    $sqlw .= $comma . '(' . $w[1] . ' ' . $eq . ' (' . $w[2] . ')) ';
                }
            }
            if ($eq == 'BETWEEN') {
                $b1 = (string) $w[1];
                $b2 = (string) $w[2];
                $b3 = (string) $w[3];
                $sqlw .= $comma . " $b1 BETWEEN '$b2' AND '$b3' ";
            }
            if ($eq == 'SEARCH') {
                $j = count($w);
                $sql_search = '';
                $c = '';
                for ($i = 1; $i <= $j - 1 - 1; $i++) {
                    $x = (string) $w[$i];
                    $y = (string) $w[$j - 1];
                    $y = $this->db->EscapeString($y);
                    $sql_search .= $c . " ($x LIKE '%$y%') ";
                    $c = 'OR';
                }
                $sqlw .= $comma . '(' . $sql_search . ') ';
            }
            if ($eq == 'ARRAY') {
                $c = '';
                $sql_array = '';
                if (!is_array($w[1])) {
                    continue;
                }

                if (count($w[1]) == 0) {
                    continue;
                }

                foreach ($w[1] as $x => $y) {
                    $y[1] = $this->db->EscapeString($y[1]);
                    $sql_array .= $c . " $y[0]='$y[1]' ";
                    $c = 'OR';
                }
                $sqlw .= $comma . '(' . $sql_array . ') ';
            }
            if ($eq == 'ARRAY_NOT') {
                $c = '';
                $sql_array = '';
                if (!is_array($w[1])) {
                    continue;
                }

                if (count($w[1]) == 0) {
                    continue;
                }

                foreach ($w[1] as $x => $y) {
                    $y[1] = $this->db->EscapeString($y[1]);
                    $sql_array .= $c . " $y[0]<>'$y[1]' ";
                    $c = 'OR';
                }
                $sqlw .= $comma . '(' . $sql_array . ') ';
            }
            if ($eq == 'ARRAY_LIKE') {
                $c = '';
                $sql_array = '';
                if (!is_array($w[1])) {
                    continue;
                }

                if (count($w[1]) == 0) {
                    continue;
                }

                foreach ($w[1] as $x => $y) {
                    $y[1] = $this->db->EscapeString($y[1]);
                    $sql_array .= $c . " ($y[0] LIKE '$y[1]') ";
                    $c = 'OR';
                }
                $sqlw .= $comma . '(' . $sql_array . ') ';
            }
            if ($eq == 'IN' | $eq == 'NOT IN') {
                $c = '';
                $sql_array = '';
                if (!is_array($w[2])) {
                    $sql_array = $w[2];
                } else {
                    if (count($w[2]) == 0) {
                        continue;
                    }

                    foreach ($w[2] as $x => $y) {
                        $y = $this->db->EscapeString($y);
                        $sql_array .= $c . " '$y' ";
                        $c = ',';
                    }
                }
                $sqlw .= $comma . ' ' . $w[1] . ' ' . $eq . ' (' . $sql_array . ') ';
            }
            if ($eq == 'META_NAME') {
                if (count($w) != 3) {
                    continue;
                }

                $sql_array = '';
                $sql_meta = 's:' . strlen($w[2]) . ':"' . $w[2] . '";';
                $sql_meta = $this->db->EscapeString($sql_meta);
                $sql_array .= "$w[1] LIKE '%$sql_meta%'";
                $sqlw .= $comma . '(' . $sql_array . ') ';
            }
            if ($eq == 'META_NAMEVALUE') {
                if (count($w) == 4) {
                    $sql_array = '';
                    $sql_meta = 's:' . strlen($w[2]) . ':"' . $w[2] . '";' . 's:' . strlen($w[3]) . ':"' . $w[3] . '"';
                    $sql_meta = $this->db->EscapeString($sql_meta);
                    $sql_array .= "$w[1] LIKE '%$sql_meta%'";
                    $sqlw .= $comma . '(' . $sql_array . ') ';
                } elseif (count($w) == 5) {
                    $sql_array = '';
                    $sql_meta = 's:' . strlen($w[2]) . ':"' . $w[2] . '";' . $w[3];
                    $sql_meta = $this->db->EscapeString($sql_meta);
                    $sql_array .= "$w[1] LIKE '%$sql_meta%'";
                    $sqlw .= $comma . '(' . $sql_array . ') ';
                }
            }
            if ($eq == 'CUSTOM') {
                $sqlw .= $comma . ' ' . $w[1] . ' ';
            }
            $comma = 'AND';
        }

        return $sqlw;
    }

    /**
     * 构造查询语句
     * @param string $table
     * @param string $select
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param array|null $option
     * @return string 返回构造的语句
     */
    public function Select($table, $select = null, $where = null, $order = null, $limit = null, $option = null) {
        $this->ReplacePre($table);

        if (is_array($option) == false) {
            $option = array();
        }
        $sql = new $this->sql($this->db);
        $sql->select($table)->option($option)->where($where)->orderBy($order)->limit($limit);

        if (isset($option['select2count'])) {
            foreach ($select as $key => $value) {
                if (count($value) > 2) {
                    $sql->count(array_slice($value, 1));
                } else {
                    $sql->count($value);
                }
                
            }
        } else {
            $sql->column($select);
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
     * 构造计数语句
     * @param string $table
     * @param string $count
     * @param string $where
     * @param null $option
     * @return string 返回构造的语句
     */
    public function Count($table, $count, $where = null, $option = null) {
        $this->ReplacePre($table);

        if (is_array($option) == false) {
            $option = array();
        }

        $option['select2count'] = true;

        return $this->Select($table, $count, $where, null, null, $option);
    }

    /**
     * 构造数据更新语句
     * @param string $table
     * @param string $keyvalue
     * @param string $where
     * @param array|null $option
     * @return string 返回构造的语句
     */
    public function Update($table, $keyvalue, $where, $option = null) {
        $this->ReplacePre($table);
        $sql = new $this->sql($this->db);

        return $sql->update($table)->data($keyvalue)->where($where)->option($option)->sql;
    }

    /**
     * 构造数据插入语句
     * @param string $table
     * @param string $keyvalue
     * @return string 返回构造的语句
     */
    public function Insert($table, $keyvalue) {
        $this->ReplacePre($table);
        $sql = new $this->sql($this->db);

        return $sql->insert($this->db)->insert($table)->data($keyvalue)->sql;
    }

    /**
     * 构造数据删除语句
     * @param string $table
     * @param string $where
     * @param array|null $option
     * @return string 返回构造的语句
     */
    public function Delete($table, $where, $option = null) {
        $this->ReplacePre($table);
        $sql = new $this->sql($this->db);

        return $sql->delete($this->db)->delete($table)->where($where)->option($option)->sql;
    }

    /**
     * 返回经过过滤的SQL语句
     * @param $sql
     * @return mixed
     */
    public function Filter($sql) {
        $_SERVER['_query_count'] = $_SERVER['_query_count'] + 1;

        foreach ($GLOBALS['hooks']['Filter_Plugin_DbSql_Filter'] as $fpname => &$fpsignal) {
            $fpname($sql);
        }
        //Logs($sql);
        return $sql;
    }

    /**
     * 导出sql生成语句，用于备份数据用。
     * @param $type 数据连接类型
     * @return mixed
     */
    private $_explort_db = null;
    public function Export($table, $keyvalue, $type = 'mysql') {

        if ($type == 'mysql' && $this->_explort_db === null) {
            $this->_explort_db = new DbMySQL;
        }

        if ($type == 'mysqli' && $this->_explort_db === null) {
            $this->_explort_db = new DbMySQLi;
        }

        if ($type == 'pdo_mysql' && $this->_explort_db === null) {
            $this->_explort_db = new Dbpdo_MySQL;
        }

        if ($type == 'sqlite' && $this->_explort_db === null) {
            $this->_explort_db = new DbSQLite;
        }

        if ($type == 'sqlite3' && $this->_explort_db === null) {
            $this->_explort_db = new DbSQLite3;
        }

        if ($type == 'pdo_sqlite' && $this->_explort_db === null) {
            $this->_explort_db = new Dbpdo_SQLite;
        }

        if ($this->_explort_db === null) {
            $this->_explort_db = new DbMySQL;
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
