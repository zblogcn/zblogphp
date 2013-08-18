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
		//if ($name=='Meta') {
		//	$this->Metas->Unserialize($value);
		//}
		$this->Data[$name]  =  $value;
	}

	public function __get($name) 
	{
		global $zbp;
		//if ($name=='Meta') {
		//	$this->Data['Meta'] = $this->Metas->Serialize();
		//}
		return $this->Data[$name];
	}


	function LoadInfoByID($id){
		global $zbp;

		$s="SELECT * FROM " . $this->table . " WHERE " . $this->datainfo['ID'][0] . "=$id";

		$array = $zbp->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByAssoc($array[0]);
			return true;
		}else{
			return false;
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
		$this->Metas->Unserialize($this->Data['Meta']);
		return true;
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
		$this->Metas->Unserialize($this->Data['Meta']);
		return true;
	}	

	function Save(){
		global $zbp;

		$this->Data['Meta'] = $this->Metas->Serialize();

		$keys=array();
		foreach ($this->datainfo as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');

		foreach ($this->datainfo as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$this->Data[$key];
			}else{
				$keyvalue[$value[0]]=$this->Data[$key];
			}
		}
		array_shift($keyvalue);

		if ($this->ID  ==  0) {
			$sql = $zbp->db->sql->Insert(get_class($this),$keyvalue);
			$this->ID = $zbp->db->Insert($sql);
		} else {
			$sql = $zbp->db->sql->Update(get_class($this),$keyvalue,array(array('=',$this->datainfo['ID'][0],$this->ID)));
			return $zbp->db->Update($sql);
		}

		return true;

	}

	function Del(){
		global $zbp;
		$sql = $zbp->db->sql->Delete(get_class($this),array(array('=',$this->datainfo['ID'][0],$this->ID)));
		$zbp->db->Delete($sql);
		return true;
	}
}




?>