<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
class SQL__MySQL extends SQL__Global
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
        $this->option['engine'] = $GLOBALS['zbp']->option['ZC_MYSQL_ENGINE'];
    }

    /**
     * @override
     */
    public function reset()
    {
        parent::reset();
        $this->option['engine'] = $GLOBALS['zbp']->option['ZC_MYSQL_ENGINE'];

        return $this;
    }

    /**
     * @override
     */
    public function exist($table, $dbname = '')
    {
        $table = str_replace('%pre%', $this->db->dbpre, $table);
        $this->pri_sql = array("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbname' AND TABLE_NAME='$table'");

        return $this;
    }

    /**
     *
     * @override
     */
    protected function buildCreate()
    {
        global $zbp;

        if (!empty($this->index) && empty($this->data)) {
            $this->buildIndex();
            return;
        } elseif (isset($this->other) && empty($this->data)) {
            $this->buildDatabase();
            return;
        }

        $zbp->ConvertTableAndDatainfo();

        $sqlAll = array();
        foreach ($this->table as $tableIndex => $table) {
            $sql = array();
            if (isset($this->option['temporary'])) {
                $sql[] = 'CREATE TEMPORARY TABLE IF NOT EXISTS ' . $table;
            } else {
                $sql[] = 'CREATE TABLE IF NOT EXISTS ' . $table;
            }
            $sql[] = ' (';
            $engine = $this->option['engine'];
            $idname = GetValueInArrayByCurrent($this->data, 0);

            $i = 0;
            foreach ($this->data as $key => $value) {
                $comment = '';
                if (isset($value[4])) {
                    $value[4] = str_replace('\'', '', $value[4]);
                    $comment = " COMMENT '{$value[4]}'";
                }
                if ($value[1] == 'integer') {
                    if ($i == 0) {
                        $sql[] = $value[0] . ' int(11) NOT NULL AUTO_INCREMENT' . "{$comment},";
                    } else {
                        if ($value[2] == '') {
                            $sql[] = $value[0] . ' int(11) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'tinyint') {
                            $sql[] = $value[0] . ' tinyint(4) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'smallint') {
                            $sql[] = $value[0] . ' smallint(6) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'mediumint') {
                            $sql[] = $value[0] . ' mediumint(9) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'int') {
                            $sql[] = $value[0] . ' int(11) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'bigint') {
                            $sql[] = $value[0] . ' bigint(20) NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        }
                    }
                }
                if ($value[1] == 'boolean') {
                    $sql[] = $value[0] . ' tinyint(1) NOT NULL DEFAULT \'' . (int) $value[3] . '\'' . "{$comment},";
                }
                if ($value[1] == 'char') {
                    $sql[] = $value[0] . ' char(' . (int) $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                }
                if ($value[1] == 'string') {
                    if ($value[2] != '') {
                        if (strpos($value[2], 'char') !== false) {
                            $charnumber = (int) str_replace(array('char', '(', ')'), '', $value[2]);
                            $charnumber = ($charnumber == 0) ? 250 : $charnumber;
                            $sql[] = $value[0] . ' char(' . $charnumber . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif (is_int($value[2])) {
                            $sql[] = $value[0] . ' varchar(' . $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . "{$comment},";
                        } elseif ($value[2] == 'tinytext') {
                            $sql[] = $value[0] . ' tinytext NOT NULL ' . "{$comment},";
                        } elseif ($value[2] == 'text') {
                            $sql[] = $value[0] . ' text NOT NULL ' . "{$comment},";
                        } elseif ($value[2] == 'mediumtext') {
                            $sql[] = $value[0] . ' mediumtext NOT NULL ' . "{$comment},";
                        } elseif ($value[2] == 'longtext') {
                            $sql[] = $value[0] . ' longtext NOT NULL ' . "{$comment},";
                        }
                    } else {
                        $sql[] = $value[0] . ' longtext NOT NULL ' . "{$comment},";
                    }
                }
                if ($value[1] == 'double' || $value[1] == 'float') {
                    $sql[] = $value[0] . " $value[1] NOT NULL DEFAULT 0" . "{$comment},";
                }
                if ($value[1] == 'decimal') {
                    if (is_array($value[2])) {
                        $d1 = $value[2][0];
                        $d2 = $value[2][1];
                    } else {
                        $d = str_replace(array('(', ')'), '', $value[2]);
                        $d1 = SplitAndGet($d, ',', 0);
                        $d2 = SplitAndGet($d, ',', 1);
                    }
                    $sql[] = $value[0] . " $value[1]($d1,$d2) NOT NULL DEFAULT 0" . "{$comment},";
                }
                if ($value[1] == 'date' || $value[1] == 'time' || $value[1] == 'datetime') {
                    $sql[] = $value[0] . " $value[1] NOT NULL" . "{$comment},";
                }
                if ($value[1] == 'timestamp') {
                    $sql[] = $value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" . "{$comment},";
                }
                $i += 1;
            }
            $sql[] = 'PRIMARY KEY (' . $idname . ')';
            $myengtype = $this->db->dbengine;

            if (is_array($engine) && count($engine) > 0) {
                $myengtype = $engine[1];
            }
            if (!$myengtype) {
                $myengtype = $GLOBALS['zbp']->option['ZC_MYSQL_ENGINE'];
            }

            $charset = $GLOBALS['zbp']->option['ZC_MYSQL_CHARSET'];
            $collate = $GLOBALS['zbp']->option['ZC_MYSQL_COLLATE'];

            if (isset($this->option['charset']) && !empty($this->option['charset'])) {
                $charset = strtolower($this->option['charset']);
            }
            if (isset($this->option['collate']) && !empty($this->option['collate'])) {
                $collate = strtolower($this->option['collate']);
            }
            if ($charset == 'utf8mb4' && stripos($collate, 'utf8mb4_') === false) {
                $collate = str_ireplace('utf8_', 'utf8mb4_', $collate);
            }

            $sql[] = ') ENGINE=' . $myengtype . ' DEFAULT CHARSET=' . $charset . ' COLLATE=' . $collate . ' AUTO_INCREMENT=1 ;';
            $sqlAll[] = implode(' ', $sql);
        }
        $this->pri_sql = $sqlAll;
    }

    /**
     * @override
     */
    protected function buildSelect()
    {
        if (isset($this->option['sql_no_cache'])) {
            $this->sqlPush('SQL_NO_CACHE ');
        } elseif (isset($this->option['sql_cache'])) {
            $this->sqlPush('SQL_CACHE ');
        }
        if (isset($this->option['sql_buffer_result'])) {
            $this->sqlPush('SQL_BUFFER_RESULT ');
        }
        if (isset($this->option['sql_big_result'])) {
            $this->sqlPush(' SQL_BIG_RESULT ');
        }
        if (isset($this->option['sql_small_result'])) {
            $this->sqlPush('SQL_SMALL_RESULT ');
        }
        parent::buildSelect();
    }

    protected function buildRandomBefore()
    {
        $table = $this->table[0];
        if (in_array($table, $GLOBALS['table'])) {
            $key = array_search($table, $GLOBALS['table']);
            $datainfo = $GLOBALS['datainfo'][$key];
            $d = reset($datainfo);
            $id = $d[0];
            $this->where[] = "{$id} >= (SELECT FLOOR( RAND() * ((SELECT MAX({$id}) FROM `{$table}`)-(SELECT MIN({$id}) FROM `{$table}`) + 1) + (SELECT MIN({$id}) FROM `{$table}`)))";
        }
    }

    protected function buildRandom()
    {
        $sql = &$this->pri_sql;
        $table = $this->table[0];

        if (in_array($table, $GLOBALS['table'])) {
            $sql[] = ' LIMIT ' . implode('', $this->extend['RANDOM']);
        } else {
            $sql[] = 'ORDER BY RAND() LIMIT ' . implode('', $this->extend['RANDOM']);
        }
    }

}
