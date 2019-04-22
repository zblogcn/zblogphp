<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * MySQL数据库操作类.
 */
class Database__MySQL implements Database__Interface
{
    public $type = 'mysql';
    public $version = '';

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
     * @var string|null 数据库引擎
     */
    public $dbengine = null;
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
        return addslashes($s);
    }

    /**
     * 连接数据库.
     *
     * @param array $array 数据库连接配置
     *                     $array=array(
     *                     'dbmysql_server',
     *                     'dbmysql_username',
     *                     'dbmysql_password',
     *                     'dbmysql_name',
     *                     'dbmysql_pre',
     *                     'dbmysql_port',
     *                     'persistent'
     *                     'engine')
     *
     * @return bool
     */
    public function Open($array)
    {
        if ($array[6] == false) {
            $db_link = @mysql_connect($array[0] . ':' . $array[5], $array[1], $array[2]);
        } else {
            $db_link = @mysql_pconnect($array[0] . ':' . $array[5], $array[1], $array[2]);
        }

        if (!$db_link) {
            return false;
        }

        $myver = mysql_get_server_info($db_link);
        $this->version = substr($myver, 0, strpos($myver, "-"));
        if (version_compare($this->version, '5.5.3') >= 0) {
            $u = "utf8mb4";
        } else {
            $u = "utf8";
        }
        if (mysql_set_charset($u, $db_link) == false) {
            mysql_set_charset("utf8", $db_link);
        }

        $this->db = $db_link;
        if (mysql_select_db($array[3], $this->db)) {
            $this->dbpre = $array[4];
            $this->dbname = $array[3];
            $this->dbengine = $array[7];

            return true;
        } else {
            $this->Close();
        }

        return false;
    }

    /**
     * 创建数据库.
     *
     * @param string $dbmysql_server
     * @param string $dbmysql_port
     * @param string $dbmysql_username
     * @param string $dbmysql_password
     * @param string $dbmysql_name
     *
     * @return bool true:创建成功 false:失败 null:是已存在而没有执行创建
     */
    public function CreateDB($dbmysql_server, $dbmysql_port, $dbmysql_username, $dbmysql_password, $dbmysql_name)
    {
        $db_link = mysql_connect($dbmysql_server . ':' . $dbmysql_port, $dbmysql_username, $dbmysql_password);

        $myver = mysql_get_server_info($db_link);
        $myver = substr($myver, 0, strpos($myver, "-"));
        if (version_compare($myver, '5.5.3') >= 0) {
            $u = "utf8mb4";
        } else {
            $u = "utf8";
        }
        if (mysql_set_charset($u, $db_link) == false) {
            mysql_set_charset("utf8", $db_link);
        }

        $this->db = $db_link;
        $this->dbname = $dbmysql_name;
        $s = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$dbmysql_name'";
        $a = $this->Query($s);
        $c = 0;
        if (is_array($a)) {
            $b = current($a);
            if (is_array($b)) {
                $c = (int) current($b);
            }
        }
        if ($c == 0) {
            $r = mysql_query($this->sql->Filter('CREATE DATABASE ' . $dbmysql_name), $this->db);
            if ($r === false) {
                return false;
            }

            return true;
        }
    }

    /**
     * 关闭数据库连接.
     */
    public function Close()
    {
        if (is_resource($this->db)) {
            mysql_close($this->db);
            $this->db = null;
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
                mysql_query($this->sql->Filter($s), $this->db);
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
        $results = mysql_query($this->sql->Filter($query), $this->db);
        if (mysql_errno()) {
            trigger_error(mysql_error($this->db), E_USER_NOTICE);
        }

        $data = array();
        if (is_resource($results)) {
            while ($row = mysql_fetch_assoc($results)) {
                $data[] = $row;
            }
        } else {
            $data[] = $results;
        }

        //if(true==true){
        if (true !== true) {
            $query = "EXPLAIN " . $query;
            $results2 = mysql_query($this->sql->Filter($query), $this->db);
            $explain = array();
            if ($results2) {
                while ($row = mysql_fetch_assoc($results2)) {
                    $explain[] = $row;
                }
            }
            logs("\r\n" . $query . "\r\n" . var_export($explain, true));
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
        return mysql_query($this->sql->Filter($query), $this->db);
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
        return mysql_query($this->sql->Filter($query), $this->db);
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
        mysql_query($this->sql->Filter($query), $this->db);

        return mysql_insert_id($this->db);
    }

    /**
     * 新建表.
     *
     * @param string $tablename 表名
     * @param array  $datainfo  表结构
     */
    public function CreateTable($table, $datainfo, $engine = null)
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
