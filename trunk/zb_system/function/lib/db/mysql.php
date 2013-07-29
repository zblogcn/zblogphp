<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

// TODO:
// mysql_connect将被替换
// 需要添加mysqli或PDO_mysql
// http://php.net/manual/zh/function.mysql-connect.php

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

		$db_link = @mysql_connect($array[0], $array[1], $array[2]);

		if(!$db_link){
			return false;
		} else {
			$this->db = $db_link;
			mysql_query("SET NAMES 'utf8'",$db_link);
			if(mysql_select_db($array[3], $this->db)){
				$this->dbpre=$array[4];
				return true;
			} else {
				$this->Close();
				return false;
			}	
		}



	}

	function Close(){

	}

	function CreateTable($path){
		$a=explode(';',str_replace('%pre%', $this->dbpre, file_get_contents($path.'zb_system/defend/createtable/mysql.sql')));
		foreach ($a as $s) {
			mysql_query($s);
		}
	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);

		$results = mysql_query($query);
		// 遍历出来print_r($query);die();
		$data = array();
		if($results){
			while($row = mysql_fetch_assoc($results)){
				$data[] = $row;
			}
		}
		return $data;

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($query);
	}

	function Delete($query){

	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($query);
		return mysql_insert_id();
	}

}

?>