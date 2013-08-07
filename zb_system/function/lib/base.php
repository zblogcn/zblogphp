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
	public $datainfo = array();

	public $Metas = null;

	protected $Data = array();
	
	public function __set($name, $value) 
	{
		global $zbp;
		if ($name=='Meta') {
			$this->Metas->Unserialize($value);
			return ;
		}
		$this->Data[$name]  =  $value;
	}

	public function __get($name) 
	{
		global $zbp;
		if ($name=='Meta') {
			return $this->Metas->Serialize();
		}
		return $this->Data[$name];
	}


	function LoadInfoByID($id){
		global $zbp;

		$s="SELECT * FROM " . $this->table . " WHERE " . $this->datainfo['ID'][0] . "=$id";

		$array = $zbp->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByAssoc($array[0]);
		}

	}

	function LoadInfoByAssoc($array){
		global $zbp;

		foreach ($this->datainfo as $key => $value) {
			if($value[1] == 'boolean'){
				$this->Data[$key]=(boolean)$array[$value[0]];
			}elseif($value[1] == 'string'){
				$this->Data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$value[0]]);
			}else{
				$this->Data[$key]=$array[$value[0]];
			}			
		}
	}

	function LoadInfoByArray($array){
		global $zbp;

		$i = 0;
		foreach ($this->datainfo as $key => $value) {
			if($value[1] == 'boolean'){
				$this->Data[$key]=(boolean)$array[$i];
			}elseif($value[1] == 'string'){
				$this->Data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$i]);
			}else{
				$this->Data[$key]=$array[$i];
			}
			$i += 1;
		}
	}	

	function Save(){
		global $zbp;
		
		$keys=array();
		foreach ($this->datainfo as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');

		foreach ($this->datainfo as $key => $value) {
			$keyvalue[$value[0]]=$this->$key;
		}
		array_shift($keyvalue);

		if ($this->ID  ==  0) {
			$sql = $zbp->db->sql->Insert(get_class($this),$keyvalue);
			$this->ID = $zbp->db->Insert($sql);
		} else {
			$sql = $zbp->db->sql->Update(get_class($this),$keyvalue,array(array('=',$this->datainfo['ID'][0],$this->ID)));
			return $zbp->db->Update($sql);
		}

	}

	function Del(){
		global $zbp;
	}
}




?>