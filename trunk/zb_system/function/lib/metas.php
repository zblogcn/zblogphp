<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class Metas {

	public $Data=array();

	public function __set($name, $value)
	{
		$this->Data[$name] = $value;
	}

	public function __get($name)
	{
		if(!isset($this->Data[$name]))return null;
		return $this->Data[$name];
	}

	public static function ConvertArray($a){
		$m = new Metas;		
		if(is_array($a)){
			$m->Data=$a;
		}
		return $m;
	}

	public function HasKey($name){
		return array_key_exists($name,$this->Data);
	}

	public function Del($name){

		 unset($this->Data[$name]); 
	}

	public function Serialize(){
		global $zbp;
		if(count($this->Data)==0)return '';
		foreach ($this->Data as $key => $value) {
			if(is_string($value)){
				$this->Data[$key]=str_replace($zbp->host,'{#ZC_BLOG_HOST#}',$value);
			}
		}
		return serialize($this->Data);
	}

	public function Unserialize($s){
		global $zbp;
		if($s=='')return false;
		$this->Data=unserialize($s);
		if(count($this->Data)==0)return false;
		foreach ($this->Data as $key => $value) {
			if(is_string($value)){
				$this->Data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$value);
			}
		}
	}


}


?>