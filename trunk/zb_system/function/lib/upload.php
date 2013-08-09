<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Upload extends Base{


	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['Upload'];	
		$this->datainfo=&$zbp->datainfo['Upload'];

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;
		$this->PostTime = time();
	}

	function SaveFile($tmp){
		global $zbp;
		echo $this->FullFile;
		if(!file_exists($zbp->path . $zbp->usersdir . $this->Dir)){
			@mkdir($zbp->path . $zbp->usersdir . $this->Dir, 0777,true);	
		}
		@move_uploaded_file($tmp, $this->FullFile);			
	}

	public function Time($s='Y-m-d H:i:s'){
		return date($s,$this->PostTime);
	}

	public function __set($name, $value)
	{
        global $zbp;
		if ($name=='Url') {
			return null;
		}
		if ($name=='Dir') {
			return null;
		}
		if ($name=='FullFile') {
			return null;
		}
		if ($name=='Author') {
			return null;
		}		
		parent::__set($name, $value);
	}

	public function __get($name)
	{
        global $zbp;
		if ($name=='Url') {
			return $zbp->host . $zbp->usersdir . $this->Dir . $this->Name;
		}
		if ($name=='Dir') {
			return 'upload/' .date('m',$this->PostTime) . '/' . date('d',$this->PostTime) . '/';
		}
		if ($name=='FullFile') {
			return  $zbp->path . $zbp->usersdir . $this->Dir . $this->Name;
		}
		if ($name=='Author') {
			return $zbp->GetMemberByID($this->AuthorID);
		}
		return parent::__get($name);
	}

}
?>