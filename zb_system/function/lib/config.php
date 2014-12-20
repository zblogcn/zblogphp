<?php
/**
 * 配置类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class Config {

	/**
	 * @var array 存储Config相应数值的数组
	 */
	private $data=array();
	/**
	 * @param string $name key名
	 * @param $value
	 */

	public function __set($name, $value){
		$this->data[$name] = $value;
	}

	/**
	 * @param string $name key名
	 * @return null
	 */
	public function __get($name){
		if(!isset($this->data[$name]))return null;
		return $this->data[$name];
	}
	
	/**
	* 获取Data数据
	* @return array
	*/
	function GetData(){
		return $this->data;
	}

	/**
	 * 检查Data属性（数组）属性值是是否存在相应key
	 * @param string $name key名
	 * @return bool
	 */
	public function HasKey($name){
		return array_key_exists($name,$this->data);
	}

	/**
	 * 检查Data属性（数组）中的单元数目
	 * @return int
	 */
	public function CountItem(){
		return count($this->data);
	}

	/**
	 * 删除Data属性（数组）中的相应项
	 * @param string $name key名
	 */
	public function Del($name){

		 unset($this->data[$name]);
	}

	/**
	 * 将Data属性（数组）值序列化
	 * @return string 返回序列化的值
	 */
	public function Serialize(){
		global $zbp;
		if(count($this->data)==0)return '';
		return serialize($this->data);
	}

	/**
	 * 将序列化的值反序列化后赋予Data属性值
	 * @param string $s 序列化值
	 * @return bool
	 */
	public function Unserialize($s){

		if($s=='')return false;
			$this->data=@unserialize($s);
		if(!is_array($this->data)){
			$this->data=array();
			return false;
		}

		return true;
	}

}
