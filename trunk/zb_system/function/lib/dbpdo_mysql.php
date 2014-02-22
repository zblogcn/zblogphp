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

	public $sql=null;

	function __construct()
	{
		$this->sql=new DbSql;
		$this->sql->type=__CLASS__;
	}

	public function EscapeString($s){
		return addslashes($s);
	}

	function Open($array){
		/*$array=array(
		GetVars('dbmysql_server','POST'),
		GetVars('dbmysql_username','POST'),
		GetVars('dbmysql_password','POST'),
		GetVars('dbmysql_name','POST'),
		GetVars('dbmysql_pre','POST'));
		GetVars('dbmysql_port','POST'));
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
		return true;
	}

	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
		$db_link = new PDO('mysql:host=' . $dbmysql_server . ';port=' . $dbmysql_port,$dbmysql_username,$dbmysql_password,$options);
		$this->db = $db_link;
		$this->db->exec('CREATE DATABASE ' . $dbmysql_name);
	}

	function Close(){

	}

	function QueryMulit($s){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){$this->db->exec($s);}
		}
	}

	function Query($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		// 遍历出来
		$results = $this->db->query($query);
		//fetch || fetchAll
		if($results){
			return $results->fetchAll();
		}
		else{
			return array();
		}

	}

	function Update($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($query);
	}

	function Delete($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($query);
	}

	function Insert($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		$this->db->exec($query);
		return $this->db->lastInsertId();
	}

	function CreateTable($tablename,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($tablename,$datainfo));
	}

	function DelTable($tablename){
		$this->QueryMulit($this->sql->DelTable($tablename));
	}

	function ExistTable($tablename){
		$zbp=ZBlogPHP::GetInstance();
		$a=$this->Query($this->sql->ExistTable($tablename,$zbp->option['ZC_MYSQL_NAME']));
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
