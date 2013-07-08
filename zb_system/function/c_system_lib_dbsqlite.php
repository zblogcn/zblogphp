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
class DbMySQL implements iDataBase
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
		CreateTable_SQLite();
	}

	function Query(){

	}

	function Update(){

	}

	function Delete(){
		
	}

}

?>