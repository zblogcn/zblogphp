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
class DbPgSQL implements iDataBase
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

		if($array[6]==false){
			$db_link = @pg_connect("host=$array[0] port=$array[5] dbname=$array[3] user=$array[1] password=$array[2]");
		}else{
			$db_link = @pg_pconnect("host=$array[0] port=$array[5] dbname=$array[3] user=$array[1] password=$array[2]");
		}

		if(!$db_link){
			return false;
		} else {
			$this->db = $db_link;
			$this->dbpre=$array[4];
			pg_set_client_encoding($db_link,'utf8');
			return true;
		}

	}
	
	function CreateDB($dbpgsql_server,$dbpgsql_port,$dbpgsql_username,$dbpgsql_password,$dbpgsql_name){
		$db_link = @pg_connect("host=$dbpgsql_server port=$dbpgsql_port user=$dbpgsql_username password=$dbpgsql_password");
		$this->db = $db_link;
		@pg_query('CREATE DATABASE ' . $dbpgsql_name);
	}

	function Close(){

	}

	function QueryMulit($s){
		$a=explode(';',str_replace('%pre%', $this->dbpre,$s));
		foreach ($a as $s) {
			pg_query($s);
		}
	}

	function Query($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		$results = pg_query($query);
		$data = array();
		if($results){
			while($row = pg_fetch_assoc($results)){
				$data[] = $row;
			}
		}
		return $data;
	}

	function Update($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return pg_query($query);
	}

	function Delete($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		return pg_query($query);
	}

	function Insert($query){
		$query=str_replace('%pre%', $this->dbpre, $query);
		pg_query($query);
		return pg_insert_id();
	}

	function CreateTable($tablename,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($tablename,$datainfo));
	}

	function DelTable($tablename){
		$this->Query($this->sql->DelTable($tablename));
	}

	function ExistTable($tablename){
		$zbp=ZBlogPHP::GetInstance();
		$a=$this->Query($this->sql->ExistTable($tablename,$zbp->option['ZC_PGSQL_NAME']));
		if($a){
			return true;
		}else{
			return false;
		}
	}
}
