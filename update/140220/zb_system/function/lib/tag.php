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
		parent::__construct($zbp->table['Tag'],$zbp->datainfo['Tag']);
	}

	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Tag_Call'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this,$method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Url') {
			return null;
		}
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->data[$name]  =  $value;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
			$u = new UrlRule($zbp->option['ZC_TAGS_REGEX']);
			$u->Rules['{%id%}']=$this->ID;
			$u->Rules['{%alias%}']=$this->Alias==''?urlencode($this->Name):$this->Alias;
			return $u->Make();
		}
		if ($name=='Template') {
			$value=$this->data[$name];
			if($value=='')$value=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			return $value;
		}
		return parent::__get($name);
	}

	function Save(){
        global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->data['Template'] = '';
		return parent::Save();
	}
	
}
