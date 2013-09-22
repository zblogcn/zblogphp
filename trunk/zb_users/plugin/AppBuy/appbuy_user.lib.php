<?php

//global $zbp;

$table['appbuyuser']='%pre%appbuyuser';
$datainfo['appbuyuser']=array(
	'ID' => array('sp_ID','integer','',0),
	'AlipayName' => array('sp_AlipayName','string',24,''),
	'AlipayID' => array('sp_AlipayID','string',16,''),
	'AlipayUserName' => array('sp_AlipayUserName','string',50,''),
	'Email' => array('sp_Email','string',50,''),
	'Guid' => array('sp_Guid','string',36,''),
	'Password' => array('sp_Password','string',32,''),
	'Domain' => array('sp_Domain','string',64,''),
	'CreatTime' => array('sp_CreatTime','integer','',0),
	'LoginTime' => array('sp_LoginTime','integer','',0),
	'CreatIP' => array('sp_CreatIP','string',15,''),
	'LoginIP' => array('sp_LoginIP','string',15,''),
	'Status' => array('sp_Status','integer','','1'),
);
class AppBuyUser extends Base{

	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['appbuyuser'];
		$this->datainfo=&$zbp->datainfo['appbuyuser'];
		
		$this->Metas=new Metas;
		
		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->ID = 0;
	}
	
	public function __set($name, $value)
	{
        global $zbp;
		parent::__set($name, $value);
	}
	
	public function __get($name)
	{
        global $zbp;
		if ($name=='DomainList') {
			$Domainlist = explode("|", $this->Domain);
			return $Domainlist;
		}

		return parent::__get($name);
	}
	
	static function AppBuyGetPassWord($id,$guid){

		return md5(md5($id). $guid);

	}
	
	function VerifyUser(){
		$this->LoadInfoByID($_COOKIE["appbuy_id"]);
		$pw = md5($this->Password);
		if($pw == $_COOKIE["appbuy_pw"]){
			return true;
		}else{
			return false;
		}
	}
}



?>