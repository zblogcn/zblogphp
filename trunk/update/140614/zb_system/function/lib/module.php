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
		parent::__construct($zbp->table['Module'],$zbp->datainfo['Module']);
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
			$n='module_norefresh_' . $this->FileName;
			if($value==true){
				$zbp->cache->$n=true;
				$zbp->SaveCache();
			}else{
				if($zbp->cache->HasKey($n)==true){
					$zbp->cache->Del($n);
					$zbp->SaveCache();
				}
			}
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
			$n='module_norefresh_' . $this->FileName;
			if($zbp->cache->HasKey($n)==true){
				return true;
			}else{
				return false;
			}
		}
		return parent::__get($name);
	}

}
