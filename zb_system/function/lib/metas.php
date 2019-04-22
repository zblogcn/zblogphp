<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 扩展内容类.
 *
 * @property string Name
 * @property int|string Count
 * @property string Url
 */
class Metas
{
    /**
     * @var array 存储Metas相应数值的数组
     */
    private $_data = array();

    /**
     * @param string $name key名
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * @param string $name key名
     *
     * @return null
     */
    public function __get($name)
    {
        if (!isset($this->_data[$name])) {
            return;
        }

        return $this->_data[$name];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    /**
     * 将数组数据转换为Metas实例.
     *
     * @param array $a
     *
     * @return Metas
     */
    public static function ConvertArray($a)
    {
        $m = new self();
        if (is_array($a)) {
            $m->_data = $a;
        }

        return $m;
    }

    /**
     * 获取Data数据.
     *
     * @return array
     */
    public function GetData()
    {
        return $this->_data;
    }

    /**
     * 依据zbp设置替换签标为host值或是固定域名.
     *
     * @param string $value
     *
     * @return string
     */
    public static function ReplaceTag2Host($value)
    {
        global $bloghost;

        return str_replace('{#ZC_BLOG_HOST#}', $bloghost, $value);
    }

    /**
     * 依据zbp设置替换host值为签标.
     *
     * @param string $value
     *
     * @return string
     */
    public static function ReplaceHost2Tag($value)
    {
        global $bloghost;

        return str_replace($bloghost, '{#ZC_BLOG_HOST#}', $value);
    }

    /**
     * 检查Data属性（数组）属性值是是否存在相应key.
     *
     * @param string $name key名
     *
     * @return bool
     */
    public function HasKey($name)
    {
        return array_key_exists($name, $this->_data);
    }

    /**
     * 检查Data属性（数组）中的单元数目.
     *
     * @return int
     */
    public function CountItem()
    {
        return count($this->_data);
    }

    /**
     * 删除Data属性（数组）中的相应项.
     *
     * @param string $name key名
     */
    public function Del($name)
    {
        unset($this->_data[$name]);
    }

    /**
     * 将Data属性（数组）值序列化.
     *
     * @return string 返回序列化的值
     */
    public function Serialize()
    {
        if (count($this->_data) == 0) {
            return '';
        }

        $data = $this->_data;
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = self::ReplaceHost2Tag($value);
            }
        }

        //return json_encode($data);
        return serialize($data);
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
        if ($s == '') {
            return false;
        }

        //if(strpos($s,'{')===0){
        //$this->_data=json_decode($s,true);
        //}else{
        $this->_data = @unserialize($s);
        //}
        if (is_array($this->_data)) {
            if (count($this->_data) == 0) {
                return true;
            }

            foreach ($this->_data as $key => $value) {
                if (is_string($value)) {
                    $this->_data[$key] = self::ReplaceTag2Host($value);
                }
            }
        } else {
            $this->_data = array();

            return false;
        }

        return true;
    }
}
