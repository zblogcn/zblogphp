<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 数据操作基类.
 *
 * @property mixed ID
 */
class Base
{
    /**
     * @var string 数据表
     */
    protected $table = '';
    /**
     * @var array 表结构信息
     */
    protected $datainfo = array();
    /**
     * @var array 数据
     */
    protected $data = array();

    /**
     * @var Metas|null 扩展元数据
     */
    public $Metas = null;
    /**
     * @var Database__Interface db
     */
    protected $db = null;
    /**
     * @var string 类名
     */
    protected $classname = '';

    /**
     * @param string $table     数据表
     * @param array  $datainfo  数据表结构信息
     * @param string $classname
     * @param bool   $hasmetas
     * @param null   $db
     */
    public function __construct(&$table, &$datainfo, $classname = '', $hasmetas = true, &$db = null)
    {
        if ($db !== null && is_object($db)) {
            $this->db = &$db;
        } else {
            $this->db = &$GLOBALS['zbp']->db;
        }

        $this->table = &$table;
        $this->datainfo = &$datainfo;

        if (function_exists('get_called_class')) {
            $this->classname = get_called_class();
        } elseif (is_string($classname)) {
            $this->classname = $classname;
        }

        if (true == $hasmetas) {
            $this->Metas = new Metas();
        }

        foreach ($this->datainfo as $key => $value) {
            $this->data[$key] = $value[3];
        }
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
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * 获取数据库数据.
     *
     * @return array
     */
    public function GetData()
    {
        return $this->data;
    }

    /**
     * 获取数据表.
     *
     * @return string
     */
    public function GetTable()
    {
        return $this->table;
    }

    /**
     * 获取表结构.
     *
     * @return array
     */
    public function GetDataInfo()
    {
        return $this->datainfo;
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
        $id_field = reset($this->datainfo);
        $id_field = $id_field[0];
        $s = $this->db->sql->Select($this->table, array('*'), array(array('=', $id_field, $id)), null, null, null);

        $array = $this->db->Query($s);
        if (count($array) > 0) {
            $this->LoadInfoByAssoc($array[0]);

            return true;
        } else {
            return false;
        }
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
        global $bloghost;
        foreach ($this->datainfo as $key => $value) {
            if (!isset($array[$value[0]])) {
                continue;
            }

            if ($value[1] == 'boolean') {
                $this->data[$key] = (bool) $array[$value[0]];
            } elseif ($value[1] == 'string') {
                if ($key == 'Meta') {
                    $this->data[$key] = $array[$value[0]];
                    $this->Metas->Unserialize($this->data['Meta']);
                } else {
                    $this->data[$key] = str_replace('{#ZC_BLOG_HOST#}', $bloghost, $array[$value[0]]);
                }
            } else {
                $this->data[$key] = $array[$value[0]];
            }
        }
        //foreach ($GLOBALS['hooks']['Filter_Plugin_Base_Data_Load'] as $fpname => &$fpsignal) {
        //    $fpname($this, $this->data);
        //}

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
        global $table, $datainfo;
        $field_table = array_flip($table);
        $field_table = $field_table[$this->table];
        $field_name = $datainfo[$field_table][$field][0];
        $sql = $this->db->sql->Select($this->table, array('*'), array(array('=', $field_name, $field_value)), null, null, null);
        $array = $this->db->Query($sql);

        if (count($array) > 0) {
            $this->LoadInfoByAssoc($array[0]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 从数组中加载数据.
     *
     * @param $array
     *
     * @return bool
     */
    public function LoadInfoByArray($array)
    {
        global $bloghost;
        $i = 0;
        foreach ($this->datainfo as $key => $value) {
            if (count($array) == $i) {
                continue;
            }

            if ($value[1] == 'boolean') {
                $this->data[$key] = (bool) $array[$i];
            } elseif ($value[1] == 'string') {
                if ($key == 'Meta') {
                    $this->data[$key] = $array[$i];
                    if (isset($this->data['Meta'])) {
                        $this->Metas->Unserialize($this->data['Meta']);
                    }
                } else {
                    $this->data[$key] = str_replace('{#ZC_BLOG_HOST#}', $bloghost, $array[$i]);
                }
            } else {
                $this->data[$key] = $array[$i];
            }
            $i += 1;
        }
        //foreach ($GLOBALS['hooks']['Filter_Plugin_Base_Data_Load'] as $fpname => &$fpsignal) {
        //    $fpname($this, $this->data);
        //}

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
        if (isset($this->data['Meta'])) {
            $this->data['Meta'] = $this->Metas->Serialize();
        }

        $keys = array();
        foreach ($this->datainfo as $key => $value) {
            if (!is_array($value) || count($value) != 4) {
                continue;
            }

            $keys[] = $value[0];
        }
        $keyvalue = array_fill_keys($keys, '');

        foreach ($this->datainfo as $key => $value) {
            if (!is_array($value) || count($value) != 4) {
                continue;
            }

            if ($value[1] == 'boolean') {
                $keyvalue[$value[0]] = (int) $this->data[$key];
            } elseif ($value[1] == 'integer') {
                $keyvalue[$value[0]] = (int) $this->data[$key];
            } elseif ($value[1] == 'float') {
                $keyvalue[$value[0]] = (float) $this->data[$key];
            } elseif ($value[1] == 'double') {
                $keyvalue[$value[0]] = (float) $this->data[$key];
            } elseif ($value[1] == 'string') {
                if ($key == 'Meta') {
                    $keyvalue[$value[0]] = $this->data[$key];
                } else {
                    $keyvalue[$value[0]] = str_replace($bloghost, '{#ZC_BLOG_HOST#}', $this->data[$key]);
                }
            } else {
                $keyvalue[$value[0]] = $this->data[$key];
            }
        }
        array_shift($keyvalue);

        $id_field = reset($this->datainfo);
        $id_name = key($this->datainfo);
        $id_field = $id_field[0];

        if ($this->$id_name == 0) {
            $sql = $this->db->sql->Insert($this->table, $keyvalue);
            $this->$id_name = $this->db->Insert($sql);
        } else {
            $sql = $this->db->sql->Update($this->table, $keyvalue, array(array('=', $id_field, $this->$id_name)));

            return $this->db->Update($sql);
        }

        return true;
    }

    /**
     * 删除数据.
     *
     * @return bool
     */
    public function Del()
    {
        $id_field = reset($this->datainfo);
        $id_name = key($this->datainfo);
        $id_field = $id_field[0];
        $sql = $this->db->sql->Delete($this->table, array(array('=', $id_field, $this->$id_name)));
        $this->db->Delete($sql);

        return true;
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
}
