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

	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Url') {
			return null;
		}
		if ($name=='Template') {
			if($value==$zbp->option['ZC_CATALOG_DEFAULT_TEMPLATE'])$value='';
			return $this->Data[$name]  =  $value;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
			return $zbp->host . '?tag=' . $this->ID;
		}
		if ($name=='Template') {
			$value=$this->Data[$name];
			if($value=='')$value=$zbp->option['ZC_CATALOG_DEFAULT_TEMPLATE'];
			return $value;
		}
		return parent::__get($name);
	}

}


?>