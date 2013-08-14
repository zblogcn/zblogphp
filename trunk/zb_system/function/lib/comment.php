<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Comment extends Base{


	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Comment'];	
		$this->datainfo=&$zbp->datainfo['Comment'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

	}

	public function Time($s='Y-m-d H:i:s'){
		return date($s,(int)$this->PostTime);
	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Author') {
			return null;
		}
		if ($name=='Comments') {
			return null;
		}
		if ($name=='Level') {
			return null;
		}
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Author') {
			$m=$zbp->GetMemberByID($this->AuthorID);
			if($m->ID==0){
				$m->Name=$this->Name;
				$m->Email=$this->Email;
				$m->HomePage=$this->HomePage;
			}
			return $m;
		}
		if ($name=='Comments') {
			$array=array();
			foreach ($zbp->comments as $comment) {
				if($comment->ParentID==$this->ID){
					$array[]=&$zbp->comments[$comment->ID];
				}
			}
			return $array;
		}
		if ($name=='Level') {
			if($this->ParentID==0){return 0;}

			$c1=$zbp->GetCommentByID($this->ParentID);
			if($c1->ParentID==0){return 1;}

			$c2=$zbp->GetCommentByID($c1->ParentID);
			if($c2->ParentID==0){return 2;}

			$c3=$zbp->GetCommentByID($c2->ParentID);
			if($c3->ParentID==0){return 3;}

			return 4;
		}
		return parent::__get($name);
	}

}


?>