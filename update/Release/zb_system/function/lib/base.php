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

	protected $table='';
	protected $datainfo = array();
	protected $data = array();

	public $Metas = null;

	function __construct(&$table,&$datainfo){
        global $zbp;

        $this->table=$table;
        $this->datainfo=$datainfo;

		$this->Metas=new Metas;

		foreach ($this->datainfo as $key => $value) {
			$this->data[$key]=$value[3];
		}
	}

	public function __set($name, $value){
		$this->data[$name]  =  $value;
	}

	public function __get($name){
		return $this->data[$name];
	}

	function GetData(){
		return $this->data;
	}
	
	function GetTable(){
		return $this->table;
	}
	
	function GetDataInfo(){
		return $this->datainfo;
	}

	function LoadInfoByID($id){
		global $zbp;

		$id=(int)$id;
		//$s="SELECT * FROM " . $this->table . " WHERE " . $this->datainfo['ID'][0] . "=$id";
		$s = $zbp->db->sql->Select($this->table,array('*'),array(array('=',$this->datainfo['ID'][0],$id)),null,null,null);

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
			if(!isset($array[$value[0]]))continue;
			if($value[1] == 'boolean'){
				$this->data[$key]=(boolean)$array[$value[0]];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$this->data[$key]=$array[$value[0]];
				}else{
					$this->data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$value[0]]);
				}
			}else{
				$this->data[$key]=$array[$value[0]];
			}
		}
		if(isset($this->data['Meta']))$this->Metas->Unserialize($this->data['Meta']);
		return true;
	}

	function LoadInfoByArray($array){
		global $zbp;

		$i = 0;
		foreach ($this->datainfo as $key => $value) {
			if(count($array)==$i)continue;
			if($value[1] == 'boolean'){
				$this->data[$key]=(boolean)$array[$i];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$this->data[$key]=$array[$i];
				}else{
					$this->data[$key]=str_replace('{#ZC_BLOG_HOST#}',$zbp->host,$array[$i]);
				}
			}else{
				$this->data[$key]=$array[$i];
			}
			$i += 1;
		}
		if(isset($this->data['Meta']))$this->Metas->Unserialize($this->data['Meta']);
		return true;
	}

	function Save(){
		global $zbp;

		if(isset($this->data['Meta']))$this->data['Meta'] = $this->Metas->Serialize();

		$keys=array();
		foreach ($this->datainfo as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');

		foreach ($this->datainfo as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$this->data[$key];
			}elseif($value[1] == 'integer'){
				$keyvalue[$value[0]]=(integer)$this->data[$key];
			}elseif($value[1] == 'float'){
				$keyvalue[$value[0]]=(float)$this->data[$key];
			}elseif($value[1] == 'double'){
				$keyvalue[$value[0]]=(double)$this->data[$key];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$keyvalue[$value[0]]=$this->data[$key];
				}else{
					$keyvalue[$value[0]]=str_replace($zbp->host,'{#ZC_BLOG_HOST#}',$this->data[$key]);
				}
			}else{
				$keyvalue[$value[0]]=$this->data[$key];
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
