<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

 
class Post extends Base{


	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Post'];	
		$this->datainfo=&$zbp->datainfo['Post'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;
		$this->Title	= $GLOBALS['lang']['msg']['unnamed'];
		$this->PostTime	= time();		
	}


	public function Time($s='Y-m-d H:i:s'){
		return date($s,$this->PostTime);
	}

	function TagsToNameString(){
		global $zbp;
		$s=$this->Tag;
		if($s=='')return '';
		$s=str_replace('}{', '|', $s);
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$b=explode('|', $s);
		$b=array_unique($b);

		$a=$zbp->LoadTagsByIDString($this->Tag);
		$s='';
		$c='';
		foreach ($b as $key) {
			if(isset($zbp->tags[$key])){
				$c[] = $zbp->tags[$key]->Name;
			}
		}
		if(!$c)return '';
		$s=implode(',', $c);
		return $s;
	}

	public function __set($name, $value) 
	{
        global $zbp;
		switch ($name) {
			case 'Category':
			case 'Author':
			case 'TypeName':
			case 'Url':
			case 'Tags':
				return null;
				break;
			case 'Template':
				if($value==$zbp->option['ZC_ARTICLE_DEFAULT_TEMPLATE'])$value='';
				return $this->Data[$name]  =  $value;
				break;
			default:
				parent::__set($name, $value);
				break;
		}
	}

	public function __get($name) 
	{
        global $zbp;
		switch ($name) {
			case 'Category':
				return $zbp->GetCategoryByID($this->CateID);
				break;
			case 'Author':
				return $zbp->GetMemberByID($this->AuthorID);
				break;
			case 'StatusName':
				return $zbp->lang['post_status_name'][$this->Status];
				break;
			case 'Url':
				if($this->Type==ZC_POST_TYPE_ARTICLE){
					$u = new UrlRule($zbp->option['ZC_ARTICLE_REGEX']);
				}else{
					$u = new UrlRule($zbp->option['ZC_PAGE_REGEX']);
				}
				$u->Rules['{%id%}']=$this->ID;
				$u->Rules['{%alias%}']=$this->Alias;
				return $u->Make();
				break;
			case 'Tags':
				return $zbp->LoadTagsByIDString($this->Tag);
				break;
			case 'Template':
				$value=$this->Data[$name];
				if($value=='')$value=$zbp->option['ZC_ARTICLE_DEFAULT_TEMPLATE'];
				return $value;
			default:
				return parent::__get($name);
				break;
		}

	}

}

?>