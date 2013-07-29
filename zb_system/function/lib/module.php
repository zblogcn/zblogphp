<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */




class Module extends Base{


	function __construct()
	{
		$this->table=&$GLOBALS['table']['Module'];	
		$this->datainfo=&$GLOBALS['datainfo']['Module'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		$this->Order = 0;
	}


	public function __set($name, $value) 
	{
		if ($name=='Content') {
			$Content = str_replace($GLOBALS['zbp']->host,'{#ZC_BLOG_HOST#}' , $value);
			$this->Data[$name] = $Content;
			return null;
		}
		$this->Data[$name] = $value;
	}

	public function __get($name) 
	{
		if ($name=='Content') {
			$Content = str_replace('{#ZC_BLOG_HOST#}',$GLOBALS['zbp']->host,$this->Data[$name]);
			return $Content;
		}
		return $this->Data[$name];
	}

}



?>