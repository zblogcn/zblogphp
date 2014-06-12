<?php
/**
 * pdo_MySQL数据库操作类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/DataBase 类库
 */
class Dbpdo_MySQL implements iDataBase {

	/**
	* @var string|null SQL语句分隔符
	*/
	public $dbpre = null;
	/**
	* @var string|null 数据库服务器
	*/
	private $db = null;
	/**
	* @var string|null 数据库名
	*/
	public $dbname = null;
	/**
	* @var DbSql|null 
	*/
	public $sql=null;
	/**
	* 构造函数，实例化$sql参数
	*/
	function __construct()
	{
		$this->sql=new DbSql($this);
	}

	/**
	* @param $s
	* @return string
	*/
	public function EscapeString($s){
		return addslashes($s);
	}

	/**
	* @param $array
	* @return bool
	*/
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

	/**
	* @param string $dbmysql_server
	* @param string $dbmysql_port
	* @param string $dbmysql_username
	* @param string $dbmysql_password
	* @param string $dbmysql_name
	*/
	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
		$db_link = new PDO('mysql:host=' . $dbmysql_server . ';port=' . $dbmysql_port,$dbmysql_username,$dbmysql_password,$options);
		$this->db = $db_link;
		$this->dbname=$dbmysql_name;
		$s="SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$dbmysql_name'";
		$a=$this->Query($s);
		$c=0;
		if(is_array($a)){
			$b=current($a);
			if(is_array($b)){
				$c=(int)current($b);
			}
		}
		if($c==0){
			$this->db->exec($this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
			return true;
		}
	}
	
	/**
	* 关闭数据库连接
	*/
	function Close(){
		$this->db=null;
	}

	/**
	* 拼接SQL语句
	* @param $s 
	*/
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

	/**
	* @param $query
	* @return array
	*/
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

	/**
	* @param $query
	* @return bool|mysqli_result
	*/
	function Update($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($this->sql->Filter($query));
	}

	/**
	* @param $query
	* @return bool|mysqli_result
	*/
	function Delete($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return $this->db->query($this->sql->Filter($query));
	}

	/**
	* @param $query
	* @return int 
	*/
	function Insert($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		$this->db->exec($this->sql->Filter($query));
		return $this->db->lastInsertId();
	}

	/**
	* @param $table
	* @param $datainfo
	*/
	function CreateTable($table,$datainfo){
		$this->QueryMulit($this->sql->CreateTable($table,$datainfo));
	}

	/**
	* @param $table
	*/
	function DelTable($table){
		$this->QueryMulit($this->sql->DelTable($table));
	}
	
	/**
	* @param $table
	* @return bool
	*/
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
