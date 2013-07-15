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
abstract class Base
{

	public $table='';
	public $datainfo=array();

	protected $db=null;

	public $Metas=array();

	public $Data=array();
	
    public function __set($name, $value) 
    {
		$this->Data[$name] = $value;
    }

    public function __get($name) 
    {
    	return $this->Data[$name];
    }


	function LoadInfoByID($id){

		$s="SELECT * FROM " . $this->table . " WHERE " . $this->datainfo[0][0] . "=$id";

		$array=$this->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByAssoc($array[0]);
		}

	}

	function LoadInfoByAssoc($array){
		foreach ($this->datainfo as $key => $value) {
			$this->$key=$array[$value[0]];
		}
	}

	function LoadInfoByArray($array){
		$i=0;
		foreach ($this->datainfo as $key => $value) {
			$this->$key=$array[$i];
			$i+=1;
		}
	}	

	function Post(){

		if ($this->ID==0) {
			$s="INSERT INTO " . $this->table . " (";
			$a=array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0]==$this->datainfo['ID'][0]) {continue;}
				$a[]=$value[0];
			}
			$s.=implode(',', $a);
			$s.=") VALUES (";
			$a=array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0]==$this->datainfo['ID'][0]) {continue;}
				if ($value[1]=='string') {
					$a[]='\'' . addslashes($this->$key) . '\'';	
				}elseif ($value[1]=='boolean') {
					$a[]=(integer)$this->$key;
				}else{
					$a[]=$this->$key;		
				}
			}
			$s.=implode(',', $a);
			$s.=")";

			$this->ID=$this->db->Insert($s);
		} else {
			$s="UPDATE " . $this->table . " SET ";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==$this->datainfo['ID'][0]) {continue;}
				if ($value[1]=='string') {
					$a[]=$value[0] . '=\'' . addslashes($this->$key) . '\'';
				}elseif ($value[1]=='boolean') {
					$a[]=$value[0] . '=' . (integer)$this->$key;
				}else{
					$a[]=$value[0] . '=' . $this->$key;	
				}
			}
			$s.=implode(',', $a);
			$s.=" WHERE " . $this->datainfo['ID'][0] . "=" . $this->ID;
			$this->db->Update($s);
		}


	}


}

$table=array('Module'=> '%pre%Module');
$datainfo=array('Module'=> array(
'ID'=>array('mod_ID','integer','',0),
'Name'=>array('mod_Name','string',50,''),
'FileName'=>array('mod_FileName','string',50,''),
'Order'=>array('mod_Order','integer','',0),
'Content'=>array('mod_Content','string','',''),
'IsHidden'=>array('mod_IsHidden','boolean','',0),
'SidebarID'=>array('mod_SidebarID','integer','',0),
'HtmlID'=>array('mod_HtmlID','string',50,''),
'Ftype'=>array('mod_Ftype','string',5,''),
'MaxLi'=>array('mod_MaxLi','integer','',0),
'Source'=>array('mod_Source','string',50,''),
'ViewType'=>array('mod_ViewType','string',50,''),
'IsHideTitle'=>array('mod_IsHideTitle','boolean','',0),
'Meta'=>array('mod_Meta','string','',''),
	));


class Module extends Base{


	function __construct()
	{
		$this->table=&$GLOBALS['table']['Module'];	
		$this->datainfo=&$GLOBALS['datainfo']['Module'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

		$this->db = &$GLOBALS['zbp']->db;
		$this->ID = 0;
		$this->Order = 0;
	}


}




?>