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
class DbSQLite3 implements iDataBase
{
	
	public $dbpre = null;
	private $db = null;

	function __construct()
	{
		# code...
	}

	function Open($array){
		if ($this->db = new SQLite3($array[0]) ){
			$this->dbpre=$array[1];
			return true;
		} else{
			return false;
		}
	}

	function Close(){
		$this->db->close();
	}

	function CreateTable(){
		CreateTable_SQLite3();
	}

	function Query(){

	}

	function Update(){

	}

	function Delete(){
		
	}

}

?>