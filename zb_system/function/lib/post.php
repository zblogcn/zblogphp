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
		$this->zbp=&$GLOBALS['zbp'];
		$this->table=&$this->zbp->table['Post'];	
		$this->datainfo=&$this->zbp->datainfo['Post'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$this->zbp->db;
		$this->ID = 0;
		$this->Name	= $GLOBALS['lang']['msg']['unnamed'];
	}


	public function __set($name, $value) 
	{
		switch ($name) {
			case 'Category':
			case 'Author':
			case 'TypeName':
				return null;
				break;
			default:
				$this->Data[$name] = $value;
				break;
		}
	}

	public function __get($name) 
	{

		switch ($name) {
			case 'Category':
				return $this->zbp->GetCategoryByID($this->CateID);
				break;
			case 'Author':
				return $this->zbp->GetMemberByID($this->AuthorID);
				break;
			case 'StatusName':
				return $this->zbp->lang['post_status_name'][$this->Status];
				break;
			default:
				return $this->Data[$name];
				break;
		}

	}

}

?>