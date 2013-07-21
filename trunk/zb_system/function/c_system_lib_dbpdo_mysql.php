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
class Dbpdo_MySQL implements iDataBase
{
	
	public $dbpre = null;
	private $db = null;

	function __construct()
	{
		# code...
	}

	function Open($array){
		/*$array=array(
		GetVars('dbmysql_server','POST'),
		GetVars('dbmysql_username','POST'),
		GetVars('dbmysql_password','POST'),
		GetVars('dbmysql_name','POST'),
		GetVars('dbmysql_pre','POST'));
		*/

		//new PDO(DB_TYPE.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWD);

		$db_link = new PDO('mysql:host='.$array[0].';dbname='.$array[3],$array[1],$array[2]);
		$db_link->query('set names utf8;');
		$this->db = $db_link;		
		$this->dbpre=$array[4];
		return true;
		



	}

	function Close(){

	}

	function CreateTable($path){
		$a=explode(';',str_replace('%pre%', $this->dbpre, file_get_contents($path.'zb_system/defend/createtable/mysql.sql')));

		foreach ($a as $s) {
			$this->db->exec($s);
		}
	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);
		$result = $this->db->query($query);
		// 遍历出来

		//fetch || fetchAll
		if($result){
			return $result->fetchAll();
		}
		else{
			return array();
		}

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($query);
	}

	function Delete($query){

	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		$this->db->exec($query);
		return $this->db->lastInsertId();
	}

}

?>