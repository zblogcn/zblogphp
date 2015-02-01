<?php
/**
 * 自定义域类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class Metas {

	/**
	 * @var array 存储Metas相应数值的数组
	 */
	public $Data=array();
	/**
	 * @param string $name key名
	 * @param $value
	 */

	public function __set($name, $value){
		$this->Data[$name] = $value;
	}

	/**
	 * @param string $name key名
	 * @return null
	 */
	public function __get($name){
		if(!isset($this->Data[$name]))return null;
		return $this->Data[$name];
	}

	/**
	 * 将数组数据转换为Metas实例
	 * @param array $a
	 * @return Metas
	 */
	public static function ConvertArray($a){
		$m = new Metas;
		if(is_array($a)){
			$m->Data=$a;
		}
		return $m;
	}

	/**
	 * 依据zbp设置替换签标为host值或是固定域名
	 * @param string $value
	 * @return string
	 */
	public static function ReplaceTag2Host($value){
		global $bloghost;
		return str_replace('{#ZC_BLOG_HOST#}',$bloghost,$value);
	}

	/**
	 * 依据zbp设置替换host值为签标
	 * @param string $value
	 * @return string
	 */
	public static function ReplaceHost2Tag($value){
		global $bloghost;
		return str_replace($bloghost,'{#ZC_BLOG_HOST#}',$value);
	}

	/**
	 * 检查Data属性（数组）属性值是是否存在相应key
	 * @param string $name key名
	 * @return bool
	 */
	public function HasKey($name){
		return array_key_exists($name,$this->Data);
	}

	/**
	 * 检查Data属性（数组）中的单元数目
	 * @return int
	 */
	public function CountItem(){
		return count($this->Data);
	}

	/**
	 * 删除Data属性（数组）中的相应项
	 * @param string $name key名
	 */
	public function Del($name){

		 unset($this->Data[$name]);
	}

	/**
	 * 将Data属性（数组）值序列化
	 * @return string 返回序列化的值
	 */
	public function Serialize(){
		if(count($this->Data)==0)return '';
		$data=$this->Data;
		foreach ($data as $key => $value)
			if(is_string($value))
				$data[$key]=self::ReplaceHost2Tag($value);
		//return json_encode($data);
		return serialize($data);
	}

	/**
	 * 将序列化的值反序列化后赋予Data属性值
	 * @param string $s 序列化值
	 * @return bool
	 */
	public function Unserialize($s){

		if($s=='')return false;
		//if(strpos($s,'{')===0){
			//$this->Data=json_decode($s,true);
		//}else{
			$this->Data=@unserialize($s);
		//}
		if(is_array($this->Data)){
			if(count($this->Data)==0)return true;
			foreach ($this->Data as $key => $value)
				if(is_string($value))
					$this->Data[$key]=self::ReplaceTag2Host($value);
		}else{
			$this->Data=array();
			return false;
		}

		return true;
	}

}
