<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


/**
* BaseMember
*/
class BaseMember
{

	public $ID=null;
	public $Guid=null;
	public $Name=null;
	public $Level=null;
	public $Password=null;
	public $Email=null;
	public $HomePage=null;
	public $Count=null;
	public $Alias=null;
	public $TemplateName=null;
	public $Intro=null;
	public $MetaString=null;
}



/**
* Member
*/
class Member extends BaseMember
{
	private $db;
	
	function __construct()
	{
		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		var_dump($this->db);
	}


	function LoadInfoByID($id){

	}

	function LoadInfoByArray($array){
		$this->ID=$array[0];
		$this->Guid=$array[1];
		$this->Name=$array[2];
		$this->Level=$array[3];
		$this->Password=$array[4];
		$this->Email=$array[5];
		$this->HomePage=$array[6];
		$this->Count=$array[7];
		$this->Alias=$array[8];
		$this->TemplateName=$array[9];
		$this->Intro=$array[10];
		$this->MetaString=$array[11];
	}

	function Post(){

		var_dump($this->Password);

	}
}

?>