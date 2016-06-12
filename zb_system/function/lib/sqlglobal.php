<?php
class SQLGlobal {

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
	private $methodKeyword = array('SELECT', 'INSERT', 'DROP', 'DELETE', 'CREATE', 'UPDATE');
	private $selectFunctionKeyword = array('COUNT', 'MIN', 'MAX', 'SUM');
	private $otherKeyword = array('FIELD', 'INDEX');

	/**
	 * @var null 数据库连接实例
	 */
	private $db = null;
	/**
	 * @var null|string 数据库类型名称
	 */
	private $dbclass = null;

	private function validateParamater($param) {
		if (is_null($param)) {
			return false;
		} else if (is_string($param)) {
			if ($param == "") {
				return false;
			}
		} else if (is_array($param) && count($param) == 0) {
			return false;
		}
		return true;
	}

	/**
	 * @param object $db
	 */
	function __construct(&$db = null) {
		$this->db = &$db;
		$this->dbclass = get_class($this->db);
	}

	function __call($callName, $argu) {
		$upperKeyword = strtoupper($callName);
		if (in_array($upperKeyword, $this->methodKeyword)) {
			$this->method = $upperKeyword;
			$this->table = is_array($argu[0]) ? $argu[0] : $argu;
			return $this;
		} else if (in_array($upperKeyword, $this->otherKeyword)) {
			if ($upperKeyword == 'INDEX') {
				foreach ($argu as $key => $value) {
					//is_array($argu[0]) ? $argu[0] : $argu;
					$this->index[key($value)] = current($value);
				}
			} else {
				$this->data = is_array($argu[0]) ? $argu[0] : $argu;
			}
			return $this;
		} else if (in_array($upperKeyword, $this->selectFunctionKeyword)) {
			/**
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
		} else {
			$lowerKeyword = strtolower($callName);
			if (is_callable($this, $lowerKeyword)) {
				return call_user_func_array(array($this, $lowerKeyword), $argu);
			}
		}
		throw new Exception("Unimplemented $callName");
	}
	function __get($getName) {
		$upperKeyword = strtoupper($getName);
		if ($upperKeyword == "SQL") {
			$ret = $this->sql();
			$this->reset();
			return $ret;
		}
		return $this->$getName;
	}
	function __set($name, $value) {
		if (isset($this->$name)) {
			$this->$name = $value;
		} else {
			throw 'Unknown attribute: ' . $name;
		}
	}
	/**
	 * If we use $this->$getName directly, PHP will throw [Indirect modification of overloaded property]
	 * So we have to wrap it.
	 * It maybe a bug of PHP.
	 * @see  http://stackoverflow.com/questions/10454779/php-indirect-modification-of-overloaded-property
	 */
	function _sqlPush($sql) {
		$this->_sql[] = $sql;
	}
	/**
	 * Re-initialize this class
	 * @return $this
	 */
	function reset() {
		foreach (get_class_vars(get_class($this)) as $var => $defVal) {
			if ($var == "db" || $var == "dbclass") {
				continue;
			}
			$this->$var = $defVal;
		}
		return $this;
	}
	/**
	 * Set SQL query option
	 * @return $this
	 */
	function option($option) {
		if (!$this->validateParamater($option)) {
			return $this;
		}

		$this->option = array_merge_recursive($this->option, array_change_key_case($option, CASE_LOWER));
		return $this;
	}
	/**
	 * Set column for query
	 * @return $this
	 */
	function column($columns) {
		if (!$this->validateParamater($columns)) {
			return $this;
		}

		if (is_array($columns)) {
			$this->columns = array_merge($this->columns, $columns);
		} else {
			$this->columns[] = $columns;
		}
		return $this;
	}
	/**
	 * Set limit & offset
	 * @example limit(5)
	 * @example limit(10, 1)
	 * @example limit(array(10, 1))
	 */
	function limit() {

		if (func_num_args() == 2) {
			$this->option['limit'] = func_get_arg(1);
			$this->option['offset'] = func_get_arg(0);
		} else if (func_num_args() == 1) {
			$arg = func_get_arg(0);

			if (!$this->validateParamater($arg)) {
				return $this;
			} else if (is_array($arg)) {
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
	 * Set where query
	 * @example array(array('=', 'a', 'b'), array('=', 'a', 'b'))
	 * @example array(array('=', 'a', 'b'))
	 * @example array('=', 'a', 'b'), array('=', 'a', 'b')
	 * @example array('=', 'a', 'b')
	 * @return $this
	 */
	function where() {
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
	 * Set having
	 * @return $this
	 */
	function having() {
		if (!$this->validateParamater(func_get_arg(0))) {
			return $this;
		}

		$this->having = array_merge_recursive($this->having, func_get_args());
		return $this;
	}
	/**
	 * GroupBy
	 * @return $this
	 */
	function groupBy($groupBy) {
		if (!$this->validateParamater($groupBy)) {
			return $this;
		} else if (is_array($groupBy)) {
			$this->groupBy = array_merge($this->groupBy, $groupBy);
		} else {
			$this->groupBy[] = $groupBy;
		}
		return $this;
	}
	/**
	 * Order by
	 * @example orderBy(array(array('bbb' => 'desc'), 'aaa'))
	 * @example orderBy(array('bbb' => 'desc'), 'aaa')
	 * @example orderBy('aaaa')
	 * @example orderBy(array('a', 'b', 'c'))
	 * @return  $this
	 */
	function orderBy() {
		$order = func_get_args();

		if (count($order) == 1) {
			if (isset($order[0]) && is_array($order[0])) {
				$order = $order[0];
			} else if (!$this->validateParamater($order[0])) {
				return $this;
			}
		}
		if (count($order) == 1 && !isset($order[0])) {
			$order = array($order);
		}

		foreach ($order as $key => $value) {
			$ret = $value;
			if (!is_array($ret)) {
				$ret = array($value => '');
			}
			$this->orderBy = array_merge_recursive($this->orderBy, $ret);
		}
		return $this;
	}
	/**
	 * Set data for INSERT & UPDATE
	 * @example array('key' => 'value', 'key2' => 'value2')
	 * @return $this
	 */
	function data() {
		$data = func_num_args() == 1 ? func_get_arg(0) : func_get_args();
		if (!$this->validateParamater($data)) {
			return $this;
		}
		$this->data = array_merge_recursive($this->data, $data);

		return $this;
	}
	/**
	 * @todo
	 */
	function exist($table) {
		return $this->sql();
	}
	function query($sql = null) {
		if (is_null($sql)) {
			$sql = $this->sql();
		}

	}
	private function sql() {
		$sql = &$this->_sql;
		$sql = array("$this->method");
		$callableMethod = 'build' . ucfirst($this->method);
		//echo get_class($this) . '<br/>';
		$this->$callableMethod();
		return implode(' ', $sql);
	}

	protected function buildTable() {
		$sql = &$this->_sql;
		$table = &$this->table;
		$tableData = array();

		//array_walk
		foreach ($table as $index => $tableValue) {
			if (is_string($tableValue)) {
				$tableData[] = " `$tableValue` ";
			}
			if (is_array($tableValue)) {
				$tableData[] = " `$tableValue[0]` $tableValue[1] ";
			}
		}
		$sql[] = implode($tableData, ", ");
	}
	protected function buildColumn() {
		$sql = &$this->_sql;
		$columns = &$this->columns;
		if (count($columns) > 0) {
			$selectStr = implode($columns, ',');
			if (trim($selectStr) == '') {
				$selectStr = '*';
			}
			$sql[] = " {$selectStr} ";
		} else {
			$sql[] = "*";
		}
	}
	protected function buildWhere($originalWhere = null, $whereKeyword = null) {
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

			$eq = strtoupper($value[0]);
			if (in_array($eq, array('=', '<>', '>', '<', '>=', '<=', 'NOT LIKE', 'LIKE', 'ILIKE', 'NOT ILIKE'))) {
				$x = (string) $value[1];
				$y = $this->db->EscapeString((string) $value[2]);
				$whereData[] = " `$x` $eq '$y' ";
			} else if ($eq == 'EXISTS' || $eq == 'NOT EXISTS') {
				if (!isset($value[2])) {
					$whereData[] = " $eq ( $value[1] ) ";
				} else {
					$whereData[] = " (( `$value[1]` $eq $value[2] )) ";
				}
			} else if ($eq == 'BETWEEN') {
				$whereData[] = " (`$value[1]` BETWEEN '$value[2]' AND '$value[3]') ";
			} else if ($eq == 'SEARCH') {
				$searchCount = count($value);
				$sqlSearch = array();
				for ($i = 1; $i <= $searchCount - 1 - 1; $i++) {
					$x = (string) $value[$i];
					$y = $this->db->EscapeString((string) $value[$searchCount - 1]);
					$sqlSearch[] = " (`$x` LIKE '%$y%') ";
				}
				$whereData[] = " ((1 = 1) AND (" . implode(' OR ', $sqlSearch) . ') )';
			} else if ($eq == 'ARRAY' || $eq == 'NOT ARRAY' || $eq == "LIKE ARRAY") {
				switch ($eq) {
				case 'ARRAY':$symbol = '=';
					break;
				case 'NOT ARRAY':$symbol = '<>';
					break;
				case 'LIKE ARRAY':$symbol = 'LIKE';
					break;
				default:throw new Exception("Unknown $eq");
				}
				$symbol = $eq == 'ARRAY' ? '=' : '<>';
				$sqlArray = array();
				if (!is_array($value[1])) {
					continue;
				}
				if (count($value[1]) == 0) {
					continue;
				}
				foreach ($value[1] as $x => $y) {
					$y[1] = $this->db->EscapeString($y[1]);
					$sqlArray[] = " `$y[0]` $symbol '$y[1]' ";
				}
				$whereData[] = " ((1 = 1) AND (" . implode(' OR ', $sqlArray) . ') )';
			} else if ($eq == 'IN' || $eq == 'NOT IN') {

				$sqlArray = array();
				if (!is_array($value[2])) {
					$sqlArray[] = $value[2];
					continue;
				}
				if (count($value[2]) == 0) {
					continue;
				}

				foreach ($value[2] as $x => $y) {
					$y = $this->db->EscapeString($y);
					$sqlArray[] = " '$y' ";
				}
				$whereData[] = " ((1 = 1) AND (`$value[1]` $eq (" . implode(', ', $sqlArray) . ') ) )';

			} else if ($eq == 'META_NAME') {
				if (count($value) != 3) {
					continue;
				}
				$sqlMeta = 's:' . strlen($value[2]) . ':"' . $value[2] . '";';
				$sqlMeta = $this->db->EscapeString($sqlMeta);
				$whereData[] = "(`$value[1]` LIKE '%$sqlMeta%')";
			} else if ($eq == 'META_NAMEVALUE') {
				if (count($value) == 4) {
					$sqlMeta = 's:' . strlen($value[2]) . ':"' . $value[2] . '";' . 's:' . strlen($value[3]) . ':"' . $value[3] . '"';
					$sqlMeta = $this->db->EscapeString($sqlMeta);
					$whereData[] = "(`$value[1]` LIKE '%$sqlMeta%')";
				} elseif (count($value) == 5) {
					$sqlMeta = 's:' . strlen($value[2]) . ':"' . $value[2] . '";' . $value[3];
					$sqlMeta = $this->db->EscapeString($sqlMeta);
					$whereData[] = "(`$value[1]` LIKE '%$sqlMeta%')";
				}
			} else if ($eq == "CUSTOM") {
				$whereData[] = $value[1];
			}
		}
		$sql[] = implode(' AND ', $whereData);
	}
	protected function buildOrderBy() {
		$sql = &$this->_sql;
		if (count($this->orderBy) == 0) {
			return;
		}

		$sql[] = "ORDER BY";
		$orderByData = array();

		foreach ($this->orderBy as $key => $value) {
			$orderByData[] = "$key $value";
		}
		$sql[] = implode(', ', $orderByData);
	}
	/**
	 * @todo
	 */
	protected function buildJoin() {
		$sql = &$this->_sql;
	}
	protected function buildGroupBy() {
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
	protected function buildHaving() {
		$sql = &$this->_sql;
		if (count($this->having) == 0) {
			return;
		}

		$sql[] = "HAVING";
		$this->buildWhere($this->having, ' ');
	}
	protected function buildLimit() {
		$sql = &$this->_sql;

		if (isset($this->option['limit'])) {
			$sql[] = "LIMIT " . $this->option['limit'];
		}
		if (isset($this->option['offset'])) {
			$sql[] = "OFFSET " . $this->option['offset'];
		}
	}
	/**
	 * @todo
	 **/
	protected function buildPagebar() {

	}

	protected function buildSelect() {
		$sql = &$this->_sql;
		if (isset($this->option['sql_no_cache'])) {
			$sql[] = 'SQL_NO_CACHE ';
		}
		if (isset($this->option['sql_cache'])) {
			$sql[] = 'SQL_CACHE ';
		}
		if (isset($this->option['sql_buffer_result'])) {
			$sql[] = 'SQL_BUFFER_RESULT ';
		}
		// Unimplemented select2count
		$this->buildColumn();
		$sql[] = 'FROM';
		$this->buildTable();
		if (isset($this->option['useindex'])) {
			if (is_array($this->option['useindex'])) {
				$sql[] = 'USE INDEX (' . implode($this->option['useindex'], ',') . ') ';
			} else {
				$sql[] = 'USE INDEX (' . $this->option['useindex'] . ') ';
			}
		}
		if (isset($this->option['forceindex'])) {
			if (is_array($this->option['forceindex'])) {
				$sql[] = 'FORCE INDEX (' . implode($this->option['forceindex'], ',') . ') ';
			} else {
				$sql[] = 'FORCE INDEX (' . $this->option['forceindex'] . ') ';
			}
		}
		if (isset($this->option['ignoreindex'])) {
			if (is_array($this->option['ignoreindex'])) {
				$sql[] = 'IGNORE INDEX (' . implode($this->option['ignoreindex'], ',') . ') ';
			} else {
				$sql[] = 'IGNORE INDEX (' . $this->option['ignoreindex'] . ') ';
			}
		}
		$this->buildWhere();
		$this->buildGroupBy();
		$this->buildHaving();
		$this->buildOrderBy();
		$this->buildLimit();
	}
	protected function buildUpdate() {
		$sql = &$this->_sql;
		$sql[] = $this->buildTable();
		$sql[] = 'SET';
		$updateData = array();
		foreach ($this->data as $index => $value) {
			if (is_null($value)) {
				continue;
			}
			$escapedValue = $this->db->EscapeString($value);
			$updateData[] = "$index = '$value'";
		}
		$sql[] = implode(', ', $updateData);
		$this->buildWhere();
		return $sql;
	}
	protected function buildDelete() {
		$sql = &$this->_sql;
		$sql[] = 'FROM';
		$this->buildTable();
		$this->buildWhere();
	}
	protected function buildInsert() {
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
			$keyData[] = "`$key`";
			$valueData[] = " '$v' ";
		}
		$sql[] = '(' . implode($keyData, ',') . ')';
		$sql[] = ' VALUES (';
		$sql[] = implode($valueData, ',');
		$sql[] = ')';
	}

	protected function buildDrop() {
		$sql = &$this->_sql;
		$sql[] = 'TABLE';
		$this->buildTable();
	}

	/**
	 * @todo
	 */
	protected function buildCreate() {

	}

	protected function buildIndex() {

	}
}