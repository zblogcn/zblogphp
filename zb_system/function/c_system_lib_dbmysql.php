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

	}

	function Close(){

	}

	function CreateTable(){
		CreateTable_MySQL();
	}

	function Query(){

	}

	function Update(){

	}

	function Delete(){
		
	}


}

?>