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
	
	function __construct()
	{
		# code...
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

	function CreateTable(){
		#sqlite_query($this->db, 'CREATE TABLE foo (bar varchar(10))');

		foreach ($GLOBALS['TableSql_SQLite'] as $s) {
			$s=str_replace('%pre%', $this->dbpre, $s);
			sqlite_query($this->db, $s);
		}

	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);

		$result = sqlite_query($this->db,$query);
		// 遍历出来
		$data = array();
		while($row = sqlite_fetch_array($result)){
			echo "string";
			$data[] = $row;
		}
		return $data;

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		sqlite_query($this->db,$query);
	}

	function Delete($query){
		
	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		sqlite_query($this->db,$query);
		return sqlite_last_insert_rowid($this->db);
	}

}

?>