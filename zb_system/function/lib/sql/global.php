<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * @property string sql 最终生成的SQL语句
 *
 * @method SQL__Global select(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global insert(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global update(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global delete(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global create(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global drop(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global count(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global min(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global max(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 * @method SQL__Global sum(mixed $a, mixed $b = null, mixed $c = null, mixed $d = null, mixed $e = null)
 */
class SQL__Global
{
    /**
     * @var string 类名
     * @description 如果是PHP 5.3的话，可以用get_called_class
     */
    public $className = __CLASS__;

    private $_sql = array();
    protected $option = array(
        'whereKeyword' => 'WHERE',
    );
    protected $method = 'SELECT';
    protected $table = array();
    protected $data = array();
    protected $columns = array();
    protected $where = array();
    protected $join = array();
    protected $orderBy = array();
    protected $groupBy = array();
    protected $having = array();
    protected $index = array();
    private $methodKeyword = array('ALTER','SELECT','INSERT', 'DROP', 'DELETE', 'CREATE', 'UPDATE', 'TRUNCATE');
    private $selectFunctionKeyword = array('COUNT', 'MIN', 'MAX', 'SUM');
    private $otherKeyword = array('FIELD','INDEX','TABLE','DATABASE');
    private $extendKeyword = array('SELECTANY','FROM','IFEXISTS','INNERJOIN','LEFTJOIN','RIGHTJOIN','JOIN','FULLJOIN','UNION','ADDCOLUMN','DROPCOLUMN','ALTERCOLUMN');
    protected $extend = array();
    protected $other = array();

    /**
     * @var null 数据库连接实例
     */
    private $db = null;
    /**
     * @var null|string 数据库类型名称
     */
    private $dbclass = null;

    public function init()
    {
        return new $this->className($this->db);
    }

    private function validateParamater($param)
    {
        if (is_null($param)) {
            return false;
        } elseif (is_string($param)) {
            if ($param == "") {
                return false;
            }
        } elseif (is_array($param) && count($param) == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param object $db
     */
    public function __construct(&$db = null)
    {
        $this->db = &$db;
        $this->dbclass = get_class($this->db);
    }

    /**
     * @param $callName
     * @param $argu
     *
     * @throws Exception
     *
     * @return SQL__Global|mixed
     */
    public function __call($callName, $argu)
    {
        $upperKeyword = strtoupper($callName);
        if (in_array($upperKeyword, $this->methodKeyword)) {
            $this->method = $upperKeyword;
            if(!isset($argu[0])){
                $argu[0]='';
            }
            $this->table = is_array($argu[0]) ? $argu[0] : $argu;
            $this->table = str_replace('%pre%', $this->db->dbpre, $this->table);
            return $this;
        } elseif (in_array($upperKeyword, $this->otherKeyword)) {
            if ($upperKeyword == 'INDEX') {
                foreach ($argu as $key => $value) {
                    if(!is_array($value)){
                        $this->other[$upperKeyword] = $argu;
                        break;
                    }
                    //is_array($argu[0]) ? $argu[0] : $argu;
                    $this->index[key($value)] = current($value);
                }
            } elseif ($upperKeyword == 'TABLE' || $upperKeyword == 'DATABASE') {
                $this->other[$upperKeyword] = $argu;
            } else {
                $this->data = is_array($argu[0]) ? $argu[0] : $argu;
            }
            return $this;
        } elseif (in_array($upperKeyword, $this->selectFunctionKeyword)) {
            /*
             * Count
             * @example count('log_ID')
             * @example count('log_ID', 'countLogId')
             * @example count(array('log_Id', 'countLogId'))
             * @return [type] [description]
             */
            if (count($argu) == 1) {
                $arg = $argu[0];
                if (is_string($arg)) {
                    $this->columns[] = "$upperKeyword($arg)";
                } else {
                    $this->columns[] = "$upperKeyword($arg[0]) AS $arg[1]";
                }
            } else {
                $this->columns[] = "$upperKeyword($argu[0]) AS $argu[1]";
            }

            return $this;
        } elseif (in_array($upperKeyword, $this->extendKeyword)) {
            $this->extend[$upperKeyword] = $argu;
            return $this;
        } else {
            $lowerKeyword = strtolower($callName);
            if (is_callable($this, $lowerKeyword)) {
                return call_user_func_array(array($this, $lowerKeyword), $argu);
            }
        }
        // @codeCoverageIgnoreEnd
        throw new Exception("Unimplemented $callName");
    }

    public function __get($getName)
    {
        $upperKeyword = strtoupper($getName);
        if ($upperKeyword == "SQL") {
            $ret = $this->sql();
            $this->reset();

            return $ret;
        }

        return $this->$getName;
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            throw new Exception('Unknown attribute: ' . $name);
        }
    }

    /**
     * If we use $this->$getName directly, PHP will throw [Indirect modification of overloaded property]
     * So we have to wrap it.
     * It maybe a bug of PHP.
     *
     * @see  http://stackoverflow.com/questions/10454779/php-indirect-modification-of-overloaded-property
     *
     * @param $sql
     */
    public function _sqlPush($sql)
    {
        $this->_sql[] = $sql;
    }

    /**
     * Re-initialize this class.
     *
     * @return SQL__Global
     */
    public function reset()
    {
        foreach (get_class_vars(get_class($this)) as $var => $defVal) {
            if ($var == "db" || $var == "dbclass") {
                continue;
            }
            $this->$var = $defVal;
        }

        return $this;
    }

    /**
     * Set SQL query option.
     *
     * @param $option
     *
     * @return SQL__Global
     */
    public function option($option)
    {
        if (!$this->validateParamater($option)) {
            return $this;
        }

        $this->option = array_merge_recursive($this->option, array_change_key_case($option, CASE_LOWER));

        return $this;
    }

    protected function columnLoaderArray($columns)
    {
        foreach ($columns as $column) {
            if (is_array($column)) {
                if (count($column) > 1) {
                    $this->columns[] = "$column[0] AS $column[1]";
                } else {
                    $this->columns[] = $column[0];
                }
            } else {
                $this->columns[] = $column;
            }
        }
    }

    /**
     * Set column for query.
     *
     * @param $columns
     *
     * @return SQL__Global
     */
    public function column($columns)
    {
        if (!$this->validateParamater($columns)) {
            return $this;
        }
        $nums = func_num_args();
        if ($nums == 1) {
            if (is_array($columns)) {
                if (count($columns) == 2) {
                    if (is_string($columns[1])) {
                        $this->columns[] = "$columns[0] AS $columns[1]";
                    } else {
                        $this->columnLoaderArray($columns);
                    }
                } elseif (count($columns) == 1) {
                    $this->columns[] = "$columns[0]";
                } else {
                    $this->columnLoaderArray($columns);
                }
            } else {
                $this->columns[] = $columns;
            }
        } else {
            $args = func_get_args(); // Fuck PHP 5.2
            $this->columnLoaderArray($args);
        }

        return $this;
    }

    /**
     * Set limit & offset.
     *
     * @example limit(5)
     * @example limit(10, 1)
     * @example limit(array(10, 1))
     *
     * @return SQL__Global
     */
    public function limit()
    {
        if (func_num_args() == 2) {
            $this->option['limit'] = func_get_arg(1);
            $this->option['offset'] = func_get_arg(0);
        } elseif (func_num_args() == 1) {
            $arg = func_get_arg(0);

            if (!$this->validateParamater($arg)) {
                return $this;
            } elseif (is_array($arg)) {
                if (count($arg) == 2) {
                    $this->option['offset'] = $arg[0];
                    $this->option['limit'] = $arg[1];
                } else {
                    $this->option['limit'] = $arg[0];
                }
            } else {
                $this->option['limit'] = $arg;
            }
        }

        return $this;
    }

    /**
     * Set where query.
     *
     * @example array(array('=', 'a', 'b'), array('=', 'a', 'b'))
     * @example array(array('=', 'a', 'b'))
     * @example array('=', 'a', 'b'), array('=', 'a', 'b')
     * @example array('=', 'a', 'b')
     *
     * @return SQL__Global
     */
    public function where()
    {
        $where = func_num_args() == 1 ? func_get_arg(0) : func_get_args();
        if (!$this->validateParamater($where)) {
            return $this;
        }

        if (is_array($where[0])) {
            if (count($where) == 1) {
                if (is_array($where[0][0])) {
                    $where = $where[0];
                }
            }
        } else {
            $where = array($where);
        }
        $this->where = array_merge_recursive($this->where, $where);

        return $this;
    }

    /**
     * Set having.
     *
     * @param $having
     *
     * @return SQL__Global
     */
    public function having($having)
    {
        if (!$this->validateParamater($having)) {
            return $this;
        } elseif (is_array($having)) {
            $this->having = array_merge($this->having, $having);
        } elseif (func_num_args() > 1) {
            $args = func_get_args(); // Fuck PHP 5.2
            $this->having = array_merge($this->having, $args);
        } else {
            $this->having[] = $having;
        }

        return $this;
    }

    /**
     * GroupBy.
     *
     * @param $groupBy
     *
     * @return SQL__Global
     */
    public function groupBy($groupBy)
    {
        if (!$this->validateParamater($groupBy)) {
            return $this;
        } elseif (is_array($groupBy)) {
            $this->groupBy = array_merge($this->groupBy, $groupBy);
        } elseif (func_num_args() > 1) {
            $args = func_get_args(); // Fuck PHP 5.2
            $this->groupBy = array_merge($this->groupBy, $args);
        } else {
            $this->groupBy[] = $groupBy;
        }

        return $this;
    }

    /**
     * Order by.
     *
     * @return SQL__Global
     */
    public function orderBy()
    {
        $order = func_get_args();
        if (!$this->validateParamater($order)) {
            return $this;
        }
        foreach ($order as $key => $value) {
            $ret = $value;
            if (!$this->validateParamater($ret)) {
                continue;
            }
            if (!is_array($ret)) {
                $ret = array($value => '');
            }

            $this->orderBy = array_merge_recursive($this->orderBy, $ret);
        }

        return $this;
    }

    /**
     * Set data for INSERT & UPDATE.
     *
     * @example array('key' => 'value', 'key2' => 'value2')
     *
     * @return SQL__Global
     */
    public function data()
    {
        $data = func_num_args() == 1 ? func_get_arg(0) : func_get_args();
        if (!$this->validateParamater($data)) {
            return $this;
        }
        $this->data = array_merge_recursive($this->data, $data);

        return $this;
    }

    /**
     * @todo
     *
     * @param string $table
     * @param string $dbname
     *
     * @return SQL__Global
     */
    public function exist($table, $dbname = '')
    {
        return $this;
    }

    /**
     * @param $sql
     */
    public function query($sql = null)
    {
        if (is_null($sql)) {
            $sql = $this->sql(); // wtf is it??
        }
    }

    private function sql()
    {
        $sql = &$this->_sql;
        if (count($sql) == 0) {
            $sql = array("$this->method");
            $callableMethod = 'build' . ucfirst($this->method);
            $this->$callableMethod();
        }

        //logs(implode(' ', $sql) . "\r\n");
        //return var_export($sql,true);
        //return var_export($this->extend,true);

        return implode(' ', $sql);
    }

    protected function buildTable()
    {
        $sql = &$this->_sql;
        $table = &$this->table;
        $tableData = array();

        //array_walk
        foreach ($table as $index => $tableValue) {
            if (is_string($tableValue)) {
                $tableData[] = " $tableValue "; // 为保证兼容性，不加反引号
            }
            if (is_array($tableValue)) {
                $tableData[] = " $tableValue[0] $tableValue[1] ";
            }
        }
        $sql[] = implode(", ", $tableData);
    }

    protected function buildColumn()
    {
        $sql = &$this->_sql;
        $columns = &$this->columns;
        if (count($columns) > 0) {
            $selectStr = implode(',', $columns);
            $sql[] = " {$selectStr} ";
        } else {
            $sql[] = "*";
        }
    }

    protected function buildWhere($originalWhere = null, $whereKeyword = null)
    {
        $sql = &$this->_sql;
        $where = is_null($originalWhere) ? $this->where : $originalWhere;
        if (count($where) == 0) {
            return;
        }
        $sql[] = is_null($whereKeyword) ? $this->option['whereKeyword'] : $whereKeyword;
        $whereData = array();

        foreach ($where as $index => $value) {
            if (is_string($value)) {
                $whereData[] = $value;
                continue;
            }
            $whereData[] = $this->buildWhere_Single($value);
        }
        $sql[] = implode(' AND ', $whereData);
    }

    protected function buildWhere_Single($value)
    {
        $whereData = '';
        $eq = strtoupper($value[0]);
        if (in_array($eq, array('=', '<>', '>', '<', '>=', '<=', 'NOT LIKE', 'LIKE', 'ILIKE', 'NOT ILIKE'))) {
            $x = (string) $value[1];
            $y = $this->db->EscapeString((string) $value[2]);
            $whereData = " $x $eq '$y' ";
        } elseif (($eq == 'AND') && count($value) > 2) {
            $sqlArray = array();
            foreach ($value as $x => $y) {
                if ($x == 0) {
                    continue;
                }
                $sqlArray[] = $this->buildWhere_Single($y);
            }
            $whereData = " ( " . implode(' AND ', $sqlArray) . ') ';
        } elseif ($eq == 'EXISTS' || $eq == 'NOT EXISTS') {
            if (!isset($value[2])) {
                $whereData = " $eq ( $value[1] ) ";
            } else {
                $whereData = " (1 = 1) ";

                return $whereData;
            }
        } elseif ($eq == 'BETWEEN') {
            $whereData = " ($value[1] BETWEEN '$value[2]' AND '$value[3]') ";
        } elseif ($eq == 'SEARCH') {
            $searchCount = count($value);
            $sqlSearch = array();
            for ($i = 1; $i <= $searchCount - 1 - 1; $i++) {
                $x = (string) $value[$i];
                $y = $this->db->EscapeString((string) $value[$searchCount - 1]);
                $sqlSearch[] = " ($x LIKE '%$y%') ";
            }
            $whereData = " ((1 = 1) AND (" . implode(' OR ', $sqlSearch) . ') )';
        } elseif (($eq == 'OR' || $eq == 'ARRAY') && count($value) > 2) {
            $sqlArray = array();
            foreach ($value as $x => $y) {
                if ($x == 0) {
                    continue;
                }
                $sqlArray[] = $this->buildWhere_Single($y);
            }
            $whereData = " ( " . implode(' OR ', $sqlArray) . ') ';
        } elseif ($eq == 'ARRAY' || $eq == 'NOT ARRAY' || $eq == 'LIKE ARRAY' || $eq == 'ILIKE ARRAY' || $eq == 'ARRAY_LIKE' || $eq == 'ARRAY_ILIKE') {
            if ($eq == 'ARRAY') {
                $symbol = '=';
            } elseif ($eq == 'NOT ARRAY') {
                $symbol = '<>';
            } elseif ($eq == 'LIKE ARRAY' || $eq == 'ARRAY_LIKE') {
                $symbol = 'LIKE';
            } elseif ($eq == 'ILIKE ARRAY' || $eq == 'ARRAY_ILIKE') {
                $symbol = 'ILIKE';
            } else {
                $symbol = '=';
            }
            $sqlArray = array();
            if (!is_array($value[1])) {
                $whereData = " (1 = 1) ";

                return $whereData;
            }
            foreach ($value[1] as $x => $y) {
                $y[1] = $this->db->EscapeString($y[1]);
                $sqlArray[] = " $y[0] $symbol '$y[1]' ";
            }
            $whereData = " ((1 = 1) AND (" . implode(' OR ', $sqlArray) . ') )';
        } elseif ($eq == 'IN' || $eq == 'NOT IN') {
            $sqlArray = array();
            if (!is_array($value[2])) {
                if ($this->validateParamater($value[2])) {
                    $whereData = " ($value[1] $eq ($value[2])) ";

                    return $whereData;
                } else {
                    $whereData = " (1 = 1) ";
                }

                return $whereData;
            }

            if (count($value[2]) == 0) {
                $whereData = " (1 = 1) ";

                return $whereData;
            }

            foreach ($value[2] as $x => $y) {
                $y = $this->db->EscapeString($y);
                $sqlArray[] = " '$y' ";
            }
            $whereData = " ((1 = 1) AND ($value[1] $eq (" . implode(', ', $sqlArray) . ') ) )';
        } elseif ($eq == 'META_NAME') {
            if (count($value) != 3) {
                $whereData = " (1 = 1) ";

                return $whereData;
            }
            $sqlMeta = 's:' . strlen($value[2]) . ':"' . $value[2] . '";';
            $sqlMeta = $this->db->EscapeString($sqlMeta);
            $whereData = "($value[1] LIKE '%$sqlMeta%')";
        } elseif ($eq == 'META_NAMEVALUE') {
            if (count($value) != 4) {
                $whereData = " (1 = 1) ";

                return $whereData;
            }
            $sqlMeta = 's:' . strlen($value[2]) . ':"' . $value[2] . '";' . 's:' . strlen($value[3]) . ':"' . $value[3] . '"';
            $sqlMeta = $this->db->EscapeString($sqlMeta);
            $whereData = "($value[1] LIKE '%$sqlMeta%')";
        } elseif ($eq == "CUSTOM") {
            $whereData = $value[1];
        } elseif(count($value) == 1 ) {
            $whereData = '(' . $value[0] . ')';
        }

        return $whereData;
    }

    protected function buildOrderBy()
    {
        $sql = &$this->_sql;
        if (count($this->orderBy) == 0) {
            return;
        }

        $sql[] = "ORDER BY";
        $orderByData = array();

        foreach ($this->orderBy as $key => $value) {
            if (is_int($key)) {
                $orderByData[] = "$value";
            } else {
                $orderByData[] = "$key $value";
            }
        }
        $sql[] = implode(', ', $orderByData);
    }

    protected function buildGroupBy()
    {
        $sql = &$this->_sql;
        if (count($this->groupBy) == 0) {
            return;
        }

        $sql[] = "GROUP BY";
        $groupByData = array();
        foreach ($this->groupBy as $key => $value) {
            $groupByData[] = $value;
        }
        $sql[] = implode(', ', $groupByData);
    }

    protected function buildHaving()
    {
        $sql = &$this->_sql;
        if (count($this->having) == 0) {
            return;
        }

        $sql[] = "HAVING";
        $this->buildWhere($this->having, ' ');
    }

    protected function buildLimit()
    {
        $sql = &$this->_sql;

        if (isset($this->option['limit'])) {
            if ($this->option['limit'] > 0) {
                $sql[] = "LIMIT " . $this->option['limit'];

                if (isset($this->option['offset'])) {
                    $sql[] = "OFFSET " . $this->option['offset'];
                }
            }
        }
    }

    /**
     * @todo
     **/
    protected function buildPagebar()
    {
    }

    protected function buildBeforeWhere()
    {
        // Do nothing yet
    }

    protected function buildOthers()
    {
        // Do nothing yet
    }

    protected function buildALTER()
    {
        $sql = &$this->_sql;

        $sql[] = 'TABLE';
        $this->buildTable();
        if( array_key_exists ('ADDCOLUMN',$this->extend) ){
            $this->buildADDCOLUMN();
        }elseif( array_key_exists ('DROPCOLUMN',$this->extend) ){
            $this->buildDROPCOLUMN();
        }elseif( array_key_exists ('ALTERCOLUMN',$this->extend) ){
            $this->buildLEFTJOIN();
        }
    }

    protected function buildSelect()
    {
        $sql = &$this->_sql;

        // Unimplemented select2count
        if( array_key_exists ('UNION',$this->extend) ){
            $this->buildUnion();
            return ;
        }
        if( array_key_exists ('SELECTANY',$this->extend) ){
            $this->buildSelectAny();
        }
        $this->buildColumn();
        if( array_key_exists ('FROM',$this->extend) ){
            $this->buildFrom();
        }else{
            $sql[] = 'FROM';
            $this->buildTable();
        }
        if( array_key_exists ('JOIN',$this->extend) ){
            $this->buildJOIN();
        }elseif( array_key_exists ('INNERJOIN',$this->extend) ){
            $this->buildINNERJOIN();
        }elseif( array_key_exists ('LEFTJOIN',$this->extend) ){
            $this->buildLEFTJOIN();
        }elseif( array_key_exists ('RIGHTJOIN',$this->extend) ){
            $this->buildRIGHTJOIN();
        }elseif( array_key_exists ('FULLJOIN',$this->extend) ){
            $this->buildFULLJOIN();
        }

        $this->buildBeforeWhere();
        $this->buildWhere();
        $this->buildGroupBy();
        $this->buildHaving();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->buildOthers();
    }

    protected function buildSELECTANY()
    {
        $sql = &$this->_sql;
        $this->extend['SELECTANY'] = empty($this->extend['SELECTANY'])?array('*'):$this->extend['SELECTANY'];
        //$sql[] = implode(' ,',$this->extend['SELECTANY']);
        foreach ($this->extend['SELECTANY'] as $key => $value) {
            $this->columns[] = $value;
        }
    }

    protected function buildADDCOLUMN()
    {
        $sql = &$this->_sql;
        $sql[] = ' ADD COLUMN';
        $sql[] = implode(' ',$this->extend['ADDCOLUMN']);
    }
    protected function buildALTERCOLUMN()
    {
        $sql = &$this->_sql;
        $sql[] = ' ALTER COLUMN';
        $sql[] = implode(' ',$this->extend['ALTERCOLUMN']);
    }
    protected function buildDROPCOLUMN()
    {
        $sql = &$this->_sql;
        $sql[] = ' DROP COLUMN';
        $sql[] = implode(' ',$this->extend['DROPCOLUMN']);
    }

    protected function buildJOIN()
    {
        $sql = &$this->_sql;
        $sql[] = ' JOIN';
        $sql[] = implode(' ,',$this->extend['JOIN']);
    }
    protected function buildINNERJOIN()
    {
        $sql = &$this->_sql;
        $sql[] = ' INNER JOIN';
        $sql[] = implode(' ,',$this->extend['INNERJOIN']);
    }
    protected function buildLEFTJOIN()
    {
        $sql = &$this->_sql;
        $sql[] = ' LEFT JOIN';
        $sql[] = implode(' ,',$this->extend['LEFTJOIN']);
    }
    protected function buildRIGHTJOIN()
    {
        $sql = &$this->_sql;
        $sql[] = ' RIGHT JOIN';
        $sql[] = implode(' ,',$this->extend['RIGHTJOIN']);
    }
    protected function buildFULLJOIN()
    {
        $sql = &$this->_sql;
        $sql[] = ' FULL JOIN';
        $sql[] = implode(' ,',$this->extend['FULLJOIN']);
    }

    protected function buildFROM()
    {
        $sql = &$this->_sql;
        $sql[] = ' FROM';
        $sql[] = implode(' ,',$this->extend['FROM']);
    }

    protected function buildUnion()
    {
        $sql = &$this->_sql;
        $sql = array();
        $sql[] = $this->extend['UNION'][0];
        $sql[] = ' UNION ';
        $sql[] = $this->extend['UNION'][1];
    }

    protected function buildUpdate()
    {
        $sql = &$this->_sql;
        $sql[] = $this->buildTable();
        $sql[] = 'SET';
        $updateData = array();
        foreach ($this->data as $index => $value) {
            if (is_null($value)) {
                continue;
            }
            $escapedValue = $this->db->EscapeString($value);
            $updateData[] = "$index = '$escapedValue'";
        }
        $sql[] = implode(', ', $updateData);
        $this->buildWhere();

        return $sql;
    }

    protected function buildDelete()
    {
        $sql = &$this->_sql;
        $sql[] = 'FROM';
        $this->buildTable();
        $this->buildWhere();
    }

    protected function buildInsert()
    {
        $sql = &$this->_sql;
        $sql[] = 'INTO';
        $this->buildTable();
        $keyData = array();
        $valueData = array();
        foreach ($this->data as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            $v = $this->db->EscapeString($value);
            $keyData[] = "$key";
            $valueData[] = " '$v' ";
        }
        $sql[] = '(' . implode(',', $keyData) . ')';
        $sql[] = ' VALUES (';
        $sql[] = implode(',', $valueData);
        $sql[] = ')';
    }

    protected function buildTRUNCATE()
    {
        $sql = &$this->_sql;
        $sql[] = 'TABLE';
        $this->buildTable();
    }

    protected function buildIFEXISTS()
    {
        $sql = &$this->_sql;
        if( array_key_exists ('IFEXISTS',$this->extend) ){
            $sql[] = 'IF EXISTS';
        }
    }

    protected function buildDrop()
    {
        $sql = &$this->_sql;

        if( array_key_exists ('INDEX',$this->other) ){
            $sql[] = 'INDEX';
            $sql[] = implode(' ,',$this->other['INDEX']);
            if ($this->db->type == 'mysql') {
                $sql[] = 'ON';
                $this->buildTable();
            }
            return ;
        }
        if( array_key_exists ('DATABASE',$this->other) ){
            $sql[] = 'DATABASE';
            $this->buildIFEXISTS();
            $sql[] = implode(' ,',$this->other['DATABASE']);
            return ;
        }

        $sql[] = 'TABLE';
        $this->buildIFEXISTS();
        $this->buildTable();
        if(empty( trim(implode('',$this->table)) )){
            $sql[] = implode(' ,',$this->other['TABLE']);
        }
    }

    protected function buildCreate()
    {
        // Do nothing yet
    }

    protected function buildIndex()
    {
        // Do nothing yet
    }
}
