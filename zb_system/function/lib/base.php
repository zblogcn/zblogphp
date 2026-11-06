<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 数据操作基类.
 *
 * @property mixed ID
 * @property mixed Type
 * @property mixed Name
 * @property mixed FileName
 */
class Base
{
    /**
     * @var null|Metas 扩展元数据
     */
    public $Metas;

    /**
     * @var string 数据表
     */
    protected $table;

    /**
     * @var array 表结构信息
     */
    protected $datainfo;

    /**
     * @var array 数据
     */
    protected $data = [];

    /**
     * @var array 原始数据
     */
    protected $original = [];

    /**
     * @var Database__Interface db
     */
    protected $db;

    /**
     * @var string 类名
     */
    protected $classname = '';

    /**
     * @var string ID名
     */
    protected $idname = '';

    /**
     * @var bool 是否自动替换host
     */
    protected $isreplacehost = true;

    /**
     * @param string $table     数据表
     * @param array  $datainfo  数据表结构信息
     * @param string $classname 已经无用但还是保留
     * @param bool   $hasmetas
     * @param null   $db
     */
    public function __construct(&$table, &$datainfo, $classname = '', $hasmetas = true, &$db = null)
    {
        if (null !== $db && is_object($db)) {
            $this->db = &$db;
        } else {
            $this->db = &$GLOBALS['zbp']->db;
        }

        $this->table = &$table;
        $this->datainfo = &$datainfo;
        reset($this->datainfo);
        $this->idname = key($this->datainfo);

        $this->classname = get_class($this);

        if (true == $hasmetas) {
            $this->Metas = new Metas();
        }

        foreach ($this->datainfo as $key => $value) {
            $this->data[$key] = $value[3];
        }
        $this->original = $this->data;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * 将数据用JSON格式输出.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) json_encode($this->data);
    }

    /**
     *  __clone().
     *
     * @return object
     */
    public function __clone()
    {
        $this->LoadInfoByDataArray($this->data);
    }

    /**
     * DebugInfo >= php 5.6.
     */
    public function __debugInfo()
    {
        $array = [];
        foreach ($this as $key => $value) {
            if ('datainfo' == $key || 'db' == $key) {
                continue;
            }
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * 获取数据库数据(不设$key就返回整个data数组).
     *
     * @param null|mixed $key
     *
     * @return array
     */
    public function GetData($key = null)
    {
        if (null == $key) {
            return $this->data;
        }

        return $this->data[$key];
    }

    /**
     * 获取原始数据(不设$key就返回整个original数组).
     *
     * @param null|mixed $key
     *
     * @return array
     */
    public function GetOriginal($key = null)
    {
        if (null == $key) {
            return $this->original;
        }

        return $this->original[$key];
    }

    /**
     * 设置Data数据.
     *
     * @param array|string $key   如果是array，就忽略$value
     * @param null|mixed   $value
     *
     * @return bool
     */
    public function SetData($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $key2 => $value2) {
                $this->data[$key2] = $value2;
            }

            return true;
        }
        if (null !== $value) {
            $this->data[$key] = $value;

            return true;
        }

        return false;
    }

    /**
     * 删除data的键，谨用.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function UnsetData($key)
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);

            return true;
        }

        return false;
    }

    /**
     * 放弃修改.
     *
     * @param null|string $key
     *
     * @return bool
     */
    public function RevertChanges($key = null)
    {
        if (null == $key) {
            $this->data = $this->original;

            return true;
        }

        if (array_key_exists($key, $this->data) && array_key_exists($key, $this->original)) {
            $this->data[$key] = $this->original[$key];

            return true;
        }

        return false;
    }

    /**
     * 获取数据表.
     *
     * @return string
     */
    public function &GetTable()
    {
        return $this->table;
    }

    /**
     * 获取表结构.
     *
     * @return array
     */
    public function &GetDataInfo()
    {
        return $this->datainfo;
    }

    /**
     * 获取Database__Interface.
     *
     * @return Database__Interface
     */
    public function &GetDb()
    {
        return $this->db;
    }

    /**
     * 获取数据库内指定ID的数据.
     *
     * @param int $id 指定ID
     *
     * @return bool
     */
    public function LoadInfoByID($id)
    {
        $id = (int) $id;
        $id_name = $this->idname;
        $id_field = $this->datainfo[$id_name][0];

        $s = $this->db->sql->Select($this->table, ['*'], [['=', $id_field, $id]], null, null, null);

        $array = $this->db->Query($s);
        if (count($array) > 0) {
            $this->LoadInfoByAssoc($array[0]);

            return true;
        }

        return false;
    }

    /**
     * 根据数组从数据库内查找数据并返回.
     *
     * @param array $array 待查找数组
     *
     * @return bool
     */
    public function LoadInfoByAssoc($array)
    {
        if (!is_array($array)) {
            return false;
        }

        foreach ($this->datainfo as $key => $value) {
            if (!array_key_exists($value[0], $array)) {
                continue;
            }

            $v = $array[$value[0]];
            if ('string' == $value[1] || 'char' == $value[1]) {
                if ('Meta' != $key) {
                    $this->data[$key] = ($this->isreplacehost) ? $this->ReplaceTag2Host($v) : $v;
                } else {
                    $this->data[$key] = $v;
                    $this->Metas->Unserialize($this->data['Meta']);
                }
            } elseif ('boolean' != $value[1]) {
                $this->data[$key] = $v;
            } else {
                $this->data[$key] = (bool) $v;
            }
        }
        //foreach ($GLOBALS['hooks']['Filter_Plugin_Base_Data_Load'] as $fpname => &$fpsignal) {
        //    $fpname($this, $this->data);
        //}

        $this->original = $this->data;

        return true;
    }

    /**
     * 根据特定的字段和值搜索数据.
     *
     * @param string $field       字段(限string,int,bool)
     * @param string $field_value 数据值
     *
     * @return bool
     */
    public function LoadInfoByField($field, $field_value)
    {
        return $this->LoadInfoByFields(
            [$field => $field_value],
        );
    }

    /**
     * 根据多个特定的字段和值搜索数据.
     *
     * @param array $fields 多个字段数组(如 ['AuthorID' => '1', 'CateID' => '1', 'Meta' => ['area' => '北京','keywords' => 'network']])
     *
     * @return bool
     */
    public function LoadInfoByFields($fields)
    {
        global $table, $datainfo;
        $field_table = array_flip($table);
        $field_table = $field_table[$this->table];
        $conditions = [];
        foreach ($fields as $field_key => $field_value) {
            if (0 === strcasecmp($field_key, 'meta') && isset($this->datainfo['Meta'])) {
                foreach ($field_value as $k => $v) {
                    if (is_numeric($k)) {
                        $conditions[] = ['META_NAME', $this->datainfo['Meta'][0], $v];
                    } else {
                        $conditions[] = ['META_NAMEVALUE', $this->datainfo['Meta'][0], $k, $v];
                    }
                }
            } else {
                $field_name = $datainfo[$field_table][$field_key][0];
                $conditions[] = ['=', $field_name, $field_value];
            }
        }
        $sql = $this->db->sql->Select($this->table, ['*'], $conditions, null, 1, null);
        $array = $this->db->Query($sql);
        if (count($array) > 0) {
            $this->LoadInfoByAssoc($array[0]);

            return true;
        }

        return false;
    }

    /**
     * 从数组(整数索引key)中加载数据.
     *
     * @param $array
     *
     * @return bool
     */
    public function LoadInfoByArray($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $i = 0;
        foreach ($this->datainfo as $key => $value) {
            if (count($array) == $i) {
                continue;
            }

            $v = $array[$i];
            if ('string' == $value[1] || 'char' == $value[1]) {
                if ('Meta' != $key) {
                    $this->data[$key] = ($this->isreplacehost) ? $this->ReplaceTag2Host($v) : $v;
                } else {
                    $this->data[$key] = $v;
                    $this->Metas->Unserialize($this->data['Meta']);
                }
            } elseif ('boolean' != $value[1]) {
                $this->data[$key] = $v;
            } else {
                $this->data[$key] = (bool) $v;
            }
            ++$i;
        }
        //foreach ($GLOBALS['hooks']['Filter_Plugin_Base_Data_Load'] as $fpname => &$fpsignal) {
        //    $fpname($this, $this->data);
        //}

        $this->original = $this->data;

        return true;
    }

    /**
     * 从Data数组中加载数据.
     *
     * @param $array
     *
     * @return bool
     */
    public function LoadInfoByDataArray($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $array = array_change_key_case($array, CASE_LOWER);
        foreach ($this->datainfo as $key => $value) {
            if (!array_key_exists(strtolower($key), $array)) {
                continue;
            }

            $v = $array[strtolower($key)];
            if ('string' == $value[1] || 'char' == $value[1]) {
                if ('Meta' != $key) {
                    $this->data[$key] = ($this->isreplacehost) ? $this->ReplaceTag2Host($v) : $v;
                } else {
                    $this->data[$key] = $v;
                    $this->Metas->Unserialize($this->data['Meta']);
                }
            } elseif ('boolean' != $value[1]) {
                $this->data[$key] = $v;
            } else {
                $this->data[$key] = (bool) $v;
            }
        }
        //foreach ($GLOBALS['hooks']['Filter_Plugin_Base_Data_Load'] as $fpname => &$fpsignal) {
        //    $fpname($this, $this->data);
        //}

        $this->original = $this->data;

        return true;
    }

    /**
     * 保存数据.
     *
     * @return bool
     */
    public function Save()
    {
        global $bloghost;
        if (array_key_exists('Meta', $this->data)) {
            $this->data['Meta'] = $this->Metas->Serialize();
        }

        $keys = [];
        foreach ($this->datainfo as $key => $value) {
            if (!is_array($value) || count($value) < 4) {
                continue;
            }

            $keys[] = $value[0];
        }
        $keyvalue = array_fill_keys($keys, '');
        $keyvalue_orig = [];

        foreach ($this->datainfo as $key => $value) {
            if (!is_array($value) || count($value) < 4) {
                continue;
            }
            if (!array_key_exists($key, $this->data)) {
                //如果unset(某个$key)就不再插入或修改该数据
                unset($keyvalue[$value[0]]);

                continue;
            }

            if ('boolean' == $value[1]) {
                $keyvalue[$value[0]] = (int) $this->data[$key];
                $keyvalue_orig[$value[0]] = (int) $this->original[$key];
            } elseif ('integer' == $value[1]) {
                $keyvalue[$value[0]] = (int) $this->data[$key];
                $keyvalue_orig[$value[0]] = (int) $this->original[$key];
            } elseif ('float' == $value[1]) {
                $keyvalue[$value[0]] = (float) $this->data[$key];
                $keyvalue_orig[$value[0]] = (float) $this->original[$key];
            } elseif ('double' == $value[1]) {
                $keyvalue[$value[0]] = (float) $this->data[$key];
                $keyvalue_orig[$value[0]] = (float) $this->original[$key];
            } elseif ('string' == $value[1] || 'char' == $value[1]) {
                if ('Meta' == $key || '/' == $bloghost) {
                    $keyvalue[$value[0]] = $this->data[$key];
                    $keyvalue_orig[$value[0]] = $this->original[$key];
                } else {
                    $keyvalue[$value[0]] = ($this->isreplacehost) ? $this->ReplaceHost2Tag($this->data[$key]) : $this->data[$key];
                    $keyvalue_orig[$value[0]] = ($this->isreplacehost) ? $this->ReplaceHost2Tag($this->original[$key]) : $this->original[$key];
                }
            } else {
                $keyvalue[$value[0]] = $this->data[$key];
                $keyvalue_orig[$value[0]] = $this->original[$key];
            }
        }
        array_shift($keyvalue);
        array_shift($keyvalue_orig);

        $id_name = $this->idname;
        $id_field = $this->datainfo[$id_name][0];

        if (empty($this->{$id_name})) {
            if (0 == count($keyvalue)) {
                return true;
            }
            $sql = $this->db->sql->Insert($this->table, $keyvalue);
            $this->{$id_name} = $this->db->Insert($sql);
        } else {
            foreach ($keyvalue as $key => $value) {
                if (array_key_exists($key, $keyvalue_orig)) {
                    if ($value === $keyvalue_orig[$key]) {
                        unset($keyvalue[$key]);
                    }
                }
            }
            if (0 == count($keyvalue)) {
                return true;
            }
            $sql = $this->db->sql->Update($this->table, $keyvalue, [['=', $id_field, $this->{$id_name}]]);
            $r = $this->db->Update($sql);

            $this->original = $this->data;

            return $r;
        }

        $this->original = $this->data;

        return true;
    }

    /**
     * 删除数据.
     *
     * @return bool
     */
    public function Del()
    {
        $id_name = $this->idname;
        $id_field = $this->datainfo[$id_name][0];
        $sql = $this->db->sql->Delete($this->table, [['=', $id_field, $this->{$id_name}]]);
        $this->db->Delete($sql);
        if ('Base' !== $this->classname) {
            $GLOBALS['zbp']->RemoveCache($this);
        }

        return true;
    }

    /**
     * Clone对象.
     *
     * @param mixed      $with_original
     * @param null|mixed $classname
     *
     * @return object
     */
    public function Cloned($with_original = false, $classname = null)
    {
        if (empty($classname)) {
            $classname = $this->classname;
        }
        if ('Base' == $classname) {
            $new = new $classname($this->table, $this->datainfo);
        } else {
            $new = new $classname();
        }

        if (false == $with_original) {
            $new->LoadInfoByDataArray($this->data);
        } else {
            $new->LoadInfoByDataArray($this->original);
        }

        return $new;
    }

    /**
     * Get ID Name.
     *
     * @return int|string
     */
    public function GetIdName()
    {
        return $this->idname;
    }

    public function ReplaceTag2Host($s)
    {
        global $bloghost;

        return str_replace('{#ZC_BLOG_HOST#}', $bloghost, (string) $s);
    }

    public function ReplaceHost2Tag($s)
    {
        global $bloghost;

        return str_replace($bloghost, '{#ZC_BLOG_HOST#}', (string) $s);
    }
}
