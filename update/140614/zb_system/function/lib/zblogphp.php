<?php
/**
 * zbp全局操作类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib 类库
 */
class ZBlogPHP {

	private static $_zbp=null;
	/**
	 * @var null|string 版本号
	 */
	public $version=null;
	/**
	 * @var null 数据库
	 */
	public $db = null;
	/**
	 * @var array 配置选项
	 */
	public $option = array();
	/**
	 * @var array 语言
	 */
	public $lang = array();
	/**
	 * @var null|string 路径
	 */
	public $path = null;
	/**
	 * @var null|string 域名
	 */
	public $host = null;
	/**
	 * @var null cookie作用域
	 */
	public $cookiespath=null;
	/**
	 * @var null guid
	 */
	public $guid=null;
	/**
	 * @var null|string 当前链接
	 */
	public $currenturl=null;
	/**
	 * @var null|string 用户目录
	 */
	public $usersdir = null;
	/**
	 * @var null 验证码地址
	 */
	public $validcodeurl = null;
	/**
	 * @var null
	 */
	public $feedurl = null;
	/**
	 * @var null
	 */
	public $searchurl = null;
	/**
	 * @var null
	 */
	public $ajaxurl = null;

	/**
	 * @var array 用户数组
	 */
	public $members=array();
	/**
	 * @var array 用户数组（以用户名为键）
	 */
	public $membersbyname=array();
	/**
	 * @var array 分类数组
	 */
	public $categorys=array();
	/**
	 * @var array 分类数组（已排序）
	 */
	public $categorysbyorder=array();
	/**
	 * @var int 分类最大层数
	 */
	public $categorylayer=0;
	/**
	 * @var array 模块数组
	 */
	public $modules=array();
	/**
	 * @var array 模块数组（以文件名为键）
	 */
	public $modulesbyfilename=array();
	/**
	 * @var array 配置选项
	 */
	public $configs=array();
	/**
	 * @var array 标签数组
	 */
	public $tags=array();
	/**
	 * @var array 标签数组（以标签名为键）
	 */
	public $tagsbyname=array();
	/**
	 * @var array 评论数组
	 */
	public $comments = array();
	/**
	 * @var array 文章列表数组
	 */
	public $posts=array();

	/**
	 * @var null|string 当前页面标题
	 */
	public $title=null;
	/**
	 * @var null 网站名
	 */
	public $name=null;
	/**
	 * @var null 网站子标题
	 */
	public $subname=null;
	/**
	 * @var null 当前主题
	 */
	public $theme = null;
	/**
	 * @var null 当前主题风格
	 */
	public $style = null;

	/**
	 * @var null 当前用户
	 */
	public $user=null;
	/**
	 * @var Metas|null 缓存
	 */
	public $cache=null;

	private $readymodules=array(); #模块
	private $readymodules_function=array(); #模块函数
	private $readymodules_parameters=array(); #模块函数的参数

	/**
	 * @var array|null 数据表
	 */
	public $table=null;
	/**
	 * @var array|null 数据表信息
	 */
	public $datainfo=null;
	/**
	 * @var array|null 操作列表
	 */
	public $actions=null;
	/**
	 * @var mixed|null|string 当前操作
	 */
	public $action=null;

	private $isinitialize=false; #是否初始化成功
	private $isconnect=false; #是否连接成功
	private $isload=false; #是否载入
	private $issession=false; #是否使用session
	public $ismanage=false; #是否管理员
	private $isgzip=false; #是否开启gzip
	private $isgziped=false; #是否已经过gzip压缩

	/**
	 * @var null 当前模板
	 */
	public $template = null;
	/**
	 * @var array 模板列表
	 */
	public $templates = array();
	/**
	 * @var array 模板标签
	 */
	public $templatetags = array();
	/**
	 * @var null 社会化评论
	 */
	public $socialcomment = null;
	/**
	 * @var null 模板头部
	 */
	public $header = null;
	/**
	 * @var null 模板尾部
	 */
	public $footer = null;

	/**
	 * @var array 主题列表
	 */
	public $themes = array();
	/**
	 * @var array 插件列表
	 */
	public $plugins = array();
	/**
	 * @var array 激活的插件列表
	 */
	public $activeapps = array();

	/**
	 * @var int 管理页面显示条数
	 */
	public $managecount = 50;
	/**
	 * @var int 页码显示条数
	 */
	public $pagebarcount = 10;
	/**
	 * @var int 搜索返回条数
	 */
	public $searchcount = 10;
	/**
	 * @var int 文章列表显示条数
	 */
	public $displaycount = 10;
	/**
	 * @var int 评论显示数量
	 */
	public $commentdisplaycount = 10;

	/**
	 * @var array 默认侧栏
	 */
	public $sidebar =array();
	/**
	 * @var array 侧栏2
	 */
	public $sidebar2=array();
	/**
	 * @var array 侧栏3
	 */
	public $sidebar3=array();
	/**
	 * @var array 侧栏4
	 */
	public $sidebar4=array();
	/**
	 * @var array 侧栏5
	 */
	public $sidebar5=array();


	/**
	 * 获取唯一实例
	 * @return null|ZBlogPHP
	 */
	static public function GetInstance(){
		if(!isset(self::$_zbp)){
			self::$_zbp=new ZBlogPHP;
		}
		return self::$_zbp;
	}

	/**
	 * 构造函数，加载基本配置到$zbp
	 */
	function __construct() {

		global $option,$lang,$blogpath,$bloghost,$cookiespath,$usersdir,$table,$datainfo,$actions,$action;
		global $blogversion,$blogtitle,$blogname,$blogsubname,$blogtheme,$blogstyle,$currenturl,$activeapps;

		ZBlogException::SetErrorHook();

		//基本配置加载到$zbp内
		$this->version = &$blogversion;
		$this->option = &$option;
		$this->lang = &$lang;
		$this->path = &$blogpath;
		$this->host = &$bloghost;
		$this->cookiespath = &$cookiespath;
		$this->usersdir = &$usersdir;

		$this->table = &$table;
		$this->datainfo = &$datainfo;
		$this->actions = &$actions;
		$this->currenturl = &$currenturl;
		$this->action = &$action;
		$this->activeapps = &$activeapps;

		if (trim($this->option['ZC_BLOG_CLSID']) == ''){
			$this->option['ZC_BLOG_CLSID'] = GetGuid();
		}
		$this->guid = &$this->option['ZC_BLOG_CLSID'];

		$this->title = &$blogtitle;
		$this->name = &$blogname;
		$this->subname = &$blogsubname;
		$this->theme = &$blogtheme;
		$this->style = &$blogstyle;

		$this->managecount = &$this->option['ZC_MANAGE_COUNT'];
		$this->pagebarcount = &$this->option['ZC_PAGEBAR_COUNT'];
		$this->searchcount = &$this->option['ZC_SEARCH_COUNT'];
		$this->displaycount = &$this->option['ZC_DISPLAY_COUNT'];
		$this->commentdisplaycount = &$this->option['ZC_COMMENTS_DISPLAY_COUNT'];

		$this->cache = new Metas;

	}


	/**
	 *析构函数，释放资源
	 */
	function __destruct(){
		$this->Terminate();
	}

	/**
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	function __call($method, $args) {
		foreach ($GLOBALS['Filter_Plugin_Zbp_Call'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		if($this->option['ZC_DEBUG_MODE']==true) $this->ShowError(81,__FILE__,__LINE__);
	}

	/**
	 * 设置参数值
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	function __set($name, $value){
		foreach ($GLOBALS['Filter_Plugin_Zbp_Set'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($name, $value);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		if($this->option['ZC_DEBUG_MODE']==true) $this->ShowError(81,__FILE__,__LINE__);
	}

	/**
	 * 获取参数值
	 * @param $name
	 * @return mixed
	 */
	function __get($name){
		foreach ($GLOBALS['Filter_Plugin_Zbp_Get'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($name);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		if($this->option['ZC_DEBUG_MODE']==true) $this->ShowError(81,__FILE__,__LINE__);
	}

################################################################################################################
#初始化

	/**
	 * 初始化$zbp
	 * @return bool
	 */
	public function Initialize(){

		$oldzone=$this->option['ZC_TIME_ZONE_NAME'];
		date_default_timezone_set($oldzone);

		$oldlang=$this->option['ZC_BLOG_LANGUAGEPACK'];
		$this->lang = require($this->path . 'zb_users/language/' . $oldlang . '.php');

		if($this->option['ZC_SITE_TURNOFF']==true){
			Http503();
			$this->ShowError(82,__FILE__,__LINE__);
			return false;
		}

		if(!$this->OpenConnect())return false;

		$this->LoadConfigs();
		$this->LoadCache();
		$this->LoadOption();

		if($oldlang!=$this->option['ZC_BLOG_LANGUAGEPACK']){
			$this->lang = require($this->path . 'zb_users/language/' . $this->option['ZC_BLOG_LANGUAGEPACK'] . '.php');
		}

		if(isset($this->option['ZC_DEBUG_MODE_STRICT'])){
			ZBlogException::$isstrict = (bool)$this->option['ZC_DEBUG_MODE_STRICT'];
		}
		if(isset($this->option['ZC_DEBUG_MODE_WARNING'])){
			ZBlogException::$iswarning = (bool)$this->option['ZC_DEBUG_MODE_WARNING'];
		}

		if($this->option['ZC_PERMANENT_DOMAIN_ENABLE']==true){
			$this->host=$this->option['ZC_BLOG_HOST'];
			$this->cookiespath=substr($this->host,strpos($this->host,'/',8));
		}else{
			$this->option['ZC_BLOG_HOST']=$this->host;
		}

		$this->option['ZC_BLOG_VERSION']=ZC_BLOG_VERSION;
		$this->option['ZC_BLOG_PRODUCT_FULL']=$this->option['ZC_BLOG_PRODUCT'] . ' ' . $this->option['ZC_BLOG_VERSION'];
		$this->option['ZC_BLOG_PRODUCT_FULLHTML']='<a href="http://www.zblogcn.com/" title="RainbowSoft Z-BlogPHP" target="_blank">' . $this->option['ZC_BLOG_PRODUCT_FULL'] . '</a>';
		$this->option['ZC_BLOG_PRODUCT_HTML']='<a href="http://www.zblogcn.com/" title="RainbowSoft Z-BlogPHP" target="_blank">' . $this->option['ZC_BLOG_PRODUCT'] . '</a>';

		if($oldzone!=$this->option['ZC_TIME_ZONE_NAME']){
			date_default_timezone_set($this->option['ZC_TIME_ZONE_NAME']);
		}

		/*if(isset($_COOKIE['timezone'])){
			$tz=GetVars('timezone','COOKIE');
			if(is_numeric($tz)){
				$tz=sprintf('%+d',-$tz);
				date_default_timezone_set('Etc/GMT' . $tz);
				$this->timezone=date_default_timezone_get();
			}
		}*/

		header('Product:' . $this->option['ZC_BLOG_PRODUCT_FULL']);

		$this->validcodeurl=$this->host . 'zb_system/script/c_validcode.php';
		$this->feedurl=$this->host . 'feed.php';
		$this->searchurl=$this->host . 'search.php';
		$this->ajaxurl=$this->host . 'zb_system/cmd.php?act=ajax&src=';

		#创建User类
		$this->user=new Member();

		$this->isinitialize=true;

	}


	/**
	 * 重建索引并载入
	 * @return bool
	 */
	public function Load(){

		if(!$this->isinitialize)return false;

		if($this->isload)return false;

		foreach($this->table as &$tb){
			$tb=str_replace('%pre%', $this->db->dbpre, $tb);
		}

		$this->StartGzip();

		header('Content-type: text/html; charset=utf-8');

		$this->LoadMembers();

		$this->LoadCategorys();
		#$this->LoadTags();
		$this->LoadModules();

		$this->Verify();

		$this->RegBuildModule('catalog','BuildModule_catalog');

		$this->RegBuildModule('calendar','BuildModule_calendar');

		$this->RegBuildModule('comments','BuildModule_comments');

		$this->RegBuildModule('previous','BuildModule_previous');

		$this->RegBuildModule('archives','BuildModule_archives');

		$this->RegBuildModule('navbar','BuildModule_navbar');

		$this->RegBuildModule('tags','BuildModule_tags');

		$this->RegBuildModule('statistics','BuildModule_statistics');

		$this->RegBuildModule('authors','BuildModule_authors');

		$this->LoadTemplate();

		$this->MakeTemplatetags();

		$this->template=$this->PrepareTemplate();

		foreach ($GLOBALS['Filter_Plugin_Zbp_Load'] as $fpname => &$fpsignal) $fpname();

		if($this->ismanage) $this->LoadManage();

		$this->isload=true;

		return true;
	}

	/**
	 * 载入管理
	 */
	public function LoadManage(){

		if($this->user->Status==ZC_MEMBER_STATUS_AUDITING) $this->ShowError(79,__FILE__,__LINE__);
		if($this->user->Status==ZC_MEMBER_STATUS_LOCKED) $this->ShowError(80,__FILE__,__LINE__);

		$this->CheckTemplate();

		if(GetVars('dishtml5','COOKIE')){
			$this->option['ZC_ADMIN_HTML5_ENABLE']=false;
		}else{
			$this->option['ZC_ADMIN_HTML5_ENABLE']=true;
		}

		foreach ($GLOBALS['Filter_Plugin_Zbp_LoadManage'] as $fpname => &$fpsignal) $fpname();

	}

	/**
	 *终止连接，释放资源
	 */
	public function Terminate(){
		if($this->isinitialize){
			foreach ($GLOBALS['Filter_Plugin_Zbp_Terminate'] as $fpname => &$fpsignal) $fpname();
			$this->CloseConnect();
			unset($this->db);
			$this->isinitialize=false;
		}
	}


	/**
	 * 初始化数据库连接
	 * @param string $type 数据连接类型
	 * @return bool
	 */
	public function InitializeDB($type){
		if(!trim($type))return false;
		$newtype='Db'.trim($type);
		$this->db=new $newtype();
	}

	/**
	 * 连接数据库
	 * @return bool
	 * @throws Exception
	 */
	public function OpenConnect(){

		if($this->isconnect)return false;
		if(!$this->option['ZC_DATABASE_TYPE'])return false;
		switch ($this->option['ZC_DATABASE_TYPE']) {
			case 'sqlite':
			case 'sqlite3':
				try {
					$this->InitializeDB($this->option['ZC_DATABASE_TYPE']);
					if($this->db->Open(array(
							$this->usersdir . 'data/' . $this->option['ZC_SQLITE_NAME'],
							$this->option['ZC_SQLITE_PRE']
						))==false){
						$this->ShowError(69,__FILE__,__LINE__);
					}
				} catch (Exception $e) {
					throw new Exception("SQLite DateBase Connection Error.");
				}
				break;
			case 'mysql':
			case 'mysqli':
			case 'pdo_mysql':
			default:
				try {
					$this->InitializeDB($this->option['ZC_DATABASE_TYPE']);
					if($this->db->Open(array(
							$this->option['ZC_MYSQL_SERVER'],
							$this->option['ZC_MYSQL_USERNAME'],
							$this->option['ZC_MYSQL_PASSWORD'],
							$this->option['ZC_MYSQL_NAME'],
							$this->option['ZC_MYSQL_PRE'],
							$this->option['ZC_MYSQL_PORT'],
							$this->option['ZC_MYSQL_PERSISTENT']
						))==false){
						$this->ShowError(67,__FILE__,__LINE__);
					}
				} catch (Exception $e) {
					throw new Exception("MySQL DateBase Connection Error.");
				}
				break;
		}
		$this->isconnect=true;
		return true;

	}

	/**
	 * 关闭数据库连接
	 */
	public function CloseConnect(){
		if($this->isconnect){
			$this->db->Close();
			$this->isconnect=false;
		}
	}


	/**
	 * 启用session
	 * @return bool
	 */
	public function StartSession(){
		if($this->issession==true)return false;
		session_start();
		$this->issession=true;
		return true;
	}


	/**
	 * 终止session
	 * @return bool
	 */
	public function EndSession(){
		if($this->issession==false)return false;
		session_unset();
		session_destroy();
		$this->issession=false;
		return true;
	}

################################################################################################################
#插件用Configs表相关设置函数

	/**
	 * 载入插件Configs表
	 */
	public function LoadConfigs(){

		$this->configs=array();
		$sql = $this->db->sql->Select($this->table['Config'],array('*'),'','','','');
		$array=$this->db->Query($sql);
		foreach ($array as $c) {
			$m=new Metas;
			$m->Unserialize($c['conf_Value']);
			$this->configs[$c['conf_Name']]=$m;
		}
	}

	/**
	 * 删除Configs表
	 * @param string $name Configs表名
	 * @return bool
	 */
	public function DelConfig($name){
		$sql = $this->db->sql->Delete($this->table['Config'],array(array('=','conf_Name',$name)));
		$this->db->Delete($sql);
		return true;
	}

	/**
	 * 保存Configs表
	 * @param string $name Configs表名
	 * @return bool
	 */
	public function SaveConfig($name){

		if(!isset($this->configs[$name]))return false;

		$kv=array('conf_Name'=>$name,'conf_Value'=>$this->configs[$name]->Serialize());
		$sql = $this->db->sql->Select($this->table['Config'],array('*'),array(array('=','conf_Name',$name)),'','','');
		$array=$this->db->Query($sql);

		if(count($array)==0){
			$sql = $this->db->sql->Insert($this->table['Config'],$kv);
			$this->db->Insert($sql);
		}else{
			array_shift($kv);
			$sql = $this->db->sql->Update($this->table['Config'],$kv,array(array('=','conf_Name',$name)));
			$this->db->Update($sql);
		}

		return true;
	}

	/**
	 * 获取Configs表值
	 * @param string $name Configs表名
	 * @return mixed
	 */
	public function Config($name){
		if(!isset($this->configs[$name])){
			$m=new Metas;
			$this->configs[$name]=$m;
		}
		return $this->configs[$name];
	}

################################################################################################################
#Cache相关

	/**
	 * 保存缓存
	 * @return bool
	 */
	public function SaveCache(){
		#$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
		#$c=serialize($this->cache);
		#@file_put_contents($s, $c);
		//$this->configs['cache']=$this->cache;
		$this->SaveConfig('cache');
		return true;
	}

	/**
	 * 加载缓存
	 * @return bool
	 */
	public function LoadCache(){
		#$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
		#if (file_exists($s))
		#{
		#	$this->cache=unserialize(@file_get_contents($s));
		#}
		$this->cache=$this->Config('cache');
		return true;
	}

################################################################################################################
#保存zbp设置函数

	/**
	 * 保存配置
	 * @return bool
	 */
	public function SaveOption(){

		$this->option['ZC_BLOG_CLSID']=$this->guid;

		if( strpos('|SAE|BAE2|ACE|TXY|', '|'.$this->option['ZC_YUN_SITE'].'|')===false ){
			$s="<?php\r\n";
			$s.="return ";
			$s.=var_export($this->option,true);
			$s.="\r\n?>";
			@file_put_contents($this->usersdir . 'c_option.php',$s);
		}

		foreach ($this->option as $key => $value) {
			$this->Config('system')->$key = $value;
		}
		$this->SaveConfig('system');
		return true;
	}


	/**
	 * 载入配置
	 * @return bool
	 */
	public function LoadOption(){

		$array=$this->Config('system')->Data;

		if(empty($array))return false;
		if(!is_array($array))return false;
		foreach ($array as $key => $value) {
			//if($key=='ZC_PERMANENT_DOMAIN_ENABLE')continue;
			//if($key=='ZC_BLOG_HOST')continue;
			//if($key=='ZC_BLOG_CLSID')continue;
			//if($key=='ZC_BLOG_LANGUAGEPACK')continue;
			if($key=='ZC_YUN_SITE')continue;
			if($key=='ZC_DATABASE_TYPE')continue;
			if($key=='ZC_SQLITE_NAME')continue;
			if($key=='ZC_SQLITE_PRE')continue;
			if($key=='ZC_MYSQL_SERVER')continue;
			if($key=='ZC_MYSQL_USERNAME')continue;
			if($key=='ZC_MYSQL_PASSWORD')continue;
			if($key=='ZC_MYSQL_NAME')continue;
			if($key=='ZC_MYSQL_CHARSET')continue;
			if($key=='ZC_MYSQL_PRE')continue;
			if($key=='ZC_MYSQL_ENGINE')continue;
			if($key=='ZC_MYSQL_PORT')continue;
			if($key=='ZC_MYSQL_PERSISTENT')continue;
			if($key=='ZC_SITE_TURNOFF')continue;			
			$this->option[$key]=$value;
		}
		return true;
	}

################################################################################################################
#权限及验证类

	/**
	 * 验证操作权限
	 * @param string $action 操作
	 * @return bool
	 */
	function CheckRights($action){

		foreach ($GLOBALS['Filter_Plugin_Zbp_CheckRights'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($action);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		if(!isset($this->actions[$action])){
			if(is_numeric($action)){
				if ($this->user->Level > $action) {
					return false;
				} else {
					return true;
				}
			}
		}else{
			if ($this->user->Level > $this->actions[$action]) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * 根据用户等级验证操作权限
	 * @param int $level 用户等级
	 * @param string $action 操作
	 * @return bool
	 */
	function CheckRightsByLevel($level,$action){

		foreach ($GLOBALS['Filter_Plugin_Zbp_CheckRightsByLevel'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($level,$action);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}

		if(is_int($action)){
			if ($level > $action) {
				return false;
			} else {
				return true;
			}
		}

		if ($level > $this->actions[$action]) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * 验证用户登录(COOKIE中的用户名密码)
	 * @return bool
	 */
	public function Verify(){
		return $this->Verify_MD5Path(GetVars('username','COOKIE'),GetVars('password','COOKIE'));
	}

	/**
	 * 验证用户登录（二次MD5密码）
	 * @param string $name 用户名
	 * @param string $ps_and_path 二次md5加密后的密码
	 * @return bool
	 */
	public function Verify_MD5Path($name,$ps_and_path){
		if (isset($this->membersbyname[$name])){
			$m=$this->membersbyname[$name];
			if(md5($m->Password . $this->guid) == $ps_and_path){
				$this->user=$m;
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * 验证用户登录（一次MD5密码）
	 * @param string $name 用户名
	 * @param string $md5pw md5加密后的密码
	 * @return bool
	 */
	public function Verify_MD5($name,$md5pw){
		if (isset($this->membersbyname[$name])){
			$m=$this->membersbyname[$name];
			return $this->Verify_Final($name,md5($md5pw . $m->Guid));
		}else{
			return false;
		}
	}

	/**
	 * 验证用户登录（加盐的密码）
	 * @param string $name 用户名
	 * @param string $originalpw 密码明文与Guid连接后的字符串
	 * @return bool
	 */
	public function Verify_Original($name,$originalpw){
		return $this->Verify_MD5($name,md5($originalpw));
	}

	/**
	 * 验证用户登录
	 * @param string $name 用户名
	 * @param string $password 二次加密后的密码
	 * @return bool
	 */
	public function Verify_Final($name,$password){
		if (isset($this->membersbyname[$name])){
			$m=$this->membersbyname[$name];
			if(strcasecmp ( $m->Password ,  $password ) ==  0){
				$this->user=$m;
				return true;
			}else{
				return false;
			}
		}
	}









################################################################################################################
#
	/**
	 * 生成模块
	 */
	function BuildModule(){

		foreach ($GLOBALS['Filter_Plugin_Zbp_BuildModule'] as $fpname => &$fpsignal)$fpname();

		foreach ($this->readymodules as $modfilename) {
			if(isset($this->modulesbyfilename[$modfilename])){
				if(isset($this->readymodules_function[$modfilename])){
					$m=$this->modulesbyfilename[$modfilename];
					if($m->NoRefresh==true)continue;
					if(function_exists($this->readymodules_function[$modfilename])){
						if(!isset($this->readymodules_parameters[$modfilename])){
							$m->Content=call_user_func($this->readymodules_function[$modfilename]);
						}else{
							$m->Content=call_user_func($this->readymodules_function[$modfilename],$this->readymodules_parameters[$modfilename]);
						}
					}
					$m->Save();
				}
			}
		}

	}

	/**
	 * 重建模块
	 * @param string $modfilename 模块名
	 * @param string $userfunc 用户函数
	 */
	function RegBuildModule($modfilename,$userfunc){
		$this->readymodules_function[$modfilename]=$userfunc;
	}

	/**
	 * 添加模块
	 * @param string $modfilename 模块名
	 * @param null $parameters 模块参数
	 */
	function AddBuildModule($modfilename,$parameters=null){
		$this->readymodules[$modfilename]=$modfilename;
		$this->readymodules_parameters[$modfilename]=$parameters;
	}

	/**
	 * 删除模块
	 * @param string $modfilename 模块名
	 */
	function DelBuildModule($modfilename){
		unset($this->readymodules[$modfilename]);
		unset($this->readymodules_function[$modfilename]);
		unset($this->readymodules_parameters[$modfilename]);
	}

	/**
	 * 所有模块重置
	 */
	function AddBuildModuleAll(){
		$m=array('catalog','calendar','comments','previous','archives','navbar','tags','authors');
		foreach ($m as $key => $value) {
			$this->readymodules[$value]=$value;
		}
	}

################################################################################################################
#加载函数

	/**
	 *载入用户列表
	 */
	public function LoadMembers(){

		$array=$this->GetMemberList();
		foreach ($array as $m) {
			$this->members[$m->ID]=$m;
			$this->membersbyname[$m->Name]=&$this->members[$m->ID];
		}
	}

	/**
	 * 载入分类列表
	 * @return bool
	 */
	public function LoadCategorys(){

		$lv0=array();
		$lv1=array();
		$lv2=array();
		$lv3=array();
		$array=$this->GetCategoryList(null,null,array('cate_Order'=>'ASC'),null,null);
		if(count($array)==0)return false;
		foreach ($array as $c) {
			$this->categorys[$c->ID]=$c;
		}
		foreach ($this->categorys as $id=>$c) {
			$l='lv' . $c->Level;
			${$l}[$c->ParentID][]=$id;
		}

		if(count($lv0)>0)$this->categorylayer=1;
		if(count($lv1)>0)$this->categorylayer=2;
		if(count($lv2)>0)$this->categorylayer=3;
		if(count($lv3)>0)$this->categorylayer=4;

		foreach ($lv0[0] as $id0) {
			$this->categorysbyorder[$id0]=&$this->categorys[$id0];
			if(!isset($lv1[$id0])){continue;}
			foreach ($lv1[$id0] as $id1) {
				if($this->categorys[$id1]->ParentID==$id0){
					$this->categorys[$id0]->SubCategorys[]=$this->categorys[$id1];
					$this->categorysbyorder[$id1]=&$this->categorys[$id1];
					if(!isset($lv2[$id1])){continue;}
					foreach ($lv2[$id1] as $id2) {
						if($this->categorys[$id2]->ParentID==$id1){
							$this->categorys[$id0]->SubCategorys[]=$this->categorys[$id2];
							$this->categorys[$id1]->SubCategorys[]=$this->categorys[$id2];
							$this->categorysbyorder[$id2]=&$this->categorys[$id2];
							if(!isset($lv3[$id2])){continue;}
							foreach ($lv3[$id2] as $id3) {
								if($this->categorys[$id3]->ParentID==$id2){
									$this->categorys[$id0]->SubCategorys[]=$this->categorys[$id3];
									$this->categorys[$id1]->SubCategorys[]=$this->categorys[$id3];
									$this->categorys[$id2]->SubCategorys[]=$this->categorys[$id3];
									$this->categorysbyorder[$id3]=&$this->categorys[$id3];
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 *载入标签列表
	 */
	public function LoadTags(){

		$array=$this->GetTagList();
		foreach ($array as $t) {
			$this->tags[$t->ID]=$t;
			$this->tagsbyname[$t->Name]=&$this->tags[$t->ID];
		}

	}

	/**
	 * 载入模块列表
	 * @return null
	 */
	public function LoadModules(){

		$array=$this->GetModuleList();
		foreach ($array as $m) {
			$this->modules[]=$m;

			$this->modulesbyfilename[$m->FileName]=$m;
		}

		$dir=$this->usersdir . 'theme/' . $this->theme . '/include/';
		if(!file_exists($dir))return null;
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$m=new Module();
			$m->FileName=$sortname;
			$m->Content=file_get_contents($fullname);
			$m->Type='div';
			$m->Source='theme';
			$this->modules[]=$m;
			$this->modulesbyfilename[$m->FileName]=$m;
		}

	}

	/**
	 *载入当前主题
	 */
	public function LoadThemes(){
		$dirs=GetDirsInDir($this->usersdir . 'theme/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('theme',$id)==true){
				$this->themes[]=$app;
			}
		}

	}

	/**
	 *载入插件列表
	 */
	public function LoadPlugins(){
		$dirs=GetDirsInDir($this->usersdir . 'plugin/');

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('plugin',$id)==true){
				$this->plugins[]=$app;
			}
		}

	}

	/**
	 * 载入应用列表
	 * @param string $type 应用类型
	 * @param string $id 应用ID
	 * @return App
	 */
	public function LoadApp($type,$id){
		$app = new App;
		$app->LoadInfoByXml($type,$id);
		return $app;
	}

################################################################################################################
#模板相关函数

	/**
	 *解析模板标签
	 */
	public function MakeTemplatetags(){

		$this->templatetags=array();

		$option=$this->option;
		unset($option['ZC_BLOG_CLSID']);
		unset($option['ZC_SQLITE_NAME']);
		unset($option['ZC_SQLITE3_NAME']);
		unset($option['ZC_MYSQL_USERNAME']);
		unset($option['ZC_MYSQL_PASSWORD']);
		unset($option['ZC_MYSQL_NAME']);

		$this->templatetags['zbp']=&$this;
		$this->templatetags['user']=&$this->user;
		$this->templatetags['option']=&$option;
		$this->templatetags['lang']=&$this->lang;
		$this->templatetags['version']=&$this->version;
		$this->templatetags['categorys']=&$this->categorys;
		$this->templatetags['modules']=&$this->modulesbyfilename;
		$this->templatetags['title']=htmlspecialchars($this->title);
		$this->templatetags['host']=&$this->host;
		$this->templatetags['path']=&$this->path;
		$this->templatetags['cookiespath']=&$this->cookiespath;
		$this->templatetags['name']=htmlspecialchars($this->name);
		$this->templatetags['subname']=htmlspecialchars($this->subname);
		$this->templatetags['theme']=&$this->theme;
		$this->templatetags['style']=&$this->style;
		$this->templatetags['language']=$this->option['ZC_BLOG_LANGUAGE'];
		$this->templatetags['copyright']=$this->option['ZC_BLOG_COPYRIGHT'];
		$this->templatetags['zblogphp']=$this->option['ZC_BLOG_PRODUCT_FULL'];
		$this->templatetags['zblogphphtml']=$this->option['ZC_BLOG_PRODUCT_FULLHTML'];
		$this->templatetags['zblogphpabbrhtml']=$this->option['ZC_BLOG_PRODUCT_HTML'];
		$this->templatetags['type']='';
		$this->templatetags['page']='';
		$this->templatetags['socialcomment']=&$this->socialcomment;
		$this->templatetags['header']=&$this->header;
		$this->templatetags['footer']=&$this->footer;
		$this->templatetags['validcodeurl']=&$this->validcodeurl;
		$this->templatetags['feedurl']=&$this->feedurl;
		$this->templatetags['searchurl']=&$this->searchurl;
		$this->templatetags['ajaxurl']=&$this->ajaxurl;
		$s=array(
			$option['ZC_SIDEBAR_ORDER'],
			$option['ZC_SIDEBAR2_ORDER'],
			$option['ZC_SIDEBAR3_ORDER'],
			$option['ZC_SIDEBAR4_ORDER'],
			$option['ZC_SIDEBAR5_ORDER']
		);
		foreach ($s as $k =>$v) {
			$a=explode('|', $v);
			$ms=array();
			foreach ($a as $v2) {
				if(isset($this->modulesbyfilename[$v2])){
					$m=$this->modulesbyfilename[$v2];
					$ms[]=$m;
				}
			}
			//reset($ms);
			$s='sidebar' . ($k==0?'':$k+1);
			$this->$s=$ms;
			$ms=null;
		}
		$this->templatetags['sidebar']=&$this->sidebar;
		$this->templatetags['sidebar2']=&$this->sidebar2;
		$this->templatetags['sidebar3']=&$this->sidebar3;
		$this->templatetags['sidebar4']=&$this->sidebar4;
		$this->templatetags['sidebar5']=&$this->sidebar5;

		foreach ($GLOBALS['Filter_Plugin_Zbp_MakeTemplatetags'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this->templatetags);
		}

	}

	/**
	 * 预加载模板
	 * @return Template
	 */
	public function PrepareTemplate(){
		//创建模板类
		$template = new Template();
		$template->SetPath($this->usersdir . 'theme/'. $this->theme .'/compile/');
		$template->SetTagsAll($this->templatetags);

		foreach ($GLOBALS['Filter_Plugin_Zbp_PrepareTemplate'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($template);
		}
		return $template;
	}

	/**
	 *载入模板
	 */
	public function LoadTemplate(){

		$this->templates=array();

		#先读默认的
		$dir=$this->path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		#再读当前的
		$dir=$this->usersdir .'theme/' . $this->theme . '/template/';
		$files=GetFilesInDir($dir,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		if(!isset($this->templates['sidebar2'])){
			$this->templates['sidebar2']=str_replace('$sidebar', '$sidebar2', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar3'])){
			$this->templates['sidebar3']=str_replace('$sidebar', '$sidebar3', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar4'])){
			$this->templates['sidebar4']=str_replace('$sidebar', '$sidebar4', $this->templates['sidebar']);
		}
		if(!isset($this->templates['sidebar5'])){
			$this->templates['sidebar5']=str_replace('$sidebar', '$sidebar5', $this->templates['sidebar']);
		}

		foreach ($GLOBALS['Filter_Plugin_Zbp_LoadTemplate'] as $fpname => &$fpsignal) {
			$fpname($this->templates);
		}
	}

	/**
	 * 模板解析
	 * @return bool
	 */
	public function BuildTemplate(){

		if( strpos('|SAE|BAE2|ACE|TXY|', '|'.$this->option['ZC_YUN_SITE'].'|')!==false )return false;
		//初始化模板
		$this->LoadTemplate();

		if(strpos($this->templates['comments'], 'AjaxCommentBegin')===false)
			$this->templates['comments']='<label id="AjaxCommentBegin"></label>' . $this->templates['comments'];

		if(strpos($this->templates['comments'], 'AjaxCommentEnd')===false)
			$this->templates['comments']=$this->templates['comments'] . '<label id="AjaxCommentEnd"></label>';

		if(strpos($this->templates['comment'], 'id="cmt{$comment.ID}"')===false&&strpos($this->templates['comment'], 'id=\'cmt{$comment.ID}\'')===false){
			$this->templates['comment']='<label id="cmt{$comment.ID}"></label>'. $this->templates['comment'];
		}

		if(strpos($this->templates['commentpost'], 'inpVerify')===false&&strpos($this->templates['commentpost'], '=\'verify\'')===false&&strpos($this->templates['commentpost'], '="verify"')===false){
			$verify='{if $option[\'ZC_COMMENT_VERIFY_ENABLE\'] && !$user.ID}<p><input type="text" name="inpVerify" id="inpVerify" class="text" value="" size="28" tabindex="4" /> <label for="inpVerify">'.$this->lang['msg']['validcode'].'(*)</label><img style="width:{$option[\'ZC_VERIFYCODE_WIDTH\']}px;height:{$option[\'ZC_VERIFYCODE_HEIGHT\']}px;cursor:pointer;" src="{$article.ValidCodeUrl}" alt="" title="" onclick="javascript:this.src=\'{$article.ValidCodeUrl}&amp;tm=\'+Math.random();"/></p>{/if}';

			if(strpos($this->templates['commentpost'], '<!--verify-->')!==false){
				$this->templates['commentpost']=str_replace('<!--verify-->',$verify,$this->templates['commentpost']);
			}elseif(strpos($this->templates['commentpost'], '</form>')!==false){
				$this->templates['commentpost']=str_replace('</form>',$verify.'</form>',$this->templates['commentpost']);
			}
			else{
				$this->templates['commentpost'] .= $verify;
			}
		}

		if(strpos($this->templates['header'], '{$header}')===false){
			if(strpos($this->templates['header'], '</head>')!==false){
				$this->templates['header']=str_replace('</head>','</head>' . '{$header}',$this->templates['header']);
			}elseif(strpos($this->templates['header'], '</ head>')!==false){
				$this->templates['header']=str_replace('</ head>','</ head>' . '{$header}',$this->templates['header']);
			}else{
				$this->templates['header'] .= '{$header}';
			}
		}

		if(strpos($this->templates['footer'], '{$footer}')===false){
			if(strpos($this->templates['footer'], '</body>')!==false){
				$this->templates['footer']=str_replace('</body>','{$footer}' . '</body>',$this->templates['footer']);
			}elseif(strpos($this->templates['footer'], '</ body>')!==false){
				$this->templates['footer']=str_replace('</ body>','{$footer}' . '</ body>',$this->templates['footer']);
			}elseif(strpos($this->templates['footer'], '</html>')!==false){
				$this->templates['footer']=str_replace('</html>','{$footer}' . '</html>',$this->templates['footer']);
			}elseif(strpos($this->templates['footer'], '</ html>')!==false){
				$this->templates['footer']=str_replace('</ html>','{$footer}' . '</ html>',$this->templates['footer']);
			}else{
				$this->templates['footer'] = '{$footer}' . $this->templates['footer'];
			}
		}

		$dir=$this->usersdir . 'theme/'. $this->theme .'/compile/';

		if(!file_exists($dir)){
			@mkdir($dir,0755,true);
		}

		$files2=array();
		foreach ($this->templates as $name=>$file) {
			$files2[]=$dir . $name . '.php';
		}

		//清空目标目录
		$files = GetFilesInDir($dir,'php');

		$files3 = array_diff($files,$files2);

		foreach ($files3 as $fullname) {
			@unlink($fullname);
		}

		//创建模板类
		$template = new Template();
		$template->SetPath($dir);

		//模板接口
		foreach ($GLOBALS['Filter_Plugin_Zbp_BuildTemplate'] as $fpname => &$fpsignal) {
			$fpname($this->templates);
		}

		$template->CompileFiles($this->templates);

	}

	/**
	 *更新模板缓存
	 */
	public function CheckTemplate(){
		$s=implode($this->templates);
		$md5=md5($s);
		if($md5!=$this->cache->templates_md5){
			$this->BuildTemplate();
			$this->cache->templates_md5=$md5;
			$this->SaveCache();
		}
	}

################################################################################################################
#加载数据对像List函数

	/**
	 * 自定义查询语句获取数据库数据列表
	 * @param string $table 数据表
	 * @param string $datainfo 数据字段
	 * @param string $sql SQL操作语句
	 * @return array
	 */
	function GetListCustom($table,$datainfo,$sql){

		$array=null;
		$list=array();
		$array=$this->db->Query($sql);
		if(!isset($array)){return array();}
		foreach ($array as $a) {
			$l=new Base($table,$datainfo);
			$l->LoadInfoByAssoc($a);
			$list[]=$l;
		}
		return $list;
	}


	/**
	 * @param $type
	 * @param $sql
	 * @return array
	 */
	function GetList($type,$sql){

		$array=null;
		$list=array();
		$array=$this->db->Query($sql);
		if(!isset($array)){return array();}
		foreach ($array as $a) {
			$l=new $type();
			$l->LoadInfoByAssoc($a);
			$list[]=$l;
		}
		return $list;
	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetPostList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);
		return $this->GetList('Post',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @param bool $readtags
	 * @return array
	 */
	function GetArticleList($select=null,$where=null,$order=null,$limit=null,$option=null,$readtags=true){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		if(is_array($where))array_unshift($where,array('=','log_Type','0'));
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);
		$array = $this->GetList('Post',$sql);

		if($readtags){
			$tagstring = '';
			foreach ($array as $a) {
				$tagstring .= $a->Tag;
				$this->posts[$a->ID]=$a;
			}
			$this->LoadTagsByIDString($tagstring);
		}

		return $array;

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetPageList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		if(is_array($where))array_unshift($where,array('=','log_Type','1'));
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);
		$array = $this->GetList('Post',$sql);
		foreach ($array as $a) {
			$this->posts[$a->ID]=$a;
		}
		return $array;

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetCommentList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Comment'],$select,$where,$order,$limit,$option);
		$array=$this->GetList('Comment',$sql);
		foreach ($array as $comment) {
			$this->comments[$comment->ID]=$comment;
		}
		return $array;

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetMemberList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Member'],$select,$where,$order,$limit,$option);
		return $this->GetList('Member',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetTagList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Tag'],$select,$where,$order,$limit,$option);
		return $this->GetList('Tag',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetCategoryList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Category'],$select,$where,$order,$limit,$option);
		return $this->GetList('Category',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetModuleList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Module'],$select,$where,$order,$limit,$option);
		return $this->GetList('Module',$sql);
	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetUploadList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Upload'],$select,$where,$order,$limit,$option);
		return $this->GetList('Upload',$sql);
	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	function GetCounterList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Counter'],$select,$where,$order,$limit,$option);
		return $this->GetList('Counter',$sql);
	}


################################################################################################################
#wp类似

	/**
	 * @param $sql
	 * @return mixed
	 */
	function get_results($sql){
		return $this->db->Query($sql);
	}


################################################################################################################
#读取对象函数


	/**
	 * 通过ID获取文章实例
	 * @param int $id
	 * @return Post
	 */
	function GetPostByID($id){
		if($id==0)return new Post;
		if(isset($this->posts[$id])){
			return $this->posts[$id];
		}else{
			$p = new Post;
			$p->LoadInfoByID($id);
			$this->posts[$id]=$p;
			return $p;
		}
	}

	/**
	 * 通过ID获取分类实例
	 * @param int $id
	 * @return Category
	 */
	function GetCategoryByID($id){
		if(isset($this->categorys[$id])){
			return $this->categorys[$id];
		}else{
			return new Category;
		}
	}

	/**
	 * 通过分类名获取分类实例
	 * @param string $name
	 * @return Category
	 */
	function GetCategoryByName($name){
		$name=trim($name);
		foreach ($this->categorys as $key => &$value) {
			if($value->Name==$name){
				return $value;
			}
		}
		return new Category;
	}

	/**
	 * 通过分类别名获取分类实例
	 * @param string $name
	 * @return Category
	 */
	function GetCategoryByAliasOrName($name){
		$name=trim($name);
		foreach ($this->categorys as $key => &$value) {
			if(($value->Name==$name)||($value->Alias==$name)){
				return $value;
			}
		}
		return new Category;
	}

	/**
	 * 通过ID获取模块实例
	 * @param int $id
	 * @return Module
	 */
	function GetModuleByID($id){
		if($id==0){
			$m = new Module;
			return $m;
		}else{
			foreach ($this->modules as $key => $value) {
				if($value->ID==$id)return $value;
			}
			$m = new Module;
			return $m;
		}
	}

	/**
	 * 通过ID获取用户实例
	 * @param int $id
	 * @return Member
	 */
	function GetMemberByID($id){
		if(isset($this->members[$id])){
			return $this->members[$id];
		}
		$m = new Member;
		$m->Guid=GetGuid();
		return $m;
	}

	/**
	 * 通过用户获取用户实例
	 * @param string $name
	 * @return Member
	 */
	function GetMemberByAliasOrName($name){
		$name=trim($name);
		foreach ($this->members as $key => &$value) {
			if(($value->Name==$name)||($value->Alias==$name)){
				return $value;
			}
		}
		return new Member;
	}

	/**
	 * 通过ID获取评论实例
	 * @param int $id
	 * @return Comment
	 */
	function GetCommentByID($id){
		if(isset($this->comments[$id])){
			return $this->comments[$id];
		}else{
			$c = new Comment;
			if($id==0){
				return $c;
			}else{
				$c->LoadInfoByID($id);
				$this->comments[$id]=$c;
				return $c;
			}
		}
	}

	/**
	 * 通过ID获取附件实例
	 * @param int $id
	 * @return Upload
	 */
	function GetUploadByID($id){
		$m = new Upload;
		if($id>0){
			$m->LoadInfoByID($id);
		}
		return $m;
	}

	/**
	 * 通过ID获取审计类实例
	 * @param int $id
	 * @return Counter
	 */
	function GetCounterByID($id){
		$m = new Counter;
		if($id>0){
			$m->LoadInfoByID($id);
		}
		return $m;
	}

	/**
	 * 通过tag名获取tag实例
	 * @param string $name
	 * @return Tag
	 */
	function GetTagByAliasOrName($name){
		$a=array();
		$a[]=array('tag_Alias',$name);
		$a[]=array('tag_Name',$name);
		$array=$this->GetTagList('*',array(array('array',$a)),'',1,'');
		if(count($array)==0){
			return new Tag;
		}else{
			$this->tags[$array[0]->ID]=$array[0];
			$this->tagsbyname[$array[0]->ID]=&$this->tags[$array[0]->ID];
			return $this->tags[$array[0]->ID];
		}
	}

	/**
	 * 通过ID获取tag实例
	 * @param int $id
	 * @return Tag
	 */
	function GetTagByID($id){
		if(isset($this->tags[$id])){
			return $this->tags[$id];
		}else{
			$array=$this->GetTagList('',array(array('=','tag_ID',$id)),'',array(1),'');
			if(count($array)==0){
				return new Tag;
			}else{
				$this->tags[$array[0]->ID]=$array[0];
				$this->tagsbyname[$array[0]->ID]=&$this->tags[$array[0]->ID];
				return $this->tags[$array[0]->ID];
			}

		}
	}

	/**
	 * 通过类似'{1}{2}{3}{4}{4}'载入tags
	 * @param $s
	 * @return array
	 */
	function LoadTagsByIDString($s){
		if($s=='')return array();
		$s=str_replace('}{', '|', $s);
		$s=str_replace('{', '', $s);
		$s=str_replace('}', '', $s);
		$a=explode('|', $s);
		$b=array();
		foreach ($a as &$value) {
			$value = trim($value);
			if($value)$b[]=$value;
		}
		$t=array_unique($b);

		if(count($t)==0)return array();

		$a=array();
		$b=array();
		$c=array();
		foreach ($t as $v) {
			if(isset($this->tags[$v])==false){
				$a[]=array('tag_ID',$v);
				$c[]=$v;
			}else{
				$b[$v]=&$this->tags[$v];
			}
		}

		if(count($a)==0){
			return $b;
		}else{
			$t=array();
			//$array=$this->GetTagList('',array(array('array',$a)),'','','');
			$array=$this->GetTagList('',array(array('IN','tag_ID',$c)),'','','');
			foreach ($array as $v) {
				$this->tags[$v->ID]=$v;
				$this->tagsbyname[$v->Name]=&$this->tags[$v->ID];
				$t[$v->ID]=&$this->tags[$v->ID];
			}
			return $b+$t;
		}
	}

	/**
	 * 通过类似'aaa,bbb,ccc,ddd'载入tags
	 * @param string $s 标签名字符串，如'aaa,bbb,ccc,ddd
	 * @return array
	 */
	function LoadTagsByNameString($s){
		$s=str_replace(';', ',', $s);
		$s=str_replace('，', ',', $s);
		$s=str_replace('、', ',', $s);
		$s=trim($s);
		$s=strip_tags($s);
		if($s=='')return array();
		if($s==',')return array();
		$a=explode(',', $s);
		$t=array_unique($a);

		if(count($t)==0)return array();

		$a=array();
		$b=array();
		foreach ($t as $v) {
			if(isset($this->tagsbyname[$v])==false){
				$a[]=array('tag_Name',$v);
			}else{
				$b[$v]=&$this->tagsbyname[$v];
			}
		}

		if(count($a)==0){
			return $b;
		}else{
			$t=array();
			$array=$this->GetTagList('',array(array('array',$a)),'','','');
			foreach ($array as $v) {
				$this->tags[$v->ID]=$v;
				$this->tagsbyname[$v->Name]=&$this->tags[$v->ID];
				$t[$v->Name]=&$this->tags[$v->ID];
			}
			return $b+$t;
		}
	}

################################################################################################################
#杂项
	/**
	 * 验证评论key
	 * @param $id
	 * @param $key
	 * @return bool
	 */
	function VerifyCmtKey($id,$key){
		$nowkey=md5($this->guid . $id . date('Y-m-d'));
		$nowkey2=md5($this->guid . $id . date('Y-m-d',time()-(3600*24)));
		if($key==$nowkey||$key==$nowkey2){
			return true;
		}
	}

	/**
	 * 检查应用是否安装并启用
	 * @param string $name 应用（插件或主题）的ID
	 * @return bool
	 */
	function CheckPlugin($name){
		//$s=$this->option['ZC_BLOG_THEME'] . '|' . $this->option['ZC_USING_PLUGIN_LIST'];
		//return HasNameInString($s,$name);
		return in_array($name,$this->activeapps);
	}
	
	/**
	 * 检查应用是否安装并启用
	 * @param string $name 应用ID（插件或主题）
	 * @return bool
	 */
	function CheckApp($name){
		return $this->CheckPlugin($name);
	}

	#$type=category,tag,page,item
	/**
	 * 向导航菜单添加相应条目
	 * @param string $type $type=category,tag,page,item
	 * @param string $id
	 * @param string $name
	 * @param string $url
	 */
	function AddItemToNavbar($type='item',$id,$name,$url){

		if(!$type)$type='item';
		$m=$this->modulesbyfilename['navbar'];
		$s=$m->Content;

		$a='<li id="navbar-'.$type.'-'.$id.'"><a href="'.$url.'">'.$name.'</a></li>';

		if($this->CheckItemToNavbar($type,$id)){
			$s=preg_replace('/<li id="navbar-'.$type.'-'.$id.'">.*?<\/li>/', $a, $s);
		}else{
			$s.='<li id="navbar-'.$type.'-'.$id.'"><a href="'.$url.'">'.$name.'</a></li>';
		}


		$m->Content=$s;
		$m->Save();

	}

	/**
	 * 删除导航菜单中相应条目
	 * @param string $type
	 * @param $id
	 */
	function DelItemToNavbar($type='item',$id){

		if(!$type)$type='item';
		$m=$this->modulesbyfilename['navbar'];
		$s=$m->Content;

		$s=preg_replace('/<li id="navbar-'.$type.'-'.$id.'">.*?<\/li>/', '', $s);

		$m->Content=$s;
		$m->Save();

	}

	/**
	 * 检查条目是否在导航菜单中
	 * @param string $type
	 * @param $id
	 * @return bool
	 */
	function CheckItemToNavbar($type='item',$id){

		if(!$type)$type='item';
		$m=$this->modulesbyfilename['navbar'];
		$s=$m->Content;
		return (bool)strpos($s,'id="navbar-'.$type.'-'.$id.'"');

	}

	#$signal = good,bad,tips
	private $hint1=null,$hint2=null,$hint3=null,$hint4=null,$hint5=null;
	/**
	 * 设置提示消息并存入Cookie
	 * @param string $signal 提示类型（good|bad|tips）
	 * @param string $content 提示内容
	 */
	function SetHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];
		}
		if($this->hint1==null){
			$this->hint1=$signal . '|' . $content;
			setcookie("hint_signal1", $signal . '|' . $content,time()+3600,$this->cookiespath);
		}elseif($this->hint2==null){
			$this->hint2=$signal . '|' . $content;
			setcookie("hint_signal2", $signal . '|' . $content,time()+3600,$this->cookiespath);
		}elseif($this->hint3==null){
			$this->hint3=$signal . '|' . $content;
			setcookie("hint_signal3", $signal . '|' . $content,time()+3600,$this->cookiespath);
		}elseif($this->hint4==null){
			$this->hint4=$signal . '|' . $content;
			setcookie("hint_signal4", $signal . '|' . $content,time()+3600,$this->cookiespath);
		}elseif($this->hint5==null){
			$this->hint5=$signal . '|' . $content;
			setcookie("hint_signal5", $signal . '|' . $content,time()+3600,$this->cookiespath);
		}
	}

	/**
	 * 提取Cookie中的提示消息
	 */
	function GetHint(){
		for ($i = 1; $i <= 5; $i++) {
			$signal=GetVars('hint_signal' . $i,'COOKIE');
			if($signal){
				$a=explode('|', $signal);
				$this->ShowHint($a[0],$a[1]);
				setcookie("hint_signal" . $i , '',time()-3600,$this->cookiespath);
			}
		}
	}

	/**
	 * 显示提示消息
	 * @param string $signal 提示类型（good|bad|tips）
	 * @param string $content 提示内容
	 */
	function ShowHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];
		}
		echo "<div class='hint'><p class='hint hint_$signal'>$content</p></div>";
	}

	/**
	 * 显示错误信息
	 * @api Filter_Plugin_Zbp_ShowError
	 * @param $idortext
	 * @param null $file
	 * @param null $line
	 * @return mixed
	 * @throws Exception
	 */
	function ShowError($idortext,$file=null,$line=null){

		if((int)$idortext==2){
			Http404();
		}

		ZBlogException::$error_id=(int)$idortext;
		ZBlogException::$error_file=$file;
		ZBlogException::$error_line=$line;

		if(is_numeric($idortext))$idortext=$this->lang['error'][$idortext];

		foreach ($GLOBALS['Filter_Plugin_Zbp_ShowError'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($idortext,$file,$line);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}

		throw new Exception($idortext);
	}

	/**
	 * 获取会话Token
	 * @return string
	 */
	function GetToken(){
		return md5($this->guid . date('Ymd') . $this->user->Name . $this->user->Password);
	}

	/**
	 * 验证会话Token
	 * @param $t
	 * @return bool
	 */
	function ValidToken($t){
		if($t==md5($this->guid . date('Ymd') . $this->user->Name . $this->user->Password)){
			return true;
		}
		if($t==md5($this->guid . date('Ymd',strtotime("-1 day")) . $this->user->Name . $this->user->Password)){
			return true;
		}
		return false;
	}

	/**
	 * 显示验证码
	 *
	 * @api Filter_Plugin_Zbp_ShowValidCode 如该接口未被挂载则显示默认验证图片
	 * @param string $id 页面ID
	 * @return mixed
	 */
	function ShowValidCode($id=''){

		foreach ($GLOBALS['Filter_Plugin_Zbp_ShowValidCode'] as $fpname => &$fpsignal) {
			return $fpname($id);//*
		}

		$_vc = new ValidateCode();
		$_vc->GetImg();
		setcookie('zbpvalidcode' . md5($this->guid . $id), md5( $this->guid . date("Ymd") . $_vc->GetCode() ), null,$this->cookiespath);
	}


	/**
	 * 比对验证码
	 *
	 * @api Filter_Plugin_Zbp_CheckValidCode 如该接口未被挂载则比对默认验证码
	 * @param string $vaidcode 验证码数值
	 * @param string $id 页面ID
	 * @return bool
	 */
	function CheckValidCode($vaidcode,$id=''){
		$vaidcode = strtolower($vaidcode);
		foreach ($GLOBALS['Filter_Plugin_Zbp_CheckValidCode'] as $fpname => &$fpsignal) {
			return $fpname($vaidcode,$id);//*
		}

		$original=GetVars('zbpvalidcode' . md5($this->guid . $id),'COOKIE');
		if(md5( $this->guid . date("Ymd") . $vaidcode)==$original) return true;
	}


	/**
	 * 检查并开启Gzip压缩
	 */
	function CheckGzip(){
		if(	extension_loaded("zlib")&&
			isset($_SERVER["HTTP_ACCEPT_ENCODING"])&&
			strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")
			)
			$this->isgzip=true;
	}

	/**
	 * 启用Gzip
	 */
	function StartGzip(){
		if($this->isgziped)return false;

		if(!headers_sent()&&$this->isgzip&&isset($this->option['ZC_GZIP_ENABLE'])&&$this->option['ZC_GZIP_ENABLE']){
			if(ini_get('output_handler'))return false;
			$a=ob_list_handlers();
			if(in_array('ob_gzhandler',$a) || in_array('zlib output compression',$a))return false;
			if(function_exists('ini_set')){
				ini_set('zlib.output_compression', 'On');
				ini_set('zlib.output_compression_level', '5');
			}elseif(function_exists('ob_gzhandler')){
				ob_start('ob_gzhandler');
			}
			ob_start();
			$this->isgziped=true;
			return true;
		}
	}

	/**
	 * 跳转到安装页面
	 * @param bool $yun 是否云主机（SAE等）
	 */
	function  RedirectInstall($yun=false){
		if(!$yun){
			if(!$this->option['ZC_DATABASE_TYPE']){Redirect('./zb_install/index.php');}
		}else{
			if($this->option['ZC_YUN_SITE']){
				if($this->Config('system')->CountItem()==0){Redirect('./zb_install/index.php');}
			}
		}
	}
	
}