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

		if ($this->db = mysql_connect($array[0], $array[1], $array[2])) {
			if(mysql_select_db($array[3], $this->db)){
				$this->dbpre=$array[4];
				return true;
			} else {
				$this->Close();
				return false;
			}
		} else {
			return false;
		}


	}

	function Close(){

	}

	function CreateTable(){
		mysql_query('CREATE TABLE foo (bar varchar(10))');
	}

	function Query(){
//mysql_query('CREATE DATABASE `zblog`'); // 创建数据库
	}

	function Update(){

	}

	function Delete(){

	}


}

?>