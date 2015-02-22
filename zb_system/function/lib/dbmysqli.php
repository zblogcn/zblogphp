<?php
/**
 * MySQLi数据库操作类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/DataBase 类库
 */
class DbMySQLi implements iDataBase {

	public $type = 'mysql';

	/**
	 * @var string|null 数据库名前缀
	 */
	public $dbpre = null;
	private $db = null; #数据库连接实例
	/**
	* @var string|null 数据库名
	*/
	public $dbname = null;
	/**
	* @var string|null 数据库引擎
	*/
	public $dbengine = null;
	/**
	* @var DbSql|null DbSql实例
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
	 * 对字符串进行转义，在指定的字符前添加反斜杠，即执行addslashes函数
	 * @param string $s
	 * @return string
	 */
	public function EscapeString($s){
		return addslashes($s);
	}

	/**
	 * 连接数据库
	 * @param array $array 数据库连接配置
	 *              $array=array(
	 *                  'dbmysql_server',
	 *                  'dbmysql_username',
	 *                  'dbmysql_password',
	 *                  'dbmysql_name',
	 *                  'dbmysql_pre',
	 *                  'dbmysql_port',
	 *                  'persistent'
						'engine')
	 * @return bool
	 */
	function Open($array){
		$db = mysqli_init();

		if($array[6]==true){
			$array[0]='p:'.$array[0];
		}

		//mysqli_options($db,MYSQLI_READ_DEFAULT_GROUP,"max_allowed_packet=50M");
		if( @mysqli_real_connect($db,$array[0], $array[1], $array[2],$array[3],$array[5]) ){
			mysqli_set_charset($db,'utf8');
			$this->db=$db;
			$this->dbname=$array[3];
			$this->dbpre=$array[4];
			$this->dbengine = $array[7];
			return true;
		}
	}

	/**
	 * 创建数据库
	 * @param string $dbmysql_server
	 * @param string $dbmysql_port
	 * @param string $dbmysql_username
	 * @param string $dbmysql_password
	 * @param string $dbmysql_name
	 * @return bool
	 */
	function CreateDB($dbmysql_server,$dbmysql_port,$dbmysql_username,$dbmysql_password,$dbmysql_name){
		$db = mysqli_connect($dbmysql_server, $dbmysql_username, $dbmysql_password, null,$dbmysql_port);
		$this->db = $db;
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
			mysqli_query($this->db,$this->sql->Filter('CREATE DATABASE ' . $dbmysql_name));
			return true;
		}
	}

	/**
	 * 关闭数据库连接
	 */
	function Close(){
		if(is_object($this->db))
			mysqli_close($this->db);
	}

	/**
	 * 执行多行SQL语句
	 * @param string $s 以;号分隔的多条SQL语句
	 * @return array
	 */
	function QueryMulit($s){return $this->QueryMulti($s);}//错别字函数，历史原因保留下来
	function QueryMulti($s){
		//$a=explode(';',str_replace('%pre%', $this->dbpre, $s));
		$a=explode(';',$s);
		foreach ($a as $s) {
			$s=trim($s);
			if($s<>''){
				mysqli_query($this->db,$this->sql->Filter($s));
			}
		}
	}

	/**
	 * @param $query
	 * @return array
	 */
	function Query($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		$results = mysqli_query($this->db,$this->sql->Filter($query));
		if(mysqli_errno($this->db))trigger_error(mysqli_error($this->db),E_USER_NOTICE);
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

	/**
	 * @param $query
	 * @return bool|mysqli_result
	 */
	function Update($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$this->sql->Filter($query));
	}

	/**
	 * @param $query
	 * @return bool|mysqli_result
	 */
	function Delete($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		return mysqli_query($this->db,$this->sql->Filter($query));
	}

	/**
	 * @param $query
	 * @return int|string
	 */
	function Insert($query){
		//$query=str_replace('%pre%', $this->dbpre, $query);
		mysqli_query($this->db,$this->sql->Filter($query));
		return mysqli_insert_id($this->db);
	}

	/**
	 * @param $table
	 * @param $datainfo
	 */
	function CreateTable($table,$datainfo,$engine=null){
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
