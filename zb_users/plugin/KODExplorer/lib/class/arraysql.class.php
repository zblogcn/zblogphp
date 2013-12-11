<?php 
/**
* 数组的类似数据库操作,传入数组，增删改查。
* 形如：多条数据的数组。
array(
	array('name'=>'',path=>''),
	array('name'=>'',path=>''),
	……
)
*/

class arraysql{	
	var $array;

	function __construct($array=array()){
		$this->array=$array;
	}

	function insert($list=array()){//保证主键值,第一个为主键
		$num=count($this->array);
		$keys=array_keys($list);//list传入的第一个键值
		$keys=$keys[0];
		$key_has=0;//默认没有键值重复标记

		for ($i=0; $i < $num; $i++) {//list传入的第一个键值
			if ($this->array[$i][$keys]==$list[$keys]){
				$key_has=1;
				break;
			}
		}

		if (!$key_has){//没有重复则插入
			array_push($this->array, $list);
			return true;
		}
		else {
			return false;
		}		
	}

	//array中，找到为key键值的值为where的一条数据，将数据修改为array()
	function update($key,$where,$arr=array()){
		$num=count($this->array);
		for ($i=0; $i < $num; $i++) {//list传入的第一个键值
			if ($this->array[$i][$key]==$where){
				$this->array[$i]=$arr;
				return true;
			}
		}
		return false;
	}

	//查找某一条记录，返回记录数组。
	function select($key,$where){
		$num=count($this->array);
		for ($i=0; $i < $num; $i++) {//list传入的第一个键值
			if ($this->array[$i][$key]==$where){
				return $this->array[$i];
			}
		}
		return false;
	}

	//删除某个列。
	function del($key,$value){
		$num=count($this->array);
		for ($i=0; $i < $num; $i++) { 
			if ($this->array[$i][$key]==$value) {
				//unset($this->array[$i]);
				array_splice($this->array,$i,1);
				return true;
			}
		}
		return false;
	}
	function getarray(){
		return $this->array;
	}
}
