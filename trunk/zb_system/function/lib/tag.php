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
        global $zbp;
		$this->table=&$zbp->table['Tag'];	
		$this->datainfo=&$zbp->datainfo['Tag'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;

	}


}


?>