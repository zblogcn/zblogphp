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
        global $zbp;
		$this->table=&$zbp->table['Module'];	
		$this->datainfo=&$zbp->datainfo['Module'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='SourceType') {
			return null;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='SourceType') {
			if($this->Source=='system'){
				return 'system';
			}elseif($this->Source=='user'){
				return 'user';
			}elseif($this->Source=='theme'){
				return 'theme';
			}else{
				return 'plugin';
			}
		}
		return parent::__get($name);
	}

}



?>