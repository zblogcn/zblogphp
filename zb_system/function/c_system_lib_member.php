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
	public $Intro=null;
	public $PostTime=null;	
	public $Template=null;
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
		$this->Count = 0;
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
		$this->Intro=$array[9];
		$this->PostTime=$array[10];
		$this->Template=$array[11];
		$this->MetaString=$array[12];
	}

	function Post(){
$this->PostTime=time();
		var_dump($this->Password);
		if ($this->ID==0) {
			$s=<<<sql
INSERT INTO %pre%Member(
mem_Guid,
mem_Level,
mem_Name,
mem_PassWord,
mem_Email,
mem_HomePage,
mem_Count,
mem_Alias,
mem_Intro,
mem_PostTime,
mem_Template,
mem_Meta
) VALUES (
'$this->Guid',
$this->Level,
'$this->Name',
'$this->Password',
'$this->Email',
'$this->HomePage',
$this->Count,
'$this->Alias',
'$this->Intro',
$this->PostTime,
'$this->Template',
'$this->MetaString'
)
sql;
			$this->ID=$this->db->Insert($s);
			var_dump($this->ID);
		} else {

		}
		

	}
}

?>