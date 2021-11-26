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
    private $p_data = array();

    /**
     * @var boolean 是否自动替换host
     */
    protected $isreplacehost = true;

    /**
     * @param string $name key名
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->p_data[$name] = $value;
    }

    /**
     * @param string $name key名
     *
     * @return null
     */
    public function __get($name)
    {
        if (!isset($this->p_data[$name])) {
            return;
        }

        return $this->p_data[$name];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->p_data[$name]);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->p_data[$name]);
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
            $m->p_data = $a;
        }

        return $m;
    }

    /**
     * 获取Data数据(不设$key就返回整个data数组).
     *
     * @return array
     */
    public function GetData($key = null)
    {
        if (null == $key) {
            return $this->p_data;
        } else {
            return $this->p_data[$key];
        }
    }

    /**
     * 依据zbp设置替换签标为host值或是固定域名.
     *
     * @param string $value
     *
     * @return string
     */
    private static function ReplaceTag2Host($value)
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
    private static function ReplaceHost2Tag($value)
    {
        global $bloghost;

        return str_replace($bloghost, '{#ZC_BLOG_HOST#}', $value);
    }

    /**
     * 多维数组替换host值为签标.
     *
     * @param array $array
     * @param method $method
     *
     * @return array
     */
    public static function ReplaceTagArray($array, $method)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::ReplaceTagArray($value, $method);
            } elseif (is_string($value)) {
                $value = self::$method($value);
            }
        }
        return $array;
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
        return array_key_exists($name, $this->p_data);
    }

    /**
     * 检查Data属性（数组）中的单元数目.
     *
     * @return int
     */
    public function CountItem()
    {
        return count($this->p_data);
    }

    /**
     * 删除Data属性（数组）中的相应项.
     *
     * @param string $name key名
     */
    public function Del($name)
    {
        unset($this->p_data[$name]);
    }

    /**
     * 将Data属性（数组）值序列化.
     *
     * @return string 返回序列化的值
     */
    public function Serialize()
    {
        if (count($this->p_data) == 0) {
            return '';
        }

        $data = $this->p_data;

        if ($this->isreplacehost) {
            $data = self::ReplaceTagArray($data, 'ReplaceHost2Tag');
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
        //$this->p_data=json_decode($s,true);
        //}else{
        @$this->p_data = unserialize($s);
        //}
        if (is_array($this->p_data)) {
            if (count($this->p_data) == 0) {
                return true;
            }
            if ($this->isreplacehost) {
                $this->p_data = self::ReplaceTagArray($this->p_data, 'ReplaceTag2Host');
            }
        } else {
            $this->p_data = array();

            return false;
        }

        return true;
    }

}
