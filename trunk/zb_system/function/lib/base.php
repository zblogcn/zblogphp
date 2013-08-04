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

	public $Data = array();
	
	public function __set($name, $value) 
	{
		global $zbp;
		if ($name=='Meta') {
			$this->Metas->unserialize($value);
			return ;
		}
		$this->Data[$name]  =  $value;
	}

	public function __get($name) 
	{
		global $zbp;
		if ($name=='Meta') {
			return $this->Metas->serialize();
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

	function Post(){
		global $zbp;
		
		if ($this->ID  ==  0) {
			$s="INSERT INTO " . $this->table . " (";
			$a = array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0] == $this->datainfo['ID'][0]) {continue;}
				$a[]=$value[0];
			}
			$s .= implode(',', $a);
			$s .= ") VALUES (";
			$a = array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0] == $this->datainfo['ID'][0]) {continue;}
				if ($value[1] == 'string') {
					$a[]='\'' . $zbp->db->EscapeString(str_replace($zbp->host,'{#ZC_BLOG_HOST#}' , $this->$key)) . '\'';	
				}elseif ($value[1] == 'boolean') {
					$a[]=(integer)$this->$key;
				}else{
					$a[] = (integer)$this->$key;		
				}
			}
			$s .= implode(',', $a);
			$s .= ")";
			$this->ID = $zbp->db->Insert($s);
		} else {
			$s="UPDATE " . $this->table . " SET ";
			$a = array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0] == $this->datainfo['ID'][0]) {continue;}
				if ($value[1] == 'string') {
					$a[]=$value[0] . '=\'' . $zbp->db->EscapeString(str_replace($zbp->host,'{#ZC_BLOG_HOST#}' , $this->$key)) . '\'';
				}elseif ($value[1] == 'boolean') {
					$a[]=$value[0] . '=' . (integer)$this->$key;
				}else{
					$a[]=$value[0] . '=' . (integer)$this->$key;	
				}
			}
			$s .= implode(', ', $a);
			$s .= " WHERE " . $this->datainfo['ID'][0] . "=" . $this->ID;
			return $zbp->db->Update($s);
		}


	}


}




?>