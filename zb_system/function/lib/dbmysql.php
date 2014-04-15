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
	public $dbname = null;

	public $sql=null;

	function __construct()
	{
		$this->sql=new DbSql($this);
	}

	public function EscapeString($s){
		return addslashes($s);
	}

	function Open($array){
		/*$array=array(
			'dbmysql_server',
			'dbmysql_username',
			'dbmysql_password',
			'dbmysql_name',
			'dbmysql_pre',
			'dbmysql_port',
			'persistent'
		*/
		if($array[6]==false){
			$db_link = @mysql_connect($array[0] . ':' . $array[5], $array[1], $array[2]);
		}else{
			$db_link = @mysql_pconnect($array[0] . ':' . $array[5], $array[1], $array[2]);
		}

		if(!$db_link){
			return false;
		} else {
			$this->db = $db_link;
			mysql_query("SET NAMES 'utf8'",$db_link);
			if(mysql_select_db($array[3], $this->db)){
				$this->dbpre=$array[4];
				$this->dbname=$array[3];
				return true;
			} else {
				$this->Close();
				return false;
			}
		}

	}

	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$db_link = @mysql_connect($dbmysql_server . ':' . $dbmysql_port, $dbmysql_username, $dbmysql_password);
		$this->db = $db_link;
		$this->dbname=$dbmysql_name;
		mysql_query('CREATE DATABASE ' . $dbmysql_name);
	}

	function Close(){
		mysql_close($this->db);
	}

	function QueryMulit($s){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$a=explode(';',str_replace('%pre%', $this->dbpre,$s));
		foreach ($a as $s) {
			mysql_query($s);
		}
	}

	function Query($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		$results = mysql_query($query);
		$data = array();
		if(is_resource($results)){
			while($row = mysql_fetch_assoc($results)){
				$data[] = $row;
			}
		}else{
			$data[] = $results;
		}
		return $data;
	}

	function Update($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($query);
	}

	function Delete($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysql_query($query);
	}

	function Insert($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysql_query($query);
		return mysql_insert_id();
	}

	function CreateTable($tablename,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($tablename,$datainfo));
	}

	function DelTable($tablename){
		$this->QueryMulit($this->sql->DelTable($tablename));
	}

	function ExistTable($tablename){

		$a=$this->Query($this->sql->ExistTable($tablename,$this->dbname));
		if(!is_array($a))return false;
		$b=current($a);
		if(!is_array($b))return false;
		$c=(int)current($b);
		if($c>0){
			return true;
		}else{
			return false;
		}
	}
}
