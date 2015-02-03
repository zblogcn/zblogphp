<?php
/**
 * 文章分类类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Article 类库
 */
class Category extends Base {

	/**
	* @var array 下层分类
	*/
	public $SubCategorys=array();

	/**
	* 构造函数
	*/
	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Category'],$zbp->datainfo['Category'],__CLASS__);

		$this->Name	= $zbp->lang['msg']['unnamed'];
	}

	/**
	* 魔术方法：重载，可通过接口Filter_Plugin_Category_Call添加自定义函数
	* @api Filter_Plugin_Category_Call
	* @param string $method 方法
	* @param mixed $args 参数
	* @return mixed
	*/
	function __call($method, $args) {
		foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Call'] as $fpname => &$fpsignal) {
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
		if ($name=='Symbol') {
			return null;
		}
		if ($name=='Level') {
			return null;
		}
		if ($name=='SymbolName') {
			return null;
		}
		if ($name=='Parent') {
			return null;
		}		
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->data[$name]  =  $value;
		}
		if ($name=='LogTemplate') {
			if($value==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$value='';
			return $this->data[$name]  =  $value;
		}
		parent::__set($name, $value);
	}

	/**
	* @param $name
	* @return int|mixed|null|string
	*/
	public function __get($name)
	{
		global $zbp;
		if ($name=='Url') {
			foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Url'] as $fpname => &$fpsignal) {
				$fpsignal=PLUGIN_EXITSIGNAL_NONE;
				$fpreturn=$fpname($this);
				if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
			}
			$u = new UrlRule($zbp->option['ZC_CATEGORY_REGEX']);
			$u->Rules['{%id%}']=$this->ID;
			$u->Rules['{%alias%}'] = $this->Alias==''?urlencode($this->Name):$this->Alias;
			return $u->Make();
		}
		if ($name=='Symbol') {
			if($this->ParentID==0){
				return ;
			}else{
				$l=$this->Level;
				if($l==1){
					return '&nbsp;└';	
				}elseif($l==2){
					return '&nbsp;&nbsp;&nbsp;└';	
				}elseif($l==3){
					return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└';	
				}
				return ;
			}
		}
		if ($name=='Level') {
			if($this->ParentID==0){
				$this->RootID=0;
				return 0;
			}
			if($zbp->categorys[$this->ParentID]->ParentID==0){
				$this->RootID=$this->ParentID;
				return 1;
			}
			if($zbp->categorys[$zbp->categorys[$this->ParentID]->ParentID]->ParentID==0){
				$this->RootID=$zbp->categorys[$this->ParentID]->ParentID;
				return 2;
			}
			if($zbp->categorys[$zbp->categorys[$zbp->categorys[$this->ParentID]->ParentID]->ParentID]->ParentID==0){
				$this->RootID=$zbp->categorys[$zbp->categorys[$this->ParentID]->ParentID]->ParentID;				
				return 3;
			}

			return 0;
		}
		if ($name=='SymbolName') {
			return $this->Symbol . htmlspecialchars($this->Name);
		}
		if ($name=='Parent') {
			if($this->ParentID==0){
				return null;
			}else{
				return $zbp->categorys[$this->ParentID];
			}
		}	
		if ($name=='Template') {
			$value=$this->data[$name];
			if($value=='')$value=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			return $value;
		}
		if ($name=='LogTemplate') {
			$value=$this->data[$name];
			if($value=='')$value=$zbp->option['ZC_POST_DEFAULT_TEMPLATE'];
			return $value;
		}
		return parent::__get($name);
	}

	/**
	* 保存分类数据
	* @return bool
	*/
	function Save(){
		global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->data['Template'] = '';
		if($this->LogTemplate==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$this->data['LogTemplate'] = '';
		foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Save'] as $fpname => &$fpsignal) {
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
		foreach ($GLOBALS['hooks']['Filter_Plugin_Category_Del'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($this);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		return parent::Del();
	}
}
