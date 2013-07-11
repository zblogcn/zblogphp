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
	public $Meta=null;

}



/**
* Member
*/
class Member extends BaseMember
{

	private $db;
	public $Metas=array();

	function __construct()
	{
		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		$this->Count = 0;
		var_dump($this->db);
	}

	function LoadInfoByID($id){

$s=<<<sql
SELECT 
mem_ID,
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
 FROM %pre%Member WHERE mem_ID=$id
sql;
		$array=$this->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByArray($array[0]);
		}

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
		$this->Meta=$array[12];
	}

	function Post(){

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
'$this->Meta'
)
sql;
			$this->ID=$this->db->Insert($s);
			var_dump($this->ID);
			var_dump($this->PostTime);			
		} else {

$s=<<<sql
UPDATE %pre%Member SET 
mem_Guid='$this->Guid',
mem_Level=$this->Level,
mem_Name='$this->Name',
mem_PassWord='$this->Password',
mem_Email='$this->Email',
mem_HomePage='$this->HomePage',
mem_Count=$this->Count,
mem_Alias='$this->Alias',
mem_Intro='$this->Intro',
mem_PostTime=$this->PostTime,
mem_Template='$this->Template',
mem_Meta='$this->Meta'
WHERE 
mem_ID=$this->ID
sql;
$this->db->Update($s);

		}
		

	}
}

?>