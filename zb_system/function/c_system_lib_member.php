<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */



class Member extends Base{


	function __construct()
	{
		$this->table=&$GLOBALS['table']['Member'];	
		$this->datainfo=&$GLOBALS['datainfo']['Member'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		$this->Count = 0;
		$this->Level = 0;
	}


}









/*
class BaseMember
{

	static public $table='%pre%Member';

	static public $datainfo=array(
'ID'=>array('mem_ID','integer','',0),
'Guid'=>array('mem_Guid','string',36,''),
'Name'=>array('mem_Name','string',20,''),
'Level'=>array('mem_Level','integer','',5),
'Password'=>array('mem_Password','string',32,''),
'Email'=>array('mem_Email','string',50,''),
'HomePage'=>array('mem_HomePage','string',250,''),
'Count'=>array('mem_Count','integer','',0),
'Alias'=>array('mem_Alias','string',250,''),
'Intro'=>array('mem_Intro','string','',''),
'PostTime'=>array('mem_PostTime','integer','',0),	
'Template'=>array('mem_Template','string',50,''),
'Meta'=>array('mem_Meta','string','',''),
	);
	
	public $Data=array();
	
	function __construct(){
		foreach (self::$datainfo as $key => $value) {
			$Data[$key]=$value[3];
		}
	}

    public function __set($name, $value) 
    {
		$this->Data[$name] = $value;
    }

    public function __get($name) 
    {
    	return $this->Data[$name];
    }


}




class Member extends BaseMember
{

	private $db;
	public $Metas=array();

	function __construct()
	{
		parent::__construct();
		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		$this->Count = 0;
	}

	function LoadInfoByID($id){

		$s="SELECT * FROM " . self::$table . " WHERE mem_ID=$id";

		$array=$this->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByAssoc($array[0]);
		}

	}

	function LoadInfoByAssoc($array){
		foreach (self::$datainfo as $key => $value) {
			$this->$key=$array[$value[0]];
		}
	}

	function LoadInfoByArray($array){
		$i=0;
		foreach (self::$datainfo as $key => $value) {
			$this->$key=$array[$i];
			$i+=1;
		}
	}	

	function Post(){

		if ($this->ID==0) {
/*
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

			$s="INSERT INTO " . self::$table . " (";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mem_ID') {continue;}
				$a[]=$value[0];
			}
			$s.=implode(',', $a);
			$s.=") VALUES (";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mem_ID') {continue;}
				if ($value[1]==='string') {
					$a[]='\'' . addslashes($this->$key) . '\'';	
				}else{
					$a[]=$this->$key;		
				}
			}
			$s.=implode(',', $a);
			$s.=")";

			$this->ID=$this->db->Insert($s);
			var_dump($this->ID);
			var_dump($this->PostTime);			
		} else {
/*
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

			$s="UPDATE " . self::$table . " SET ";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mem_ID') {continue;}
				if ($value[1]==='string') {
					$a[]=$value[0] . '=\'' . addslashes($this->$key) . '\'';
				}else{
					$a[]=$value[0] . '=' . $this->$key;	
				}
			}
			$s.=implode(',', $a);
			$s.=" WHERE mem_ID=" . $this->ID;
			$this->db->Update($s);
			var_dump($this->ID);
			var_dump($this->PostTime);	
		}
		

	}
}
*/




?>