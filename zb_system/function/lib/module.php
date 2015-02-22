<?php
/**
 * 模块类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Article 类库
 */
class Module extends Base{

	/**
	 * 构造函数
	 */
	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Module'],$zbp->datainfo['Module'],__CLASS__);
	}

	/**
	 * 设置参数值
	 * @param string $name
	 * @param mixed $value
	 * @return null
	 */
	public function __set($name, $value)
	{
		global $zbp;
		if ($name=='SourceType') {
			return null;
		}
		if ($name=='NoRefresh') {
			if((bool)$value)
				$this->Metas->norefresh = (bool)$value;
			else
				$this->Metas->Del('norefresh');
			return null;
		}
		parent::__set($name, $value);
	}

	/**
	 * 获取参数值
	 * @param $name
	 * @return bool|mixed|string
	 */
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
			}elseif($this->Source=='plugin_' . $zbp->theme){
				return 'theme';
			}else{
				return 'plugin';
			}
		}
		if ($name=='NoRefresh') {
			return (bool)$this->Metas->norefresh;
		}
		return parent::__get($name);
	}

	/**
	 * @return bool
	 */
	function Save(){
		global $zbp;
		foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Save'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		if($this->Source=='theme'){
			if(!$this->FileName)return true;
			$c = $this->Content;
			$d = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/';
			$f = $d . $this->FileName . '.php';
			if(!file_exists($d)){
				@mkdir($d,0755);
			}
			@file_put_contents($f, $c);
			return true;
		}
		return parent::Save();
	}

	/**
	 * @return bool
	 */
	function Del(){
		global $zbp;
		foreach ($GLOBALS['hooks']['Filter_Plugin_Module_Del'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		if($this->Source=='theme'){
			if(!$this->FileName)return true;
			$f = $zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . $this->FileName . '.php';
			if (file_exists($f)){
				@unlink($f);
			}
			return true;
		}
		return parent::Del();
	}

}
