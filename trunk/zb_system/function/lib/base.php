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

	protected $db = null;

	public $Metas = array();

	public $Data = array();
	
	public function __set($name, $value) 
	{
		$this->Data[$name]  =  $value;
	}

	public function __get($name) 
	{
		return $this->Data[$name];
	}

/**
 * 		返回条件搜索后的表id数组
 * @	$wheresearch=array(id=>array('>'=>"10"),name=>array('like'=>'ZB'))
 * @	$orderby=array(id=>'ASC',order=>'ASC')
 * @	$limit=array(0,30)
 */
	function GetLibIDArray($wheresearch, $orderby, $limit){	

		$s = "SELECT `". $this->datainfo['ID'][0] ."` FROM " . $this->table . "";
		
		if(!empty($wheresearch)) {
			$s .= ' WHERE ';
			foreach($wheresearch as $k=>$v) {
				if(!is_array($v)) {
					$v = addslashes($v);
					$s .= "$k = '$v' AND ";
				} else {
					foreach($v as $k1=>$v1) {
						$v1 = addslashes($v1);
						$k1 == 'LIKE' && ($k1 = ' LIKE ') && $v1 = "%$v1%";
						$s .= "$k$k1'$v1' AND ";
					}
				}
			}
			$s = substr($s, 0, -4);
		}
		
		if(!empty($orderby)) {
			$s .= ' ORDER BY ';
			$comma = '';
			foreach($orderby as $k=>$v) {
				$s .= $comma."$k $v";
				$comma = ',';
			}
		}
		$s .= ($limit ? " LIMIT $limit[0], $limit[1]" : '');
		
		foreach ($this->db->Query($s) as $key => $value) {
			$array[$key] = $value[$this->datainfo['ID'][0]];
		}
		return $array;
	}

	function LoadInfoByID($id){

		$s="SELECT * FROM " . $this->table . " WHERE " . $this->datainfo['ID'][0] . "=$id";

		$array = $this->db->Query($s);
		if (count($array)>0) {
			$this->LoadInfoByAssoc($array[0]);
		}

	}

	function LoadInfoByAssoc($array){
		foreach ($this->datainfo as $key => $value) {
			if($value[1] == 'boolean'){
				$this->Data[$key]=(boolean)$array[$value[0]];
			}else{
				$this->Data[$key]=$array[$value[0]];
			}			
		}
	}

	function LoadInfoByArray($array){
		$i = 0;
		foreach ($this->datainfo as $key => $value) {
			if($value[1] == 'boolean'){
				$this->Data[$key]=(boolean)$array[$i];
			}else{
				$this->Data[$key]=$array[$i];
			}
			$i += 1;
		}
	}	

	function Post(){

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
					$a[]='\'' . $this->db->EscapeString($this->$key) . '\'';	
				}elseif ($value[1] == 'boolean') {
					$a[]=(integer)$this->$key;
				}else{
					$a[] = $this->$key;		
				}
			}
			$s .= implode(',', $a);
			$s .= ")";
			Logs($s);
			$this->ID = $this->db->Insert($s);
		} else {
			$s="UPDATE " . $this->table . " SET ";
			$a = array();
			foreach ($this->datainfo as $key => $value) {
				if ($value[0] == $this->datainfo['ID'][0]) {continue;}
				if ($value[1] == 'string') {
					$a[]=$value[0] . '=\'' . $this->db->EscapeString($this->$key) . '\'';
				}elseif ($value[1] == 'boolean') {
					$a[]=$value[0] . '=' . (integer)$this->$key;
				}else{
					$a[]=$value[0] . '=' . $this->$key;	
				}
			}
			$s .= implode(', ', $a);
			$s .= " WHERE " . $this->datainfo['ID'][0] . "=" . $this->ID;
			Logs($s);
			return $this->db->Update($s);
		}


	}


}




?>