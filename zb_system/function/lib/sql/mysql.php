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
     * @todo
     * @override
     */
    public function exist($table, $dbname = '')
    {
        $this->_sql = array("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbname' AND TABLE_NAME='$table'");

        return $this;
    }

    /**
     * @todo
     * @override
     */
    protected function buildCreate()
    {

        //parent::buildCreate();
        if (!empty($this->index) && empty($this->data)) {
            $this->buildIndex();

            return;
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

            if (is_array($engine) && count($engine) > 0) {
                $myengtype = $engine[1];
            }

            if (!$myengtype) {
                $myengtype = $GLOBALS['zbp']->option['ZC_MYSQL_ENGINE'];
            }

            $sql[] = ') ENGINE=' . $myengtype . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
            $sqlAll[] = implode($sql, ' ');
        }
        $this->_sql = $sqlAll;
    }

    protected function buildIndex()
    {
        $sql = array();
        //var_dump($this->index);
        foreach ($this->index as $indexkey => $indexvalue) {
            $indexname = $indexkey;
            $indexfield = $indexvalue;

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
            $sqlAll = array();
        }
    }

    /**
     * @override
     */
    protected function buildBeforeWhere()
    {
        if (isset($this->option['useindex'])) {
            if (is_array($this->option['useindex'])) {
                $this->_sqlPush('USE INDEX (' . implode($this->option['useindex'], ',') . ') ');
            } else {
                $this->_sqlPush('USE INDEX (' . $this->option['useindex'] . ') ');
            }
        }
        if (isset($this->option['forceindex'])) {
            if (is_array($this->option['forceindex'])) {
                $this->_sqlPush('FORCE INDEX (' . implode($this->option['forceindex'], ',') . ') ');
            } else {
                $this->_sqlPush('FORCE INDEX (' . $this->option['forceindex'] . ') ');
            }
        }
        if (isset($this->option['ignoreindex'])) {
            if (is_array($this->option['ignoreindex'])) {
                $this->_sqlPush('IGNORE INDEX (' . implode($this->option['ignoreindex'], ',') . ') ');
            } else {
                $this->_sqlPush('IGNORE INDEX (' . $this->option['ignoreindex'] . ') ');
            }
        }
    }

    /**
     * @override
     */
    protected function buildSelect()
    {
        if (isset($this->option['sql_no_cache'])) {
            $this->_sqlPush('SQL_NO_CACHE ');
        }
        if (isset($this->option['sql_cache'])) {
            $this->_sqlPush('SQL_CACHE ');
        }
        if (isset($this->option['sql_buffer_result'])) {
            $this->_sqlPush('SQL_BUFFER_RESULT ');
        }
        parent::buildSelect();
    }
}
