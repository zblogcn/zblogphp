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
			$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
		}else{
			$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_PERSISTENT => true);
		}
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_PERSISTENT => true);
		$db_link = new PDO('mysql:host=' . $array[0] . ';port=' . $array[5] . ';dbname=' . $array[3],$array[1],$array[2],$options);
		$this->db = $db_link;
		$this->dbpre=$array[4];
		$this->dbname=$array[3];
		return true;
	}

	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
		$db_link = new PDO('mysql:host=' . $dbmysql_server . ';port=' . $dbmysql_port,$dbmysql_username,$dbmysql_password,$options);
		$this->db = $db_link;
		$this->dbname=$dbmysql_name;
		$this->db->exec($this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
	}

	function Close(){
		$this->db=null;
	}

	function QueryMulit($s){
		//$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
		$a=explode(';',$s);
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){
				$this->db->exec($this->sql->Filter($s));
			}
		}
	}

	function Query($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		// 遍历出来
		$results = $this->db->query($this->sql->Filter($query));
		//fetch || fetchAll
		if(is_object($results)){
			return $results->fetchAll();
		}else{
			return array($results);
		}

	}

	function Update($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($this->sql->Filter($query));
	}

	function Delete($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($this->sql->Filter($query));
	}

	function Insert($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		$this->db->exec($this->sql->Filter($query));
		return $this->db->lastInsertId();
	}

	function CreateTable($table,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($table,$datainfo));
	}

	function DelTable($table){
		$this->QueryMulit($this->sql->DelTable($table));
	}

	function ExistTable($table){

		$a=$this->Query($this->sql->ExistTable($table,$this->dbname));
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
