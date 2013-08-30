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

	function DelFile(){
		@unlink($this->FullFile);
	}

	function SaveFile($tmp){
		global $zbp;
		if(!file_exists($zbp->usersdir . $this->Dir)){
			@mkdir($zbp->usersdir . $this->Dir, 0777,true);	
		}
		@move_uploaded_file($tmp, $this->FullFile);			
	}

	function SaveBase64File($str64){
		global $zbp;
		if(!file_exists($zbp->usersdir . $this->Dir)){
			@mkdir($zbp->usersdir . $this->Dir, 0777,true);	
		}
		$s=base64_decode($str64);
		$this->Size=strlen($s);
		file_put_contents($zbp->usersdir . $this->Dir . $this->Name, $s);
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
			return $zbp->host . 'zb_users/' . $this->Dir . $this->Name;
		}
		if ($name=='Dir') {
			return 'upload/' .date('Y',$this->PostTime) . '/' . date('n',$this->PostTime) . '/';
		}
		if ($name=='FullFile') {
			return  $zbp->usersdir . $this->Dir . $this->Name;
		}
		if ($name=='Author') {
			return $zbp->GetMemberByID($this->AuthorID);
		}
		return parent::__get($name);
	}

}
?>