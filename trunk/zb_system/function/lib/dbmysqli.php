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
class DbMySQLi implements iDataBase
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
		$db = mysqli_init(); 

		if($array[6]==true){
			$array[0]='p:'.$array[0];
		}

		//mysqli_options($db,MYSQLI_READ_DEFAULT_GROUP,"max_allowed_packet=50M");
		mysqli_real_connect($db,$array[0], $array[1], $array[2],$array[3],$array[5]);
		mysqli_set_charset($db,'utf8');
		$this->db=$db;
		$this->dbpre=$array[4];
		return true;
	}

	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$db = @mysqli_connect($dbmysql_server, $dbmysql_username, $dbmysql_password, null,$dbmysql_port);
		$this->db = $db;
		mysqli_query($this->db,'CREATE DATABASE ' . $dbmysql_name);
	}

	function Close(){
		mysqli_close($this->db);
	}

	function QueryMulit($s){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){
				mysqli_query($this->db,$s);
			}
		}
	}

	function Query($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		$results = mysqli_query($this->db,$query);
		$data = array();
		if($results){
			while($row = mysqli_fetch_assoc($results)){
				$data[] = $row;
			}
		}
		return $data;
	}

	function Update($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$query);
	}

	function Delete($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$query);
	}

	function Insert($query){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] +1;
		$query=str_replace('%pre%', $this->dbpre, $query);
		mysqli_query($this->db,$query);
		return mysqli_insert_id($this->db);
	}

	function CreateTable($tablename,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($tablename,$datainfo));
	}

	function DelTable($tablename){
		$this->Query($this->sql->DelTable($tablename));
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
