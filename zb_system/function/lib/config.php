<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 配置类.
 */
class Config
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
     * @var array 原始db数据数组
     */
    protected $data = array();

    /**
     * @var array 存储Config相应key-value数值的数组
     */
    protected $kvdata = array();

    /**
     * @var Database__Interface
     */
    protected $db = null;

    /**
     * $itemname string 项目名称.
     *
     * @param string $itemName
     * @param null   $db
     */
    public function __construct($itemName = '', &$db = null)
    {
        if ($db !== null) {
            $this->db = &$db;
        } else {
            $this->db = &$GLOBALS['zbp']->db;
        }

        $this->table = &$GLOBALS['table']['Config'];
        $this->datainfo = &$GLOBALS['datainfo']['Config'];

        foreach ($this->datainfo as $key => $value) {
            $this->data[$key] = $value[3];
        }

        if ($itemName) {
            $itemName = FilterCorrectName($itemName);
        }

        $this->data['Name'] = $itemName;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $name = FilterCorrectName($name);
        $this->kvdata[$name] = $value;
    }

    /**
     * @param string $name key名
     *
     * @return null
     */
    public function __get($name)
    {
        if (!isset($this->kvdata[$name])) {
            return;
        }

        return $this->kvdata[$name];
    }

    /**
     * 获取Data数据.
     *
     * @return array
     */
    public function GetData()
    {
        return $this->kvdata;
    }

    /**
     * 获取Config的Item(项目名).
     *
     * @return string
     */
    public function GetItemName()
    {
        return $this->data['Name'];
    }

    /**
     * 检查KVData属性（数组）属性值是是否存在相应key.
     *
     * @param string $name key名
     *
     * @return bool
     */
    public function HasKey($name)
    {
        return array_key_exists($name, $this->kvdata);
    }

    /**
     * 检查KVData属性（数组）中的单元数目.
     *
     * @return int
     */
    public function CountItem()
    {
        return count($this->kvdata);
    }

    /**
     * 删除KVData属性（数组）中的相应项
     * Del名称和数据库删除函数有冲突
     *
     * @param string $name key名
     */
    public function Del($name)
    {
        $name = FilterCorrectName($name);
        unset($this->kvdata[$name]);
    }

    /**
     * 将Data属性（数组）值序列化.
     *
     * @return string 返回序列化的值
     */
    public function Serialize()
    {
        global $bloghost;
        if (count($this->kvdata) == 0) {
            return '';
        }

        $array = $this->kvdata;
        foreach ($array as $key => &$value) {
            if (is_string($value)) {
                $value = str_replace($bloghost, '{#ZC_BLOG_HOST#}', $value);
            }
        }

        return serialize($array);
    }

    /**
     * 将序列化的值反序列化后赋予Data属性值
     *
     * @param string $s 序列化值
     *
     * @return bool
     */
    public function Unserialize($s)
    {
        global $bloghost;

        if ($s == '') {
            return false;
        }

        $this->kvdata = @unserialize($s);
        if (!is_array($this->kvdata)) {
            $this->kvdata = array();

            return false;
        }

        foreach ($this->kvdata as $key => &$value) {
            if (is_string($value)) {
                $value = str_replace('{#ZC_BLOG_HOST#}', $bloghost, $value);
            }
        }

        return true;
    }

    /**
     * 从数组中加载数据.
     *
     * @param array $array 关联数组
     *
     * @return bool
     */
    public function LoadInfoByAssoc($array)
    {
        foreach ($this->datainfo as $key => $value) {
            if (!isset($array[$value[0]])) {
                continue;
            }

            $this->data[$key] = $array[$value[0]];
        }
        $this->Unserialize($this->data['Value']);

        return true;
    }

    /**
     * 保存数据.
     *
     * @return bool
     */
    public function Save()
    {
        $name = $this->GetItemName();
        $value = $this->Serialize();

        if ($name == '') {
            return false;
        }

        $kv = array('conf_Name' => $name, 'conf_Value' => $value);
        $sql = $this->db->sql->Select($this->table, 'conf_Name', array(array('=', 'conf_Name', $name)), '', '', '');
        $array = $this->db->Query($sql);

        if (count($array) == 0) {
            $sql = $this->db->sql->Insert($this->table, $kv);
            $this->db->Insert($sql);
        } else {
            array_shift($kv);
            $sql = $this->db->sql->Update($this->table, $kv, array(array('=', 'conf_Name', $name)));
            $this->db->Update($sql);
        }

        return true;
    }

    /**
     * 删除数据
     * Del名字已被占用，改用Delete表示从数据库删除
     * 从$zbp及数据库中删除该实例数据.
     *
     * @return bool
     */
    public function Delete()
    {
        $name = $this->GetItemName();
        $sql = $this->db->sql->Delete($this->table, array(array('=', 'conf_Name', $name)));
        $this->db->Delete($sql);

        return true;
    }

    /**
     * toString.
     *
     * 将Base对像返回JSON数据
     *
     * @return string
     */
    public function __toString()
    {
        return (string) json_encode($this->kvdata);
    }

    /**
     * 占位.
     *
     * @param $name
     *
     * @return bool
     */
    public function SaveKey($name)
    {
        return $this->Save();
    }

    /**
     * 占位.
     *
     * @param $name
     *
     * @return bool
     */
    public function DelKey($name)
    {
        $name = FilterCorrectName($name);
        if (!isset($this->kvdata[$name])) {
            return false;
        }

        unset($this->kvdata[$name]);

        return $this->Save();
    }
}
