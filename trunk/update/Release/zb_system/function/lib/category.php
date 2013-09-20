<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Category extends Base{

	public $SubCategorys=array();

	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Category'];	
		$this->datainfo=&$zbp->datainfo['Category'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->Name	= $GLOBALS['lang']['msg']['unnamed'];
	}

	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Category_Call'] as $fpname => &$fpsignal) {
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
		if ($name=='Symbol') {
			return null;
		}
		if ($name=='Level') {
			return null;
		}
		if ($name=='SymbolName') {
			return null;
		}
		if ($name=='Template') {
			if($value==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$value='';
			return $this->Data[$name]  =  $value;
		}
		if ($name=='LogTemplate') {
			if($value==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$value='';
			return $this->Data[$name]  =  $value;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
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
					return '└';	
				}elseif($l==2){
					return '&nbsp;&nbsp;└';	
				}elseif($l==3){
					return '&nbsp;&nbsp;&nbsp;&nbsp;└';	
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
		if ($name=='Template') {
			$value=$this->Data[$name];
			if($value=='')$value=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
			return $value;
		}
		if ($name=='LogTemplate') {
			$value=$this->Data[$name];
			if($value=='')$value=$zbp->option['ZC_POST_DEFAULT_TEMPLATE'];
			return $value;
		}
		return parent::__get($name);
	}

	function Save(){
        global $zbp;
		if($this->Template==$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'])$this->Data['Template'] = '';
		if($this->LogTemplate==$zbp->option['ZC_POST_DEFAULT_TEMPLATE'])$this->Data['LogTemplate'] = '';
		return parent::Save();
	}

}

?>