<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

/**
* BaseClass
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




?>