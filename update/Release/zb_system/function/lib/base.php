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
class Base
{

	public $table='';
	public $datainfo = array();

	public $Metas = null;

	protected $Data = array();
	
	function __construct($table,$datainfo)
	{
        global $zbp;

        $this->table=$table;
        $this->datainfo=$datainfo;

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

	}

	public function __set($name, $value)
	{
		$this->Data[$name]  =  $value;
	}

	public function __get($name) 
	{
		return $this->Data[$name];
	}

	function GetDataArray(){
		return $this->Data;
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
				if($key=='Meta'){
					$this->Data[$key]=$array[$value[0]];
				}else{
					$this->Data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$value[0]]);
				}
			}else{
				$this->Data[$key]=$array[$value[0]];
			}			
		}
		if(isset($this->Data['Meta']))$this->Metas->Unserialize($this->Data['Meta']);
		return true;
	}

	function LoadInfoByArray($array){
		global $zbp;

		$i = 0;
		foreach ($this->datainfo as $key => $value) {
			if($value[1] == 'boolean'){
				$this->Data[$key]=(boolean)$array[$i];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$this->Data[$key]=$array[$value[0]];
				}else{
					$this->Data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$value[0]]);
				}
			}else{
				$this->Data[$key]=$array[$i];
			}
			$i += 1;
		}
		if(isset($this->Data['Meta']))$this->Metas->Unserialize($this->Data['Meta']);
		return true;
	}	

	function Save(){
		global $zbp;

		if(isset($this->Data['Meta']))$this->Data['Meta'] = $this->Metas->Serialize();

		$keys=array();
		foreach ($this->datainfo as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');

		foreach ($this->datainfo as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$this->Data[$key];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$keyvalue[$value[0]]=$this->Data[$key];
				}else{
					$keyvalue[$value[0]]=str_replace($zbp->host,'{#ZC_BLOG_HOST#}',$this->Data[$key]);
				}
			}else{
				$keyvalue[$value[0]]=$this->Data[$key];
			}
		}
		array_shift($keyvalue);

		if ($this->ID  ==  0) {
			$sql = $zbp->db->sql->Insert($this->table,$keyvalue);
			$this->ID = $zbp->db->Insert($sql);
		} else {
			$sql = $zbp->db->sql->Update($this->table,$keyvalue,array(array('=',$this->datainfo['ID'][0],$this->ID)));
			return $zbp->db->Update($sql);
		}

		return true;

	}

	function Del(){
		global $zbp;
		$sql = $zbp->db->sql->Delete($this->table,array(array('=',$this->datainfo['ID'][0],$this->ID)));
		$zbp->db->Delete($sql);
		return true;
	}
}




?>