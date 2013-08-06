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

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Url') {
			return null;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
			return $zbp->host . '?tag=' . $this->ID;
		}
		return parent::__get($name);
	}

}


?>