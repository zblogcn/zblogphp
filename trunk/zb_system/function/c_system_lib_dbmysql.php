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
		foreach ($GLOBALS['TableSql_MySQL'] as $s) {
			$s=str_replace('%pre%', $this->dbpre, $s);
			mysql_query($s);
		}
	}

	function Query($query){

		$query=str_replace('%pre%', $this->dbpre, $query);

		$result = mysql_query($query);
		// 遍历出来
		$data = array();
		while($row = mysql_fetch_assoc($result)){
			$data[] = $row;
		}
		return $data;

	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($query);
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