<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
class SQL__SQLite extends SQL__Global
{
    /**
     * @override
     */
    public $className = __CLASS__;

    /**
     * @param object $db
     */
    public function __construct(&$db = null)
    {
        parent::__construct($db);
    }

    /**
     * @todo
     * @override
     */
    public function exist($table, $dbname = '')
    {
        $this->_sql = array("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table'");

        return $this;
    }

    /**
     * @todo
     * @override
     */
    protected function buildCreate()
    {
        $sqlAll = array();
        foreach ($this->table as $tableIndex => $table) {
            $sql = array();

            $sql[] = 'CREATE TABLE ' . $table;
            $sql[] = ' (';
            $createData = array();

            $idname = GetValueInArrayByCurrent($this->data, 0);

            $i = 0;
            foreach ($this->data as $key => $value) {
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
            $sql[] = 'CREATE UNIQUE INDEX ' . $table . '_' . $idname;
            $sql[] = ' on ' . $table . ' (' . $idname . ');';
            $sqlAll[] = implode($sql, ' ');
        }
        $this->_sql = $sqlAll;
    }
}
