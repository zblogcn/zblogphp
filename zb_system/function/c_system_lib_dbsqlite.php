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

	function Query(){

	}

	function Update(){

	}

	function Delete(){
		
	}

	function Insert($query){
		
	}

}

?>