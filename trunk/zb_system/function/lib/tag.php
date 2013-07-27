<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */



class Tag extends Base{


	function __construct()
	{
		$this->table=&$GLOBALS['table']['Tag'];	
		$this->datainfo=&$GLOBALS['datainfo']['Tag'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;

	}


}


?>