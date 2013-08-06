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
		return $this->Data[$name];
	}

	public function serialize() 
	{
		if(count($this->Data)==0)return '';
		return serialize($this->Data);
	}

	public function unserialize($s) 
	{
		if($s=='')return;
		$this->Data=unserialize($s);
	}	


}


?>