<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class ZBlogPHP{
	static private $_zbp=null;
	public $db = null;
	public $option = array();
	public $lang = array();
	public $path = null;
	public $host = null;
	public $cookiespath=null;
	public $guid=null;

	public $members=array();
	public $membersbyname=array();
	public $categorys=array();
	public $tags=array();
	#public $modules=array();
	public $modulesbyfilename=array();
	public $templates=array();
	public $configs=array();
	public $_configs=array();	
	public $cache_includes=array();
	public $template_includes=array();
	public $templatetags=array();	
	public $title=null;

	public $user=null;
	public $cache=array();
	#cache={name,value,time}

	public $table=null;
	public $datainfo=null;

	public $templatepath=null;

	public $isconnect=false;
	public $isdelay_savecache=false;	
	
	static public function GetInstance(){
		if(!isset(self::$_zbp)){
			self::$_zbp=new ZBlogPHP;
		}
		return self::$_zbp;
	}
	
	function __construct() {

		$this->option = &$GLOBALS['option'];
		$this->lang = &$GLOBALS['lang'];
		$this->path = &$GLOBALS['blogpath'];
		$this->host = &$GLOBALS['bloghost'];
		$this->cookiespath = &$GLOBALS['cookiespath'];

		$this->table=&$GLOBALS['table'];
		$this->datainfo=&$GLOBALS['datainfo'];

		if (trim($this->option['ZC_BLOG_CLSID'])==''){
			$this->guid=GetGuid();
		}else{
			$this->guid=&$this->option['ZC_BLOG_CLSID'];
		}

		$this->option['ZC_BLOG_HOST']=&$GLOBALS['bloghost'];
		//define();

		$this->title=&$GLOBALS['blogtitle'];
		
		$this->user=new Member();

		$this->templatepath=$this->path . 'zb_users/' . $this->option['ZC_TEMPLATE_DIRECTORY'] . '/';


		$this->title=$this->option['ZC_BLOG_TITLE'] . '-' . $this->option['ZC_BLOG_SUBTITLE'];
	}


	function __destruct(){
		$db = null;
	}

	function __call($method, $args) {
		throw new Exception('');
	}

	#初始化连接
	public function Initialize(){

		ActivePlugin();

		$this->LoadCache();

		$this->OpenConnect();
		$this->LoadMembers();
		$this->LoadCategorys();
		$this->LoadModules();
		$this->LoadConfigs();

		if (isset($this->membersbyname[GetVars('username','COOKIE')])) {
			$m=$this->membersbyname[GetVars('username','COOKIE')];
			if($m->Password == md5(GetVars('password','COOKIE') . $m->Guid)){
				$this->user=$m;
			}
		}


		$this->LoadTemplates();
		#$this->LoadCacheIncludes();
		#$this->LoadTemplateIncludes();
		$this->MakeTemplatetags();

	}


	#终止连接，释放资源
	public function Terminate(){
		if($this->isconnect){
			$this->db->Close();
		}
		if($this->isdelay_savecache){
			$this->SaveCache();
		}		

	}


	public function GetCache($name){
		if(array_key_exists($name,$this->cache)){
			return $this->cache[$name];
		}
	}
	public function GetCacheValue($name){

		if(array_key_exists($name,$this->cache)){

			return $this->cache[$name]['value'];
		}
	}
	public function GetCacheTime($name){
		if(array_key_exists($name,$this->cache)){
			return $this->cache[$name]['time'];
		}
	}
	public function SetCache($name,$value){
		$time=time();
		$this->cache[$name]=array('value'=>$value,'time'=>$time);
	}
	public function DelCache($name){
		unset($this->cache[$name]);
	}
	public function SaveCache($delay=false){

		if($delay==true){
			$this->isdelay_savecache=true;
		}else{
			$s=$this->path . 'zb_users/cache/' . $this->guid . '.cache';
			$c=serialize($this->cache);
			file_put_contents($s, $c);
			$this->isdelay_savecache=false;
		}

	}
	public function LoadCache(){
		$s=$this->path . 'zb_users/cache/' . $this->guid . '.cache';
		if (file_exists($s)) {
			$this->cache=unserialize(file_get_contents($s));
		}
	}


	public function OpenConnect(){

		if($this->isconnect){return;}

		switch ($this->option['ZC_DATABASE_TYPE']) {
		case 'mysql':
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
			$this->db=&$db;
			if($db->Open(array(
				$this->path . $this->option['ZC_SQLITE_NAME'],
				$this->option['ZC_SQLITE_PRE']
				))==false){
				throw new Exception('SQLite数据库打不开啦！');
			}
			break;
		case 'sqlite3':
			$db=DbFactory::Create('sqlite3');
			$this->db=&$db;
			if($db->Open(array(
				$this->path . $this->option['ZC_SQLITE3_NAME'],
				$this->option['ZC_SQLITE3_PRE']
				))==false){
				throw new Exception('SQLite3数据库打不开啦！');
			}
			break;
		}
		$this->isconnect=true;	
	}


	public function SaveOption(){

		$this->option['ZC_BLOG_CLSID']=$this->guid;

		$s="<?php\r\n";
		$s.="return ";
		$s.=var_export($this->option,true);
		$s.="\r\n?>";

		file_put_contents($this->path . 'zb_users/c_option.php',$s);
	}	

	public function LoadMembers(){

		$s='SELECT * FROM ' . $this->table['Member'];
		$array=$this->db->Query($s);
		foreach ($array as $ma) {
			$m=new Member();
			$m->LoadInfoByAssoc($ma);
			$this->members[$m->ID]=$m;
			$this->membersbyname[$m->Name]=&$this->members[$m->ID];
		}
	}

	public function LoadCategorys(){
		$s='SELECT * FROM ' . $this->table['Category'];
		$array=$this->db->Query($s);
		foreach ($array as $ca) {
			$c=new Category();
			$c->LoadInfoByAssoc($ca);
			$this->categorys[$c->ID]=$c;
		}
	}

	public function LoadModules(){
		$s='SELECT * FROM ' . $this->table['Module'];
		$array=$this->db->Query($s);
		foreach ($array as $ma) {
			$m=new Module();
			$m->LoadInfoByAssoc($ma);
			#$this->modules[$m->ID]=$m;
			#$this->modulesbyfilename[$m->FileName]=&$this->modules[$m->ID];
			$this->modulesbyfilename[$m->FileName]=$m;
		}

		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/include/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$m=new Module();
			$m->FileName=$sortname;
			$m->Content=file_get_contents($fullname);
			$m->Type='div';
			#$this->template_includes[$sortname]=file_get_contents($fullname);

			#$this->modules[$m->ID]=$m;
			#$this->modulesbyfilename[$m->FileName]=&$this->modules[$m->ID];
			$this->modulesbyfilename[$m->FileName]=$m;
		}


	}


	public function LoadTemplates(){
		#先读默认的
		$dir=$this->path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		#再读当前的
		$dir=$this->path .'zb_users/theme/' . $this->option['ZC_BLOG_THEME'] . '/template/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}

	}



	public function LoadConfigs(){

		$s='SELECT * FROM %pre%Config';
		$array=$this->db->Query($s);
		foreach ($array as $c) {
			$this->configs[$c['conf_Name']]=$c['conf_Value'];
			$this->_configs[$c['conf_Name']]=$c['conf_Value'];			
		}
	}

	public function SaveConfigs(){

		foreach ($this->configs as $name => $value) {
			if(isset($this->_configs[$name])){
				#update
			}else{
				#insert
			}
		}

		$this->_configs=$this->configs;
	}


	public function MakeTemplatetags(){


		foreach ($this->modulesbyfilename as $key => $mod) {
			$this->templatetags[strtoupper($key)]=$mod->Content;
		}

		foreach ($this->option as $key => $value) {
			$this->templatetags[strtoupper($key)]=$value;
		}

		#$this->templatetags['ZC_BLOG_SUB_NAME']=&$this->templatetags['ZC_BLOG_SUBTITLE'];
		#$this->templatetags['ZC_BLOG_NAME']=&$this->templatetags['ZC_BLOG_TITLE'];
		$this->templatetags['blogtitle']=&$this->title;

	}









	public function CompileFile($content){
		foreach ($this->templates as $name => $file) {
			$content=str_ireplace('{template:' . $name . '}', '<?php include $this->IncludeTemplate("' . $name . '");?>', $content);
		}

		foreach ($this->modulesbyfilename as $key => $value) {
			$content=str_ireplace('{module:' . $key . '}', '<?php echo $this->IncludeModule("' . $key . '");?>', $content);
		}

		foreach ($this->templatetags as $key => $value) {
			$content=str_ireplace('<#' . $key . '#>', '<?php echo $this->templatetags["' . $key . '"];?>', $content);
			$content=str_ireplace('{#' . $key . '#}', '<?php echo $this->templatetags["' . $key . '"];?>', $content);			
		}
		#正则替换{$变量}
		$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $\\1; ?>', $content);
		return $content;
	}



	public function Compiling(){

		#先生成sidebar1-5
		$sidebars=array(1=>'',2=>'',3=>'',4=>'',5=>'');
		$s=array($this->option['ZC_SIDEBAR_ORDER'],
				$this->option['ZC_SIDEBAR_ORDER2'],
				$this->option['ZC_SIDEBAR_ORDER3'],
				$this->option['ZC_SIDEBAR_ORDER4'],
				$this->option['ZC_SIDEBAR_ORDER5'] );

		foreach ($s as $k =>$v) {
			$a=explode(':', $v);
			foreach ($a as $v2) {
				if(isset($this->modulesbyfilename[$v2])){
					$f='<?php $this->IncludeModuleFull(\'' . $v2 . '\');?>' . "\r\n";
				}
				$sidebars[($k+1)] .=$f ;
			}
		}
		$this->templates['sidebar']=$sidebars[1];
		$this->templates['sidebar2']=$sidebars[2];
		$this->templates['sidebar3']=$sidebars[3];
		$this->templates['sidebar4']=$sidebars[4];
		$this->templates['sidebar5']=$sidebars[5];

		#把所有模板编辑到template目录下
		foreach ($this->templates as $name => $file) {
			$f=$this->CompileFile($file);
			file_put_contents($this->templatepath . $name . '.php', $f, LOCK_EX);
		}

	}






	function ViewList($page,$cate,$auth,$date,$tags){

		foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($page,$cate,$auth,$date,$tags);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		$this->title=$this->option['ZC_BLOG_SUBTITLE'];
		$html=null;

/*
		if(isset($this->templatetags['TEMPLATE_DEFAULT'])){$html=$this->templatetags['TEMPLATE_DEFAULT'];}

		foreach ($this->templatetags as $key => $value) {
			$html=str_replace('<#' . $key . '#>', $value, $html);
		}
*/

		include $this->templatepath . $this->option['ZC_INDEX_DEFAULT_TEMPLATE'] . '.php';
		#return $html;

	}

	function ViewArticle(){


	}

	function ViewPage(){


	}
		
	function IncludeTemplate($name){
		return $this->templatepath . $name . '.php';
	}

	function IncludeModule($name){
		if(isset($this->modulesbyfilename[$name])){
			$c=$this->modulesbyfilename[$name]->Content;
			return str_replace('{#ZC_BLOG_HOST#}', $this->host, $c);
		}
	}
	function IncludeModuleFull($name){
		if(isset($this->modulesbyfilename[$name])){
			$module=$this->modulesbyfilename[$name];
		}else{
			$module=new Module;
		}		
		include $this->templatepath . 'b_module' . '.php';
	}
}

?>