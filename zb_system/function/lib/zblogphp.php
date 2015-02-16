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
	 * @var array 语言包list
	 */
	public $langpacklist = array();
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
	 * @var null
	 */
	public $xmlrpcurl = null;
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
	public $categories=null;
	/**
	 * @var array 分类数组（已排序）
	 */
	public $categorysbyorder=array();
	public $categoriesbyorder=null;
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
	 * @var Config|null 缓存
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
	 * @var array|null 类型序列
	 */
	public $posttype=null;
	/**
	 * @var array|null 操作列表
	 */
	public $actions=null;
	/**
	 * @var mixed|null|string 当前操作
	 */
	public $action=null;

	private $isinitialized=false; #是否初始化成功
	private $isconnected=false; #是否连接成功
	private $isload=false; #是否载入
	private $issession=false; #是否使用session
	public $ismanage=false; #是否加载管理模式
	private $isgzip=false; #是否开启gzip

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
	 * @var array 可替换的标签数组
	 */
	public $replacetags = array();
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
	 * 初始化数据库连接
	 * @param string $type 数据连接类型
	 * @return object or null
	 */
	static public function InitializeDB($type){
		if(!trim($type))return null;
		$newtype='Db'.trim($type);
		return new $newtype();
	}

	/**
	 * 构造函数，加载基本配置到$zbp
	 */
	function __construct() {

		global $option,$lang,$blogpath,$bloghost,$cookiespath,$usersdir,$table,
				$datainfo,$actions,$action,$blogversion,$blogtitle,$blogname,$blogsubname,
				$blogtheme,$blogstyle,$currenturl,$activeapps,$posttype;

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
		$this->posttype = &$posttype;
		$this->currenturl = &$currenturl;
		$this->action = &$action;
		$this->activeapps = &$activeapps;

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
		
		$this->categories = &$this->categorys;
		$this->categoriesbyorder = &$this->categorysbyorder;

		$this->user = new stdClass;
		foreach($this->datainfo['Member'] as $key=>$value){
			$this->user->$key=$value[3];
		}
	}


	/**
	 *析构函数，释放资源
	 */
	function __destruct(){
		$this->Terminate();
	}

	/**
     * @api Filter_Plugin_Zbp_Call
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	function __call($method, $args) {
		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Call'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($method, $args);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		trigger_error ( $this->lang['error'][81] ,  E_USER_WARNING );
	}

	/**
	 * 设置参数值
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	function __set($name, $value){
		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Set'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($name, $value);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		trigger_error ( $this->lang['error'][81] ,  E_USER_WARNING );
	}

	/**
	 * 获取参数值
	 * @param $name
	 * @return mixed
	 */
	function __get($name){
		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Get'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($name);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		trigger_error ( $this->lang['error'][81] ,  E_USER_WARNING );
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
		$this->LoadLanguage('system','');

		if($this->option['ZC_SITE_TURNOFF']==true){
			Http503();
			$this->ShowError(82,__FILE__,__LINE__);
			return false;
		}

		if(!$this->OpenConnect()){
			return false;
		}

		$this->LoadConfigs();
		$this->LoadCache();
		$this->LoadOption();

		$this->RegPostType(0,'article',$this->option['ZC_ARTICLE_REGEX'],$this->option['ZC_POST_DEFAULT_TEMPLATE']);
		$this->RegPostType(1,'page',$this->option['ZC_PAGE_REGEX'],$this->option['ZC_POST_DEFAULT_TEMPLATE']);

		if($this->option['ZC_BLOG_LANGUAGEPACK']==='SimpChinese')$this->option['ZC_BLOG_LANGUAGEPACK']='zh-cn';
		if($this->option['ZC_BLOG_LANGUAGEPACK']==='TradChinese')$this->option['ZC_BLOG_LANGUAGEPACK']='zh-tw';

		if($oldlang!=$this->option['ZC_BLOG_LANGUAGEPACK']){
			$this->LoadLanguage('system','');
		}

		if(isset($this->option['ZC_DEBUG_MODE_STRICT'])){
			ZBlogException::$isstrict = (bool)$this->option['ZC_DEBUG_MODE_STRICT'];
		}
		if(isset($this->option['ZC_DEBUG_MODE_WARNING'])){
			ZBlogException::$iswarning = (bool)$this->option['ZC_DEBUG_MODE_WARNING'];
		}
		if(isset($this->option['ZC_DEBUG_LOG_ERROR'])){
			ZBlogException::$islogerror = (bool)$this->option['ZC_DEBUG_LOG_ERROR'];
		}

		if($this->option['ZC_PERMANENT_DOMAIN_ENABLE']==true){
			$this->host=$this->option['ZC_BLOG_HOST'];
			$this->cookiespath=substr($this->host,strpos($this->host,'/',8));
		}else{
			$this->option['ZC_BLOG_HOST']=$this->host;
		}

		$this->option['ZC_BLOG_PRODUCT'] = 'Z-BlogPHP';
		$this->option['ZC_BLOG_VERSION'] = ZC_BLOG_VERSION;
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
		$this->xmlrpcurl = $this->host . 'zb_system/xml-rpc/index.php';

		$this->isinitialized=true;

		return true;
	}


	/**
	 * 载入
	 * @return bool
	 */
	public function Load(){

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Load_Pre'] as $fpname => &$fpsignal) {
			$fpreturn = $fpname();
			if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
				$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;
			}
		}

		if(!$this->isinitialized){
			return false;
		}

		if($this->isload)return false;

		$this->StartGzip();

		header('Content-type: text/html; charset=utf-8');

		$this->LoadMembers($this->option['ZC_LOADMEMBERS_LEVEL']);
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

		if($this->ismanage){
			$this->LoadManage();
		}else{
			if(isset($this->templates['404']))
				Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','Include_ShowError404');

			$ak = array_keys($this->replacetags);
			$av = array_values($this->replacetags);
			foreach($this->modulesbyfilename as &$m)
				$m->Content = str_replace($ak,$av,$m->Content);
		}

		Add_Filter_Plugin('Filter_Plugin_Login_Header','Include_AddonAdminFont');
		Add_Filter_Plugin('Filter_Plugin_Other_Header','Include_AddonAdminFont');
		Add_Filter_Plugin('Filter_Plugin_Admin_Header','Include_AddonAdminFont');

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Load'] as $fpname => &$fpsignal) $fpname();

		$this->isload=true;

		return true;
	}

	/**
	 * 载入管理
	 */
	public function LoadManage(){

		if( !(isset($this->option['ZC_PERMANENT_DOMAIN_WITH_ADMIN'])  && $this->option['ZC_PERMANENT_DOMAIN_WITH_ADMIN']) )
			$this->host = GetCurrentHost($this->path,$this->cookiespath);

		if($this->user->Status==ZC_MEMBER_STATUS_AUDITING) $this->ShowError(79,__FILE__,__LINE__);
		if($this->user->Status==ZC_MEMBER_STATUS_LOCKED) $this->ShowError(80,__FILE__,__LINE__);

		Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu','Include_Admin_Addpagesubmenu');
		Add_Filter_Plugin('Filter_Plugin_Admin_TagMng_SubMenu','Include_Admin_Addtagsubmenu');
		Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu','Include_Admin_Addcatesubmenu');
		Add_Filter_Plugin('Filter_Plugin_Admin_MemberMng_SubMenu','Include_Admin_Addmemsubmenu');
		Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu','Include_Admin_Addmodsubmenu');
		Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu','Include_Admin_Addcmtsubmenu');

		$this->CheckTemplate();

		if(GetVars('addinfo' . str_replace('/','',$this->cookiespath) ,'COOKIE')){
			$dishtml5=json_decode(GetVars('addinfo' . str_replace('/','',$this->cookiespath),'COOKIE'));
			if(is_object($dishtml5) && property_exists($dishtml5,'dishtml5'))
				$dishtml5=(bool)$dishtml5->dishtml5;
			else
				$dishtml5=false;
			if($dishtml5)
				$this->option['ZC_ADMIN_HTML5_ENABLE']=false;
			else
				$this->option['ZC_ADMIN_HTML5_ENABLE']=true;
		}

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_LoadManage'] as $fpname => &$fpsignal) $fpname();

	}

	/**
	 *终止连接，释放资源
	 */
	public function Terminate(){
		if($this->isinitialized){
			foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Terminate'] as $fpname => &$fpsignal) $fpname();
			$this->CloseConnect();
			unset($this->db);
			$this->isinitialized=false;
		}
	}


	/**
	 * 连接数据库
	 * @return bool
	 * @throws Exception
	 */
	public function OpenConnect(){

		if($this->isconnected)return false;
		if(!$this->option['ZC_DATABASE_TYPE'])return false;
		switch ($this->option['ZC_DATABASE_TYPE']) {
			case 'sqlite':
			case 'sqlite3':
			case 'pdo_sqlite':
				$this->db = ZBlogPHP::InitializeDB($this->option['ZC_DATABASE_TYPE']);
				if($this->db->Open(array(
						$this->usersdir . 'data/' . $this->option['ZC_SQLITE_NAME'],
						$this->option['ZC_SQLITE_PRE']
					))==false){
					$this->ShowError(69,__FILE__,__LINE__);
				}
				break;
			case 'pgsql':
			case 'pdo_pgsql':
				$this->db = ZBlogPHP::InitializeDB($this->option['ZC_DATABASE_TYPE']);
				if($this->db->Open(array(
						$this->option['ZC_PGSQL_SERVER'],
						$this->option['ZC_PGSQL_USERNAME'],
						$this->option['ZC_PGSQL_PASSWORD'],
						$this->option['ZC_PGSQL_NAME'],
						$this->option['ZC_PGSQL_PRE'],
						$this->option['ZC_PGSQL_PORT'],
						$this->option['ZC_PGSQL_PERSISTENT']
					))==false){
					$this->ShowError(67,__FILE__,__LINE__);
				}
				break;
			case 'mysql':
			case 'mysqli':
			case 'pdo_mysql':
			default:
				$this->db = ZBlogPHP::InitializeDB($this->option['ZC_DATABASE_TYPE']);
				if($this->db->Open(array(
						$this->option['ZC_MYSQL_SERVER'],
						$this->option['ZC_MYSQL_USERNAME'],
						$this->option['ZC_MYSQL_PASSWORD'],
						$this->option['ZC_MYSQL_NAME'],
						$this->option['ZC_MYSQL_PRE'],
						$this->option['ZC_MYSQL_PORT'],
						$this->option['ZC_MYSQL_PERSISTENT'],
						$this->option['ZC_MYSQL_ENGINE'],
					))==false){
					$this->ShowError(67,__FILE__,__LINE__);
				}
				break;
		}
		$this->isconnected=true;
		return true;

	}

	/**
	 * 关闭数据库连接
	 */
	public function CloseConnect(){
		if($this->isconnected){
			$this->db->Close();
			$this->isconnected=false;
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

		$array = $this->GetListType('Config',$sql);
		foreach ($array as $c) {
			$n=$c->GetItemName();
			$this->configs[$n]=$c;
		}return;
		$configs_name = $configs_namevalue = array();
		foreach ($array as $c) {
			$n=$c->GetItemName();
			$configs_name[$n]=$n;
			$configs_namevalue[$n]=$c;
		}
		natcasesort($configs_name);
		foreach ($configs_name as $name) {
			$this->configs[$name]=$configs_namevalue[$name];
		}
		unset($configs_name,$configs_namevalue);
	}

	/**
	 * 保存Configs表
	 * @param string $name Configs表名
	 * @return bool
	 */
	public function SaveConfig($name){
		if(!isset($this->configs[$name]))return false;
		$this->configs[$name]->Save();
		return true;
	}

	/**
	 * 删除Configs表
	 * @param string $name Configs表名
	 * @return bool
	 */
	public function DelConfig($name){
		if(!isset($this->configs[$name]))return false;
		$this->configs[$name]->Delete();
		return true;
	}

	/**
	 * 获取Configs表值
	 * @param string $name Configs表名
	 * @return mixed
	 */
	public function Config($name){
		if(!isset($this->configs[$name])){
			$name=FilterCorrectName($name);
			if(!$name)return ;
			$this->configs[$name]=new Config($name);
		}
		return $this->configs[$name];
	}

	/**
	 * 查某Config是否存在
	 * @param string $name Configs表名
	 * @return bool
	 */
	public function HasConfig($name){
		return isset($this->configs[$name]);
	}

################################################################################################################
#Cache相关
	private $cache_hash = null;

	/**
	 * 保存缓存
	 * @return bool
	 */
	public function SaveCache(){
		#$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
		#$c=serialize($this->cache);
		#@file_put_contents($s, $c);
		//$this->configs['cache']=$this->cache;
		$new_hash = md5($this->Config('cache'));
		if($this->cache_hash == $new_hash)return true;
		$this->SaveConfig('cache');
		$this->cache_hash = $new_hash;
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
		$this->cache = $this->Config('cache');
		$this->cache_hash = md5($this->Config('cache'));
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

		if( strpos('|SAE|BAE2|ACE|TXY|', '|'.$this->option['ZC_YUN_SITE'].'|')===false && file_exists($this->usersdir . 'c_option.php')==false ){
			$s="<" . "?" . "php\r\n";
			$s.="return ";
			$option = array();
			foreach ($this->option as $key => $value) {
				if(
					($key=='ZC_YUN_SITE') ||
					($key=='ZC_DATABASE_TYPE') ||
					($key=='ZC_SQLITE_NAME') ||
					($key=='ZC_SQLITE_PRE') ||
					($key=='ZC_MYSQL_SERVER') ||
					($key=='ZC_MYSQL_USERNAME') ||
					($key=='ZC_MYSQL_PASSWORD') ||
					($key=='ZC_MYSQL_NAME') ||
					($key=='ZC_MYSQL_CHARSET') ||
					($key=='ZC_MYSQL_PRE') ||
					($key=='ZC_MYSQL_ENGINE') ||
					($key=='ZC_MYSQL_PORT') ||
					($key=='ZC_MYSQL_PERSISTENT') ||
					($key=='ZC_PGSQL_SERVER') ||
					($key=='ZC_PGSQL_USERNAME') ||
					($key=='ZC_PGSQL_PASSWORD') ||
					($key=='ZC_PGSQL_NAME') ||
					($key=='ZC_PGSQL_CHARSET') ||
					($key=='ZC_PGSQL_PRE') ||
					($key=='ZC_PGSQL_PORT') ||
					($key=='ZC_PGSQL_PERSISTENT') ||
					($key=='ZC_SITE_TURNOFF')
				)$option[$key]=$value;
			}
			$s.=var_export($option,true);
			$s.=";";
			@file_put_contents($this->usersdir . 'c_option.php',$s);
		}

		foreach ($this->option as $key => $value) {
			$this->Config('system')->$key = $value;
		}

		$this->Config('system')->ZC_BLOG_HOST = chunk_split($this->Config('system')->ZC_BLOG_HOST,1,"|");
		$this->SaveConfig('system');
		return true;
	}


	/**
	 * 载入配置
	 * @return bool
	 */
	public function LoadOption(){

		$array=$this->Config('system')->GetData();

		if(empty($array))return false;
		if(!is_array($array))return false;
		foreach ($array as $key => $value) {
			//if($key=='ZC_PERMANENT_DOMAIN_ENABLE')continue;
			//if($key=='ZC_BLOG_HOST')continue;
			//if($key=='ZC_BLOG_CLSID')continue;
			//if($key=='ZC_BLOG_LANGUAGEPACK')continue;
			if($key=='ZC_BLOG_HOST')
				$value=str_replace('|','',$value);
			if(
				($key=='ZC_YUN_SITE') ||
				($key=='ZC_DATABASE_TYPE') ||
				($key=='ZC_SQLITE_NAME') ||
				($key=='ZC_SQLITE_PRE') ||
				($key=='ZC_MYSQL_SERVER') ||
				($key=='ZC_MYSQL_USERNAME') ||
				($key=='ZC_MYSQL_PASSWORD') ||
				($key=='ZC_MYSQL_NAME') ||
				($key=='ZC_MYSQL_CHARSET') ||
				($key=='ZC_MYSQL_PRE') ||
				($key=='ZC_MYSQL_ENGINE') ||
				($key=='ZC_MYSQL_PORT') ||
				($key=='ZC_MYSQL_PERSISTENT') ||
				($key=='ZC_PGSQL_SERVER') ||
				($key=='ZC_PGSQL_USERNAME') ||
				($key=='ZC_PGSQL_PASSWORD') ||
				($key=='ZC_PGSQL_NAME') ||
				($key=='ZC_PGSQL_CHARSET') ||
				($key=='ZC_PGSQL_PRE') ||
				($key=='ZC_PGSQL_PORT') ||
				($key=='ZC_PGSQL_PERSISTENT') ||
				($key=='ZC_SITE_TURNOFF')
			)continue;
			$this->option[$key]=$value;
		}
		if(!extension_loaded('gd'))$this->option['ZC_COMMENT_VERIFY_ENABLE'] = False;
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

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_CheckRights'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($action);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
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

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_CheckRightsByLevel'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($level,$action);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
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
		$m = null;
		$u = trim(GetVars('username','COOKIE'));
		$p = trim(GetVars('password','COOKIE'));
		if($this->Verify_MD5Path($u,$p,$m)==true){
			$this->user = $m;
			return true;
		}
		$this->user = new Member;
		return false;
	}

	/**
	 * 验证用户登录（MD5加zbp->guid盐后的密码）
	 * @param string $name 用户名
	 * @param string $ps_path_hash MD5加zbp->guid盐后的密码
	 * @param object $member 返回读取成功的member对象
	 * @return bool
	 */
	public function Verify_MD5Path($name,$ps_path_hash,&$member=null){
		if($name=='' || $ps_path_hash==''){
			return false;
		}
		$m = $this->GetMemberByName($name);
		if ($m->ID > 0){
			if($m->PassWord_MD5Path == $ps_path_hash){
				$member=$m;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 验证用户登录（一次MD5密码）
	 * @param string $name 用户名
	 * @param string $md5pw md5加密后的密码
	 * @param object $member 返回读取成功的member对象
	 * @return bool
	 */
	public function Verify_MD5($name,$md5pw,&$member=null){
		if($name=='' || $md5pw==''){
			return false;
		}
		$m = $this->GetMemberByName($name);
		if ($m->ID > 0){
			return $this->Verify_Final($name,md5($md5pw . $m->Guid),$member);
		}else{
			return false;
		}
	}

	/**
	 * 验证用户登录（原始明文密码）
	 * @param string $name 用户名
	 * @param string $originalpw 密码明文
	 * @param object $member 返回读取成功的member对象
	 * @return bool
	 */
	public function Verify_Original($name,$originalpw,&$member=null){
		if($name=='' || $originalpw==''){
			return false;
		}
		$m = $this->GetMemberByName($name);
		if ($m->ID > 0){
			return $this->Verify_MD5($name,md5($originalpw),$member);
		}else{
			return false;
		}
	}

	/**
	 * 验证用户登录（数据库保存的最终运算后密码）
	 * @param string $name 用户名
	 * @param string $password 二次加密后的密码
	 * @param object $member 返回读取成功的member对象
	 * @return bool
	 */
	public function Verify_Final($name,$password,&$member=null){
		if($name=='' || $password==''){
			return false;
		}
		$m = $this->GetMemberByName($name);
		if ($m->ID > 0){
			if(strcasecmp( $m->Password ,  $password ) ==  0){
				$member = $m;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}









################################################################################################################
#
	/**
	 * 生成模块
	 */
	function BuildModule(){

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_BuildModule'] as $fpname => &$fpsignal)$fpname();

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
	public function LoadMembers($level = 0){
		if($level == -1) return ;
		$where = null;
		if($level > 0){
			$where = array(array('<=','mem_Level',$level));
		}
		$this->members = array();
		$this->membersbyname = array();
		$array=$this->GetMemberList(null,$where);
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

		$this->categorys = array();
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

		$this->tags = array();
		$this->tagsbyname = array();
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

		$this->modules = array();
		$this->modulesbyfilename = array();
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

		$this->themes = array();
		$dirs=GetDirsInDir($this->usersdir . 'theme/');
		natcasesort($dirs);
		array_unshift($dirs,$this->theme);
		$dirs=array_unique($dirs);
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

		$this->plugins = array();
		$dirs=GetDirsInDir($this->usersdir . 'plugin/');
		natcasesort($dirs);

		foreach ($dirs as $id) {
			$app = new App;
			if($app->LoadInfoByXml('plugin',$id)==true){
				$this->plugins[]=$app;
			}
		}

	}

	/**
	 * 载入指定应用
	 * @param string $type 应用类型(theme|plugin)
	 * @param string $id 应用ID
	 * @return App
	 */
	public function LoadApp($type,$id){
		$app = new App;
		$app->LoadInfoByXml($type,$id);
		return $app;
	}

	/**
	 * 载入指定应用语言包
	 * @param string $type 应用类型(system|theme|plugin)
	 * @param string $id 应用ID
	 * @return null
	 */
	public function LoadLanguage($type,$id,$default=''){
		if($type=='system'){
			if($default=='')$default=$this->option['ZC_BLOG_LANGUAGEPACK'];
			if(is_readable($f=$this->path . 'zb_users/language/' . $default . '.php')){
				$this->lang = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='zh-cn';
			if(is_readable($f=$this->path . 'zb_users/language/' . $default . '.php')){
				$this->lang = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='en';
			if(is_readable($f=$this->path . 'zb_users/language/' . $default . '.php')){
				$this->lang = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
		}elseif($type=='plugin' || $type=='theme'){
			if($default=='')$default=$this->option['ZC_BLOG_LANGUAGEPACK'];
			if(is_readable($f=$this->path . 'zb_users/'.$type.'/'.$id.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='zh-cn';
			if(is_readable($f=$this->path . 'zb_users/'.$type.'/'.$id.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='en';
			if(is_readable($f=$this->path . 'zb_users/'.$type.'/'.$id.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
		}elseif($type!='' && $id!=''){
			if($default=='')$default=$this->option['ZC_BLOG_LANGUAGEPACK'];
			if(is_readable($f=$this->path . $type.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='zh-cn';
			if(is_readable($f=$this->path . $type.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
			$default='en';
			if(is_readable($f=$this->path . $type.'/language/' . $default . '.php')){
				$this->lang[$id] = require($f);
				$this->langpacklist[]=array($type,$id,$default);
				return true;
			}
		}
	}

	public function ReloadLanguages($default){
		$array = $this->langpacklist;
		$this->lang = $this->langpacklist = array();
		foreach($array as $v){
			$this->LoadLanguage($v[0],$v[1],$v[2]);
		}
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
		unset($option['ZC_MYSQL_USERNAME']);
		unset($option['ZC_MYSQL_PASSWORD']);
		unset($option['ZC_MYSQL_NAME']);
		unset($option['ZC_MYSQL_PORT']);
		unset($option['ZC_MYSQL_SERVER']);
		unset($option['ZC_PGSQL_USERNAME']);
		unset($option['ZC_PGSQL_PASSWORD']);
		unset($option['ZC_PGSQL_NAME']);
		unset($option['ZC_PGSQL_PORT']);
		unset($option['ZC_PGSQL_SERVER']);
		unset($option['ZC_DATABASE_TYPE']);

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

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_MakeTemplatetags'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($this->templatetags);
		}

		$t=array();
		$o=array();
		foreach($this->templatetags as $k => $v){
			if(is_string($v) || is_numeric($v) || is_bool($v) )
				$t['{$' . $k . '}']=$v;
		}
		foreach($option as $k => $v){
			if(is_string($v) || is_numeric($v) || is_bool($v) )
				$o['{#' . $k . '#}']=$v;
		}
		$this->replacetags = $t + $o;
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

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_PrepareTemplate'] as $fpname => &$fpsignal) {
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

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_LoadTemplate'] as $fpname => &$fpsignal) {
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
		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_BuildTemplate'] as $fpname => &$fpsignal) {
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
	 * 查询指定数据结构的sql并返回Base对象列表
	 * @param string $table 数据表
	 * @param string $datainfo 数据字段
	 * @param string $sql SQL操作语句
	 * @return array
	 */
	public function GetListCustom($table,$datainfo,$sql){

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
	 * 查询ID数据的指定数据结构的sql并返回Base对象列表
	 * @param string $table 数据表
	 * @param string $datainfo 数据字段
	 * @param array $array ID数组
	 * @return array
	 */
	public function GetListCustomByArray($table,$datainfo,$array){
		if(!is_array($array))return array();
		if(count($array)==0)return array();
		$where = array();
		$where[] = array('IN',$datainfo['ID'][0],implode(',',$array));
		$sql = $this->db->sql->Select($table,'*',$where);
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
	 * 已改名GetListType,1.5版中扔掉有歧义的GetList
	 *
	 * @param $type
	 * @param $sql
	 * @return array
	 */
	public function GetListType($type,$sql){

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
	 * 查询ID数据的指定类型的sql并返回指定类型对象列表
	 * @param string $type 类型
	 * @param array $array ID数组
	 * @return array
	 */
	public function GetListTypeByArray($type,$array){
		if(!is_array($array))return array();
		if(count($array)==0)return array();
		$where = array();
		$where[] = array('IN',$this->datainfo[$type]['ID'][0],implode(',',$array));
		$sql = $this->db->sql->Select($this->table[$type],'*',$where);
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
	public function GetPostList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);

		$array = $this->GetListType('Post',$sql);
		foreach ($array as $a) {
			$this->posts[$a->ID]=$a;
		}
		return $array;
	}

	/**
	 * 通过ID数组获取文章实例
	 * @param array $array
	 * @return array Posts
	 */
	public function GetPostByArray($array){
		return $this->GetListTypeByArray('Post',$array);
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
	public function GetArticleList($select=null,$where=null,$order=null,$limit=null,$option=null,$readtags=true){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		if(is_array($where))array_unshift($where,array('=','log_Type','0'));
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);
		$array = $this->GetListType('Post',$sql);

		foreach ($array as $a) {
			$this->posts[$a->ID]=$a;
		}

		if($readtags){
			$tagstring = '';
			foreach ($array as $a) {
				$tagstring .= $a->Tag;
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
	public function GetPageList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		if(empty($where)){$where = array();}
		if(is_array($where))array_unshift($where,array('=','log_Type','1'));
		$sql = $this->db->sql->Select($this->table['Post'],$select,$where,$order,$limit,$option);
		$array = $this->GetListType('Post',$sql);
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
	public function GetCommentList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Comment'],$select,$where,$order,$limit,$option);
		$array=$this->GetListType('Comment',$sql);
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
	public function GetMemberList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Member'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Member',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	public function GetTagList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Tag'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Tag',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	public function GetCategoryList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Category'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Category',$sql);

	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	public function GetModuleList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Module'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Module',$sql);
	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	public function GetUploadList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Upload'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Upload',$sql);
	}

	/**
	 * @param null $select
	 * @param null $where
	 * @param null $order
	 * @param null $limit
	 * @param null $option
	 * @return array
	 */
	public function GetCounterList($select=null,$where=null,$order=null,$limit=null,$option=null){

		if(empty($select)){$select = array('*');}
		$sql = $this->db->sql->Select($this->table['Counter'],$select,$where,$order,$limit,$option);
		return $this->GetListType('Counter',$sql);
	}


################################################################################################################
#wp类似

	/**
	 * @param $sql
	 * @return mixed
	 */
	public function get_results($sql){
		return $this->db->Query($sql);
	}


################################################################################################################
#读取对象函数


	/**
	 * 通过ID获取文章实例
	 * @param int $id
	 * @return Post
	 */
	public function GetPostByID($id){
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
	public function GetCategoryByID($id){
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
	public function GetCategoryByName($name){
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
	public function GetCategoryByAliasOrName($name){
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
	public function GetModuleByID($id){
		if($id==0){
			$m = new Module;
		}else{
			foreach ($this->modules as $key => $value) {
				if($value->ID==$id)return $value;
			}
			$m = new Module;
		}
		return $m;
	}

	/**
	 * 通过FileName获取模块实例
	 * @param string $fn
	 * @return Module
	 */
	public function GetModuleByFileName($fn){
		$fn=trim($fn);
		if(!$fn){
			$m = new Module;
		}else{
			if(isset($this->modulesbyfilename[$fn])){
				return $this->modulesbyfilename[$fn];
			}
			$m = new Module;
		}
		return $m;
	}

	/**
	 * 通过ID获取用户实例
	 * @param int $id
	 * @return Member
	 */
	public function GetMemberByID($id){
		if($id==0){
			$m = new Member;
			$m->Guid=GetGuid();
			return $m;
		}
		if(isset($this->members[$id])){
			return $this->members[$id];
		}

		$sql = $this->db->sql->Select($this->table['Member'],'*',array(array('=','mem_ID',$id)),null,1,null);
		$am = $this->GetListType('Member',$sql);
		if(count($am) == 1){
			$m = $am[0];
			$this->members[$m->ID] = $m;
			$this->membersbyname[$m->Name] = &$this->members[$m->ID];
			return $m;
		};

		$m = new Member;
		$m->Guid=GetGuid();
		return $m;
	}

	/**
	 * 通过用户名获取用户实例(不区分大小写)
	 * @param string $name
	 * @return Member
	 */
	public function GetMemberByName($name){
		$name=trim($name);
		if (!$name || !CheckRegExp($name, '[username]'))return new Member;

		if(isset($this->membersbyname[$name])){
			return $this->membersbyname[$name];
		}else{
			$array = array_keys($this->membersbyname);
			foreach($array as $k=>$v){
				if(strcasecmp($name,$v)==0){
					return $this->membersbyname[$v];
				}
			}
		}

		$like=($this->db->type == 'pgsql')?'ILIKE':'LIKE';
		$sql = $this->db->sql->Select($this->table['Member'],'*',array(array($like,'mem_Name',$name)),null,1,null);
		$am = $this->GetListType('Member',$sql);
		if(count($am) > 0){
			$m = $am[0];
			$this->members[$m->ID] = $m;
			$this->membersbyname[$m->Name] = &$this->members[$m->ID];
			return $m;
		};

		$m = new Member;
		$m->Guid=GetGuid();
		return $m;
	}

	/**
	 * 通过获取用户名或别名实例(不区分大小写)
	 * @param string $name
	 * @return Member
	 */
	public function GetMemberByNameOrAlias($name){
		$name=trim($name);
		if (!$name || !CheckRegExp($name, '[username]'))return new Member;

		foreach ($this->members as $key => &$value) {
			if(strcasecmp($value->Name,$name)==0||strcasecmp($value->Alias,$name)==0){
				return $value;
			}
		}

		$like=($this->db->type == 'pgsql')?'ILIKE':'LIKE';
		$sql=$this->db->sql->Select(
			$this->table['Member'],'*',
			//where
				$this->db->sql->ParseWhere(array(array($like,'mem_Name',$name)),'')
				.
				$this->db->sql->ParseWhere(array(array($like,'mem_Alias',$name)),'OR'),
			null,
			1,
			null
		);
		$am = $this->GetListType('Member',$sql);
		if(count($am) > 0){
			$m = $am[0];
			$this->members[$m->ID] = $m;
			$this->membersbyname[$m->Name] = &$this->members[$m->ID];
			return $m;
		};

		return new Member;
	}

	/**
	 * 检查指定名称的用户是否存在(不区分大小写)
	 */
	public function CheckMemberNameExist($name){
		$m=$this->GetMemberByName($name);
		if($m->ID>0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 检查指定名称或别名的用户是否存在(不区分大小写)
	 */
	public function CheckMemberByNameOrAliasExist($name){
		$m=$this->GetMemberByNameOrAlias($name);
		if($m->ID>0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 通过ID获取评论实例
	 * @param int $id
	 * @return Comment
	 */
	public function GetCommentByID($id){
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
	public function GetUploadByID($id){
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
	public function GetCounterByID($id){
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
	public function GetTagByAliasOrName($name){
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
	public function GetTagByID($id){
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
	 * 通过类似'{1}{2}{3}{4}'载入tags
	 * @param $s
	 * @return array
	 */
	public function LoadTagsByIDString($s){
		$s=trim($s);
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
	public function LoadTagsByNameString($s){
		$s=trim($s);
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

	/**
	 * 通过数组array[111,333,444,555,666]转换成存储串
	 * @param array $array 标签ID数组
	 * @return string
	 */
	public function ConvertTagIDtoString($array){
		$s='';
		foreach($array as $a){
			$s .= '{' . $a . '}';
		}
		return $s;
	}
	
	/**
	 * 获取全部置顶文章（优先从cache里读数组）
	 */
	public function GetTopArticle(){
		if(!is_object($this->cache))return array();
		$articles_top_notorder_idarray = unserialize($this->cache->top_post_array);
		if(!is_array($articles_top_notorder_idarray)){
			CountTopArticle(null,null);
			$articles_top_notorder_idarray = unserialize($this->cache->top_post_array);
		}
		$articles_top_notorder=$this->GetPostByArray($articles_top_notorder_idarray);
		return $articles_top_notorder;
	}

################################################################################################################
#杂项
	/**
	 * 验证评论key
	 * @param $id
	 * @param $key
	 * @return bool
	 */
	public function VerifyCmtKey($id,$key){
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
	public function CheckPlugin($name){
		//$s=$this->option['ZC_BLOG_THEME'] . '|' . $this->option['ZC_USING_PLUGIN_LIST'];
		//return HasNameInString($s,$name);
		return in_array($name,$this->activeapps);
	}

	/**
	 * 检查应用是否安装并启用
	 * @param string $name 应用ID（插件或主题）
	 * @return bool
	 */
	public function CheckApp($name){
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
	public function AddItemToNavbar($type='item',$id,$name,$url){

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
	public function DelItemToNavbar($type='item',$id){

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
	public function CheckItemToNavbar($type='item',$id){

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
	public function SetHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];
		}
		$content=substr($content,0,255);
		if($this->hint1==null){
			$this->hint1=$signal . '|' . $content;
			setcookie("hint_signal1", $signal . '|' . $content,0,$this->cookiespath);
		}elseif($this->hint2==null){
			$this->hint2=$signal . '|' . $content;
			setcookie("hint_signal2", $signal . '|' . $content,0,$this->cookiespath);
		}elseif($this->hint3==null){
			$this->hint3=$signal . '|' . $content;
			setcookie("hint_signal3", $signal . '|' . $content,0,$this->cookiespath);
		}elseif($this->hint4==null){
			$this->hint4=$signal . '|' . $content;
			setcookie("hint_signal4", $signal . '|' . $content,0,$this->cookiespath);
		}elseif($this->hint5==null){
			$this->hint5=$signal . '|' . $content;
			setcookie("hint_signal5", $signal . '|' . $content,0,$this->cookiespath);
		}
	}

	/**
	 * 提取Cookie中的提示消息
	 */
	public function GetHint(){
		for ($i = 1; $i <= 5; $i++) {
			$signal='hint' . $i;
			$signal=$this->$signal;
			if($signal){
				$a=explode('|', $signal);
				$this->ShowHint($a[0],$a[1]);
				setcookie("hint_signal" . $i , '',time()-3600,$this->cookiespath);
			}
		}
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
	public function ShowHint($signal,$content=''){
		if($content==''){
			if($signal=='good')$content=$this->lang['msg']['operation_succeed'];
			if($signal=='bad')$content=$this->lang['msg']['operation_failed'];
		}
		echo "<div class=\"hint\"><p class=\"hint hint_$signal\">$content</p></div>";
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
	public function ShowError($idortext,$file=null,$line=null){

		if((int)$idortext==2){
			Http404();
		}

		ZBlogException::$error_id=(int)$idortext;
		ZBlogException::$error_file=$file;
		ZBlogException::$error_line=$line;

		if(is_numeric($idortext))$idortext=$this->lang['error'][$idortext];

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_ShowError'] as $fpname => &$fpsignal) {
			$fpsignal=PLUGIN_EXITSIGNAL_NONE;
			$fpreturn=$fpname($idortext,$file,$line);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		throw new Exception($idortext);
	}

	/**
	 * 获取会话Token
	 * @return string
	 */
	public function GetToken(){
		return md5($this->guid . date('Ymd') . $this->user->Guid);
	}

	/**
	 * 验证会话Token
	 * @param $t
	 * @return bool
	 */
	public function ValidToken($t){
		if($t==md5($this->guid . date('Ymd') . $this->user->Guid)){
			return true;
		}
		if($t==md5($this->guid . date('Ymd',strtotime("-1 day")) . $this->user->Guid)){
			return true;
		}
		return false;
	}

	/**
	 * 显示验证码
	 *
	 * @api Filter_Plugin_Zbp_ShowValidCode 如该接口未被挂载则显示默认验证图片
	 * @param string $id 命名事件
	 * @return mixed
	 */
	public function ShowValidCode($id=''){

		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_ShowValidCode'] as $fpname => &$fpsignal) {
			return $fpname($id);//*
		}

		$_vc = new ValidateCode();
		$_vc->GetImg();
		setcookie('captcha_' . crc32($this->guid . $id), md5( $this->guid . date("Ymd") . $_vc->GetCode() ), null,$this->cookiespath);
	}


	/**
	 * 比对验证码
	 *
	 * @api Filter_Plugin_Zbp_CheckValidCode 如该接口未被挂载则比对默认验证码
	 * @param string $vaidcode 验证码数值
	 * @param string $id 命名事件
	 * @return bool
	 */
	public function CheckValidCode($vaidcode,$id=''){
		$vaidcode = strtolower($vaidcode);
		foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_CheckValidCode'] as $fpname => &$fpsignal) {
			return $fpname($vaidcode,$id);//*
		}

		$original=GetVars('captcha_' . crc32($this->guid . $id),'COOKIE');
		if(md5( $this->guid . date("Ymd") . $vaidcode)==$original) return true;
	}


	/**
	 * 检查并开启Gzip压缩
	 */
	public function CheckGzip(){
		if( extension_loaded("zlib")&&
			isset($_SERVER["HTTP_ACCEPT_ENCODING"])&&
			strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")
			)
			$this->isgzip=true;
	}

	/**
	 * 启用Gzip
	 */
	public function StartGzip(){

		if( !headers_sent() && $this->isgzip && $this->option['ZC_GZIP_ENABLE'] ){
			if(ini_get('output_handler'))return false;
			$a=ob_list_handlers();
			if(in_array('ob_gzhandler',$a) || in_array('zlib output compression',$a))return false;
			if(function_exists('ini_set') && $this->option['ZC_YUN_SITE']!=='SAE'){
				ini_set('zlib.output_compression', 'On');
				ini_set('zlib.output_compression_level', '5');
			}elseif(function_exists('ob_gzhandler')){
				ob_start('ob_gzhandler');
			}
			ob_start();
			return true;
		}
	}

	/**
	 * 跳转到安装页面
	 */
	public function  RedirectInstall(){

		if(!$this->option['ZC_DATABASE_TYPE'])
			Redirect('./zb_install/index.php');

		if($this->option['ZC_YUN_SITE'])
			if($this->Config('system')->CountItem()==0)
				Redirect('./zb_install/index.php');
	}


	/**
	 * 检测当前url，如果不符合设置就跳转到固定域名的链接
	 */
	public function  RedirectPermanentDomain(){

		if($this->option['ZC_PERMANENT_DOMAIN_ENABLE']==false)return;
		if($this->option['ZC_PERMANENT_DOMAIN_REDIRECT']==false)return;

		$host = GetCurrentHost(ZBP_PATH , $null);

		if(stripos($host,$this->host)===false){
			$u=GetRequestUri();
			$u=$this->host . substr($u,1,strlen($u));
			Redirect301($u);
		}
	}
	/**
	 * 对表名和数据结构进行预转换
	 */
	private $table_datainfo_hash = null;
	public function ConvertTableAndDatainfo(){

		$now = crc32(serialize($this->table + $this->datainfo));
		if($this->table_datainfo_hash!==$now){
			$this->table = str_replace('%pre%', $this->db->dbpre, $this->table);
			if($this->db->type == 'pgsql'){
				foreach($this->datainfo as $key=>&$value){
					foreach($value as $k2=>&$v2){
						$v2[0]=strtolower($v2[0]);
					}
				}
			}
			$this->table_datainfo_hash = crc32(serialize($this->table + $this->datainfo));
		}
	}

	/**
	 * 获取已激活插件名数组
	 */
	public function GetActivePlugin(){
		$ap=explode("|", $this->option['ZC_USING_PLUGIN_LIST']);
		$ap=array_unique($ap);
		return $ap;
	}

	/**
	 * 注册PostType
	 * int $typeid 系统定义在0-99，插件自定义100-255
	 * string $urlrule 默认是取Page类型的Url Rule
	 * string $template 默认模板名page
	 */
	public function RegPostType($typeid,$name,$urlrule='',$template=''){
		if($urlrule=='')$urlrule=$this->option['ZC_PAGE_REGEX'];
		if($template=='')$template='page';
		$typeid=(int)$typeid;
		$name=strtolower(trim($name));
		if($typeid>99){
			if(isset($this->posttype[$typeid]))$this->ShowError(87,__FILE__,__LINE__);
		}
		$this->posttype[$typeid]=array($name,$urlrule,$template);
	}
	public function GetPostType_Name($typeid){
		if(isset($this->posttype[$typeid]))
			return $this->posttype[$typeid][0];
		return '';
	}
	public function GetPostType_UrlRule($typeid){
		if(isset($this->posttype[$typeid]))
			return $this->posttype[$typeid][1];
		else
			return $this->option['ZC_PAGE_REGEX'];
	}
	public function GetPostType_Template($typeid){
		if(isset($this->posttype[$typeid]))
			return $this->posttype[$typeid][2];
		else
			return 'single';
	}

	/**
	 * 注册Action
	 */
	public function RegAction($name,$level,$title){
		$this->actions[$name]=$level;
		$this->lang['actions'][$name]=$title;
	}
	public function GetAction_Title($name){
		if(isset($this->lang['actions'][$name]))return $this->lang['actions'][$name];
		return $name;
	}
}