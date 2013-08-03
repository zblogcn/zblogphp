<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Upload extends Base{


	function __construct()
	{
		$this->zbp=&$GLOBALS['zbp'];
		$this->table=&$this->zbp->table['Upload'];	
		$this->datainfo=&$this->zbp->datainfo['Upload'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$this->zbp->db;
		$this->ID = 0;

	}


}
?>