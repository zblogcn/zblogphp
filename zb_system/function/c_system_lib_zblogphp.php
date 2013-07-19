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
	public $membersbyname=array();
	public $categorys=array();
	public $tags=array();
	public $modules=array();
	public $modules_filename=array();
	public $sidebars=array(1=>'',2=>'',3=>'',4=>'',5=>'');
	public $templates=array();
	public $configs=array();
	public $cache_includes=array();
	public $template_includes=array();
	public $templatetags=array();	
	public $title=null;

	public $user=null;
	
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

		$this->title=&$GLOBALS['blogtitle'];

		$this->user=new Member();
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
			#break;
		case 'pdo_mysql':
			$db=DbFactory::Create($this->option['ZC_DATABASE_TYPE']);
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
		$this->LoadModules();

		if (isset($this->membersbyname[GetVars('username','COOKIE')])) {
			$m=$this->membersbyname[GetVars('username','COOKIE')];
			if($m->Password == md5(GetVars('password','COOKIE') . $m->Guid)){
				$this->user=$m;
			}
		}

		#$cache=$this->path . 'zb_users/cache/' . $this->guid;
		#if (file_exists($cache)) {
		#	$this->templatetags=unserialize(file_get_contents($cache));
		#	return;
		#}

		$this->LoadDefaultTemplates();
		$this->LoadTemplates();
		$this->LoadCacheIncludes();
		$this->LoadTemplateIncludes();
		$this->LoadConfigs();
		$this->BuildSidebar();	
		$this->BuildTemplatetags();




		#$s=serialize($this->templatetags);
		#file_put_contents($cache, $s);


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

		$s='SELECT * FROM ' . $GLOBALS['table']['Member'];
		$array=$this->db->Query($s);
		foreach ($array as $ma) {
			$m=new Member();
			$m->LoadInfoByAssoc($ma);
			$this->members[$m->ID]=$m;
			$this->membersbyname[$m->Name]=&$m;
		}
	}

	public function LoadCategorys(){

	}

	public function LoadModules(){
		$s='SELECT * FROM ' . $GLOBALS['table']['Module'];
		$array=$this->db->Query($s);
		foreach ($array as $ma) {
			$m=new Module();
			$m->LoadInfoByAssoc($ma);
			$this->modules[$m->ID]=$m;
			$this->modulesbyfilename[$m->FileName]=&$m;
		}
	}


	public function LoadDefaultTemplates(){
		$dir=$this->path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
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

		

		$this->templatetags['template:sidebar'] =$this->sidebars[1];
		$this->templatetags['template:sidebar2']=$this->sidebars[2];
		$this->templatetags['template:sidebar3']=$this->sidebars[3];
		$this->templatetags['template:sidebar4']=$this->sidebars[4];	
		$this->templatetags['template:sidebar5']=$this->sidebars[5];

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


	public function BuildSidebar(){

		$s=array($this->option['ZC_SIDEBAR_ORDER'],
				$this->option['ZC_SIDEBAR_ORDER2'],
				$this->option['ZC_SIDEBAR_ORDER3'],
				$this->option['ZC_SIDEBAR_ORDER4'],
				$this->option['ZC_SIDEBAR_ORDER5'] );


		foreach ($s as $k =>$v) {
			$a=explode(':', $v);
			foreach ($a as $v2) {
				$f=$this->templates['b_function'];
				$f=str_replace('<#function/content#>', '<#CACHE_INCLUDE_' . strtoupper($v2) . '#>', $f);
				$this->sidebars[($k+1)] .=$f ;
			}
		}

	}

}

?>