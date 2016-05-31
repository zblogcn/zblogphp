<?php
class SQLSQLite extends SQLGlobal {
	/**
	 * @param object $db
	 */
	function __construct(&$db = null) {
		parent::__construct($db);
	}
	/**
	 * @todo
	 * @override
	 */
	function exist($table, $dbname = '') {
		return "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table'";
	}
	/**
	 * @todo
	 * @override
	 */
	private function buildCreate() {
		$sql = &$this->_sql;
		if (is_string($this->table)) {
			$table = $this->table;
		} else {
			$table = $this->table[0];
		}
		$sql[] = 'TABLE ' . $table;
		$sql[] = ' (';
		$createData = array();

		$idname = GetValueInArrayByCurrent($this->data, 0);

		$i = 0;
		foreach ($datainfo as $key => $value) {
			if ($value[1] == 'integer') {
				if ($i == 0) {
					$createData[] = $value[0] . ' integer primary key' . ($this->dbclass == 'DbSQLite' ? '' : ' autoincrement');
				} else {
					$createData[] = $value[0] . ' integer NOT NULL DEFAULT \'' . $value[3] . '\'';
				}
			}
			if ($value[1] == 'boolean') {
				$createData[] = $value[0] . ' bit NOT NULL DEFAULT \'' . (int) $value[3] . '\'';
			}
			if ($value[1] == 'string') {
				if ($value[2] != '') {
					if (strpos($value[2], 'char') !== false) {
						$createData[] = $value[0] . ' char(' . str_replace('char', '', $value[2]) . ') NOT NULL DEFAULT \'' . $value[3] . '\'';
					} elseif (is_int($value[2])) {
						$createData[] = $value[0] . ' varchar(' . $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'';
					} else {
						$createData[] = $value[0] . ' text NOT NULL DEFAULT \'\'';
					}
				} else {
					$createData[] = $value[0] . ' text NOT NULL DEFAULT \'\'';
				}
			}
			if ($value[1] == 'double' || $value[1] == 'float') {
				$createData[] = $value[0] . " $value[1] NOT NULL DEFAULT 0";
			}
			if ($value[1] == 'date' || $value[1] == 'datetime') {
				$createData[] = $value[0] . " $value[1] NOT NULL";
			}
			if ($value[1] == 'timestamp') {
				$createData[] = $value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP";
			}
			$i += 1;
		}
		$sql[] = implode(', ', $createData);
		$sql[] = ');';
		$sql[] = 'CREATE UNIQUE INDEX ' . $table;
		$sql[] = '_' . $idname . ' on ' . $table . ' (' . $idname . ');';

	}

}