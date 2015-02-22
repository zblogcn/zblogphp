<?php
/**
 * Tag类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Article 类库
 */
class Tag extends Base{

	/**
	 *
	 */
	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Tag'],$zbp->datainfo['Tag'],__CLASS__);
	}

	/**
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	function __call($method, $args) {
		foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Call'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this,$method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
	}

	/**
	 * @param $name
	 * @param $value
	 * @return null|string
	 */
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

	/**
	 * @param $name
	 * @return mixed|string
	 */
	public function __get($name)
	{
		global $zbp;
		if ($name=='Url') {
			foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Url'] as $fpname => &$fpsignal) {
				$fpsignal=PLUGIN_EXITSIGNAL_NONE;
				$fpreturn=$fpname($this);
				if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
			}
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

	/**
	 * @return bool
	 */
	function Save(){
		global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->data['Template'] = '';
		foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Save'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		return parent::Save();
	}

	/**
	 * @return bool
	 */
	function Del(){
		foreach ($GLOBALS['hooks']['Filter_Plugin_Tag_Del'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		return parent::Del();
	}
	
}
