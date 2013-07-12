<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class ZBlogPHP{
	// 当前应用的配置
	public $option = array();
	public $lang = array();
	public $path = null;
	public $host = null;
	public $cookiespath=null;
	public $db = null;
	public $guid=null;

	public $members=array();
	public $categorys=array();
	public $tags=array();
	public $modules=array();	
	public $sidebars=array(1=>'',2=>'',3=>'',4=>'',5=>'');
	public $templates=array();
	public $configs=array();
	
	function __construct() {

		$this->option = &$GLOBALS['c_option'];
		$this->lang = &$GLOBALS['c_lang'];
		$this->path = &$GLOBALS['blogpath'];
		$this->host = &$GLOBALS['bloghost'];
		$this->cookiespath = &$GLOBALS['cookiespath'];

		if (trim($this->option['ZC_BLOG_CLSID'])===''){
			$this->guid=GetGuid();
		}else{
			$this->guid=$this->option['ZC_BLOG_CLSID'];
		}
		//define();
	}

	function __destruct(){
		$c_option = null;
		$c_land = null;
		$path = null;
		$host = null;
		
	}
	
	public function __get($var) {

	}
	
	public function __call($method, $args) {
		throw new Exception('');
	}


	#初始化连接
	public function Initialize(){

		ActivePlugin();

		switch ($this->option['ZC_DATABASE_TYPE']) {
			case 'mysql':
				$db=DbFactory::Create('mysql');
				$this->db=&$db;
				if($db->Open(array(
						$this->option['ZC_MYSQL_SERVER'],
						$this->option['ZC_MYSQL_USERNAME'],
						$this->option['ZC_MYSQL_PASSWORD'],
						$this->option['ZC_MYSQL_NAME'],
						$this->option['ZC_MYSQL_PRE']
					))==false){
					throw new Exception('MySQL数据库打不开啦！');
				}

			break;
			case 'sqlite':
				$db=DbFactory::Create('sqlite');
				$GLOBALS['zbp']->db=&$db;
				if($db->Open(array(
					$this->path . $this->option['ZC_SQLITE_NAME'],
					$this->option['ZC_SQLITE_PRE']
					))==false){
					throw new Exception('SQLite数据库打不开啦！');
				}
			break;
			case 'sqlite3':
				$this->db=DbFactory::Create('sqlite3');
				if($this->db->Open(array(
					$this->path . $this->option['ZC_SQLITE3_NAME'],
					$this->option['ZC_SQLITE3_PRE']
					))==false){
					throw new Exception('SQLite3数据库打不开啦！');
				}
			break;
		}

		$this->LoadMembers();
	}


	#终止连接，释放资源
	public function Terminate(){
		$this->db->Close();
	}


	public function SaveConfig(){

		$this->option['ZC_BLOG_CLSID']=$this->guid;


		$s="<?php\r\n";
		$s.="return ";
		$s.=var_export($this->option,true);
		$s.="\r\n?>";

		file_put_contents($this->path . 'zb_users/c_option.php',$s);
	}	

	public function LoadMembers(){

		$s='SELECT * FROM %pre%Member';
		$array=$this->db->Query($s);
		foreach ($array as $ma) {
			$m=new Member();
			$m->LoadInfoByAssoc($ma);
			//array_push($this->members,$m);
			$this->members[$m->ID]=$m;
		}

		var_dump($this->members);

	}


}

?>