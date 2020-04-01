<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 配置类.
 */
class Config implements Iterator
{
    private $position = 0;
    private $array = array(); //存$key的数组，非$value

    public function rewind()
    {
        foreach ($this->kvdata as $key => $value) {
            $this->array[] = $key;
        }
        $this->position = 0;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function valid()
    {
        return isset($this->array[$this->position]);
    }

    /**
     * @var string 数据表
     */
    protected $table = '';
    protected $t = null;
    /**
     * @var array 表结构信息
     */
    protected $datainfo = array();
    protected $d = null;
    /**
     * @var array 原始db数据数组
     */
    protected $data = array();

    /**
     * @var array 存储Config相应key-value数值的数组
     */
    protected $kvdata = array();
    /**
     * @var array 存储Config相应原始数据的数组
     */
    protected $origkvdata = array();
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
        $this->t = &$this->table;
        $this->d = &$this->datainfo;

        foreach ($this->datainfo as $key => $value) {
            $this->data[$key] = $value[3];
        }

        if ($itemName) {
            $itemName = FilterCorrectName($itemName);
        }

        $this->data['Name'] = $itemName;
        $this->position = 0;
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

    public function CountItemOrig()
    {
        return count($this->origkvdata);
    }

    /**
     * 双重意义的函数
     * $name为null就转向Delete()
     * $name不为null就转向DelKey()
     * 删除KVData属性（数组）中的相应项
     * Del名称和数据库删除函数有冲突
     *
     * @param string $name key名
     */
    public function Del($name = null)
    {
        if ($name === null) {
            return $this->Delete();
        }
        if ($name !== null) {
            return $this->DelKey($name);
        }
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
        $this->origkvdata = $this->kvdata;

        return true;
    }

    /**
     * 为了加快处理速度才写的一对WithPre,WithAfter函数.
     */
    private $data_pre_key = array();
    private $data_pre_value = array();

    public function LoadInfoByAssocSingleWithPre($array)
    {
        $key = trim($array[$this->d['Key'][0]]);
        $value = trim($array[$this->d['Value'][0]]);
        $this->data_pre_key[] = $key;
        $this->data_pre_value[] = $value;

        return true;
    }

    public function LoadInfoByAssocSingleWithAfter()
    {
        global $bloghost;
        if (count($this->data_pre_value) == 0 || count($this->data_pre_key) == 0) {
            return false;
        }
        $a = array();
        foreach ($this->data_pre_value as $key => $value) {
            if (is_array($this->data_pre_key[$key])) {
                unset($this->data_pre_key[$key]);
                unset($this->data_pre_value[$key]);
                continue;
            }
            $a[] = 's:' . strlen($this->data_pre_key[$key]) . ':"' . $this->data_pre_key[$key] . '";' . $value;
        }
        $s = 'a:' . count($this->data_pre_value) . ':{' . implode('', $a) . '}';
        $b = unserialize($s);
        $this->kvdata = $b;
        foreach ($this->kvdata as $key => &$value) {
            if (is_string($value)) {
                $value = str_replace('{#ZC_BLOG_HOST#}', $bloghost, $value);
            }
        }
        $this->origkvdata = $this->kvdata;
        $this->data_pre_key[] = array();
        $this->data_pre_value[] = array();

        return true;
    }

    public function LoadInfoByAssocSingle($array)
    {
        $key = trim($array[$this->d['Key'][0]]);
        $value = trim($array[$this->d['Value'][0]]);

        $value = $this->UnserializeSingle($value);
        $this->kvdata[$key] = $value;
        $this->origkvdata[$key] = $value;

        return true;
    }

    public function SerializeSingle($value)
    {
        global $bloghost;
        if (is_string($value)) {
            $value = str_replace($bloghost, '{#ZC_BLOG_HOST#}', $value);
        }

        return serialize($value);
    }

    public function UnserializeSingle($value)
    {
        global $bloghost;
        $value = @unserialize($value);

        if (is_string($value)) {
            $value = str_replace('{#ZC_BLOG_HOST#}', $bloghost, $value);
        }

        return $value;
    }

    /**
     * 保存数据.
     *
     * @return bool
     */
    public function Save()
    {
        $name = $this->GetItemName();
        if ($name == '') {
            return false;
        }

        $add = array_diff_key($this->kvdata, $this->origkvdata);
        $del = array_diff_key($this->origkvdata, $this->kvdata);
        $mod = array(); //array_intersect($this->kvdata, $this->origkvdata);
        foreach ($this->kvdata as $key => $value) {
            if (isset($this->origkvdata[$key]) == true && $this->kvdata[$key] != $this->origkvdata[$key]) {
                $mod[$key] = $value;
            }
        }
        //var_dump(count($this->kvdata),count($this->origkvdata));die;
        //logs(var_export( array($this->origkvdata['ZC_DEBUG_MODE'], $this->kvdata['ZC_DEBUG_MODE']), true ) );
        //logs(var_export( array('add'=>$add , 'del'=>$del , 'mod'=>$mod),true ) );
        //var_dump($this->origkvdata['ZC_DEBUG_MODE'], $this->kvdata['ZC_DEBUG_MODE']);
        //var_dump($add , $del , $mod);
        //var_dump($this->d);
        //die;

        if (($add + $del + $mod) == array()) {
            return true;
        }

        $old = $this->db->Query($this->db->sql->Select($this->table, '*', array(array('=', $this->d['Key'][0], ''))));
        //没有这个字段：array(1) { [0]=> bool(false) }
        if (count($old) == 1 && $old[0] === false) {//如果还没有建conf_Key字段就不要原子化存储

            $value = $this->Serialize();

            $kv = array($this->d['Name'][0] => $name, $this->d['Value'][0] => $value);

            $old = $this->db->Query($this->db->sql->Select($this->table, '*', array(array('=', $this->d['Name'][0], $name))));
            //没有这一行数据 array(0) { }
            if (count($old) == 0) {
                $sql = $this->db->sql->Insert($this->table, $kv);
                $this->db->Insert($sql);
            } else {
                array_shift($kv);
                $sql = $this->db->sql->Update($this->table, $kv, array(array('=', $this->d['Name'][0], $name)));
                $this->db->Update($sql);
            }
            //存储成功后重置origkvdata
            $this->origkvdata = $this->kvdata;

            return true;
        }

        $old = $this->db->Query($this->db->sql->Select($this->table, '*', array(array('=', $this->d['Name'][0], $name), array('=', $this->d['Key'][0], ''))));
        if (count($old) > 0) {//如果存在老数据，先删除老的
            $del = array();
            $mod = array();
            $add = $this->kvdata;

            $sql = $this->db->sql->Delete($this->table, array(array('=', $this->d['Name'][0], $name), array('=', $this->d['Key'][0], '')));
            $this->db->Delete($sql);
        }
        if (($add + $del + $mod) == array()) {
            return true;
        }

        $this->db->Query($this->db->sql->Transaction('begin'));

        //add
        foreach ($add as $key2 => $value2) {
            $kv2 = array($this->d['Name'][0] => $name, $this->d['Key'][0] => $key2, $this->d['Value'][0] => $this->SerializeSingle($value2));
            $sql = $this->db->sql->Insert($this->table, $kv2);
            $old = $this->db->Query($this->db->sql->Select($this->table, '*', array(array('=', $this->d['Name'][0], $name), array('=', $this->d['Key'][0], $key2))));
            if (count($old) == 0) {
                $this->db->Insert($sql);
            }
        }
        //mod
        foreach ($mod as $key3 => $value3) {
            $kv3 = array($this->d['Value'][0] => $this->SerializeSingle($value3));
            $sql = $this->db->sql->Update($this->table, $kv3, array(array('=', $this->d['Name'][0], $name), array('=', $this->d['Key'][0], $key3)));
            $this->db->Update($sql);
        }
        //del
        foreach ($del as $key4 => $value4) {
            $sql = $this->db->sql->Delete($this->table, array(array('=', $this->d['Name'][0], $name), array('=', $this->d['Key'][0], $key4)));
            $this->db->Delete($sql);
        }
        //var_dump($add,$del,$mod);die;
        $this->db->Query($this->db->sql->Transaction('commit'));
        //存储成功后重置origkvdata
        $this->origkvdata = $this->kvdata;

        return true;
    }

    /**
     * 删除数据
     * Delete表示从数据库删除
     * 从$zbp及数据库中删除该实例Config数据.
     *
     * @return bool
     */
    public function Delete()
    {
        global $zbp;
        $name = $this->GetItemName();
        $sql = $this->db->sql->Delete($this->table, array(array('=', $this->d['Name'][0], $name)));
        $this->db->Delete($sql);
        unset($zbp->configs[$name]);

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
     * 添加or修改Key.
     *
     * @param $name
     *
     * @return bool
     */
    public function AddKey($name, $value)
    {
        $name = FilterCorrectName($name);
        if (!$name) {
            return;
        }
        $this->kvdata[$name] = $value;

        return true;
    }

    /**
     * 删除Key，不推荐使用Del($name).
     *
     * @param $name
     *
     * @return bool
     */
    public function DelKey($name)
    {
        $name = FilterCorrectName($name);
        if (!isset($this->kvdata[$name])) {
            return;
        }

        unset($this->kvdata[$name]);

        return true;
    }
}
