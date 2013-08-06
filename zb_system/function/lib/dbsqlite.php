<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */



/**
* 
*/
class DbSQLite implements iDataBase
{

	public $dbpre = null;
	private $db = null;
	
	public $sql=null;

	function __construct()
	{
		# code...
	}

	public function EscapeString($s){
		return sqlite_escape_string($s);
	}
	
	function Open($array){
		if ($this->db = sqlite_open($array[0], 0666, $sqliteerror)) {
			$this->dbpre=$array[1];
			return true;
		} else {
			return false;
		}
	}

	function Close(){
		sqlite_close($this->db);
	}

	function CreateTable($path){
		$a=explode(';',str_replace('%pre%', $this->dbpre, file_get_contents($path.'zb_system/defend/createtable/sqlite.sql')));
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){sqlite_query($this->db,$s);}
		}
	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);
		// 遍历出来
		$results = sqlite_query($this->db,$query);
		$data = array();
		if($results){
			while($row = sqlite_fetch_array($results)){
				$data[] = $row;
			}
		}
		return $data;

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return sqlite_query($this->db,$query);
	}

	function Delete($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return sqlite_query($this->db,$query);
	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		sqlite_query($this->db,$query);
		return sqlite_last_insert_rowid($this->db);
	}

}

?>