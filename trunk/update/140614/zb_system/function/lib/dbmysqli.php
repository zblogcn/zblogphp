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
		$db = mysqli_init(); 

		if($array[6]==true){
			$array[0]='p:'.$array[0];
		}

		//mysqli_options($db,MYSQLI_READ_DEFAULT_GROUP,"max_allowed_packet=50M");
		mysqli_real_connect($db,$array[0], $array[1], $array[2],$array[3],$array[5]);
		mysqli_set_charset($db,'utf8');
		$this->db=$db;
		$this->dbname=$array[3];
		$this->dbpre=$array[4];
		return true;
	}

	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$db = @mysqli_connect($dbmysql_server, $dbmysql_username, $dbmysql_password, null,$dbmysql_port);
		$this->db = $db;
		$this->dbname=$dbmysql_name;
		mysqli_query($this->db,$this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
	}

	function Close(){
		mysqli_close($this->db);
	}

	function QueryMulit($s){
		//$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
		$a=explode(';',$s);
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){
				mysqli_query($this->db,$this->sql->Filter($s));
			}
		}
	}

	function Query($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		$results = mysqli_query($this->db,$this->sql->Filter($query));
		$data = array();
		if(is_object($results)){
			while($row = mysqli_fetch_assoc($results)){
				$data[] = $row;
			}
		}else{
			$data[]=$results;
		}

		//if(true==true){
		if(true!==true){
			$query="EXPLAIN " . $query;
			$results2 = mysqli_query($this->db,$this->sql->Filter($query));
			$explain=array();
			if($results2){
				while($row = mysqli_fetch_assoc($results2)){
					$explain[] = $row;
				}
			}
			logs("\r\n" . $query . "\r\n" . var_export($explain,true));
		}

		return $data;
	}

	function Update($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$this->sql->Filter($query));
	}

	function Delete($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$this->sql->Filter($query));
	}

	function Insert($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		mysqli_query($this->db,$this->sql->Filter($query));
		return mysqli_insert_id($this->db);
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
