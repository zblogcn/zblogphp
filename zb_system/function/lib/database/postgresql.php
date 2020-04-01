<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * PgSQL数据库操作类.
 */
class Database__PostgreSQL implements Database__Interface
{
    public $type = 'postgresql';
    public $version = '';
    public $error = array();

    /**
     * @var string|null 数据库名前缀
     */
    public $dbpre = null;
    private $db = null; //数据库连接
    /**
     * @var string|null 数据库名
     */
    public $dbname = null;
    /**
     * @var DbSql|null DbSql实例
     */
    public $sql = null;

    /**
     * 构造函数，实例化$sql参数.
     */
    public function __construct()
    {
        $this->sql = new DbSql($this);
    }

    /**
     * 对字符串进行转义，在指定的字符前添加反斜杠，即执行addslashes函数.
     *
     * @use addslashes
     *
     * @param string $s
     *
     * @return string
     */
    public function EscapeString($s)
    {
        return pg_escape_string($s);
    }

    /**
     * 连接数据库.
     *
     * @param array $array 数据库连接配置
     *                     $array=array(
     *                     'pgsql_server',
     *                     'pgsql_username',
     *                     'pgsql_password',
     *                     'pgsql_name',
     *                     'pgsql_pre',
     *                     'pgsql_port',
     *                     'persistent')
     *                     )
     *
     * @return bool
     */
    public function Open($array)
    {
        $s = "host={$array[0]} port={$array[5]} dbname={$array[3]} user={$array[1]} password={$array[2]} options='--client_encoding=UTF8'";
        if (false == $array[5]) {
            $db_link = pg_connect($s);
        } else {
            $db_link = pg_pconnect($s);
        }

        if (!$db_link) {
            return false;
        } else {
            $this->dbpre = $array[4];
            $this->db = $db_link;
            $v = pg_version($db_link);
            if (isset($v['client'])) {
                $this->version = $v['client'];
            }
            if (isset($v['server'])) {
                $this->version = $v['server'];
            }

            return true;
        }
    }

    /**
     * @param string $dbpgsql_server
     * @param string $dbpgsql_port
     * @param string $dbpgsql_username
     * @param string $dbpgsql_password
     * @param string $dbpgsql_name
     */
    public function CreateDB($dbpgsql_server, $dbpgsql_port, $dbpgsql_username, $dbpgsql_password, $dbpgsql_name)
    {
        $s = "host={$dbpgsql_server} port={$dbpgsql_port} user={$dbpgsql_username} password={$dbpgsql_password} options='--client_encoding=UTF8'";
        $this->db = pg_connect($s);
        $this->dbname = $dbpgsql_name;

        $r = @pg_query($this->db, $this->sql->Filter('CREATE DATABASE ' . $dbpgsql_name));

        if (is_resource($r)) {
            $st = pg_result_status($r);
            if ($st == PGSQL_BAD_RESPONSE || $st == PGSQL_NONFATAL_ERROR || $st == PGSQL_FATAL_ERROR) {
                $this->error[] = array($st, pg_result_error($r));
            }
        } else {
            $this->error[] = array(PGSQL_BAD_RESPONSE, pg_last_error($this->db));
        }

        return true;
    }

    /**
     * 关闭数据库连接.
     */
    public function Close()
    {
        if (is_resource($this->db)) {
            pg_close($this->db);
        }
    }

    /**
     * 执行多行SQL语句.
     *
     * @param string $s 以;号分隔的多条SQL语句
     */
    public function QueryMulit($s)
    {
        return $this->QueryMulti($s);
    }

    //错别字函数，历史原因保留下来

    public function QueryMulti($s)
    {
        //$a=explode(';',str_replace('%pre%', $this->dbpre,$s));
        $a = explode(';', $s);
        foreach ($a as $s) {
            $s = trim($s);
            if ($s != '') {
                $r = pg_query($this->db, $this->sql->Filter($s));
                if (is_resource($r)) {
                    $st = pg_result_status($r);
                    if ($st == PGSQL_BAD_RESPONSE || $st == PGSQL_NONFATAL_ERROR || $st == PGSQL_FATAL_ERROR) {
                        $this->error[] = array($st, pg_result_error($r));
                    }
                } else {
                    $this->error[] = array(PGSQL_BAD_RESPONSE, pg_last_error($this->db));
                }
            }
        }
    }

    /**
     * 执行SQL查询语句.
     *
     * @param string $query
     *
     * @return array 返回数据数组
     */
    public function Query($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        $results = pg_query($this->db, $this->sql->Filter($query));

        if (is_resource($results)) {
            $st = pg_result_status($results);
            if ($st == PGSQL_BAD_RESPONSE || $st == PGSQL_NONFATAL_ERROR || $st == PGSQL_FATAL_ERROR) {
                trigger_error(pg_result_error($results), E_USER_NOTICE);
            }
        }

        $data = array();
        if (is_resource($results)) {
            while ($row = pg_fetch_assoc($results)) {
                $data[] = $row;
            }
        } else {
            $data[] = $results;
        }

        return $data;
    }

    /**
     * 更新数据.
     *
     * @param string $query SQL语句
     *
     * @return resource
     */
    public function Update($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        //logs($query);
        return pg_query($this->db, $this->sql->Filter($query));
    }

    /**
     * 删除数据.
     *
     * @param string $query SQL语句
     *
     * @return resource
     */
    public function Delete($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        return pg_query($this->db, $this->sql->Filter($query));
    }

    /**
     * 插入数据.
     *
     * @param string $query SQL语句
     *
     * @return int 返回ID序列号
     */
    public function Insert($query)
    {
        //$query=str_replace('%pre%', $this->dbpre, $query);
        pg_query($this->db, $this->sql->Filter($query));
        $seq = explode(' ', $query);
        $seq = $seq[3];
        $seq = trim($seq);
        //$seq = 'select lastval();';
        $seq = "select currval('{$seq}_seq'::regclass)";

        $r = pg_query($this->db, $seq);
        $id = pg_fetch_result($r, 0, 0);

        return (int) $id;
    }

    /**
     * 新建表.
     *
     * @param string $tablename 表名
     * @param array  $datainfo  表结构
     */
    public function CreateTable($table, $datainfo)
    {
        $this->QueryMulit($this->sql->CreateTable($table, $datainfo));
    }

    /**
     * 删除表.
     *
     * @param string $table 表名
     */
    public function DelTable($table)
    {
        $this->QueryMulit($this->sql->DelTable($table));
    }

    /**
     * 判断数据表是否存在.
     *
     * @param string $table 表名
     *
     * @return bool
     */
    public function ExistTable($table)
    {
        $a = $this->Query($this->sql->ExistTable($table, $this->dbname));
        if (!is_array($a)) {
            return false;
        }

        $b = current($a);
        if (!is_array($b)) {
            return false;
        }

        $c = (int) current($b);
        if ($c > 0) {
            return true;
        } else {
            return false;
        }
    }
}
