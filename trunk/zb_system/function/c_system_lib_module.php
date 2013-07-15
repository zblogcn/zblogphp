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
class BaseModule
{

	static public $table='%pre%Module';

	static public $datainfo=array(
'ID'=>array('mod_ID','integer','',0),
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





/**
* Member
*/
class Module1 extends BaseModule
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

		$s="SELECT * FROM " . self::$table . " WHERE mod_ID=$id";

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
			$s="INSERT INTO " . self::$table . " (";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mod_ID') {continue;}
				$a[]=$value[0];
			}
			$s.=implode(',', $a);
			$s.=") VALUES (";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mod_ID') {continue;}
				if ($value[1]==='string') {
					$a[]='\'' . addslashes($this->$key) . '\'';	
				}else{
					$a[]=$this->$key;		
				}
			}
			$s.=implode(',', $a);
			$s.=")";

			$this->ID=$this->db->Insert($s);
		} else {
			$s="UPDATE " . self::$table . " SET ";
			$a=array();
			foreach (self::$datainfo as $key => $value) {
				if ($value[0]==='mod_ID') {continue;}
				if ($value[1]==='string') {
					$a[]=$value[0] . '=\'' . addslashes($this->$key) . '\'';
				}else{
					$a[]=$value[0] . '=' . $this->$key;	
				}
			}
			$s.=implode(',', $a);
			$s.=" WHERE mod_ID=" . $this->ID;
			$this->db->Update($s);
		}
		

	}
}




?>