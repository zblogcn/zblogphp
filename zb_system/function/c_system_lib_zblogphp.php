<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class ZBlogPHP{
	static private $_zbp=null;
	public $option = array();
	public $lang = array();
	public $path = null;
	public $host = null;
	public $cookiespath=null;
	public $db = null;
	public $guid=null;

	public $members=array();
	#public $members_name=array();
	public $categorys=array();
	public $tags=array();
	public $modules=array();
	public $sidebars=array(1=>'',2=>'',3=>'',4=>'',5=>'');
	public $templates=array();
	public $configs=array();
	public $cache_includes=array();
	public $template_includes=array();
	public $templatetags=array();	
	public $title=null;
	
	function __construct() {

		$this->option = &$GLOBALS['option'];
		$this->lang = &$GLOBALS['lang'];
		$this->path = &$GLOBALS['blogpath'];
		$this->host = &$GLOBALS['bloghost'];
		$this->cookiespath = &$GLOBALS['cookiespath'];

		if (trim($this->option['ZC_BLOG_CLSID'])===''){
			$this->guid=GetGuid();
		}else{
			$this->guid=$this->option['ZC_BLOG_CLSID'];
		}

		$this->option['ZC_BLOG_HOST']=&$GLOBALS['bloghost'];
		//define();
	}

	function __destruct(){
		$option = null;
		$lang = null;
		$path = null;
		$host = null;
		$db = null;
	}
	
	public function __call($method, $args) {
		throw new Exception('');
	}

	static public function GetInstance(){
		if(!isset(self::$_zbp)){
			self::$_zbp=new ZBlogPHP;
		}
		return self::$_zbp;
	}


	function OpenConnect(){
		static $isconnect=false;
		if($isconnect){return;}

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
		$isconnect=true;	
	}

	#初始化连接
	public function Initialize(){

		ActivePlugin();


		$this->OpenConnect();
		$this->LoadMembers();
		$this->LoadCategorys();

		#if (file_exists('cache')) {
		#	$this->templatetags=unserialize(file_get_contents('cache'));
		#	return;
		#}

		$this->LoadTemplates();
		$this->LoadCacheIncludes();
		$this->LoadTemplateIncludes();
		$this->LoadConfigs();
		$this->BuildTemplatetags();

		#$s=serialize($this->templatetags);
		#file_put_contents('cache', $s);


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
			$this->members[$m->ID]=$m;
			#$this->membersbyname[$m->Name]=&$m;
		}
	}

	public function LoadCategorys(){
	}

	public function LoadTemplates(){
		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/template/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
	}

	public function LoadCacheIncludes(){
		$dir=$this->path .'zb_users/include/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->cache_includes[$sortname]=file_get_contents($fullname);
		}
	}

	public function LoadTemplateIncludes(){
		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/include/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->template_includes[$sortname]=file_get_contents($fullname);
		}
	}

	public function LoadConfigs(){

		$s='SELECT * FROM %pre%Config';
		$array=$this->db->Query($s);
		foreach ($array as $c) {
			$this->configs[$c['conf_Name']]=$c['conf_Value'];
		}
	}

	public function BuildTemplatetags(){

		foreach ($this->templates as $key => $value) {
			$this->templatetags['TEMPLATE_' . strtoupper($key)]=$value;
		}

		foreach ($this->cache_includes as $key => $value) {
			$this->templatetags['CACHE_INCLUDE_' . strtoupper($key)]=$value;
		}	

		foreach ($this->option as $key => $value) {
			$this->templatetags[strtoupper($key)]=$value;
		}

		foreach ($this->lang['ZC_MSG'] as $key => $value) {
			$this->templatetags['ZC_MSG' . $key]=$value;
		}

		$this->templatetags['ZC_BLOG_SUB_NAME']=&$this->templatetags['ZC_BLOG_SUBTITLE'];
		$this->templatetags['ZC_BLOG_NAME']=&$this->templatetags['ZC_BLOG_TITLE'];

		$this->templatetags['BlogTitle']=&$this->title;

	}

}

?>