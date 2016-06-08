<?php
class SQLMySQL extends SQLGlobal {
	/**
	 * @param object $db
	 */
	function __construct(&$db = null) {
		parent::__construct($db);
		$this->option['engine'] = 'MyISAM';
	}
	/**
	 * @override
	 */
	function reset() {
		parent::reset();
		$this->option['engine'] = 'MyISAM';
	}

	/**
	 * @todo
	 * @override
	 */
	function exist($table, $dbname = '') {
		return "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbname' AND TABLE_NAME='$table'";
	}
	/**
	 * @todo
	 * @override
	 */
	protected function buildCreate() {

		//parent::buildCreate();
		if(!empty($this->index) && empty($this->data)){
			$this->buildIndex();
			return ;
		}


		$sqlAll = array();
		foreach ($this->table as $tableIndex => $table) {
			$sql = array();
			$sql[] = 'CREATE TABLE IF NOT EXISTS ' . $table;
			$sql[] = ' (';
			$engine = $this->option['engine'];
			$idname = GetValueInArrayByCurrent($this->data, 0);

			$i = 0;
			foreach ($this->data as $key => $value) {
				if ($value[1] == 'integer') {
					if ($i == 0) {
						$sql[] = $value[0] . ' int(11) NOT NULL AUTO_INCREMENT' . ',';
					} else {
						if ($value[2] == '') {
							$sql[] = $value[0] . ' int(11) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'tinyint') {
							$sql[] = $value[0] . ' tinyint(4) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'smallint') {
							$sql[] = $value[0] . ' smallint(6) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'mediumint') {
							$sql[] = $value[0] . ' mediumint(9) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'int') {
							$sql[] = $value[0] . ' int(11) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'bigint') {
							$sql[] = $value[0] . ' bigint(20) NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						}
					}
				}
				if ($value[1] == 'boolean') {
					$sql[] = $value[0] . ' tinyint(1) NOT NULL DEFAULT \'' . (int) $value[3] . '\'' . ',';
				}
				if ($value[1] == 'string') {
					if ($value[2] != '') {
						if (strpos($value[2], 'char') !== false) {
							$sql[] = $value[0] . ' char(' . str_replace('char', '', $value[2]) . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif (is_int($value[2])) {
							$sql[] = $value[0] . ' varchar(' . $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
						} elseif ($value[2] == 'tinytext') {
							$sql[] = $value[0] . ' tinytext NOT NULL ' . ',';
						} elseif ($value[2] == 'text') {
							$sql[] = $value[0] . ' text NOT NULL ' . ',';
						} elseif ($value[2] == 'mediumtext') {
							$sql[] = $value[0] . ' mediumtext NOT NULL ' . ',';
						} elseif ($value[2] == 'longtext') {
							$sql[] = $value[0] . ' longtext NOT NULL ' . ',';
						}
					} else {
						$sql[] = $value[0] . ' longtext NOT NULL ' . ',';
					}
				}
				if ($value[1] == 'double' || $value[1] == 'float') {
					$sql[] = $value[0] . " $value[1] NOT NULL DEFAULT 0" . ',';
				}
				if ($value[1] == 'date' || $value[1] == 'time' || $value[1] == 'datetime') {
					$sql[] = $value[0] . " $value[1] NOT NULL,";
				}
				if ($value[1] == 'timestamp') {
					$sql[] = $value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
				}
				$i += 1;
			}
			$sql[] = 'PRIMARY KEY (' . $idname . ')';
			$myengtype = $this->db->dbengine;
			if ($engine != null) {
				$myengtype = $engine;
			}

			if (!$myengtype) {
				$myengtype = 'MyISAM';
			}

			$sql[] = ') ENGINE=' . $myengtype . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
			$sqlAll[] = implode($sql, ' ');
		}
		$this->_sql = $sqlAll;
	}


	protected function buildIndex() {
		$sql = array();
		$indexname = key($this->index);
		$indexfield = $this->index[$indexname];

		$sql[] = 'CREATE INDEX ' . $indexname;	
		$sql[] = '(';

		foreach ($indexfield as $key => $value) {
		$sql[] = $value;
		$sql[] = ',';
		}
		array_pop($sql);
		$sql[] = ') ;';
		$sqlAll[] = implode($sql, ' ');
		$this->_sql = $sqlAll;
	}
}