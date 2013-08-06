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


	public function Time($s='Y-m-d h:i:s'){
		return date($s,$this->PostTime);
	}


	public function __set($name, $value) 
	{
        global $zbp;
		switch ($name) {
			case 'Category':
			case 'Author':
			case 'TypeName':
			case 'Url':			
				return null;
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
				return $zbp->host . 'view.php?id=' . $this->ID ;
				break;
			case 'Tags':
				return $zbp->LoadTagsByString($this->Tag);
				break;				
			default:
				return parent::__get($name);
				break;
		}

	}

}

?>