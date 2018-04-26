<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * zbp全局操作类.
 */
class ZBlogPHP
{
    private static $_zbp = null;
    /**
     * @var string 版本号
     */
    public $version = null;
    /**
     * @var Database__Interface 数据库
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
     * @var string 路径
     */
    public $path = null;
    /**
     * @var string 域名
     */
    public $host = null;
    /**
     * @var string cookie作用域
     */
    public $cookiespath = null;
    /**
     * @var string guid
     */
    public $guid = null;
    /**
     * @var string 当前链接
     */
    public $currenturl = null;
    /**
     * @var string 当前链接
     */
    public $fullcurrenturl = null;
    /**
     * @var string 用户目录
     */
    public $usersdir = null;
    /**
     * @var string 验证码地址
     */
    public $verifyCodeUrl = null;
    /**
     * @var string 验证码地址（拼写错误）
     *
     * @deprecated
     */
    public $validcodeurl = null;
    /**
     * @var string
     */
    public $feedurl = null;
    /**
     * @var string
     */
    public $searchurl = null;
    /**
     * @var string
     */
    public $ajaxurl = null;
    /**
     * @var string
     */
    public $xmlrpcurl = null;
    /**
     * @var Member[] 用户数组
     */
    public $members = array();
    /**
     * @var Member[] 用户数组（以用户名为键）
     */
    public $membersbyname = array();
    /**
     * @var Category[] 分类数组
     */
    public $categorys = array();
    public $categories = null;
    /**
     * @var Category[] 分类数组（已排序）
     */
    public $categorysbyorder = array();
    public $categoriesbyorder = null;
    /**
     * @var Module[] 模块数组
     */
    public $modules = array();
    /**
     * @var Module[] 模块数组（以文件名为键）
     */
    public $modulesbyfilename = array();
    /**
     * @var Config[] 配置选项
     */
    public $configs = array();
    /**
     * @var Tag[] 标签数组
     */
    public $tags = array();
    /**
     * @var Tag[] 标签数组（以标签名为键）
     */
    public $tagsbyname = array();
    /**
     * @var Comment[] 评论数组
     */
    public $comments = array();
    /**
     * @var Post[] 文章列表数组
     */
    public $posts = array();

    /**
     * @var string 当前页面标题
     */
    public $title = null;
    /**
     * @var string 网站名
     */
    public $name = null;
    /**
     * @var string 网站子标题
     */
    public $subname = null;
    /**
     * @var App 当前主题
     */
    public $theme = null;
    /**
     * @var array() 当前主题版本信息
     */
    public $themeinfo = array();
    /**
     * @var string 当前主题风格
     */
    public $style = null;

    /**
     * @var Member 当前用户
     */
    public $user = null;
    /**
     * @var Config 缓存
     */
    public $cache = null;

    /**
     * @var array|null 数据表
     */
    public $table = null;
    /**
     * @var array|null 数据表信息
     */
    public $datainfo = null;
    /**
     * @var array|null 类型序列
     */
    public $posttype = null;
    /**
     * @var array|null 操作列表
     */
    public $actions = null;
    /**
     * @var mixed|null|string 当前操作
     */
    public $action = null;

    private $isinitialized = false; //是否初始化成功
    private $isconnected = false; //是否连接成功
    private $isload = false; //是否载入
    private $issession = false; //是否使用session
    public $ismanage = false; //是否加载管理模式
    private $isGzip = false; //是否开启gzip
    public $isHttps = false; //是否HTTPS

    /**
     * @var Template 当前模板
     */
    public $template = null;
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
     * @var array 激活的插件列表
     */
    public $activedapps = array();
    public $activeapps;

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
     * @var int 当前实例下CSRF Token过期时间（小时）
     */
    public $csrfExpiration = 1;

    /**
     * 获取唯一实例.
     *
     * @return null|ZBlogPHP
     */
    public static function GetInstance()
    {
        if (!isset(self::$_zbp)) {
            if (isset($GLOBALS['option']['ZC_GODZBP_FILE']) && isset($GLOBALS['option']['ZC_GODZBP_NAME']) && is_readable(ZBP_PATH . $GLOBALS['option']['ZC_GODZBP_FILE'])) {
                require ZBP_PATH . $GLOBALS['option']['ZC_GODZBP_FILE'];
                self::$_zbp = new $GLOBALS['option']['ZC_GODZBP_NAME']();
            } else {
                self::$_zbp = new self();
            }
        }

        return self::$_zbp;
    }

    /**
     * 初始化数据库连接.
     *
     * @param string $type 数据连接类型
     *
     * @return Database__Interface
     */
    public static function InitializeDB($type)
    {
        if (!trim($type)) {
            return;
        }

        $newtype = 'Database__' . trim($type);

        return new $newtype();
    }

    /**
     * 构造函数，加载基本配置到$zbp.
     */
    public function __construct()
    {
        global $option, $lang, $blogpath, $bloghost, $cookiespath, $usersdir, $table,
        $datainfo, $actions, $action, $blogversion, $blogtitle, $blogname, $blogsubname,
        $blogtheme, $blogstyle, $currenturl, $activedapps, $posttype;

        if (ZBP_HOOKERROR) {
            ZBlogException::SetErrorHook();
        }

        //基本配置加载到$zbp内
        $this->version = &$blogversion;
        $this->option = &$option;
        $this->lang = &$lang;
        $this->path = &$blogpath;
        $this->host = &$bloghost; //此值在后边初始化时可能会变化!
        $this->cookiespath = &$cookiespath;
        $this->usersdir = &$usersdir;

        $this->table = &$table;
        $this->datainfo = &$datainfo;
        $this->actions = &$actions;
        $this->posttype = &$posttype;
        $this->currenturl = &$currenturl;

        $this->action = &$action;
        $this->activedapps = &$activedapps;
        $this->activeapps = &$this->activedapps;

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

        $this->user = new stdClass();
        foreach ($this->datainfo['Member'] as $key => $value) {
            $this->user->$key = $value[3];
        }
        $this->user->Metas = new Config();
    }

    /**
     *析构函数，释放资源.
     */
    public function __destruct()
    {
        $this->Terminate();
    }

    /**
     * @api Filter_Plugin_Zbp_Call
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Call'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($method, $args);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        trigger_error($this->lang['error'][81], E_USER_WARNING);
    }

    /**
     * 设置参数值
     *
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Set'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($name, $value);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        trigger_error($this->lang['error'][81], E_USER_WARNING);
    }

    /**
     * 获取参数值
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Get'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($name);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        trigger_error($this->lang['error'][81], E_USER_WARNING);
    }

    /**
     * 初始化$zbp.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function Initialize()
    {
        $oldZone = $this->option['ZC_TIME_ZONE_NAME'];
        date_default_timezone_set($oldZone);

        $oldLang = $this->option['ZC_BLOG_LANGUAGEPACK'];
        $this->LoadLanguage('system', '');

        if ($this->option['ZC_CLOSE_WHOLE_SITE'] == true) {
            Http503();
            $this->ShowError(82, __FILE__, __LINE__);

            return false;
        }

        if (!$this->OpenConnect()) {
            return false;
        }

        $this->ConvertTableAndDatainfo();

        $this->LoadConfigsOnlySystem(true);
        $this->LoadOption();

        $this->RegPostType(0, 'article', $this->option['ZC_ARTICLE_REGEX'], $this->option['ZC_POST_DEFAULT_TEMPLATE'], 0, 0);
        $this->RegPostType(1, 'page', $this->option['ZC_PAGE_REGEX'], $this->option['ZC_POST_DEFAULT_TEMPLATE'], null, null);

        if ($this->option['ZC_BLOG_LANGUAGEPACK'] === 'SimpChinese') {
            $this->option['ZC_BLOG_LANGUAGEPACK'] = 'zh-cn';
        }

        if ($this->option['ZC_BLOG_LANGUAGEPACK'] === 'TradChinese') {
            $this->option['ZC_BLOG_LANGUAGEPACK'] = 'zh-tw';
        }

        if ($oldLang != $this->option['ZC_BLOG_LANGUAGEPACK']) {
            $this->LoadLanguage('system', '');
        }

        if (isset($this->option['ZC_DEBUG_MODE_WARNING'])) {
            ZBlogException::$iswarning = (bool) $this->option['ZC_DEBUG_MODE_WARNING'];
        }
        if (isset($this->option['ZC_DEBUG_MODE_STRICT'])) {
            ZBlogException::$isstrict = (bool) $this->option['ZC_DEBUG_MODE_STRICT'];
        }
        if (isset($this->option['ZC_DEBUG_LOG_ERROR'])) {
            ZBlogException::$islogerror = (bool) $this->option['ZC_DEBUG_LOG_ERROR'];
        }

        if ($this->option['ZC_PERMANENT_DOMAIN_ENABLE'] == true) {
            $this->host = $this->option['ZC_BLOG_HOST'];
            $this->cookiespath = strstr(str_replace('://', '', $this->host), '/');
        } else {
            $this->option['ZC_BLOG_HOST'] = $this->host;
        }

        $this->option['ZC_BLOG_PRODUCT'] = 'Z-BlogPHP';
        $this->option['ZC_BLOG_VERSION'] = ZC_BLOG_VERSION;
        $this->option['ZC_NOW_VERSION'] = $this->version;  //ZC_LAST_VERSION
        $this->option['ZC_BLOG_PRODUCT_FULL'] = $this->option['ZC_BLOG_PRODUCT'] . ' ' . ZC_VERSION_DISPLAY;
        $this->option['ZC_BLOG_PRODUCT_FULLHTML'] = '<a href="http://www.zblogcn.com/" title="RainbowSoft Z-BlogPHP" target="_blank">' . $this->option['ZC_BLOG_PRODUCT_FULL'] . '</a>';
        $this->option['ZC_BLOG_PRODUCT_HTML'] = '<a href="http://www.zblogcn.com/" title="RainbowSoft Z-BlogPHP" target="_blank">' . $this->option['ZC_BLOG_PRODUCT'] . '</a>';

        if ($oldZone != $this->option['ZC_TIME_ZONE_NAME']) {
            date_default_timezone_set($this->option['ZC_TIME_ZONE_NAME']);
        }

        /*if(isset($_COOKIE['timezone'])){
            $tz=GetVars('timezone','COOKIE');
            if(is_numeric($tz)){
            $tz=sprintf('%+d',-$tz);
            date_default_timezone_set('Etc/GMT' . $tz);
            $this->timezone=date_default_timezone_get();
            }
        */

        if ($this->option['ZC_VERSION_IN_HEADER'] && !headers_sent()) {
            header('Product:' . $this->option['ZC_BLOG_PRODUCT_FULL']);
        }

        $parsedHost = parse_url($this->host);
        $this->fullcurrenturl = $parsedHost['scheme'] . '://' . $parsedHost['host'] . $this->currenturl;
        if (substr($this->host, 0, 8) == 'https://') {
            $this->isHttps = true;
        }

        $this->verifyCodeUrl = $this->host . 'zb_system/script/c_validcode.php';
        $this->validcodeurl = &$this->verifyCodeUrl;
        $this->feedurl = $this->host . 'feed.php';
        $this->searchurl = $this->host . 'search.php';
        $this->ajaxurl = $this->host . 'zb_system/cmd.php?act=ajax&src=';
        $this->xmlrpcurl = $this->host . 'zb_system/xml-rpc/index.php';

        $this->LoadConfigsOnlySystem(false);

        $this->LoadCache();

        $this->isinitialized = true;

        return true;
    }

    /**
     * 从数据库里读取信息，启动整个ZBP.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function Load()
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Load_Pre'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname();
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        if (!$this->isinitialized) {
            return false;
        }

        if ($this->isload) {
            return false;
        }

        $this->StartGzip();

        if (!headers_sent()) {
            header('Content-type: text/html; charset=utf-8');
        }

        $this->ConvertTableAndDatainfo();

        $this->LoadMembers($this->option['ZC_LOADMEMBERS_LEVEL']);
        $this->LoadCategories();
        //$this->LoadTags();
        $this->LoadModules();

        if (!(get_class($this->user) === 'Member' && $this->user->Level > 0 && !empty($this->user->ID))) {
            $this->Verify();
        }

        $this->RegBuildModule('catalog', 'ModuleBuilder::Catalog');
        $this->RegBuildModule('calendar', 'ModuleBuilder::Calendar');
        $this->RegBuildModule('comments', 'ModuleBuilder::Comments');
        $this->RegBuildModule('previous', 'ModuleBuilder::LatestArticles');
        $this->RegBuildModule('archives', 'ModuleBuilder::Archives');
        $this->RegBuildModule('navbar', 'ModuleBuilder::Navbar');
        $this->RegBuildModule('tags', 'ModuleBuilder::TagList');
        $this->RegBuildModule('statistics', 'ModuleBuilder::Statistics');
        $this->RegBuildModule('authors', 'ModuleBuilder::Authors');

        //创建模板类
        $this->template = $this->PrepareTemplate();

        // 读主题版本信息
        $app = $this->LoadApp('theme', $this->theme);
        if ($app->type !== '') {
            $this->themeinfo = $app->GetInfoArray();
        }

        if ($this->ismanage) {
            $this->LoadManage();
        }

        Add_Filter_Plugin('Filter_Plugin_Login_Header', 'Include_AddonAdminFont');
        Add_Filter_Plugin('Filter_Plugin_Other_Header', 'Include_AddonAdminFont');
        Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'Include_AddonAdminFont');

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Load'] as $fpname => &$fpsignal) {
            $fpname();
        }

        if ($this->option['ZC_DEBUG_MODE']) {
            $this->CheckTemplate(false, true);
        }

        $this->isload = true;

        return true;
    }

    /**
     * 载入管理.
     *
     * @throws Exception
     */
    public function LoadManage()
    {
        if ($this->option['ZC_PERMANENT_DOMAIN_WITH_ADMIN'] == false) {
            $this->host = GetCurrentHost($this->path, $this->cookiespath);
        }

        if (substr($this->host, 0, 8) == 'https://') {
            $this->isHttps = true;
        }

        if ($this->user->Status == ZC_MEMBER_STATUS_AUDITING) {
            $this->ShowError(79, __FILE__, __LINE__);
        }

        if ($this->user->Status == ZC_MEMBER_STATUS_LOCKED) {
            $this->ShowError(80, __FILE__, __LINE__);
        }

        Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu', 'Include_Admin_Addpagesubmenu');
        Add_Filter_Plugin('Filter_Plugin_Admin_TagMng_SubMenu', 'Include_Admin_Addtagsubmenu');
        Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu', 'Include_Admin_Addcatesubmenu');
        Add_Filter_Plugin('Filter_Plugin_Admin_MemberMng_SubMenu', 'Include_Admin_Addmemsubmenu');
        Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu', 'Include_Admin_Addmodsubmenu');
        Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu', 'Include_Admin_Addcmtsubmenu');

        $this->CheckTemplate(true);

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_LoadManage'] as $fpname => &$fpsignal) {
            $fpname();
        }
    }

    /**
     * 终止连接，释放资源.
     */
    public function Terminate()
    {
        if ($this->isinitialized) {
            foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_Terminate'] as $fpname => &$fpsignal) {
                $fpname();
            }

            $this->CloseConnect();
            unset($this->db);
            $this->isinitialized = false;
        }
    }

    /**
     * 连接数据库.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function OpenConnect()
    {
        if ($this->isconnected) {
            return false;
        }

        if (!$this->option['ZC_DATABASE_TYPE']) {
            return false;
        }

        switch ($this->option['ZC_DATABASE_TYPE']) {
            case 'sqlite':
            case 'sqlite3':
            case 'pdo_sqlite':
                $this->db = self::InitializeDB($this->option['ZC_DATABASE_TYPE']);
                if ($this->db->Open(array(
                $this->usersdir . 'data/' . $this->option['ZC_SQLITE_NAME'],
                $this->option['ZC_SQLITE_PRE'],
                )) == false) {
                    $this->ShowError(69, __FILE__, __LINE__);
                }
                break;
            case 'pgsql':
            case 'pdo_pgsql':
                $this->db = self::InitializeDB($this->option['ZC_DATABASE_TYPE']);
                if ($this->db->Open(array(
                $this->option['ZC_PGSQL_SERVER'],
                $this->option['ZC_PGSQL_USERNAME'],
                $this->option['ZC_PGSQL_PASSWORD'],
                $this->option['ZC_PGSQL_NAME'],
                $this->option['ZC_PGSQL_PRE'],
                $this->option['ZC_PGSQL_PORT'],
                $this->option['ZC_PGSQL_PERSISTENT'],
                )) == false) {
                    $this->ShowError(67, __FILE__, __LINE__);
                }
                break;
            case 'mysql':
            case 'mysqli':
            case 'pdo_mysql':
            default:
                $this->db = self::InitializeDB($this->option['ZC_DATABASE_TYPE']);
                if ($this->db->Open(array(
                $this->option['ZC_MYSQL_SERVER'],
                $this->option['ZC_MYSQL_USERNAME'],
                $this->option['ZC_MYSQL_PASSWORD'],
                $this->option['ZC_MYSQL_NAME'],
                $this->option['ZC_MYSQL_PRE'],
                $this->option['ZC_MYSQL_PORT'],
                $this->option['ZC_MYSQL_PERSISTENT'],
                $this->option['ZC_MYSQL_ENGINE'],
                )) == false) {
                    $this->ShowError(67, __FILE__, __LINE__);
                }
                break;
        }
        // utf8mb4支持
        if ($this->db->type == 'mysql' && version_compare($this->db->version, '5.5.3') < 0) {
            Add_Filter_Plugin('Filter_Plugin_DbSql_Filter', 'utf84mb_filter');
            Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'utf84mb_fixHtmlSpecialChars');
        }
        $this->isconnected = true;

        return true;
    }

    /**
     * 对表名和数据结构进行预转换.
     */
    public function ConvertTableAndDatainfo()
    {
        if ($this->db->dbpre) {
            $this->table = str_replace('%pre%', $this->db->dbpre, $this->table);
        }
        if ($this->db->type === 'postgresql') {
            foreach ($this->datainfo as $key => &$value) {
                foreach ($value as $k2 => &$v2) {
                    $v2[0] = strtolower($v2[0]);
                }
            }
        }
    }

    /**
     * 关闭数据库连接.
     */
    public function CloseConnect()
    {
        if ($this->isconnected) {
            $this->db->Close();
            $this->isconnected = false;
        }
    }

    /**
     * 启用session.
     *
     * @return bool
     */
    public function StartSession()
    {
        if (session_status() == 1) {
            session_start();
            $this->issession = true;

            return true;
        }

        return false;
    }

    /**
     * 终止session.
     *
     * @return bool
     */
    public function EndSession()
    {
        if (session_status() == 2) {
            session_write_close();
            $this->issession = false;

            return true;
        }

        return false;
    }

    /**
     * 载入插件Configs表.
     */
    public function LoadConfigs()
    {
        $this->configs = array();
        $sql = $this->db->sql->Select($this->table['Config'], array('*'), '', '', '', '');

        /** @var Config[] $array */
        $array = $this->GetListType('Config', $sql);
        foreach ($array as $c) {
            $n = $c->GetItemName();
            $this->configs[$n] = $c;
        }
    }

    /**
     * 载入插件Configs表 Only System Option And Cache.
     */
    public function LoadConfigsOnlySystem($onlysystemoption = true)
    {
        if ($onlysystemoption == true) {
            $this->configs = array();
        }
        if ($onlysystemoption == true) {
            $sql = $this->db->sql->Select($this->table['Config'], array('*'), 'conf_Name = "system"', '', '', '');
        } else {
            $sql = $this->db->sql->Select($this->table['Config'], array('*'), 'conf_Name <> "system"', '', '', '');
        }

        /** @var Config[] $array */
        $array = $this->GetListType('Config', $sql);
        foreach ($array as $c) {
            $n = $c->GetItemName();
            $this->configs[$n] = $c;
        }
    }

    /**
     * 保存Configs表.
     *
     * @param string $name Configs表名
     *
     * @return bool
     */
    public function SaveConfig($name)
    {
        if (!isset($this->configs[$name])) {
            return false;
        }

        $this->configs[$name]->Save();

        return true;
    }

    /**
     * 删除Configs表.
     *
     * @param string $name Configs表名
     *
     * @return bool
     */
    public function DelConfig($name)
    {
        if (!isset($this->configs[$name])) {
            return false;
        }

        $this->configs[$name]->Delete();
        unset($this->configs[$name]);

        return true;
    }

    /**
     * 获取Configs表值
     *
     * @param string $name Configs表名
     *
     * @return mixed
     */
    public function Config($name)
    {
        if (!isset($this->configs[$name])) {
            $name = FilterCorrectName($name);
            if (!$name) {
                return;
            }

            $this->configs[$name] = new Config($name);
        }

        return $this->configs[$name];
    }

    /**
     * 查某Config是否存在.
     *
     * @param string $name Configs表名
     *
     * @return bool
     */
    public function HasConfig($name)
    {
        return isset($this->configs[$name]) && $this->configs[$name]->CountItem() > 0;
    }

    //###############################################################################################################
    //Cache相关
    private $cache_hash = null;

    /**
     * 保存缓存.
     *
     * @return bool
     */
    public function SaveCache()
    {
        //$s=$this->usersdir . 'cache/' . $this->guid . '.cache';
        //$c=serialize($this->cache);
        //@file_put_contents($s, $c);
        //$this->configs['cache']=$this->cache;
        $new_hash = md5($this->Config('cache'));
        if ($this->cache_hash == $new_hash) {
            return true;
        }

        $this->SaveConfig('cache');
        $this->cache_hash = $new_hash;

        return true;
    }

    /**
     * 加载缓存.
     *
     * @return bool
     */
    public function LoadCache()
    {
        $this->cache = $this->Config('cache');
        $this->cache_hash = md5($this->Config('cache'));

        return true;
    }

    /**
     * 保存配置.
     *
     * @return bool
     */
    public function SaveOption()
    {
        $this->option['ZC_BLOG_CLSID'] = $this->guid;

        if (ZC_VERSION_MAJOR === '1' && ZC_VERSION_MINOR === '5') {
            if (is_dir($this->path . 'zb_system/api')) {
                @rrmdir($this->path . 'zb_system/api'); // Fix bug!!!
            }
        }

        if (strpos('|SAE|BAE2|ACE|TXY|', '|' . $this->option['ZC_YUN_SITE'] . '|') === false && file_exists($this->usersdir . 'c_option.php') == false) {
            $s = "<" . "?" . "php\r\n";
            $s .= "return ";
            $option = array();
            foreach ($this->option as $key => $value) {
                if (($key == 'ZC_YUN_SITE') ||
                    ($key == 'ZC_DATABASE_TYPE') ||
                    ($key == 'ZC_SQLITE_NAME') ||
                    ($key == 'ZC_SQLITE_PRE') ||
                    ($key == 'ZC_MYSQL_SERVER') ||
                    ($key == 'ZC_MYSQL_USERNAME') ||
                    ($key == 'ZC_MYSQL_PASSWORD') ||
                    ($key == 'ZC_MYSQL_NAME') ||
                    ($key == 'ZC_MYSQL_CHARSET') ||
                    ($key == 'ZC_MYSQL_PRE') ||
                    ($key == 'ZC_MYSQL_ENGINE') ||
                    ($key == 'ZC_MYSQL_PORT') ||
                    ($key == 'ZC_MYSQL_PERSISTENT') ||
                    ($key == 'ZC_PGSQL_SERVER') ||
                    ($key == 'ZC_PGSQL_USERNAME') ||
                    ($key == 'ZC_PGSQL_PASSWORD') ||
                    ($key == 'ZC_PGSQL_NAME') ||
                    ($key == 'ZC_PGSQL_CHARSET') ||
                    ($key == 'ZC_PGSQL_PRE') ||
                    ($key == 'ZC_PGSQL_PORT') ||
                    ($key == 'ZC_PGSQL_PERSISTENT') ||
                    ($key == 'ZC_CLOSE_WHOLE_SITE')
                ) {
                    $option[$key] = $value;
                }
            }
            $s .= var_export($option, true);
            $s .= ";";
            @file_put_contents($this->usersdir . 'c_option.php', $s);
        }

        foreach ($this->option as $key => $value) {
            $this->Config('system')->$key = $value;
        }

        $this->Config('system')->ZC_BLOG_HOST = chunk_split($this->Config('system')->ZC_BLOG_HOST, 1, "|");

        $this->SaveConfig('system');

        return true;
    }

    /**
     * 载入配置.
     *
     * @return bool
     */
    public function LoadOption()
    {
        $array = $this->Config('system')->GetData();

        if (empty($array)) {
            return false;
        }

        if (!is_array($array)) {
            return false;
        }

        foreach ($array as $key => $value) {
            //if($key=='ZC_PERMANENT_DOMAIN_ENABLE')continue;
            //if($key=='ZC_BLOG_HOST')continue;
            //if($key=='ZC_BLOG_CLSID')continue;
            //if($key=='ZC_BLOG_LANGUAGEPACK')continue;
            if ($key == 'ZC_BLOG_HOST') {
                $value = str_replace('|', '', $value);
            }

            if (($key == 'ZC_YUN_SITE') ||
                ($key == 'ZC_DATABASE_TYPE') ||
                ($key == 'ZC_SQLITE_NAME') ||
                ($key == 'ZC_SQLITE_PRE') ||
                ($key == 'ZC_MYSQL_SERVER') ||
                ($key == 'ZC_MYSQL_USERNAME') ||
                ($key == 'ZC_MYSQL_PASSWORD') ||
                ($key == 'ZC_MYSQL_NAME') ||
                ($key == 'ZC_MYSQL_CHARSET') ||
                ($key == 'ZC_MYSQL_PRE') ||
                ($key == 'ZC_MYSQL_ENGINE') ||
                ($key == 'ZC_MYSQL_PORT') ||
                ($key == 'ZC_MYSQL_PERSISTENT') ||
                ($key == 'ZC_PGSQL_SERVER') ||
                ($key == 'ZC_PGSQL_USERNAME') ||
                ($key == 'ZC_PGSQL_PASSWORD') ||
                ($key == 'ZC_PGSQL_NAME') ||
                ($key == 'ZC_PGSQL_CHARSET') ||
                ($key == 'ZC_PGSQL_PRE') ||
                ($key == 'ZC_PGSQL_PORT') ||
                ($key == 'ZC_PGSQL_PERSISTENT') ||
                ($key == 'ZC_CLOSE_WHOLE_SITE')
            ) {
                continue;
            }

            $this->option[$key] = $value;
        }
        if (!extension_loaded('gd')) {
            $this->option['ZC_COMMENT_VERIFY_ENABLE'] = false;
        }

        return true;
    }

    /**
     * 验证操作权限.
     *
     * @param string     $action 操作
     * @param int|string $level
     *
     * @return bool
     */
    public function CheckRights($action, $level = null)
    {
        if ($level === null) {
            $level = $this->user->Level;
        }

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_CheckRights'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($action, $level);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }
        if (!isset($this->actions[$action])) {
            if (is_numeric($action)) {
                return $level <= $action;
            } else {
                return false;
            }
        }

        return $level <= $this->actions[$action];
    }

    /**
     * 根据用户等级验证操作权限 1.5开始参数换顺序.
     *
     * @param string $action 操作
     * @param int    $level  用户等级
     *
     * @return bool
     */
    public function CheckRightsByLevel($action, $level)
    {
        return $this->CheckRights($action, $level);
    }

    /**
     * 验证用户登录.
     *
     * @return bool
     */
    public function Verify()
    {
        $username = trim(GetVars('username', 'COOKIE'));
        $token = trim(GetVars('token', 'COOKIE'));
        $user = $this->VerifyUserToken($token, $username);
        if (!is_null($user)) {
            $this->user = $user;

            return true;
        }
        $this->user = new Member();
        $this->user->Guid = GetGuid();

        return false;
    }

    /**
     * 返回登录成功后应保存的cookie信息.
     *
     * @param Member $m 已验过成功的member
     *
     * @return string
     */
    public function VerifyResult($m)
    {
        return $this->GenerateUserToken($m);
    }

    /**
     * 生成User Token，用于登录验证
     *
     * @param Member $user
     * @param int    $time
     *
     * @return string
     */
    public function GenerateUserToken($user, $time = 0)
    {
        if ($time === 0) {
            $time = time() + 3600 * 24;
        }

        return CreateWebToken($user->ID, $time, $user->Guid, $user->PassWord_MD5Path);
    }

    /**
     * 验证用户登录Token.
     *
     * @param string $token
     * @param string $username
     *
     * @return Member
     */
    public function VerifyUserToken($token, $username)
    {
        $user = $this->GetMemberByName($username);
        if ($user->ID > 0) {
            if (VerifyWebToken($token, $user->ID, $user->Guid, $user->PassWord_MD5Path)) {
                return $user;
            }
        }
    }

    /**
     * 验证用户登录（一次MD5密码）.
     *
     * @param string $name   用户名
     * @param string $md5pw  md5加密后的密码
     * @param Member $member 返回读取成功的member对象
     *
     * @return bool
     */
    public function Verify_MD5($name, $md5pw, &$member)
    {
        if ($name == '' || $md5pw == '') {
            return false;
        }
        $member = $this->GetMemberByName($name);
        if ($member->ID > 0) {
            return $this->Verify_Final($name, md5($md5pw . $member->Guid), $member);
        }

        return false;
    }

    /**
     * 验证用户登录（原始明文密码）.
     *
     * @param string $name       用户名
     * @param string $originalpw 密码明文
     * @param Member $member     返回读取成功的member对象
     *
     * @return bool
     */
    public function Verify_Original($name, $originalpw, &$member = null)
    {
        if ($name == '' || $originalpw == '') {
            return false;
        }
        $m = $this->GetMemberByName($name);
        if ($m->ID > 0) {
            return $this->Verify_MD5($name, md5($originalpw), $member);
        }

        return false;
    }

    /**
     * 验证用户登录（数据库保存的最终运算后密码）.
     *
     * @param string $name     用户名
     * @param string $password 二次加密后的密码
     * @param object $member   返回读取成功的member对象
     *
     * @return bool
     */
    public function Verify_Final($name, $password, &$member = null)
    {
        if ($name == '' || $password == '') {
            return false;
        }
        $m = $this->GetMemberByName($name);
        if ($m->ID > 0) {
            if (strcasecmp($m->Password, $password) == 0) {
                $member = $m;

                return true;
            }
        }

        return false;
    }

    /**
     * 验证用户登录（使用Token，替代密码保存）.
     *
     * @param string $name   用户名
     * @param string $wt     WebToken
     * @param string $wt_id  WebToken的ID识别符
     * @param object $member 返回读取成功的member对象
     *
     * @return bool
     */
    public function Verify_Token($name, $wt, $wt_id, &$member = null)
    {
        if ($name == '' || $wt == '') {
            return false;
        }
        $m = null;
        $m = $this->GetMemberByName($name);
        if ($m->ID > 0) {
            if (VerifyWebToken($wt, $wt_id, $this->guid, $m->ID, $m->Password) === true) {
                $member = $m;

                return true;
            }
        }

        return false;
    }

    /**
     * 载入用户列表.
     *
     * @param int $level 用户等级
     *
     * @return bool
     */
    public function LoadMembers($level = 0)
    {
        if ($level < 0) {
            return false;
        }

        $where = null;
        if ($level > 0) {
            $where = array(array('<=', 'mem_Level', $level));
        }
        $this->members = array();
        $this->membersbyname = array();
        $array = $this->GetMemberList(null, $where);
        foreach ($array as $m) {
            $this->members[$m->ID] = $m;
            $this->membersbyname[$m->Name] = &$this->members[$m->ID];
        }

        return true;
    }

    /**
     * 载入分类列表.
     *
     * @return bool
     */
    public function LoadCategories()
    {
        $this->categories = array();
        $this->categoriesbyorder = array();
        $lv0 = array();
        $lv1 = array();
        $lv2 = array();
        $lv3 = array();
        $array = $this->GetCategoryList(null, null, array('cate_Order' => 'ASC'), null, null);
        if (count($array) == 0) {
            return false;
        }

        foreach ($array as $c) {
            $this->categories[$c->ID] = $c;
        }

        foreach ($this->categories as $id => $c) {
            $l = 'lv' . $c->Level;
            ${$l}[$c->ParentID][] = $id;
        }

        if (!is_array($lv0[0])) {
            $lv0[0] = array();
        }

        /*
         * 以下垃圾代码，必须重构！
         */
        foreach ($lv0[0] as $id0) {
            $this->categoriesbyorder[$id0] = &$this->categories[$id0];
            if (!isset($lv1[$id0])) {
                continue;
            }
            foreach ($lv1[$id0] as $id1) {
                if ($this->categories[$id1]->ParentID == $id0) {
                    $this->categories[$id1]->RootID = $id0;
                    $this->categories[$id0]->SubCategories[] = $this->categories[$id1];
                    $this->categories[$id0]->ChildrenCategories[] = $this->categories[$id1];
                    $this->categoriesbyorder[$id1] = &$this->categories[$id1];
                    if (!isset($lv2[$id1])) {
                        continue;
                    }
                    foreach ($lv2[$id1] as $id2) {
                        if ($this->categories[$id2]->ParentID == $id1) {
                            $this->categories[$id2]->RootID = $id0;
                            $this->categories[$id0]->ChildrenCategories[] = $this->categories[$id2];
                            $this->categories[$id1]->SubCategories[] = $this->categories[$id2];
                            $this->categories[$id1]->ChildrenCategories[] = $this->categories[$id2];
                            $this->categoriesbyorder[$id2] = &$this->categories[$id2];
                            if (!isset($lv3[$id2])) {
                                continue;
                            }
                            foreach ($lv3[$id2] as $id3) {
                                if ($this->categories[$id3]->ParentID == $id2) {
                                    $this->categories[$id3]->RootID = $id0;
                                    $this->categories[$id0]->ChildrenCategories[] = $this->categories[$id3];
                                    $this->categories[$id1]->ChildrenCategories[] = $this->categories[$id3];
                                    $this->categories[$id2]->SubCategories[] = $this->categories[$id3];
                                    $this->categories[$id2]->ChildrenCategories[] = $this->categories[$id3];
                                    $this->categoriesbyorder[$id3] = &$this->categories[$id3];
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * 载入标签列表.
     *
     * @return bool
     */
    public function LoadTags()
    {
        $this->tags = array();
        $this->tagsbyname = array();
        $array = $this->GetTagList();
        foreach ($array as $t) {
            $this->tags[$t->ID] = $t;
            $this->tagsbyname[$t->Name] = &$this->tags[$t->ID];
        }

        return true;
    }

    /**
     * 载入模块列表.
     *
     * @return bool
     */
    public function LoadModules()
    {
        $this->modules = array();
        $this->modulesbyfilename = array();
        $array = $this->GetModuleList();
        foreach ($array as $m) {
            $this->modules[] = $m;
            $this->modulesbyfilename[$m->FileName] = $m;
        }

        $dir = $this->usersdir . 'theme/' . $this->theme . '/include/';
        if (file_exists($dir)) {
            $files = GetFilesInDir($dir, 'php');
            foreach ($files as $sortname => $fullname) {
                $m = new Module();
                $m->FileName = $sortname;
                $m->Content = file_get_contents($fullname);
                $m->Type = 'div';
                $m->Source = 'theme';
                $this->modules[] = $m;
                $this->modulesbyfilename[$m->FileName] = $m;
            }
        }

        return true;
    }

    /**
     * 载入主题列表.
     *
     * @return App[]
     */
    public function LoadThemes()
    {
        $allThemes = array();
        $dirs = GetDirsInDir($this->usersdir . 'theme/');
        natcasesort($dirs);
        array_unshift($dirs, $this->theme);
        $dirs = array_unique($dirs);
        foreach ($dirs as $id) {
            $app = new App();
            if ($app->LoadInfoByXml('theme', $id) == true) {
                $allThemes[] = $app;
            }
        }

        return $allThemes;
    }

    /**
     * 载入插件列表.
     *
     * @return App[]
     */
    public function LoadPlugins()
    {
        $allPlugins = array();
        $dirs = GetDirsInDir($this->usersdir . 'plugin/');
        natcasesort($dirs);

        foreach ($dirs as $id) {
            $app = new App();
            if ($app->LoadInfoByXml('plugin', $id) == true) {
                $allPlugins[] = $app;
            }
        }

        return $allPlugins;
    }

    /**
     * 载入指定应用.
     *
     * @param string $type 应用类型(theme|plugin)
     * @param string $id   应用ID
     *
     * @return App
     */
    public function LoadApp($type, $id)
    {
        $app = new App();
        $app->LoadInfoByXml($type, $id);

        return $app;
    }

    /**
     * 检查应用是否安装并启用.
     *
     * @param string $name 应用（插件或主题）的ID
     *
     * @return bool
     */
    public function CheckPlugin($name)
    {
        return in_array($name, $this->activedapps);
    }

    /**
     * 检查应用是否安装并启用.
     *
     * @param string $name 应用ID（插件或主题）
     *
     * @return bool
     */
    public function CheckApp($name)
    {
        return $this->CheckPlugin($name);
    }

    /**
     * 获取预激活插件名数组.
     *
     * @return string[]
     */
    public function GetPreActivePlugin()
    {
        $ap = explode("|", $this->option['ZC_USING_PLUGIN_LIST']);
        $ap = array_unique($ap);

        return $ap;
    }

    /**
     * 载入指定应用语言包.
     *
     * @param string $type    应用类型(system|theme|plugin)
     * @param string $id      应用ID
     * @param string $default 默认语言
     *
     * @throws Exception
     *
     * @return null
     */
    public function LoadLanguage($type, $id, $default = '')
    {
        $languagePath = $this->path;
        $languageRegEx = '/^([0-9A-Z\-_]*)\.php$/ui';
        $languageList = array();
        $language = '';
        $default = str_replace(array('/', '\\'), '', $default);
        $languagePtr = &$this->lang;

        if ($default === '') {
            $default = $this->option['ZC_BLOG_LANGUAGEPACK'];
        }

        $defaultLanguageList = array($default, 'zh-cn', 'zh-tw', 'en');

        switch ($type) {
            case 'system':
                $languagePath .= 'zb_users/language/';
                break;
            case 'plugin':
            case 'theme':
                $languagePath .= 'zb_users/' . $type . '/' . $id . '/language/';
                $languagePtr = &$this->lang[$id];
                break;
            default:
                $languagePath .= $type . '/language/';
                $languagePtr = &$this->lang[$id];
                break;
        }

        $handle = @opendir($languagePath);
        $match = null;
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match($languageRegEx, $file, $match)) {
                    $languageList[] = $match[1];
                }
            }
            closedir($handle);
        } else {
            // 这里不会执行到，在opendir时就已经抛出E_WARNING
            throw new Exception('Cannot opendir(' . $languagePath . ')');
        }

        if (count($languageList) === 0) {
            throw new Exception('No language in ' . $languagePath);
        }

        for ($i = 0; $i < count($defaultLanguageList); $i++) {
            // 在效率上，array_search和命名数组没有本质区别，至少在这里如此。
            if (false !== array_search($defaultLanguageList[$i], $languageList)) {
                $language = $defaultLanguageList[$i];
                break;
            }
        }
        if ($language === '') {
            throw new Exception('Language ' . $default . ' is not found in ' . $languagePath);
        }

        $languagePath .= $language . '.php';
        $languagePtr = require $languagePath;
        $this->langpacklist[] = array($type, $id, $language);

        return true;
    }

    /**
     * 重新读取语言包.
     *
     * @throws Exception
     */
    public function ReloadLanguages()
    {
        $array = $this->langpacklist;
        $this->lang = $this->langpacklist = array();
        foreach ($array as $v) {
            $this->LoadLanguage($v[0], $v[1], $v[2]);
        }
    }

    /**
     * 创建模板对象，预加载已编译模板
     *
     * @param string $theme 指定主题名
     *
     * @return Template
     */
    public function PrepareTemplate($theme = null)
    {
        if (is_null($theme)) {
            $theme = &$this->theme;
        }

        $template = new Template();
        $template->MakeTemplateTags();

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_MakeTemplatetags'] as $fpname => &$fpsignal) {
            $fpname($template->templateTags);
        }

        $template->SetPath($this->usersdir . 'cache/compiled/' . $theme . '/');
        $template->theme = $theme;

        return $template;
    }

    /**
     * 模板解析.
     *
     * @return bool
     */
    public function BuildTemplate()
    {
        $this->template->LoadTemplates();

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_BuildTemplate'] as $fpname => &$fpsignal) {
            $fpname($this->template->templates);
        }

        return $this->template->BuildTemplate();
    }

    /**
     * 更新模板缓存.
     *
     * @param bool $onlycheck  为真的话，只判断是否需要而不Build
     * @param bool $forcebuild
     *
     * @return true or false
     */
    public function CheckTemplate($onlycheck = false, $forcebuild = false)
    {
        $this->template->LoadTemplates();
        $s = implode($this->template->templates);
        $md5 = md5($s);

        if ($md5 != $this->cache->templates_md5) {
            if ($onlycheck == true && $forcebuild == false) {
                return false;
            }
            $this->BuildTemplate();
            $this->cache->templates_md5 = $md5;
            $this->SaveCache();
        } else {
            if ($forcebuild == true) {
                $this->BuildTemplate();
                $this->cache->templates_md5 = $md5;
                $this->SaveCache();
            }
        }

        return true;
    }

    /**
     * 生成模块.
     */
    public function BuildModule()
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_BuildModule'] as $fpname => &$fpsignal) {
            $fpname();
        }
        ModuleBuilder::Build();
    }

    /**
     * 重建模块.
     *
     * @param string $moduleFileName 模块名
     * @param string $moduleFunction 用户函数
     */
    public function RegBuildModule($moduleFileName, $moduleFunction)
    {
        ModuleBuilder::Reg($moduleFileName, $moduleFunction);
    }

    /**
     * 添加模块.
     *
     * @param string $moduleFileName 模块名
     * @param null   $parameters     模块参数
     */
    public function AddBuildModule($moduleFileName, $parameters = null)
    {
        ModuleBuilder::Add($moduleFileName, $parameters);
    }

    /**
     * 删除模块.
     *
     * @param string $moduleFileName 模块名
     */
    public function DelBuildModule($moduleFileName)
    {
        ModuleBuilder::Del($moduleFileName);
    }

    /**
     * 查询指定数据结构的sql并返回Base对象列表.
     *
     * @param string|array $table    数据表
     * @param array        $datainfo 数据字段
     * @param string       $sql      SQL操作语句
     *
     * @return array
     */
    public function GetListCustom($table, $datainfo, $sql)
    {
        $array = null;
        $list = array();
        $array = $this->db->Query($sql);
        if (!isset($array)) {
            return array();
        }
        foreach ($array as $a) {
            $l = new Base($table, $datainfo);
            $l->LoadInfoByAssoc($a);
            $list[] = $l;
        }

        return $list;
    }

    /**
     * 查询ID数据的指定数据结构的sql并返回Base对象列表.
     *
     * @param string|array $table    数据表
     * @param array        $datainfo 数据字段
     * @param array        $array    ID数组
     *
     * @return Base[]
     */
    public function GetListCustomByArray($table, $datainfo, $array)
    {
        if (!is_array($array)) {
            return array();
        }

        if (count($array) == 0) {
            return array();
        }

        $where = array();
        $where[] = array('IN', $datainfo['ID'][0], implode(',', $array));
        $sql = $this->db->sql->Select($table, '*', $where);
        $array = null;
        $list = array();
        $array = $this->db->Query($sql);
        if (!isset($array)) {
            return array();
        }
        foreach ($array as $a) {
            $l = new Base($table, $datainfo);
            $l->LoadInfoByAssoc($a);
            $list[] = $l;
        }

        return $list;
    }

    /**
     * 已改名GetListType,1.5版中扔掉有歧义的GetList.
     *
     * @param $type
     * @param $sql
     *
     * @return Base[]
     */
    public function GetListType($type, $sql)
    {
        $array = null;
        $list = array();
        $array = $this->db->Query($sql);
        if (!isset($array)) {
            return array();
        }
        foreach ($array as $a) {
            /** @var Base $l */
            $l = new $type();
            $l->LoadInfoByAssoc($a);
            $list[] = $l;
        }

        return $list;
    }

    /**
     * 查询ID数据的指定类型的sql并返回指定类型对象列表.
     *
     * @param string $type  类型
     * @param mixed  $array ID数组
     *
     * @return Base[]
     */
    public function GetListTypeByArray($type, $array)
    {
        if (!is_array($array)) {
            return array();
        }

        if (count($array) == 0) {
            return array();
        }

        $where = array();
        $where[] = array('IN', $this->datainfo[$type]['ID'][0], implode(',', $array));
        $sql = $this->db->sql->Select($this->table[$type], '*', $where);
        $array = null;
        $list = array();
        $array = $this->db->Query($sql);
        if (!isset($array)) {
            return array();
        }
        foreach ($array as $a) {
            /** @var Base $l */
            $l = new $type();
            $l->LoadInfoByAssoc($a);
            $list[] = $l;
        }

        return $list;
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Post[]
     */
    public function GetPostList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        if (empty($where)) {
            $where = array();
        }
        $sql = $this->db->sql->Select($this->table['Post'], $select, $where, $order, $limit, $option);

        /** @var Post[] $array */
        $array = $this->GetListType('Post', $sql);
        foreach ($array as $a) {
            $this->posts[$a->ID] = $a;
        }

        return $array;
    }

    /**
     * 通过ID数组获取文章实例.
     *
     * @param mixed[] $array
     *
     * @return Post[]|Base[] Posts
     */
    public function GetPostByArray($array)
    {
        return $this->GetListTypeByArray('Post', $array);
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     * @param mixed $readtags
     *
     * @return Post[]
     */
    public function GetArticleList($select = null, $where = null, $order = null, $limit = null, $option = null, $readtags = true)
    {
        if (empty($select)) {
            $select = array('*');
        }
        if (empty($where)) {
            $where = array();
        }

        if (is_array($where)) {
            $hasType = false;
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if ($key2 == 1 && $value2 == 'log_Type') {
                            $hasType = true;
                        }
                    }
                }
            }
            if (!$hasType) {
                array_unshift($where, array('=', 'log_Type', '0'));
            }
        }

        $sql = $this->db->sql->Select($this->table['Post'], $select, $where, $order, $limit, $option);

        /** @var Post[] $array */
        $array = $this->GetListType('Post', $sql);

        foreach ($array as $a) {
            $this->posts[$a->ID] = $a;
        }

        if ($readtags) {
            $tagstring = '';
            foreach ($array as $a) {
                $tagstring .= $a->Tag;
            }
            $this->LoadTagsByIDString($tagstring);
        }

        return $array;
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Post[]
     */
    public function GetPageList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        if (empty($where)) {
            $where = array();
        }
        if (is_array($where)) {
            array_unshift($where, array('=', 'log_Type', '1'));
        }

        $sql = $this->db->sql->Select($this->table['Post'], $select, $where, $order, $limit, $option);
        /** @var Post[] $array */
        $array = $this->GetListType('Post', $sql);
        foreach ($array as $a) {
            $this->posts[$a->ID] = $a;
        }

        return $array;
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Comment[]
     */
    public function GetCommentList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Comment'], $select, $where, $order, $limit, $option);
        /** @var Comment[] $array */
        $array = $this->GetListType('Comment', $sql);
        foreach ($array as $comment) {
            $this->comments[$comment->ID] = $comment;
        }

        return $array;
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Member[]|Base[]
     */
    public function GetMemberList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Member'], $select, $where, $order, $limit, $option);

        return $this->GetListType('Member', $sql);
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Tag[]|Base[]
     */
    public function GetTagList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Tag'], $select, $where, $order, $limit, $option);

        return $this->GetListType('Tag', $sql);
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Category[]|Base[]
     */
    public function GetCategoryList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Category'], $select, $where, $order, $limit, $option);

        return $this->GetListType('Category', $sql);
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Module[]|Base[]
     */
    public function GetModuleList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Module'], $select, $where, $order, $limit, $option);

        return $this->GetListType('Module', $sql);
    }

    /**
     * @param mixed $select
     * @param mixed $where
     * @param mixed $order
     * @param mixed $limit
     * @param mixed $option
     *
     * @return Upload[]|Base[]
     */
    public function GetUploadList($select = null, $where = null, $order = null, $limit = null, $option = null)
    {
        if (empty($select)) {
            $select = array('*');
        }
        $sql = $this->db->sql->Select($this->table['Upload'], $select, $where, $order, $limit, $option);

        return $this->GetListType('Upload', $sql);
    }

    /**
     * @param $sql
     *
     * @return mixed
     */
    public function get_results($sql)
    {
        return $this->db->Query($sql);
    }

    /**
     * 根据别名得到相应数据.
     *
     * @param Base[]|string &$object   缓存对象
     * @param string        $val
     * @param string        $backAttr
     * @param string        $className
     *
     * @return Base|null
     */
    private function GetSomeThingByAlias($object, $val, $backAttr = null, $className = null)
    {
        $ret = $this->GetSomeThing($object, 'Alias', $val);

        if (!is_null($ret)) {
            return $ret;
        } else {
            if (is_null($backAttr)) {
                $backAttr = $this->option['ZC_ALIAS_BACK_ATTR'];
            }

            return $this->GetSomeThing($object, $backAttr, $val, $className);
        }
    }

    /**
     * 根据ID得到相应数据.
     *
     * @param Base[]     &$object   缓存对象
     * @param string     $className 找不到ID时初始化对象的类名
     * @param int|string $id        与此类相关的ID
     *
     * @return Base|null
     */
    private function GetSomeThingById(&$object, $className, $id)
    {
        if ($id == 0) {
            return;
        }
        if ($object != null) {
            //$modules非ID为key
            if ($className == "Module") {
                if ($id > 0) {
                    foreach ($object as $key => $value) {
                        if ($value->ID == $id) {
                            return $value;
                        }
                    }
                }
                $m = new Module();

                return $m;
            }

            if (isset($object[$id])) {
                return $object[$id];
            } elseif ($className == "Post" || $className == "Comment" || $className == "Tag") {
                // 文章需要读取，其他的直接返回空对象即可
                /** @var Base $p */
                $p = new $className();
                $p->LoadInfoByID($id);
                $object[$id] = $p;

                return $p;
            } else {
                return $this->GetSomeThingByAttr($object, 'ID', $id);
            }
        } else {
            /** @var Base $p */
            $p = new $className();
            $p->LoadInfoByID($id);

            return $p;
        }
    }

    /**
     * 根据属性值得到相应数据.
     *
     * @param Base[] &$object 缓存对象
     * @param string $attr    属性名
     * @param mixed  $val     要查找的值
     *
     * @return null
     */
    private function GetSomeThingByAttr(&$object, $attr, $val)
    {
        $val = trim($val);
        foreach ($object as $key => &$value) {
            if (is_null($value)) {
                continue;
            }
            if ($value->$attr == $val) {
                return $value;
            }
        }
    }

    /**
     * 获取数据通用函数.
     *
     * @param Base[]|string $object    缓存对象（string / object）
     * @param string        $attr      欲查找的属性
     * @param mixed         $val       要查找内容
     * @param string        $className 对象未找到时，初始化类名
     *
     * @return Base|null
     */
    public function GetSomeThing($object, $attr, $val, $className = null)
    {
        $cacheObject = null;
        if (is_object($object)) {
            $cacheObject = $object;
        } elseif ($object != "") {
            $cacheObject = &$this->$object;
        }
        if ($attr == "ID") {
            $ret = $this->GetSomeThingById($cacheObject, $className, $val);
        } else {
            $ret = $this->GetSomeThingByAttr($cacheObject, $attr, $val);
        }
        if ($ret === null && !is_null($className)) {
            /** @var Base $ret */
            $ret = new $className();
        }

        return $ret;
    }

    /**
     * 通过ID获取文章实例.
     *
     * @param int $id
     *
     * @return Post|Base
     */
    public function GetPostByID($id)
    {
        return $this->GetSomeThing('posts', 'ID', $id, 'Post');
    }

    /**
     * 通过ID获取分类实例.
     *
     * @param int $id
     *
     * @return Category|Base
     */
    public function GetCategoryByID($id)
    {
        return $this->GetSomeThing('categories', 'ID', $id, 'Category');
    }

    /**
     * 通过分类名获取分类实例.
     *
     * @param string $name
     *
     * @return Category|Base
     */
    public function GetCategoryByName($name)
    {
        return $this->GetSomeThing('categories', 'Name', $name, 'Category');
    }

    /**
     * 通过分类别名获取分类实例.
     *
     * @param string $name
     * @param null   $backKey
     *
     * @return Category|Base
     */
    public function GetCategoryByAlias($name, $backKey = null)
    {
        return $this->GetSomeThingByAlias('categories', $name, $backKey, 'Category');
    }

    /**
     * 与老版本保持兼容函数.
     *
     * @param string $name
     *
     * @return Category
     */
    public function GetCategoryByAliasOrName($name)
    {
        return $this->GetCategoryByAlias($name, 'Name');
    }

    /**
     * 通过ID获取模块实例.
     *
     * @param int $id
     *
     * @return Module|Base
     */
    public function GetModuleByID($id)
    {
        return $this->GetSomeThing('modules', 'ID', $id, 'Module'); // What the fuck?
    }

    /**
     * 通过FileName获取模块实例.
     *
     * @param string $fn
     *
     * @return Module|Base
     */
    public function GetModuleByFileName($fn)
    {
        return $this->GetSomeThing('modulesbyfilename', 'FileName', $fn, 'Module');
    }

    /**
     * 通过ID获取用户实例.
     *
     * @param int $id
     *
     * @return Member|Base
     */
    public function GetMemberByID($id)
    {
        /** @var Member $ret */
        $ret = $this->GetSomeThing('members', 'ID', $id, 'Member');
        if ($ret->ID == 0) {
            $ret->Guid = GetGuid();
            //如果是部份加载用户
            if ($this->option['ZC_LOADMEMBERS_LEVEL'] != 0) {
                if ($ret->LoadInfoByID($id) == true) {
                    $this->members[$ret->ID] = $ret;
                    $this->membersbyname[$ret->Name] = &$this->members[$ret->ID];
                }
            }
        }

        return $ret;
    }

    /**
     * 通过用户名获取用户实例(不区分大小写).
     *
     * @param string $name
     *
     * @return Member|Base
     */
    public function GetMemberByName($name)
    {
        $name = trim($name);
        if (!$name || !CheckRegExp($name, '[username]')) {
            return new Member();
        }

        if (isset($this->membersbyname[$name])) {
            return $this->membersbyname[$name];
        } else {
            $array = array_keys($this->membersbyname);
            foreach ($array as $k => $v) {
                if (strcasecmp($name, $v) == 0) {
                    return $this->membersbyname[$v];
                }
            }
        }

        $like = ($this->db->type == 'pgsql') ? 'ILIKE' : 'LIKE';
        $sql = $this->db->sql->Select($this->table['Member'], '*', array(array($like, 'mem_Name', $name)), null, 1, null);

        /** @var Member[] $am */
        $am = $this->GetListType('Member', $sql);
        if (count($am) > 0) {
            $m = $am[0];
            if (!isset($this->members[$m->ID])) {
                $this->members[$m->ID] = $m;
            }
            if (!isset($this->membersbyname[$m->Name])) {
                $this->membersbyname[$m->Name] = &$this->members[$m->ID];
            }

            return $m;
        }

        return new Member();
    }

    /**
     * 通过获取用户名或别名实例(不区分大小写).
     *
     * @param string $name
     *
     * @return Member|Base
     */
    public function GetMemberByNameOrAlias($name)
    {
        $name = trim($name);
        if (!$name || !(CheckRegExp($name, '[username]') || CheckRegExp($name, '[nickname]'))) {
            return new Member();
        }

        foreach ($this->members as $key => &$value) {
            if (strcasecmp($value->Name, $name) == 0 || strcasecmp($value->Alias, $name) == 0) {
                return $value;
            }
        }

        $like = ($this->db->type == 'pgsql') ? 'ILIKE' : 'LIKE';

        $sql = $this->db->sql->get()->select($this->table['Member'])->where(array("$like array", array(
                array('mem_Name', $name),
                array('mem_Alias', $name),
            )))->limit(1)->sql;

        /** @var Member[] $am */
        $am = $this->GetListType('Member', $sql);
        if (count($am) > 0) {
            $m = $am[0];
            if (!isset($this->members[$m->ID])) {
                $this->members[$m->ID] = $m;
            }
            if (!isset($this->membersbyname[$m->Name])) {
                $this->membersbyname[$m->Name] = &$this->members[$m->ID];
            }

            return $m;
        }

        return new Member();
    }

    /**
     * 通过邮箱名获取用户实例(不区分大小写).
     *
     * @param string $email
     *
     * @return Member
     */
    public function GetMemberByEmail($email)
    {
        $email = strtolower(trim($email));
        if (!$email || !CheckRegExp($email, '[email]')) {
            return new Member();
        }

        $sql = $this->db->sql->Select($this->table['Member'], '*', array(array('LIKE', 'mem_Email', $email)), null, 1, null);
        /** @var Member[] $am */
        $am = $this->GetListType('Member', $sql);
        if (count($am) > 0) {
            $m = $am[0];
            if (!isset($this->members[$m->ID])) {
                $this->members[$m->ID] = $m;
            }
            if (!isset($this->membersbyname[$m->Name])) {
                $this->membersbyname[$m->Name] = &$this->members[$m->ID];
            }

            return $m;
        }

        return new Member();
    }

    /**
     * 检查指定名称的用户是否存在(不区分大小写).
     *
     * @param $name
     *
     * @return bool
     */
    public function CheckMemberNameExist($name)
    {
        $m = $this->GetMemberByName($name);

        return $m->ID > 0;
    }

    /**
     * 检查指定名称或别名的用户是否存在(不区分大小写).
     *
     * @param $name
     *
     * @return bool
     */
    public function CheckMemberByNameOrAliasExist($name)
    {
        $m = $this->GetMemberByNameOrAlias($name);

        return $m->ID > 0;
    }

    /**
     * 检查指定邮箱的用户是否存在(不区分大小写).
     *
     * @param $email
     *
     * @return bool
     */
    public function CheckMemberByEmailExist($email)
    {
        $m = $this->GetMemberByEmail($email);

        return $m->ID > 0;
    }

    /**
     * 通过ID获取评论实例.
     *
     * @param int $id
     *
     * @return Comment|Base
     */
    public function GetCommentByID($id)
    {
        return $this->GetSomeThing('comments', 'ID', $id, 'Comment');
    }

    /**
     * 通过ID获取附件实例.
     *
     * @param int $id
     *
     * @return Upload|Base
     */
    public function GetUploadByID($id)
    {
        return $this->GetSomeThing('', 'ID', $id, 'Upload');
    }

    /**
     * 通过tag名获取tag实例.
     *
     * @param string $name
     * @param null   $backKey
     *
     * @return Tag|Base
     */
    public function GetTagByAlias($name, $backKey = null)
    {
        $ret = $this->GetSomeThingByAlias('tags', $name, $backKey, 'Tag');
        if ($ret->ID >= 0) {
            $this->tagsbyname[$ret->ID] = &$this->tags[$ret->ID];
        }

        return $ret;
    }

    /**
     * 通过tag名获取tag实例.
     *
     * @param string $name
     *
     * @return Tag|Base
     */
    public function GetTagByAliasOrName($name)
    {
        //return $this->GetTagByAlias($name, 'Name');
        $a = array();
        $a[] = array('tag_Alias', $name);
        $a[] = array('tag_Name', $name);
        $array = $this->GetTagList('*', array(array('array', $a)), '', 1, '');
        if (count($array) == 0) {
            return new Tag();
        } else {
            $this->tags[$array[0]->ID] = $array[0];
            $this->tagsbyname[$array[0]->ID] = &$this->tags[$array[0]->ID];

            return $this->tags[$array[0]->ID];
        }
    }

    /**
     * 通过ID获取tag实例.
     *
     * @param int $id
     *
     * @return Tag|Base
     */
    public function GetTagByID($id)
    {
        $ret = $this->GetSomeThing('tags', 'ID', $id, 'Tag');
        if ($ret->ID > 0) {
            $this->tagsbyname[$ret->ID] = &$this->tags[$ret->ID];
        }

        return $ret;
    }

    /**
     * 通过类似'{1}{2}{3}{4}'载入tags.
     *
     * @param $s
     *
     * @return array
     */
    public function LoadTagsByIDString($s)
    {
        $s = trim($s);
        if ($s == '') {
            return array();
        }

        $s = str_replace('}{', '|', $s);
        $s = str_replace('{', '', $s);
        $s = str_replace('}', '', $s);
        $a = explode('|', $s);
        $b = array();
        foreach ($a as &$value) {
            $value = trim($value);
            if ($value) {
                $b[] = $value;
            }
        }
        $t = array_unique($b);

        if (count($t) == 0) {
            return array();
        }

        $a = array();
        $b = array();
        $c = array();
        foreach ($t as $v) {
            if (isset($this->tags[$v]) == false) {
                $a[] = array('tag_ID', $v);
                $c[] = $v;
            } else {
                $b[$v] = &$this->tags[$v];
            }
        }

        if (count($a) == 0) {
            return $b;
        } else {
            $t = array();
            //$array=$this->GetTagList('',array(array('array',$a)),'','','');
            $array = $this->GetTagList('', array(array('IN', 'tag_ID', $c)), '', '', '');
            foreach ($array as $v) {
                $this->tags[$v->ID] = $v;
                $this->tagsbyname[$v->Name] = &$this->tags[$v->ID];
                $t[$v->ID] = &$this->tags[$v->ID];
            }

            return $b + $t;
        }
    }

    /**
     * 通过类似'aaa,bbb,ccc,ddd'载入tags.
     *
     * @param string $s 标签名字符串，如'aaa,bbb,ccc,ddd
     *
     * @return array
     */
    public function LoadTagsByNameString($s)
    {
        $s = trim($s);
        $s = str_replace(';', ',', $s);
        $s = str_replace('，', ',', $s);
        $s = str_replace('、', ',', $s);
        $s = trim($s);
        $s = strip_tags($s);
        if ($s == '') {
            return array();
        }

        if ($s == ',') {
            return array();
        }

        $a = explode(',', $s);
        $t = array_unique($a);

        if (count($t) == 0) {
            return array();
        }

        $a = array();
        $b = array();
        foreach ($t as $v) {
            if (isset($this->tagsbyname[$v]) == false) {
                $a[] = array('tag_Name', $v);
            } else {
                $b[$v] = &$this->tagsbyname[$v];
            }
        }

        if (count($a) == 0) {
            return $b;
        } else {
            $t = array();
            $array = $this->GetTagList('', array(array('array', $a)), '', '', '');
            foreach ($array as $v) {
                $this->tags[$v->ID] = $v;
                $this->tagsbyname[$v->Name] = &$this->tags[$v->ID];
                $t[$v->Name] = &$this->tags[$v->ID];
            }

            return $b + $t;
        }
    }

    /**
     * 通过数组array[111,333,444,555,666]转换成存储串.
     *
     * @param array $array 标签ID数组
     *
     * @return string
     */
    public function ConvertTagIDtoString($array)
    {
        $s = '';
        foreach ($array as $a) {
            $s .= '{' . $a . '}';
        }

        return $s;
    }

    /**
     * 获取全部置顶文章（优先从cache里读数组）.
     *
     * @param int $type
     *
     * @return array
     */
    public function GetTopArticle($type = 0)
    {
        $varname = 'top_post_array_' . $type;
        if ($this->cache->HasKey($varname) == false) {
            return array();
        }

        $articles_top_notorder_idarray = unserialize($this->cache->$varname);
        if (!is_array($articles_top_notorder_idarray)) {
            CountTopArticle($type, null, null);
            $articles_top_notorder_idarray = unserialize($this->cache->$varname);
        }
        $articles_top_notorder = $this->GetPostByArray($articles_top_notorder_idarray);

        return $articles_top_notorder;
    }

    //###############################################################################################################
    //验证相关

    /**
     * 获取评论key.
     *
     * @param $id
     *
     * @return string
     */
    public function GetCmtKey($id)
    {
        return md5($this->guid . $id . date('Ymdh'));
    }

    /**
     * 验证评论key.
     *
     * @param $id
     * @param $key
     *
     * @return bool
     */
    public function ValidCmtKey($id, $key)
    {
        $nowkey = md5($this->guid . $id . date('Ymdh'));
        $nowkey2 = md5($this->guid . $id . date('Ymdh', time() - (3600 * 1)));

        return $key == $nowkey || $key == $nowkey2;
    }

    /**
     * 获取CSRF Token.
     *
     * @param string $id 应用ID，可以保证每个应用获取不同的Token
     *
     * @return string
     */
    public function GetCSRFToken($id = '')
    {
        $s = $this->user->ID . $this->user->Password . $this->user->Status;

        return md5($this->guid . $s . $id . date('Ymdh'));
    }

    /**
     * 验证CSRF Token.
     *
     * @param string $token
     * @param string $id    应用ID，可为每个应用生成一个专属token
     *
     * @return bool
     */
    public function VerifyCSRFToken($token, $id = '')
    {
        $userString = $this->user->ID . $this->user->Password . $this->user->Status;
        $tokenString = $this->guid . $userString . $id;

        for ($i = 0; $i <= $this->csrfExpiration; $i++) {
            if ($token === md5($tokenString . date('Ymdh', time() - (3600 * $i)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 显示验证码
     *
     * @api Filter_Plugin_Zbp_ShowValidCode 如该接口未被挂载则显示默认验证图片
     *
     * @param string $id 命名事件
     *
     * @return bool
     */
    public function ShowValidCode($id = '')
    {
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_ShowValidCode'] as $fpname => &$fpsignal) {
            return $fpname($id); //*
        }

        $_vc = new ValidateCode();
        $_vc->GetImg();
        setcookie('captcha_' . crc32($this->guid . $id), md5($this->guid . date("Ymdh") . $_vc->GetCode()), null, $this->cookiespath);

        return true;
    }

    /**
     * 比对验证码
     *
     * @api Filter_Plugin_Zbp_CheckValidCode 如该接口未被挂载则比对默认验证码
     *
     * @param string $verifyCode 验证码数值
     * @param string $id         命名事件
     *
     * @return bool
     */
    public function CheckValidCode($verifyCode, $id = '')
    {
        $verifyCode = strtolower($verifyCode);
        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_CheckValidCode'] as $fpname => &$fpsignal) {
            return $fpname($verifyCode, $id); //*
        }

        $original = GetVars('captcha_' . crc32($this->guid . $id), 'COOKIE');
        setcookie('captcha_' . crc32($this->guid . $id), '', time() - 3600, $this->cookiespath);

        return md5($this->guid . date("Ymdh") . strtolower($verifyCode)) == $original
                ||
                md5($this->guid . date("Ymdh", time() - (3600 * 1)) . strtolower($verifyCode)) == $original;
    }

    /**
     * 向导航菜单添加相应条目.
     *
     * @param string $type $type=category,tag,page,item
     * @param string $id
     * @param string $name
     * @param string $url
     */
    public function AddItemToNavbar($type, $id, $name, $url)
    {
        if (!$type) {
            $type = 'item';
        }

        $m = $this->modulesbyfilename['navbar'];
        $s = $m->Content;

        $a = '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';

        if ($this->CheckItemToNavbar($type, $id)) {
            $s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/li>/', $a, $s);
        } else {
            $s .= '<li id="navbar-' . $type . '-' . $id . '"><a href="' . $url . '">' . $name . '</a></li>';
        }

        $m->Content = $s;
        $m->Save();
    }

    /**
     * 删除导航菜单中相应条目.
     *
     * @param string $type
     * @param $id
     */
    public function DelItemToNavbar($type, $id)
    {
        if (!$type) {
            $type = 'item';
        }

        $m = $this->modulesbyfilename['navbar'];
        $s = $m->Content;

        $s = preg_replace('/<li id="navbar-' . $type . '-' . $id . '">.*?<\/li>/', '', $s);

        $m->Content = $s;
        $m->Save();
    }

    /**
     * 检查条目是否在导航菜单中.
     *
     * @param string $type
     * @param $id
     *
     * @return bool
     */
    public function CheckItemToNavbar($type, $id)
    {
        if (!$type) {
            $type = 'item';
        }

        $m = $this->modulesbyfilename['navbar'];
        $s = $m->Content;

        return (bool) strpos($s, 'id="navbar-' . $type . '-' . $id . '"');
    }

    //$signal = good,bad,tips
    private $hint1 = null;
    private $hint2 = null;
    private $hint3 = null;
    private $hint4 = null;
    private $hint5 = null;

    /**
     * 设置提示消息并存入Cookie.
     *
     * @param string $signal  提示类型（good|bad|tips）
     * @param string $content 提示内容
     */
    public function SetHint($signal, $content = '')
    {
        if ($content == '') {
            if ($signal == 'good') {
                $content = $this->lang['msg']['operation_succeed'];
            }

            if ($signal == 'bad') {
                $content = $this->lang['msg']['operation_failed'];
            }
        }
        $content = substr($content, 0, 255);
        if ($this->hint1 == null) {
            $this->hint1 = $signal . '|' . $content;
            setcookie("hint_signal1", $signal . '|' . $content, 0, $this->cookiespath);
        } elseif ($this->hint2 == null) {
            $this->hint2 = $signal . '|' . $content;
            setcookie("hint_signal2", $signal . '|' . $content, 0, $this->cookiespath);
        } elseif ($this->hint3 == null) {
            $this->hint3 = $signal . '|' . $content;
            setcookie("hint_signal3", $signal . '|' . $content, 0, $this->cookiespath);
        } elseif ($this->hint4 == null) {
            $this->hint4 = $signal . '|' . $content;
            setcookie("hint_signal4", $signal . '|' . $content, 0, $this->cookiespath);
        } elseif ($this->hint5 == null) {
            $this->hint5 = $signal . '|' . $content;
            setcookie("hint_signal5", $signal . '|' . $content, 0, $this->cookiespath);
        }
    }

    /**
     * 提取Cookie中的提示消息.
     */
    public function GetHint()
    {
        for ($i = 1; $i <= 5; $i++) {
            $signal = 'hint' . $i;
            $signal = $this->$signal;
            if ($signal) {
                $a = explode('|', $signal);
                $this->ShowHint($a[0], $a[1]);
                setcookie("hint_signal" . $i, '', time() - 3600, $this->cookiespath);
            }
        }
        for ($i = 1; $i <= 5; $i++) {
            $signal = GetVars('hint_signal' . $i, 'COOKIE');
            if ($signal) {
                $a = explode('|', $signal);
                $this->ShowHint($a[0], $a[1]);
                setcookie("hint_signal" . $i, '', time() - 3600, $this->cookiespath);
            }
        }
    }

    /**
     * 由提示消息获取HTML.
     *
     * @param string $signal  提示类型（good|bad|tips）
     * @param string $content 提示内容
     */
    public function ShowHint($signal, $content = '')
    {
        if ($content == '') {
            if ($signal == 'good') {
                $content = $this->lang['msg']['operation_succeed'];
            }

            if ($signal == 'bad') {
                $content = $this->lang['msg']['operation_failed'];
            }
        }
        echo "<div class=\"hint\"><p class=\"hint hint_$signal\">$content</p></div>";
    }

    /**
     * 显示错误信息.
     *
     * @api Filter_Plugin_Zbp_ShowError
     *
     * @param string/int $errorText
     * @param null       $file
     * @param null       $line
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function ShowError($errorText, $file = null, $line = null)
    {
        $errorCode = 0;
        if (is_numeric($errorText)) {
            $errorCode = (int) $errorText;
            $errorText = $this->lang['error'][$errorText];
        }

        if ($errorCode == 2) {
            Http404();
        }

        ZBlogException::$error_id = $errorCode;
        ZBlogException::$error_file = $file;
        ZBlogException::$error_line = $line;

        foreach ($GLOBALS['hooks']['Filter_Plugin_Zbp_ShowError'] as $fpname => &$fpsignal) {
            $fpreturn = $fpname($errorCode, $errorText, $file, $line);
            if ($fpsignal == PLUGIN_EXITSIGNAL_RETURN) {
                $fpsignal = PLUGIN_EXITSIGNAL_NONE;

                return $fpreturn;
            }
        }

        throw new Exception($errorText);
    }

    /**
     * 检查并开启Gzip压缩.
     */
    public function CheckGzip()
    {
        if (extension_loaded("zlib") &&
            isset($_SERVER["HTTP_ACCEPT_ENCODING"]) &&
            strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")
        ) {
            $this->isGzip = true;
        }
    }

    /**
     * 启用Gzip.
     */
    public function StartGzip()
    {
        if (!headers_sent() && $this->isGzip && $this->option['ZC_GZIP_ENABLE']) {
            if (ini_get('output_handler')) {
                return false;
            }

            $a = ob_list_handlers();
            if (in_array('ob_gzhandler', $a) || in_array('zlib output compression', $a)) {
                return false;
            }

            if (function_exists('ini_set') && function_exists('zlib_encode') && $this->option['ZC_YUN_SITE'] !== 'SAE') {
                @ob_end_clean();
                @ini_set('zlib.output_compression', 'On');
                @ini_set('zlib.output_compression_level', '5');
            } elseif (function_exists('ob_gzhandler')) {
                @ob_end_clean();
                @ob_start('ob_gzhandler');
            }
            ob_start();

            return true;
        }

        return false;
    }

    /**
     * 检测网站关闭，如果关闭，则抛出错误.
     *
     * @throws Exception
     */
    public function CheckSiteClosed()
    {
        if ($this->option['ZC_CLOSE_SITE']) {
            $this->ShowError(82, __FILE__, __LINE__);
            exit;
        }
    }

    /**
     * 跳转到安装页面.
     */
    public function RedirectInstall()
    {
        if (!$this->option['ZC_DATABASE_TYPE']) {
            Redirect('./zb_install/index.php');
        }

        if ($this->option['ZC_YUN_SITE']) {
            if ($this->Config('system')->CountItem() == 0) {
                Redirect('./zb_install/index.php');
            }
        }
    }

    /**
     * 检测当前url，如果不符合设置就跳转到固定域名的链接.
     */
    public function RedirectPermanentDomain()
    {
        if ($this->option['ZC_PERMANENT_DOMAIN_ENABLE'] == false) {
            return;
        }

        if ($this->option['ZC_PERMANENT_DOMAIN_REDIRECT'] == false) {
            return;
        }

        $host = str_replace(array('https://', 'http://'), array('', ''), GetCurrentHost(ZBP_PATH, $null));
        $host2 = str_replace(array('https://', 'http://'), array('', ''), $this->host);

        if ($host != $host2) {
            $u = GetRequestUri();
            $u = $this->host . substr($u, 1, strlen($u));
            Redirect301($u);
        }
    }

    /**
     * 注册PostType.
     *
     * @param $typeId
     * @param $name
     * @param string $urlRule      默认是取Page类型的Url Rule
     * @param string $template     默认模板名page
     * @param string $categoryType 当前文章类的分类Type
     * @param string $tagType      当前文章类的标签Type
     *
     * @throws Exception
     */
    public function RegPostType($typeId, $name, $urlRule = '', $template = 'single', $categoryType = null, $tagType = null)
    {
        if ($urlRule == '') {
            $urlRule = $this->option['ZC_PAGE_REGEX'];
        }

        $typeId = (int) $typeId;
        $name = strtolower(trim($name));
        if ($typeId > 99) {
            if (isset($this->posttype[$typeId])) {
                $this->ShowError(87, __FILE__, __LINE__);
            }
        }
        $this->posttype[$typeId] = array($name, $urlRule, $template, $categoryType, $tagType);
    }

    /**
     * @param $typeid
     *
     * @return string
     */
    public function GetPostType_Name($typeid)
    {
        if (isset($this->posttype[$typeid])) {
            return $this->posttype[$typeid][0];
        }

        return '';
    }

    public function GetPostType_UrlRule($typeid)
    {
        if (isset($this->posttype[$typeid])) {
            return $this->posttype[$typeid][1];
        }

        return $this->option['ZC_PAGE_REGEX'];
    }

    public function GetPostType_Template($typeid)
    {
        if (isset($this->posttype[$typeid])) {
            return $this->posttype[$typeid][2];
        }

        return 'single';
    }

    public function GetPostType_CategoryType($typeid)
    {
        if (isset($this->posttype[$typeid])) {
            return $this->posttype[$typeid][3];
        }
    }

    public function GetPostType_TagType($typeid)
    {
        if (isset($this->posttype[$typeid])) {
            return $this->posttype[$typeid][4];
        }
    }

    /**
     * 注册Action.
     *
     * @param $name
     * @param $level
     * @param $title
     */
    public function RegAction($name, $level, $title)
    {
        $this->actions[$name] = $level;
        $this->lang['actions'][$name] = $title;
    }

    /**
     * 获得Action权限注释.
     *
     * @param $name
     *
     * @return mixed
     */
    public function GetActionDescription($name)
    {
        if (isset($this->lang['actions'][$name])) {
            return $this->lang['actions'][$name];
        }

        return $name;
    }

    /**
     * 以下部分为已废弃，但考虑到兼容性保留的代码
     */

    /**
     * 验证用户登录（MD5加zbp->guid盐后的密码）.
     *
     * @deprecated
     *
     * @param string $name         用户名
     * @param string $ps_path_hash MD5加zbp->guid盐后的密码
     * @param object $member       返回读取成功的member对象
     *
     * @return bool
     */
    public function Verify_MD5Path($name, $ps_path_hash, &$member = null)
    {
        if ($name == '' || $ps_path_hash == '') {
            return false;
        }
        $m = $this->GetMemberByName($name);
        if ($m->ID > 0) {
            if ($m->PassWord_MD5Path == $ps_path_hash) {
                $member = $m;

                return true;
            }
        }

        return false;
    }

    /**
     * 获取CSRF Token的错误别名.
     *
     * @deprecated Use ``GetCSRFToken``
     *
     * @param string $id 应用ID，可以保证每个应用获取不同的Token
     *
     * @return string
     */
    public function GetToken($id = '')
    {
        return $this->GetCSRFToken($id);
    }

    /**
     * 验证CSRF Token的错误别名.
     *
     * @deprecated Use ``VerifyCSRFToken``
     *
     * @param $t
     * @param $id
     *
     * @return bool
     */
    public function ValidToken($t, $id = '')
    {
        return $this->VerifyCSRFToken($t, $id);
    }

    /**
     * @deprecated
     *
     * @return bool
     */
    public function LoadCategorys()
    {
        return $this->LoadCategories();
    }

    /**
     * 所有模块重置.
     *
     * @deprecated
     */
    public function AddBuildModuleAll()
    {
    }

    /**
     * 获取会话WebToken.
     *
     * @deprecated 毫无意义，即将废弃
     *
     * @param string $wt_id
     * @param int    $day   默认1天有效期，1小时为1/24，1分钟为1/(24*60)
     *
     * @return string
     */
    public function GetWebToken($wt_id = '', $day = 1)
    {
        $t = intval($day * 24 * 3600) + time();

        return CreateWebToken($wt_id, $t, $this->guid, $this->user->Status, $this->user->ID, $this->user->Password);
    }

    /**
     * 验证会话WebToken.
     *
     * @deprecated 毫无意义，即将废弃
     *
     * @param $wt
     * @param $wt_id
     *
     * @return bool
     */
    public function ValidWebToken($wt, $wt_id = '')
    {
        if (VerifyWebToken($wt, $wt_id, $this->guid, $this->user->Status, $this->user->ID, $this->user->Password) === true) {
            return true;
        }

        return false;
    }
}
