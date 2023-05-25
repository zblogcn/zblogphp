<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
class SQL__PostgreSQL extends SQL__Global
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
     * @override
     */
    public function exist($table, $dbname = '')
    {
        $table = str_replace('%pre%', $this->db->dbpre, $table);
        $this->pri_sql = array("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND  table_name ='$table'");

        return $this;
    }

    /**
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
            $sql[] = 'CREATE SEQUENCE ' . $table . '_seq;';
            $sql[] = 'CREATE TABLE ' . $table . ' (';
            $idname = GetValueInArrayByCurrent($this->data, 0);

            $i = 0;
            foreach ($this->data as $key => $value) {
                if ($value[1] == 'integer') {
                    if ($i == 0) {
                        $sql[] = $value[0] . ' INT NOT NULL DEFAULT nextval(\'' . $table . '_seq\')' . ',';
                    } else {
                        if ($value[2] == '') {
                            $sql[] = $value[0] . ' integer NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif ($value[2] == 'tinyint') {
                            $sql[] = $value[0] . ' integer NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif ($value[2] == 'smallint') {
                            $sql[] = $value[0] . ' smallint NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif ($value[2] == 'mediumint') {
                            $sql[] = $value[0] . ' integer NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif ($value[2] == 'int') {
                            $sql[] = $value[0] . ' integer NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif ($value[2] == 'bigint') {
                            $sql[] = $value[0] . ' bigint NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        }
                    }
                }
                if ($value[1] == 'boolean') {
                    $sql[] = $value[0] . ' char(1) NOT NULL DEFAULT \'' . (int) $value[3] . '\'' . ',';
                }
                if ($value[1] == 'char') {
                    $sql[] = $value[0] . ' char(' . (int) $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                }
                if ($value[1] == 'string') {
                    if ($value[2] != '') {
                        if (strpos($value[2], 'char') !== false) {
                            $charnumber = (int) str_replace(array('char', '(', ')'), '', $value[2]);
                            $charnumber = ($charnumber == 0) ? 250 : $charnumber;
                            $sql[] = $value[0] . ' char(' . $charnumber . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } elseif (is_int($value[2])) {
                            $sql[] = $value[0] . ' varchar(' . $value[2] . ') NOT NULL DEFAULT \'' . $value[3] . '\'' . ',';
                        } else {
                            $sql[] = $value[0] . ' text NOT NULL DEFAULT \'\',';
                        }
                    } else {
                        $sql[] = $value[0] . ' text NOT NULL DEFAULT \'\',';
                    }
                }
                if ($value[1] == 'double') {
                    $sql[] = $value[0] . " double precision NOT NULL DEFAULT 0" . ',';
                }
                if ($value[1] == 'float') {
                    $sql[] = $value[0] . " real NOT NULL DEFAULT 0" . ',';
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
                    $sql[] = $value[0] . " $value[1]($d1,$d2) NOT NULL DEFAULT 0" . ',';
                }
                if ($value[1] == 'date' || $value[1] == 'time') {
                    if ($value[3] === null) {
                        $sql[] = $value[0] . " $value[1] NULL,";
                    } else {
                        $sql[] = $value[0] . " $value[1] NOT NULL,";
                    }
                }
                if ($value[1] == 'datetime') {
                    $sql[] = $value[0] . " time NOT NULL,";
                }
                if ($value[1] == 'timestamp') {
                    $sql[] = $value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP,";
                }
                $i += 1;
            }
            $sql[] = 'PRIMARY KEY (' . $idname . ')';

            $sql[] = ');';
            $sql[] = 'CREATE INDEX ' . $table . '_ix_id on ' . $table . '(' . $idname . ');';
            $sqlAll[] = implode(' ', $sql);
        }
        $this->pri_sql = $sqlAll;
    }

    protected function buildRandomBefore()
    {
        $table = $this->table[0];
        if (in_array($table, $GLOBALS['table'])) {
            $key = array_search($table, $GLOBALS['table']);
            $datainfo = $GLOBALS['datainfo'][$key];
            $d = reset($datainfo);
            $id = $d[0];
            $i = 0;
            //$this->where[] = "{$id} >= (SELECT FLOOR( RANDOM() * ((SELECT MAX({$id}) FROM {$table})-(SELECT MIN({$id}) FROM {$table})) + (SELECT MIN({$id}) FROM {$table})))";
        }
    }

    protected function buildRandom()
    {
        $sql = &$this->pri_sql;
        $table = $this->table[0];
        if (in_array($table, $GLOBALS['table'])) {
            $sql[] = 'ORDER BY RANDOM() LIMIT ' . implode('', $this->extend['RANDOM']);
            //$sql[] = ' LIMIT ' . implode('', $this->extend['RANDOM']);
        } else {
            $sql[] = 'ORDER BY RANDOM() LIMIT ' . implode('', $this->extend['RANDOM']);
        }
    }

}
